<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FileStatus;
use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Enums\SchoolCoachType;
use App\Enums\UnitSubType;
use App\Events\ActionNeededReportEvent;
use App\Events\ConfirmationReportEvent;
use App\Events\RejectReportEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminStoreReportRequest;
use App\Http\Requests\Api\V1\AdminStoreRequest;
use App\Http\Requests\Api\V1\SubmitReportRequest;
use App\Http\Requests\Api\V1\UpdateReportRequest;
use App\Http\Requests\Api\V1\UpdateRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Http\Resources\Api\V1\ReportResource;
use App\Models\Comment;
use App\Models\DashboardItem;
use App\Models\Report;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use function Livewire\trigger;

class ReportController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'sort' => ['nullable','in:created_at,confirm'],
            'direction' => ['nullable','in:desc,asc'],
            'step' => ['nullable',Rule::enum(RequestStep::class)],
            'q' => ['nullable','string','max:50']
        ]);
        $role = OperatorRole::from(request()->get('role'));
        return ReportResource::collection(
            Report::query()
                ->when($request->filled('sub_type') , function (Builder $builder) use ($request) {
                    $builder->whereHas('request' , function (Builder $builder) use ($request) {
                        $builder->whereHas('unit' , function (Builder $builder) use ($request) {
                            $builder->where('sub_type' , $request->get('sub_type'));
                        });
                    });
                })
                ->when($request->filled('school_coach_type') , function (Builder $builder) use ($request) {
                    $builder->whereHas('request' , function (Builder $builder) use ($request) {
                        $builder->whereHas('roles' , function (Builder $builder) use ($request) {
                            $builder->where('school_coach_type' , $request->get('school_coach_type'));
                        });
                    });
                })
                ->role(\request()->get('role'))->item(\request()->get('item_id'))->with(['request','request.plan'])->whereHas('request' , function (Builder $builder) use ($request) {
                $builder->when($request->filled('q') , function (Builder $builder) use ($request) {
                   $builder->where(function (Builder $builder) use ($request) {
                       $builder->search($request->get('q'))->orWhereHas('plan' , function (Builder $builder) use ($request) {
                           $builder->search($request->get('q'));
                       })->orWhere(function (Builder $builder) use ($request){
                           $builder->whereIn('user_id' , User::query()->search($request->get('q'))->take(30)->get()->pluck('id')->toArray());
                       })->orWhereHas('unit' , function (Builder $builder) use ($request) {
                           $builder->search($request->get('q'));
                       });
                   });
                });
            })->when($request->filled('status') , function (Builder $builder) use ($request , $role) {
                $builder->where(function (Builder $builder) use ($request , $role) {
                    if ( $request->get('status') == "done_temp") {
                        $builder->whereIn('step' , $role->next());
                    } else {
                        if ($request->query('role') == OperatorRole::MOSQUE_HEAD_COACH->value) {
                            $builder->where('status' , $request->get('status'));
                        } else {
                            $builder->where('status' , $request->get('status'))->whereNotIn('step' , $role->next());
                        }
                    }
                });
            })->when($request->filled('step') , function (Builder $builder) use ($request) {
                $builder->where('step' , $request->get('step'));
            })->when($request->filled('plan_id') , function (Builder $builder) use ($request) {
               $builder->whereHas('request' , function (Builder $builder) use ($request) {
                   $builder->where('request_plan_id' , $request->get('plan_id'));
               });
            })->when($request->filled('unit_id') , function (Builder $builder) use ($request) {
                $builder->whereHas('request' , function (Builder $builder) use ($request) {
                    $builder->where('unit_id' , $request->get('unit_id'));
                });
            })->when($request->filled('sort') , function (Builder $builder) use ($request) {
                    $dir = emptyToNull( $request->get('direction')) ?? "asc";
                $builder->orderBy(emptyToNull($request->get('sort' , 'confirm')) ?? 'confirm', $dir);
            })->when(! $request->filled('sort') , function (Builder $builder) {
                $builder->orderBy('confirm');
            })->paginate((int)$request->get('per_page' , 10))
        )->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values(),
            'sub_types' => UnitSubType::classed(),
            'school_coach_type' => SchoolCoachType::labels()
        ]);
    }

    public function show($report): ReportResource
    {
        $report = Report::query()->role(\request()->get('role'))->item(\request()->get('item_id'))
            ->with(['request','images','otherVideos','video','request.areaInterfaceLetter','request.imamLetter','request.plan','request.otherImamLetter','request.otherAreaInterfaceLetter','request.images','images2'])
            ->findOrFail($report);
        return ReportResource::make(
            $report
        )->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values(),
            'back_steps' => $report->step->backSteps(),
            'sub_types' => UnitSubType::classed(),
            'school_coach_type' => SchoolCoachType::labels()
        ]);
    }

    public function create(SubmitReportRequest $submitReportRequest , $request): ReportResource|JsonResponse
    {
        $request = RequestModel::query()
            ->with(['areaInterfaceLetter','imamLetter','plan'])
            ->whereHas('plan')
            ->item(\request()->get('item_id'))
            ->where('user_id' , auth()->id())
            ->whereDoesntHave('report')
            ->confirmed()
            ->where('status' , RequestStatus::DONE)
            ->where('step' , RequestStep::FINISH)
            ->findOrFail($request);
        $data = $submitReportRequest->only(['students','body']);
        try {
            DB::beginTransaction();
            $report = $request->report()->create([
                ... $data,
                'date' => dateConverter($submitReportRequest->date ,'m'),
                'step' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER,
                'status' => RequestStatus::IN_PROGRESS,
                'amount' => 0,
                'confirm' => true,
                'item_id' => \request()->get('item_id')
            ]);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'reports/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$report->id;

            if ($submitReportRequest->hasFile('video')) {
                $video = $submitReportRequest->file('video');
                $report->video()->create([
                    'path' => $video->store($path,$disk),
                    'mime_type' => $video->getMimeType(),
                    'size' => $video->getSize(),
                    'disk' => $disk,
                    'subject' => $report::FILE_VIDEOS_SUBJECT
                ]);
            }
            $images = [];
            foreach ($submitReportRequest->file('images') as $image) {
                $images[] = [
                    'path' => $image->store($path,$disk),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'disk' => $disk,
                    'subject' => $report::FILE_IMAGES_SUBJECT,
                    'fileable_type' => $report->getMorphClass(),
                    'fileable_id' => $report->id,
                    'status' => FileStatus::PROCESSED
                ];
            }
            if ($submitReportRequest->hasFile('otherVideos')) {
                $videos = [];
                foreach ($submitReportRequest->file('otherVideos') as $v) {
                    $videos[] = [
                        'path' => $v->store($path,$disk),
                        'mime_type' => $v->getMimeType(),
                        'size' => $v->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_OTHER_VIDEOS_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($videos) > 0) {
                    $report->otherVideos()->insert($videos);
                }
            }
            if ($submitReportRequest->hasFile('images2')) {
                $images2 = [];
                foreach ($submitReportRequest->file('images2') as $v) {
                    $images2[] = [
                        'path' => $v->store($path,$disk),
                        'mime_type' => $v->getMimeType(),
                        'size' => $v->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_IMAGES2_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($images2) > 0) {
                    $report->images2()->insert($images2);
                }
            }
            $report->images()->insert($images);
            DB::commit();
            $report->load(['request','images','otherVideos','video','request.areaInterfaceLetter','request.imamLetter','request.plan']);
            return ReportResource::make($report);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال گزارش به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function confirm($report): ReportResource
    {
        $report = Report::query()
            ->item(\request()->get('item_id'))
            ->with(['request','images','video','otherVideos','request.areaInterfaceLetter','request.imamLetter','request.plan','request.otherImamLetter','request.otherAreaInterfaceLetter','images2','request.images'])
            ->whereHas('request' , function (Builder $builder)  {
                $builder->where('user_id' , auth()->id());
            })->where('confirm' , false)
            ->findOrFail($report);

        $report->update([
            'confirm' => true
        ]);
        return ReportResource::make($report);
    }

    public function update(UpdateReportRequest $updateReportRequest , $report): ReportResource|JsonResponse
    {
        $report =  Report::query()
            ->item(\request()->get('item_id'))
            ->with(['request','images','video','otherVideos','request.areaInterfaceLetter','request.imamLetter','request.plan','request.otherImamLetter','request.otherAreaInterfaceLetter'])
            ->whereIn('status' , [RequestStatus::ACTION_NEEDED,RequestStatus::PENDING])
            ->confirmed()
            ->whereHas('request' , function (Builder $builder) {
                $builder->where('user_id' , auth()->id());
            })->findOrFail($report);
        $data = $updateReportRequest->only(['students','body','amount']);
        $data['status'] = RequestStatus::IN_PROGRESS;
        if ($updateReportRequest->filled('date')) {
            $data['date'] = dateConverter($updateReportRequest->date ,'m');
        }
        try {
            DB::beginTransaction();
            $report->step = $report->last_updated_by ?? RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
            $report->fill($data);
            $disk = config('site.default_disk');
            $now = now();
            $path =  'reports/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$report->id;

            if ($report->step === RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER && $report->auto_accept_period) {
                $report->auto_accept_at = now()->addHours($report->auto_accept_period);
            } else if ($report->last_updated_by === RequestStep::APPROVAL_AREA_INTERFACE && $report->notify_period) {
                $report->next_notify_at = now()->addHours($report->notify_period);
            }
            $report->save();

            if ($updateReportRequest->hasFile('video') || ($updateReportRequest->filled('remove_video') && $updateReportRequest->remove_video) ) {
                if ($report->video) {
                    $report->video->delete();
                }
                if ($updateReportRequest->hasFile('video')) {
                    $video = $updateReportRequest->file('video');
                    $report->video()->create([
                        'path' => $video->store($path,$disk),
                        'mime_type' => $video->getMimeType(),
                        'size' => $video->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_VIDEOS_SUBJECT
                    ]);
                }
            }
            $report->files()->whereIn('id' , $updateReportRequest->get('images_to_remove',[]))->delete();
            if ($updateReportRequest->hasFile('images')) {
                $images = [];
                foreach ($updateReportRequest->file('images') ?? [] as $image) {
                    $images[] = [
                        'path' => $image->store($path,$disk),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_IMAGES_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($images) > 0) {
                    $report->images()->insert($images);
                }
            }

            if ($updateReportRequest->hasFile('otherVideos')) {
                $videos = [];
                foreach ($updateReportRequest->file('otherVideos') as $v) {
                    $videos[] = [
                        'path' => $v->store($path,$disk),
                        'mime_type' => $v->getMimeType(),
                        'size' => $v->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_OTHER_VIDEOS_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($videos) > 0) {
                    $report->otherVideos()->insert($videos);
                }
            }

            if ($updateReportRequest->hasFile('images2')) {
                $images2 = [];
                foreach ($updateReportRequest->file('images2') as $v) {
                    $images2[] = [
                        'path' => $v->store($path,$disk),
                        'mime_type' => $v->getMimeType(),
                        'size' => $v->getSize(),
                        'disk' => $disk,
                        'subject' => $report::FILE_IMAGES2_SUBJECT,
                        'fileable_type' => $report->getMorphClass(),
                        'fileable_id' => $report->id,
                        'status' => FileStatus::PROCESSED
                    ];
                }
                if (sizeof($images2) > 0) {
                    $report->images2()->insert($images2);
                }
            }

            DB::commit();
            $report->refresh();
            $report->load(['images','otherVideos','video','images2']);
            return ReportResource::make($report);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return response()->json([
            'error' => 'مشکلی در حین ارسال گزارش به وجود آمده است ، لطفا مجدد تلاش کنید'
        ] , 500);
    }

    public function adminStore(AdminStoreReportRequest $adminStoreReportRequest , $report): ReportResource
    {
        if (! \request()->filled('role')) {
            abort(403);
        }
        $itemID = \request()->get('item_id');
        $item = DashboardItem::query()->find($itemID);
        $report =  Report::query()->role(\request()->get('role'))
            ->item(\request()->get('item_id'))
            ->whereIn('status',[RequestStatus::IN_PROGRESS,RequestStatus::ACTION_NEEDED])
            ->whereIn('step',OperatorRole::from(\request()->get('role'))->step())
            ->where('step','!=',RequestStep::APPROVAL_MOSQUE_HEAD_COACH)
            ->with(['request','images','otherVideos','video','request.areaInterfaceLetter','request.imamLetter','request.plan','request.otherImamLetter','request.otherAreaInterfaceLetter','images2','request.images'])
            ->findOrFail($report);
        $report->last_updated_by = $report->step;
        $from_status = $report->status;
        $step = $report->step;

        if ($adminStoreReportRequest->action == "accept") {
            $report->status = RequestStatus::IN_PROGRESS;
            $report->toNextStep($adminStoreReportRequest->offer_amount , $adminStoreReportRequest->final_amount);
            if ($report->status === RequestStatus::DONE) {
                event(new ConfirmationReportEvent($report));
            }
        } else if ($adminStoreReportRequest->action == "reject") {
            $report->status = RequestStatus::REJECTED->value;
            event(new RejectReportEvent($report));
        } else  {
            if (
                ! in_array(
                    RequestStep::tryFrom($adminStoreReportRequest->to) ,
                    $report->step->backSteps()
                )
            ) abort(422);
            $report->step = $adminStoreReportRequest->to;
            $report->status = RequestStatus::ACTION_NEEDED->value;
            event(new ActionNeededReportEvent($report));
        }
        if ($adminStoreReportRequest->filled('comment')) {
            $report->message = $adminStoreReportRequest->input('comment');
            $report->comments()->create([
                'user_id' => auth()->id(),
                'body' => $adminStoreReportRequest->comment,
                'display_name' => OperatorRole::from(\request()->get('role'))->label($item->type),
                'from_status' => $from_status,
                'to_status' => $report->status,
                'step' => $step
            ]);
            if (! $report->messages) {
                $report->messages = [];
            }
            $messages = $report->messages;
            $messages[\request()->get('role')] = $adminStoreReportRequest->comment;
            $report->messages = $messages;
        }
        $report->save();
        return ReportResource::make($report);
    }

    public function getComments($report): AnonymousResourceCollection
    {
        $items = Comment::query()
            ->latest()
            ->with(['commentable','commentable.request','commentable.request.item'])
            ->whereHasMorph('commentable',[Report::class],function ($q) use ($report){
                $q->where('commentable_id' , $report);
            })
            ->when(\request()->filled('step') , function ($q){
                $q->where('step' , \request()->get('step'));
            })
            ->with(['user'])
            ->paginate(\request()->get('per_page' , 10));

        return CommentResource::collection($items);
    }
}
