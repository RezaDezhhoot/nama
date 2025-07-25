<?php

namespace App\Livewire\Reports;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Livewire\BaseComponent;
use App\Models\File;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreReport extends BaseComponent
{
    public $request , $report , $tab = 'report';

    public $status , $message , $comment;

    public $offer_amount , $final_amount , $type;

    public $qversion;

    public $step;

    public $qitem , $qstatus , $qtype , $qregion , $qplan , $qunit , $qstep , $qsearch;

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
        if ($this->isUpdatingMode()) {
            $this->report = Report::query()
                ->with(['request','request.user','images','otherVideos','video','request.imamLetter','request.areaInterfaceLetter','request.otherImamLetter','request.otherAreaInterfaceLetter','request.images','images2'])
                ->withCount('comments')
                ->whereHas('request')
                ->roleFilter()
                ->confirmed()
                ->findOrFail($id);
            $this->request = $this->report->request;
            $this->header = "گزارش $id";

            $this->offer_amount = $this->request->offer_amount ?? $this->request->total_amount;
            $this->final_amount = $this->request->final_amount ?? $this->request->offer_amount ?? $this->request->total_amount;
            $this->status = $this->report->status->value;
        } else abort(404);
        $this->data['status'] = RequestStatus::labels();
        $this->data['step'] = RequestStep::labels();
    }


    public function store()
    {
        if (
            in_array($this->report->status , [RequestStatus::DONE,RequestStatus::REJECTED])
        ) {
            return;
        }
        $step = $this->report->step;
        $from_status = $this->report->status;
        $this->step = emptyToNull($this->step);
        if (RequestStatus::tryFrom($this->status) !== $this->report->status || $this->step) {
            $this->validate([
                'status' => ['required',Rule::enum(RequestStatus::class)],
                'comment' => ['required','string','max:200'],
                'message' => [in_array($this->status , [RequestStatus::REJECTED->value,RequestStatus::ACTION_NEEDED->value]) ? 'required' : 'nullable','string','max:1'],
                'final_amount' => [$this->report->step ===  RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable','integer' ,'min:1'],
                'step' => ['nullable',Rule::enum(RequestStep::class)]
            ]);
            if (RequestStatus::tryFrom($this->status) === RequestStatus::DONE) {
                $this->report->status = RequestStatus::IN_PROGRESS;
                switch ($this->report->step) {
                    case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
                        $this->report->step = $this->step ?? RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
                        if ($this->report->auto_accept_period) {
                            $this->report->auto_accept_at = now()->addHours($this->report->auto_accept_period);
                        }
                        break;
                    case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                        $this->report->step = $this->step ?? RequestStep::APPROVAL_AREA_INTERFACE;
                        if ($this->report->notify_period) {
                            $this->report->next_notify_at = now()->addHours($this->report->notify_period);
                        }
                        break;
                    case RequestStep::APPROVAL_AREA_INTERFACE:
                        $this->report->step = $this->step ?? RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES;
                        break;
                    case RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                        $this->report->step = $this->step ?? RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                        $this->report->offer_amount = $this->offer_amount;
                        break;
                    case RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                        $this->report->step = $this->step ?? RequestStep::FINISH;
                        $this->report->status = RequestStatus::DONE;
                        $this->report->final_amount = $this->final_amount;
                        break;
                }
            } else {
                $this->report->status = RequestStatus::tryFrom($this->status);
                if ($this->step) {
                    $this->request->step = $this->step;
                }
            }
            $this->report->comments()->create([
                'user_id' => auth()->id(),
                'body' => $this->comment,
                'display_name' => auth()->user()->role?->label() ?? auth()->user()->nama_role?->label(),
                'from_status' => $from_status,
                'to_status' => $this->status,
                'step' => $step
             ]);
            if ($this->message) {
                $this->report->fill([
                    'message' => $this->message
                ]);
            }
            $this->report->save();
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
            redirect()->route('admin.reports.index',[
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

    public function download($id): StreamedResponse
    {
        $file = File::query()->findOrFail($id);
        return
            Storage::disk($file->disk)->download($file->path);
    }

    public function render()
    {
        return view('livewire.requests.store-report')->extends('livewire.layouts.admin');
    }
}
