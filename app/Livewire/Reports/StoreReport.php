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

    public $offer_amount , $final_amount;

    public function mount($action , $id)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->report = Report::query()
                ->with(['request','request.user','images','video','request.imamLetter','request.areaInterfaceLetter'])
                ->withCount('comments')
                ->whereHas('request')
                ->roleFilter()
                ->confirmed()
                ->findOrFail($id);
            $this->request = $this->report->request;
            $this->header = "گزارش $id";

            $this->offer_amount = $this->request->offer_amount ?? $this->request->total_amount;
            $this->final_amount = $this->request->final_amount ?? $this->request->offer_amount ?? $this->request->total_amount;
        } else abort(404);
        $this->data['status'] = RequestStatus::labels();
    }


    public function store()
    {
        if (
            in_array($this->report->status , [RequestStatus::DONE,RequestStatus::REJECTED])
        ) {
            return;
        }
        if (RequestStatus::tryFrom($this->status) !== $this->report->status) {
            $this->validate([
                'status' => ['required',Rule::enum(RequestStatus::class)],
                'comment' => ['required','string','max:200'],
                'message' => [in_array($this->status , [RequestStatus::REJECTED->value,RequestStatus::ACTION_NEEDED->value]) ? 'required' : 'nullable','string','max:200'],
                'final_amount' => [$this->report->step ===  RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable','integer' ,'min:1000']
            ]);
            if (RequestStatus::tryFrom($this->status) === RequestStatus::DONE) {
                $this->report->status = RequestStatus::IN_PROGRESS;
                switch ($this->report->step) {
                    case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
                        $this->report->step = RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
                        break;
                    case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                        $this->report->step = RequestStep::APPROVAL_AREA_INTERFACE;
                        break;
                    case RequestStep::APPROVAL_AREA_INTERFACE:
                        $this->report->step = RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES;
                        break;
                    case RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                        $this->report->step = RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                        $this->report->offer_amount = $this->offer_amount;
                        break;
                    case RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                        $this->report->step = RequestStep::FINISH;
                        $this->report->status = RequestStatus::DONE;
                        $this->report->final_amount = $this->final_amount;
                        break;
                }
            } else {
                $this->report->status = RequestStatus::tryFrom($this->status);
            }
            $this->report->comments()->create([
                'user_id' => auth()->id(),
                'body' => $this->comment,
                'display_name' => auth()->user()->nama_role->label()
            ]);
            if ($this->message) {
                $this->report->fill([
                    'message' => $this->message
                ]);
            }
            $this->report->save();
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
            $this->reset(['message','comment','status']);
            redirect()->route('admin.reports.index');
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
