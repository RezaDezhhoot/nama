<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminStoreRequest;
use App\Http\Requests\Api\V1\SubmitRequest;
use App\Http\Requests\Api\V1\UpdateRequest;
use App\Http\Resources\Api\V1\RequestResource;
use App\Models\RequestPlan;
use App\Models\UserRole;
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
            ->item(\request()->get('item_id'))
            ->select(['id','request_plan_id','step','status','confirm','created_at','updated_at'])
            ->with(['report'])
            ->when($request->filled('q') , function (Builder $builder) use ($request) {
                $builder->search($request->get('q'))->orWhereHas('plan' , function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'));
                });
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
            ->role(\request()->get('role'))
            ->paginate((int)$request->get('per_page' , 10));

        return RequestResource::collection($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values()
        ]);
    }

    public function show($request): RequestResource
    {
        $request = RequestModel::query()
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->with(['areaInterfaceLetter','imamLetter','plan','report','report.images','report.video'])
            ->findOrFail($request);

        return RequestResource::make($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values(),
            'back_steps' => $request->step->backSteps()
        ]);
    }

    public function create(SubmitRequest $submitRequest): JsonResponse|RequestResource
    {
        $itemId = \request()->get('item_id');
        $requestPlan = RequestPlan::query()
            ->published()
            ->findOrFail($submitRequest->request_plan_id);

        $validRole = UserRole::query()
            ->where('user_id' , auth()->id())
            ->where('item_id' , $itemId)
            ->where('role' , OperatorRole::MOSQUE_HEAD_COACH)
            ->whereHas('unit')
            ->firstOrFail();

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
                'step' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER,
                'confirm' => true,
                'item_id' => $itemId,
                'unit_id' => $validRole->unit_id
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$request->id;

            if ($submitRequest->hasFile('imam_letter')) {
                $imamLetter = $submitRequest->file('imam_letter');
                $request->imamLetter()->create([
                    'path' => $imamLetter->store($path,$disk),
                    'mime_type' => $imamLetter->getMimeType(),
                    'size' => $imamLetter->getSize(),
                    'disk' => $disk,
                    'subject' => $request::FILE_IMAM_LETTER_SUBJECT
                ]);
            }
            if ($submitRequest->hasFile('area_interface_letter')) {
                $areaInterfaceLetter = $submitRequest->file('area_interface_letter');
                $request->areaInterfaceLetter()->create([
                    'path' => $areaInterfaceLetter->store($path,$disk),
                    'mime_type' => $areaInterfaceLetter->getMimeType(),
                    'size' => $areaInterfaceLetter->getSize(),
                    'disk' => $disk,
                    'subject' => $request::FILE_AREA_INTERFACE_LETTER_SUBJECT
                ]);
            }
            DB::commit();
            $request->load(['areaInterfaceLetter','imamLetter','plan']);
            $request->plan->loadCount(['requests' => function ($q) {
                return $q->where('user_id' , auth()->id());
            }]);

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
            ->item(\request()->get('item_id'))
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->where('user_id' , auth()->id())
            ->where('confirm' , false)
            ->findOrFail($request);

        $request->update([
            'confirm' => true
        ]);
        return RequestResource::make($request);
    }

    public function update(UpdateRequest $updateRequest , $request): JsonResponse|RequestResource
    {
        $request = RequestModel::query()
            ->item(\request()->get('item_id'))
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->whereHas('plan')
            ->where('user_id' , auth()->id())
            ->confirmed()
            ->where('status' , RequestStatus::ACTION_NEEDED)
            ->findOrFail($request);
        $data = $updateRequest->only(['students','amount','date','body']);
        if ($updateRequest->filled('students')) {
            $data['total_amount'] = min($request->plan->max_number_people_supported, $data['students']) *  $request->plan->support_for_each_person_amount;
        }
        if ($updateRequest->filled('date')) {
            $data['date'] = dateConverter($updateRequest->date ,'m');
        }
        $data['status'] = RequestStatus::IN_PROGRESS;
        try {
            DB::beginTransaction();
            $request->update([...$data , 'step' => $request->last_updated_by]);
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
            $request->load(['areaInterfaceLetter','imamLetter','plan']);
            $request->plan->loadCount(['requests' => function ($q) {
                return $q->where('user_id' , auth()->id());
            }]);
            return RequestResource::make($request);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function adminStore(AdminStoreRequest $adminStoreRequest , $request): RequestResource
    {
        $request = RequestModel::query()
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->with(['areaInterfaceLetter','imamLetter','plan','report','report.images','report.video'])
            ->where('status',RequestStatus::IN_PROGRESS)
            ->where('step','!=',RequestStep::APPROVAL_MOSQUE_HEAD_COACH)
            ->findOrFail($request);

        $request->last_updated_by = $request->step;
        if ($adminStoreRequest->action == "accept") {
            $request->status = RequestStatus::IN_PROGRESS;
            switch ($request->step) {
//                case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
//                    $request->step = RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
//                    break;
                case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                    $request->step = RequestStep::APPROVAL_AREA_INTERFACE;
                    break;
                case RequestStep::APPROVAL_AREA_INTERFACE:
                    $request->step = RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES;
                    break;
                case RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                    $request->step = RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                    $request->offer_amount = $adminStoreRequest->offer_amount;
                    break;
                case RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                    $request->step = RequestStep::FINISH;
                    $request->status = RequestStatus::DONE;
                    $request->final_amount = $adminStoreRequest->final_amount;


                    break;
            }
        } else if ($adminStoreRequest->action == "reject") {
            $request->status = RequestStatus::REJECTED->value;
        } else  {
            if (
                ! in_array(
                    RequestStep::tryFrom($adminStoreRequest->to) ,
                    $request->step->backSteps()
                )
            ) abort(422);

            $request->step = $adminStoreRequest->to;
            $request->status = RequestStatus::ACTION_NEEDED->value;
        }
        if ($adminStoreRequest->filled('comment')) {
            $request->comments()->create([
                'user_id' => auth()->id(),
                'body' => $adminStoreRequest->comment,
                'display_name' => OperatorRole::from(\request()->get('role'))->label(),
            ]);
            $request->message = $adminStoreRequest->comment;
            $request->messages[\request()->get('role')] = $adminStoreRequest->comment;
        }

        $request->save();
        return RequestResource::make($request);
    }
}
