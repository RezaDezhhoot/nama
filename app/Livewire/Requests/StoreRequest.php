<?php

namespace App\Livewire\Requests;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Livewire\BaseComponent;
use App\Models\File;
use App\Models\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreRequest extends BaseComponent
{
    public $request , $tab = 'request';

    public $status , $message , $comment;

    public $offer_amount , $final_amount;

    public $type , $step;

    public function mount($type , $action , $id)
    {
        $this->type = $type;
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->request = Request::query()
                ->with(['plan','user','comments','unit'])
                ->withCount('comments')
                ->whereHas('plan')
                ->roleFilter()
                ->confirmed()
                ->findOrFail($id);
            $this->header = "درخواست $id";

            $this->offer_amount = $this->request->offer_amount ?? $this->request->total_amount;
            $this->final_amount = $this->request->final_amount ?? $this->request->offer_amount ?? $this->request->total_amount;
            $this->status = $this->request->status->value;
        } else abort(404);
        $this->data['status'] = RequestStatus::labels();
        $this->data['step'] = RequestStep::labels();
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
            in_array($this->request->status , [RequestStatus::DONE,RequestStatus::REJECTED])
        ) {
            return;
        }
        if (RequestStatus::tryFrom($this->status) !== $this->request->status) {
            $this->validate([
                'status' => ['required',Rule::enum(RequestStatus::class)],
                'comment' => ['required','string','max:200'],
                'message' => [in_array($this->status , [RequestStatus::REJECTED->value,RequestStatus::ACTION_NEEDED->value]) ? 'required' : 'nullable','string','max:200'],
                'final_amount' => [$this->request->step ===  RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable','integer' ,'min:1000'],
                'step' => ['nullable',Rule::enum(RequestStep::class)]
            ]);
            $this->step = emptyToNull($this->step);
            if (RequestStatus::tryFrom($this->status) === RequestStatus::DONE) {
                $this->request->status = RequestStatus::IN_PROGRESS;
                switch ($this->request->step) {
                    case RequestStep::APPROVAL_MOSQUE_HEAD_COACH:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER;
                        break;
                    case RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER:
                        $this->request->step = $this->step ?? RequestStep::APPROVAL_AREA_INTERFACE;
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
                        break;
                }
            } else {
                $this->request->status = RequestStatus::tryFrom($this->status);
                if ($this->step) {
                    $this->request->step = $this->step;
                }
            }
            $this->request->comments()->create([
                'user_id' => auth()->id(),
                'body' => $this->comment,
                'display_name' => auth()->user()->role?->label() ?? auth()->user()->nama_role?->label(),
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
            $this->reset(['message','comment','status']);
            redirect()->route('admin.requests.index',[$this->type]);
        }
    }

    public function render()
    {
        return view('livewire.requests.store-request')->extends('livewire.layouts.admin');
    }
}
