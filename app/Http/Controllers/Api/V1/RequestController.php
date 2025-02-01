<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubmitRequest;
use App\Http\Requests\Api\V1\UpdateRequest;
use App\Http\Resources\Api\V1\RequestResource;
use App\Models\RequestPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Request as RequestModel;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'sort' => ['nullable','in:created_at,confirm'],
            'direction' => ['nullable','in:desc,asc'],
            'status' => ['nullable',Rule::enum(RequestStatus::class)],
            'step' => ['nullable',Rule::enum(RequestStep::class)],
            'q' => ['nullable','string','max:50']
        ]);
        $request = RequestModel::query()
            ->select(['id','request_plan_id','step','status','confirm'])
            ->when($request->filled('q') , function (Builder $builder) use ($request) {
                $builder->search($request->get('q'));
            })->when($request->filled('sort') , function (Builder $builder) use ($request) {
                $builder->orderBy(emptyToNull($request->get('sort' , 'confirm')) ?? 'confirm', $request->get('direction' , 'asc'));
            })->when(! $request->filled('sort') , function (Builder $builder) {
                $builder->orderBy('confirm');
            })
            ->when($request->filled('status') , function (Builder $builder) use ($request) {
                $builder->where('status' , $request->get('status'));
            })->when($request->filled('step') , function (Builder $builder) use ($request) {
                $builder->where('step' , $request->get('step'));
            })
            ->with(['plan'])
            ->where('user_id' , auth()->id())
            ->paginate((int)$request->get('per_page' , 10));


        return RequestResource::collection($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values()
        ]);
    }

    public function show($request): RequestResource
    {
        $request = RequestModel::query()
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->where('user_id' , auth()->id())
            ->findOrFail($request);

        return RequestResource::make($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values()
        ]);
    }

    public function create(SubmitRequest $submitRequest): JsonResponse|RequestResource
    {
        $requestPlan = RequestPlan::query()
            ->withCount(['requests' => function ($q) {
                return $q->where('user_id' , auth()->id());
            }])
            ->published()
            ->findOrFail($submitRequest->request_plan_id);

        if ($requestPlan->requests_count >= $requestPlan->max_allocated_request) {
            return response()->json([
                'error' => 'تعداد درخواست‌های شما برای این پلن به حد مجاز رسیده است.'
            ] , 403);
        }

        $data = $submitRequest->only(['students','amount','body','sheba']);
        $data['total_amount'] = min($requestPlan->max_number_people_supported , $data['students']) * $requestPlan->support_for_each_person_amount;
        try {
            DB::beginTransaction();
            $request = $requestPlan->requests()->create([
                ... $data,
                'date' => dateConverter($submitRequest->date ,'m'),
                'user_id' => auth()->id(),
                'status' => RequestStatus::IN_PROGRESS,
                'step' => RequestStep::APPROVAL_MOSQUE_HEAD_COACH,
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$request->id;

            $imamLetter = $submitRequest->file('imam_letter');
            $request->imamLetter()->create([
                'path' => $imamLetter->store($path,$disk),
                'mime_type' => $imamLetter->getMimeType(),
                'size' => $imamLetter->getSize(),
                'disk' => $disk,
                'subject' => $request::FILE_IMAM_LETTER_SUBJECT
            ]);

            $areaInterfaceLetter = $submitRequest->file('area_interface_letter');
            $request->areaInterfaceLetter()->create([
                'path' => $areaInterfaceLetter->store($path,$disk),
                'mime_type' => $areaInterfaceLetter->getMimeType(),
                'size' => $areaInterfaceLetter->getSize(),
                'disk' => $disk,
                'subject' => $request::FILE_AREA_INTERFACE_LETTER_SUBJECT
            ]);
            DB::commit();
            $request->load(['areaInterfaceLetter','imamLetter','plan']);
            return RequestResource::make($request);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function confirm($request): RequestResource
    {
        $request = RequestModel::query()
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->where('user_id' , auth()->id())
//            ->where('confirm' , false)
            ->findOrFail($request);

        $request->update([
            'confirm' => true
        ]);
        return RequestResource::make($request);
    }

    public function update(UpdateRequest $updateRequest , $request): JsonResponse|RequestResource
    {
        $request = RequestModel::query()
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->whereHas('plan')
            ->where('user_id' , auth()->id())
            ->confirmed()
            ->where('status' , RequestStatus::ACTION_NEEDED)
            ->findOrFail($request);
        $data = $updateRequest->only(['students','amount','date','body']);
        if ($updateRequest->filled('students')) {
            $data['students'] = min($request->plan->max_number_people_supported , $data['students']);
            $data['total_amount'] = $data['students'] * $request->plan->support_for_each_person_amount;
        }
        if ($updateRequest->filled('date')) {
            $data['date'] = dateConverter($updateRequest->date ,'m');
        }
        $data['status'] = RequestStatus::IN_PROGRESS;
        try {
            DB::beginTransaction();
            $request->update($data);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$request->id;

            if ($updateRequest->hasFile('imam_letter')) {
                if ($request->imamLetter) {
                    $request->imamLetter->delete();
                }

                $imamLetter = $updateRequest->file('imam_letter');
                $request->imamLetter()->create([
                    'path' => $imamLetter->store($path,$disk),
                    'mime_type' => $imamLetter->getMimeType(),
                    'size' => $imamLetter->getSize(),
                    'disk' => $disk,
                    'subject' => $request::FILE_IMAM_LETTER_SUBJECT
                ]);
            }
            if ($updateRequest->hasFile('area_interface_letter')) {
                if ($request->areaInterfaceLetter) {
                    $request->areaInterfaceLetter->delete();
                }
                $areaInterfaceLetter = $updateRequest->file('area_interface_letter');
                $request->areaInterfaceLetter()->create([
                    'path' => $areaInterfaceLetter->store($path,$disk),
                    'mime_type' => $areaInterfaceLetter->getMimeType(),
                    'size' => $areaInterfaceLetter->getSize(),
                    'disk' => $disk,
                    'subject' => $request::FILE_AREA_INTERFACE_LETTER_SUBJECT
                ]);
            }
            DB::commit();
            $request->refresh();
            $request->load(['areaInterfaceLetter','imamLetter']);
            return RequestResource::make($request);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }
}
