<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Enums\WrittenRequestStep;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminStoreWrittenRequest;
use App\Http\Requests\Api\V1\SubmitWrittenRequest;
use App\Http\Requests\Api\V1\UpdateWrittenRequest;
use App\Http\Resources\Api\V1\WrittenRequestResource;
use App\Models\UserRole;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WrittenController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'sort' => ['nullable','in:created_at,updated_at'],
            'direction' => ['nullable','in:desc,asc'],
            'status' => ['nullable',Rule::enum(RequestStatus::class)],
            'step' => ['nullable',Rule::enum(WrittenRequestStep::class)],
            'q' => ['nullable','string','max:50']
        ]);

        $request = WrittenRequest::query()
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->when($request->filled('q') , function (Builder $builder) use ($request) {
                $builder->search($request->get('q'));
            })->when($request->filled('sort') , function (Builder $builder) use ($request) {
                $builder->orderBy($request->get('sort' , 'id') , $request->get('direction' , 'asc'));
            })->when(! $request->filled('sort') , function (Builder $builder) {
                $builder->latest('updated_at');
            })
            ->when($request->filled('status') , function (Builder $builder) use ($request) {
                $builder->where('status' , $request->get('status'));
            })->when($request->filled('step') , function (Builder $builder) use ($request) {
                $builder->where('step' , $request->get('step'));
            })
            ->where('user_id' , auth()->id())
            ->paginate((int)$request->get('per_page' , 10));

        return WrittenRequestResource::collection($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => WrittenRequestStep::values()
        ]);
    }

    public function show($id): WrittenRequestResource
    {
        return WrittenRequestResource::make(
            WrittenRequest::query()->role(\request()->get('role'))->item(\request()->get('item_id'))->with(['sign','letter'])->where('user_id' , auth()->id())->findOrFail($id)
        )->additional([
            'statuses' => RequestStatus::values(),
            'steps' => WrittenRequestStep::values()
        ]);
    }

    public function store(SubmitWrittenRequest $request): JsonResponse|WrittenRequestResource
    {
        $data = $request->only(['title','body']);
        $itemId = \request()->get('item_id');
        $validRole = UserRole::query()
            ->where('user_id' , auth()->id())
            ->where('item_id' , $itemId)
            ->where('role' , OperatorRole::MOSQUE_HEAD_COACH)
            ->whereHas('unit')
            ->firstOrFail();
        try {
            DB::beginTransaction();
            $writtenRequest = WrittenRequest::query()->create([
                ... $data,
                'step' => match (OperatorRole::from($request->reference_to)) {
                    OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES => WrittenRequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,
                    OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,
                } ,
                'status' => RequestStatus::IN_PROGRESS,
                'user_id' => auth()->id(),
                'item_id' => $itemId,
                'unit_id' => $validRole->unit_id
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'written_requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$writtenRequest->id;

            if ($request->hasFile('letter')) {
                $letter = $request->file('letter');
                $writtenRequest->letter()->create([
                    'path' => $letter->store($path,$disk),
                    'mime_type' => $letter->getMimeType(),
                    'size' => $letter->getSize(),
                    'disk' => $disk,
                    'subject' => $writtenRequest::FILE_LETTER_SUBJECT
                ]);
            }

            if ($request->hasFile('sign')) {
                $sign = $request->file('sign');
                $writtenRequest->letter()->create([
                    'path' => $sign->store($path,$disk),
                    'mime_type' => $sign->getMimeType(),
                    'size' => $sign->getSize(),
                    'disk' => $disk,
                    'subject' => $writtenRequest::FILE_SIGN_SUBJECT
                ]);
            }
            DB::commit();
            $writtenRequest->load(['letter','sign']);

            return WrittenRequestResource::make($writtenRequest);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function update(UpdateWrittenRequest $request , $id): JsonResponse|WrittenRequestResource
    {
        $writtenRequest = WrittenRequest::query()
            ->item(\request()->get('item_id'))
            ->with(['sign','letter'])->where('user_id' , auth()->id())
            ->where('status' , RequestStatus::ACTION_NEEDED)
            ->findOrFail($id);
        $data = $request->only(['title','body']);
        if ($request->filled('reference_to')) {
            $data['step'] = match (OperatorRole::from($request->reference_to)) {
                OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES => WrittenRequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,
                OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,
            };
        }
        try {
            DB::beginTransaction();
            $writtenRequest->update([
                ... $data,
                'status' => RequestStatus::IN_PROGRESS,
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'written_requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$writtenRequest->id;

            if ($request->hasFile('letter')) {
                if ($writtenRequest->letter) {
                    $writtenRequest->letter->delete();
                }
                $letter = $request->file('letter');
                $writtenRequest->letter()->create([
                    'path' => $letter->store($path,$disk),
                    'mime_type' => $letter->getMimeType(),
                    'size' => $letter->getSize(),
                    'disk' => $disk,
                    'subject' => $writtenRequest::FILE_LETTER_SUBJECT
                ]);
            }
            if ($request->hasFile('sign')) {
                if ($writtenRequest->sign) {
                    $writtenRequest->sign->delete();
                }
                $sign = $request->file('sign');
                $writtenRequest->sign()->create([
                    'path' => $sign->store($path,$disk),
                    'mime_type' => $sign->getMimeType(),
                    'size' => $sign->getSize(),
                    'disk' => $disk,
                    'subject' => $writtenRequest::FILE_SIGN_SUBJECT
                ]);
            }
            DB::commit();
            $writtenRequest->refresh();
            $writtenRequest->load(['sign','letter']);
            return WrittenRequestResource::make($writtenRequest);

        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال درخواست به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function adminStore(AdminStoreWrittenRequest $request , $id): WrittenRequestResource
    {
        $writtenRequest = WrittenRequest::query()
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->with(['sign','letter'])
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->findOrFail($id);
        $writtenRequest->countable = $request->countable;
        if ($request->action == "accept") {
            $writtenRequest->status = RequestStatus::DONE;
        }  else if ($request->action == "reject") {
            $writtenRequest->status = RequestStatus::REJECTED->value;
        } else  {
            $writtenRequest->status = RequestStatus::ACTION_NEEDED->value;
        }
        $writtenRequest->fill([
            'message' => $request->comment,
        ])->save();
        return WrittenRequestResource::make($writtenRequest);
    }
}
