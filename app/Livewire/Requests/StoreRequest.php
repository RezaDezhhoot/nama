<?php

namespace App\Livewire\Requests;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Livewire\BaseComponent;
use App\Models\File;
use App\Models\Request;
use App\Models\UserRole;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreRequest extends BaseComponent
{
    public $request , $tab = 'request';

    public $status , $message , $comment;

    public $offer_amount , $final_amount;

    public $type , $step;

    public $qitem , $qstatus , $qtype , $qregion , $qplan , $qunit , $qstep , $qsearch , $qversion;

    protected function queryString()
    {
        return [
            'qitem' => [
                'as' => 'item'
            ],
            'qstatus' => [
                'as' => 'status'
            ],
            'qtype' => [
                'as' => 'type'
            ],
            'qregion' => [
                'as' => 'region'
            ],
            'qplan' => [
                'as' => 'plan'
            ],
            'qunit' => [
                'as' => 'unit'
            ],
            'qstep' => [
                'as' => 'step'
            ],
            'qsearch' => [
                'as' => 'search'
            ],
            'qversion' => [
                'as' => 'version'
            ]
        ];
    }

    public function mount($type , $action , $id)
    {
        $this->type = $type;
        $this->setMode($action);
        $this->authorize('edit_requests_'.$type);
        if ($this->isUpdatingMode()) {
            $this->request = Request::query()
                ->relations()
                ->withCount('comments')
                ->whereHas('plan')
                ->roleFilter()
                ->confirmed()
                ->findOrFail($id);
            $this->header = "درخواست $id";

            if (in_array($this->request->step,[RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES])) {
                $this->offer_amount = $this->request->offer_amount ?? $this->request->total_amount;
                $this->final_amount = $this->request->final_amount ?? $this->request->offer_amount ?? $this->request->total_amount;
            }
            $this->status = $this->request->status->value;
        } else abort(404);
        $this->data['status'] = RequestStatus::labels();
        $this->data['step'] = RequestStep::labels($type);
    }

    public function download($id): StreamedResponse
    {
        $file = File::query()->findOrFail($id);
        return
            Storage::disk($file->disk)->download($file->path);
    }

    public function store()
    {
        if (
            $this->request->status === RequestStatus::DONE
        ) {
            return;
        }
        $amount = null;
        if ($this->request->staff && $this->request->staff_amount !== null) {
            $amount = $this->request->staff_amount / 2;
        }
        if ($this->request->single_step) {
            $amount = 0;
        }

        $step = $this->request->step;
        $from_status = $this->request->status;
        $this->step = emptyToNull($this->step);
        if (RequestStatus::tryFrom($this->status) !== $this->request->status || $this->step) {
            $this->validate([
                'status' => ['required',Rule::enum(RequestStatus::class)],
                'comment' => ['required','string','max:200'],
                'message' => [in_array($this->status , [RequestStatus::REJECTED->value,RequestStatus::ACTION_NEEDED->value]) ? 'required' : 'nullable','string','max:200'],
                'final_amount' => [$this->request->step ===  RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable' , 'integer' ],
                'offer_amount' => [$this->request->step ===  RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES ? 'required' : 'nullable' , 'integer' ],
                'step' => ['nullable',Rule::enum(RequestStep::class)]
            ]);
            if (RequestStatus::tryFrom($this->status) === RequestStatus::DONE) {
                $this->request->status = RequestStatus::IN_PROGRESS;
                switch ($this->request->step) {
                    case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
                        if ($this->request->auto_accept_period) {
                            $this->request->auto_accept_at = now()->addHours($this->request->auto_accept_period);
                        }
                        break;
                    case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_AREA_INTERFACE;
                        if ($this->request->notify_period) {
                            $this->request->next_notify_at =  now()->addHours($this->request->notify_period);
                        } else if ($this->request->unit && $this->request->unit->city_id && $this->request->unit->region_id) {
                            $area_interface = UserRole::query()
                                ->where('item_id' , $this->request->item_id)
                                ->where('city_id' , $this->request->unit->city_id)
                                ->where('region_id' , $this->request->unit->region_id)
                                ->where('role' , OperatorRole::AREA_INTERFACE)
                                ->whereNotNull('notify_period')
                                ->first();
                            if ($area_interface && $area_interface->notify_period) {
                                $this->request->next_notify_at =  now()->addHours($area_interface->notify_period);
                                $this->request->notify_period = $area_interface->notify_period;
                            }
                        }
                        break;
                    case RequestStep::APPROVAL_AREA_INTERFACE:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES;
                        break;
                    case RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                        $this->request->offer_amount = $this->offer_amount;
                        break;
                    case RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                        $this->request->step = $this->step ?? RequestStep::FINISH;
                        $this->request->status = RequestStatus::DONE;
                        $this->request->final_amount = $this->final_amount;

                        $this->request->report()->create([
                            'step' => $this->request->single_step ? RequestStep::FINISH : RequestStep::APPROVAL_MOSQUE_HEAD_COACH,
                            'status' => $this->request->single_step ? RequestStatus::DONE : RequestStatus::PENDING,
                            'amount' => 0,
                            'offer_amount' => $amount,
                            'final_amount' => $amount,
                            'confirm' => true,
                            'item_id' => $this->request->item_id,
                            'auto_accept_period' => $this->request->auto_accept_period,
                            'notify_period' => $this->request->notify_period,
                        ]);
                        break;
                }
            } else {
                $this->request->status = RequestStatus::tryFrom($this->status);
                if ($this->step) {
                    $this->request->step = $this->step;
                    if (RequestStep::tryFrom($this->step) === RequestStep::APPROVAL_AREA_INTERFACE) {
                        if ($this->request->notify_period) {
                            $this->request->next_notify_at = now()->addHours($this->request->notify_period);
                        } else if ($this->request->unit && $this->request->unit->city_id && $this->request->unit->region_id) {
                            $area_interface = UserRole::query()
                                ->where('item_id' , $this->request->item_id)
                                ->where('city_id' , $this->request->unit->city_id)
                                ->where('region_id' , $this->request->unit->region_id)
                                ->where('role' , OperatorRole::AREA_INTERFACE)
                                ->whereNotNull('notify_period')
                                ->first();
                            if ($area_interface && $area_interface->notify_period) {
                                $this->request->next_notify_at = now()->addHours($area_interface->notify_period);
                                $this->request->notify_period = $area_interface->notify_period;
                            }
                        }
                    }
                    if (RequestStep::tryFrom($this->step) === RequestStep::FINISH && ! $this->request->report()->exists()) {
                        $this->request->report()->create([
                            'step' => $this->request->single_step ? RequestStep::FINISH : RequestStep::APPROVAL_MOSQUE_HEAD_COACH,
                            'status' => $this->request->single_step ? RequestStatus::DONE : RequestStatus::PENDING,
                            'amount' => 0,
                            'offer_amount' => $amount,
                            'final_amount' => $amount,
                            'confirm' => true,
                            'item_id' => $this->request->item_id,
                            'auto_accept_period' => $this->request->auto_accept_period,
                            'notify_period' => $this->request->notify_period,
                        ]);
                    }
                }
            }
            $this->request->comments()->create([
                'user_id' => auth()->id(),
                'body' => $this->comment,
                'display_name' => auth()->user()->role?->label() ?? auth()->user()->nama_role?->label(),
                'from_status' => $from_status,
                'to_status' => $this->status,
                'step' => $step
            ]);
            $messages = $this->request->messages;
            $messages[$this->request->step->value] = $this->comment;
            $this->request->messages = $messages;
            if ($this->message) {
                $this->request->fill([
                    'message' => $this->message
                ]);
            }
            $this->request->save();
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
            redirect()->route('admin.requests.index',[
                'type' => $this->type,
                'status' => $this->qstatus,
                'region' => $this->qregion,
                'plan' => $this->qplan,
                'unit' => $this->qunit,
                'step' => $this->qstep,
                'search' => $this->qsearch,
                'version' => $this->qversion,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.requests.store-request')->extends('livewire.layouts.admin');
    }
}
