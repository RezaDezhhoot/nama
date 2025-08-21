<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestPlanVersion;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Enums\SchoolCoachType;
use App\Enums\UnitSubType;
use App\Events\ActionNeededRequestEvent;
use App\Events\ConfirmationRequestEvent;
use App\Events\RejectRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminStoreRequest;
use App\Http\Requests\Api\V1\SubmitRequest;
use App\Http\Requests\Api\V1\UpdateRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Http\Resources\Api\V1\MediaResource;
use App\Http\Resources\Api\V1\RequestResource;
use App\Models\Comment;
use App\Models\File;
use App\Models\RequestPlan;
use App\Models\User;
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
            'sort' => ['nullable','in:created_at,confirm,id'],
            'step' => ['nullable',Rule::enum(RequestStep::class)],
            'q' => ['nullable','string','max:50']
        ]);
        $role = OperatorRole::from(request()->get('role'));

        $requests = RequestModel::query()
            ->with(['report'])
            ->select("requests.*","r.final_amount as report_final_amount")
            ->leftJoin('reports AS r',"r.request_id",'=','requests.id')
            ->when($request->filled('sub_type') , function (Builder $builder) use ($request) {
                $builder->whereHas('unit' , function (Builder $builder) use ($request) {
                   $builder->where('sub_type' , $request->get('sub_type'));
                });
            })
            ->when($request->filled('invoice') , function (Builder $builder) {
                $builder->whereHas('report' , function (Builder $builder) {
                        $builder->where('status' , RequestStatus::DONE);
                    });
            })
            ->when($request->filled('school_coach_type') , function (Builder $builder) use ($request) {
                $builder->whereHas('roles' , function (Builder $builder) use ($request) {
                    $builder->where('school_coach_type' , $request->get('school_coach_type'));
                });
            })
            ->when($request->filled('version') , function (Builder $builder) use ($request) {
                $builder->whereHas('plan' , function (Builder $builder) use ($request) {
                    $builder->where('version' , $request->get('version'));
                });
            })
            ->when($request->filled('normal_request') , function (Builder $builder) use ($request) {
                $builder->where('requests.single_step' , false);
            })
            ->when($request->filled('from_date') , function (Builder $builder) use ($request) {
                $builder->where('requests.created_at' , ">=",dateConverter(convert2english($request->get('from_date')),'g'));
            })
            ->when($request->filled('to_date') , function (Builder $builder) use ($request) {
                $builder->where('requests.created_at' , "<=",dateConverter(convert2english($request->get('to_date')),'g'));
            })
            ->when($request->filled('single_request') , function (Builder $builder) use ($request) {
                $builder->where('requests.single_step' , true);
            })
            ->item(\request()->get('item_id'))
            ->when($request->filled('q') , function (Builder $builder) use ($request) {
                $builder->search($request->get('q'))->orWhereHas('plan' , function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'));
                })->orWhere(function (Builder $builder) use ($request){
                    $builder->whereIn('user_id' , User::query()->search($request->get('q'))->take(30)->get()->pluck('id')->toArray());
                })->orWhereHas('unit' , function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'));
                })->orWhereHas('report' , function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'));
                });
            })->when($request->filled('sort') , function (Builder $builder) use ($request) {
                $dir = emptyToNull( $request->get('direction')) ?? "asc";
                $builder->orderBy('requests.'.emptyToNull($request->get('sort' , 'confirm')) ?? 'confirm', $dir);
            })->when(! $request->filled('sort') , function (Builder $builder) {
                $builder->orderBy('requests.confirm');
            })
            ->when($request->filled('plan_id') , function (Builder $builder) use ($request) {
                $builder->where('requests.request_plan_id' , $request->get('plan_id'));
            })
            ->when($request->filled('unit_id') , function (Builder $builder) use ($request) {
                $builder->where('requests.unit_id' , $request->get('unit_id'));
            })
            ->when($request->filled('status') , function (Builder $builder) use ($request , $role) {
                $builder->where(function (Builder $builder) use ($request , $role) {
                    if ( $request->get('status') == "done_temp") {
                        $builder->whereIn('requests.step' , $role->next());
                    } else {
                        if ($request->query('role') == OperatorRole::MOSQUE_HEAD_COACH->value) {
                            $builder->where('requests.status' , $request->get('status'));
                        } else {
                            $builder->where('requests.status' , $request->get('status'))->whereNotIn('requests.step' , $role->next());
                        }
                    }
                });
            })->when($request->filled('step') , function (Builder $builder) use ($request) {
                $builder->where('requests.step' , $request->get('step'));
            })
            ->with(['plan','unit','report'])
            ->role(\request()->get('role'));

        $total_request_amount = 0;
        $total_report_amount = 0;
        if ($request->filled('invoice')) {
            $total_request_amount = (int)$requests->sum('requests.final_amount');
            $total_report_amount = (int)$requests->sum('r.final_amount');
        }
        $requests = $requests->paginate((int)$request->get('per_page' , 10));
        return RequestResource::collection($requests)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values(),
            'sub_types' => UnitSubType::classed(),
            'school_coach_type' => SchoolCoachType::labels(),
            'versions' => RequestPlanVersion::values(),
            'total_request_amount' => $total_request_amount ,
            'total_report_amount' => $total_report_amount ,
            'request_and_report_total_amount' =>  $total_request_amount + $total_report_amount
        ]);
    }

    public function show($request): RequestResource
    {
        $request = RequestModel::query()
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->relations()
            ->findOrFail($request);

        return RequestResource::make($request)->additional([
            'statuses' => RequestStatus::values(),
            'steps' => RequestStep::values(),
            'back_steps' => $request->step->backSteps(),
            'sub_types' => UnitSubType::classed(),
            'school_coach_type' => SchoolCoachType::values()
        ]);
    }

    public function create(SubmitRequest $submitRequest): JsonResponse|RequestResource
    {
        $itemId = \request()->get('item_id');
        $requestPlan = RequestPlan::query()
//            ->with(['requirementsv'])
            ->where('item_id' , $itemId)
            ->published()
            ->findOrFail($submitRequest->request_plan_id);

        $isActive = $requestPlan->isActive();
        if (! $isActive) {
            return response()->json([
                'error' => 'پیشنیاز های این اکشن پلن رعایت نشده است'
            ] , 403);
        }

        $validRole = UserRole::query()
            ->with(['unit'])
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
        $auto_accept_at = null;
        $request = new RequestModel;
        $request->plan()->associate($requestPlan);

        if ($requestPlan->staff && $requestPlan->staff_amount !== null) {
            $data['offer_amount'] = $requestPlan->single_step ? $requestPlan->staff_amount : $requestPlan->staff_amount / 2;
            $data['final_amount'] = $requestPlan->single_step ? $requestPlan->staff_amount : $requestPlan->staff_amount / 2;

            $data['staff'] = true;
            $data['staff_amount'] = $requestPlan->staff_amount;
        }
        if ($requestPlan->golden) {
            $data['golden'] = true;
        }

        if ($validRole->unit->parent_id) {
            $cultural_officer = UserRole::query()
                ->with('user')
                ->where('item_id' , $itemId)
                ->where('role' , OperatorRole::MOSQUE_CULTURAL_OFFICER)
                ->where('unit_id' , $validRole->unit->parent_id)
                ->whereNotNull('auto_accept_period')
                ->first();
            if ($cultural_officer && $cultural_officer->auto_accept_period) {
                $auto_accept_at = now()->addHours($cultural_officer->auto_accept_period);
                if ($cultural_officer->user) {
                    $request->controller()->associate($cultural_officer->user);
                }
            }
        }
        if ($requestPlan->image) {
            $requestPlan->image = asset($requestPlan->image);
        }
        try {
            DB::beginTransaction();
            $request->fill([
                ... $data,
                'date' => dateConverter($submitRequest->date ,'m'),
                'user_id' => auth()->id(),
                'status' => RequestStatus::IN_PROGRESS,
                'step' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER,
                'confirm' => true,
                'item_id' => $itemId,
                'unit_id' => $validRole->unit_id,
                'single_step' => $requestPlan->single_step,
                'auto_accept_at' => $auto_accept_at,
                'auto_accept_period' => $cultural_officer?->auto_accept_period ?? null,
                'plan_data' => $requestPlan
            ])->save();

            $members = $submitRequest->array('members');
            if ($requestPlan->golden && sizeof($members) > 0) {
                $request->members()->attach($members);
            }
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
            if ($submitRequest->hasFile('other_imam_letter')) {
                foreach ($submitRequest->file('other_imam_letter') ?? [] as $f) {
                    $request->otherImamLetter()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_OTHER_IMAM_LETTER_SUBJECT
                    ]);
                }
            }
            if ($submitRequest->hasFile('other_area_interface_letter')) {
                foreach ($submitRequest->file('other_area_interface_letter') ?? [] as $f) {
                    $request->otherAreaInterfaceLetter()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_OTHER_AREA_INTERFACE_LETTER_SUBJECT
                    ]);
                }
            }
            if ($submitRequest->hasFile('images')) {
                foreach ($submitRequest->file('images') ?? [] as $f) {
                    $request->images()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_IMAGES_SUBJECT
                    ]);
                }
            }
            DB::commit();
            $request->load(['areaInterfaceLetter','imamLetter','plan','unit','otherImamLetter','otherAreaInterfaceLetter','images']);
            $request->load(['members','members.image']);
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
            ->relations()
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
            ->relations()
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
            $request->fill([...$data , 'step' => $request->last_updated_by]);
            $disk = config('site.default_disk');
            $now = now();

            if ($request->golden) {
                $request->members()->sync($updateRequest->array("members"));
            }

            $path =  'requests/'.$now->year.'/'.$now->month.'/'.$now->day.'/'.$request->id;

            if ($request->last_updated_by === RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER && $request->auto_accept_period) {
                $request->auto_accept_at = now()->addHours($request->auto_accept_period);
            } else if ($request->last_updated_by === RequestStep::APPROVAL_AREA_INTERFACE && $request->notify_period) {
                $request->next_notify_at = now()->addHours($request->notify_period);
            }
            $request->save();

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

            if ($updateRequest->hasFile('other_imam_letter')) {
                foreach ($updateRequest->file('other_imam_letter') ?? [] as $f) {
                    $request->otherImamLetter()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_OTHER_IMAM_LETTER_SUBJECT
                    ]);
                }
            }
            if ($updateRequest->hasFile('other_area_interface_letter')) {
                foreach ($updateRequest->file('other_area_interface_letter') ?? [] as $f) {
                    $request->otherAreaInterfaceLetter()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_OTHER_AREA_INTERFACE_LETTER_SUBJECT
                    ]);
                }
            }
            if ($updateRequest->hasFile('images')) {
                foreach ($updateRequest->file('images') ?? [] as $f) {
                    $request->images()->create([
                        'path' => $f->store($path,$disk),
                        'mime_type' => $f->getMimeType(),
                        'size' => $f->getSize(),
                        'disk' => $disk,
                        'subject' => $request::FILE_IMAGES_SUBJECT
                    ]);
                }
            }

            DB::commit();
            $request->refresh();
            $request->load(['areaInterfaceLetter','imamLetter','plan','unit','otherImamLetter','otherAreaInterfaceLetter','images']);
            $request->load(['members','members.image']);
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
        $itemID = \request()->get('item_id');
        $request = RequestModel::query()
            ->item($itemID)
            ->role(\request()->get('role'))
            ->relations()
            ->whereIn('step',OperatorRole::from(\request()->get('role'))->step())
            ->whereIn('status',[RequestStatus::IN_PROGRESS,RequestStatus::ACTION_NEEDED])
            ->where('step','!=',RequestStep::APPROVAL_MOSQUE_HEAD_COACH)
            ->findOrFail($request);
        $from_status = $request->status;
        $step = $request->step;
        $request->last_updated_by = $request->step;
        if ($adminStoreRequest->action == "accept") {
            $request->status = RequestStatus::IN_PROGRESS;
            $request->toNextStep($adminStoreRequest->offer_amount , $adminStoreRequest->final_amount);
            if ($request->status === RequestStatus::DONE) {
                event(new ConfirmationRequestEvent($request));
            }
        } else if ($adminStoreRequest->action == "reject") {
            $request->status = RequestStatus::REJECTED->value;
            event(new RejectRequestEvent($request));
        } else  {
            if (
                ! in_array(
                    RequestStep::tryFrom($adminStoreRequest->to) ,
                    $request->step->backSteps()
                )
            ) abort(422);
            $request->step = $adminStoreRequest->to;
            $request->status = RequestStatus::ACTION_NEEDED->value;
            event(new ActionNeededRequestEvent($request));
        }

        if ($adminStoreRequest->filled('comment')) {
            $request->comments()->create([
                'user_id' => auth()->id(),
                'body' => $adminStoreRequest->comment,
                'display_name' => OperatorRole::from(\request()->get('role'))->label(),
                'from_status' => $from_status,
                'to_status' => $request->status,
                'step' => $step
            ]);
            $request->message = $adminStoreRequest->comment;
            if (! $request->messages) {
                $request->messages = [];
            }
            $messages = $request->messages;
            $messages[\request()->get('role')] = $adminStoreRequest->comment;
            $request->messages = $messages;
        }
        $request->save();
        return RequestResource::make($request);
    }

    public function getComments($request): AnonymousResourceCollection
    {
        $items = Comment::query()
            ->latest()
            ->whereHasMorph('commentable',[RequestModel::class],function ($q) use ($request){
                $q->where('commentable_id' , $request);
            })
            ->when(\request()->filled('step') , function ($q){
                $q->where('step' , \request()->get('step'));
            })
            ->with(['user'])
            ->paginate(\request()->get('per_page' , 10));

        return CommentResource::collection($items);
    }

    public function deleteFile($file): MediaResource
    {
        $f = File::query()->findOrFail($file);
        $f->delete();

        return MediaResource::make($f);
    }
}
