<?php

namespace App\Livewire\WrittenRequests;

use App\Enums\RequestStatus;
use App\Enums\WrittenRequestStep;
use App\Livewire\BaseComponent;
use App\Models\File;
use App\Models\WrittenRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreRequest extends BaseComponent
{
    public $request  , $tab = 'request';

    public $status , $message , $comment;

    public $countable = false , $financial = false, $amount;

    public function mount($action , $id)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->request = WrittenRequest::query()
                ->with(['user','letter','sign'])
                ->withCount('comments')
                ->roleFilter()
                ->findOrFail($id);
            $this->header = "گزارش کتوب $id";

            $this->countable = $this->request->countable ?? false;
        } else abort(404);
        $this->data['status'] = RequestStatus::labels();
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
                'countable' => [$this->request->step ===  WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable','boolean'],
                'financial' => [$this->request->step ===  WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ? 'required' : 'nullable','boolean'],
                'amount' => ['nullable','min:0','numeric']
            ]);
            if (RequestStatus::tryFrom($this->status) === RequestStatus::DONE) {
                $this->request->status = RequestStatus::IN_PROGRESS;
                switch ($this->request->step) {
                    case WrittenRequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES:
                        $this->request->step = WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING;
                        break;
                    case WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING:
                        $this->request->step = WrittenRequestStep::FINISH;
                        $this->request->status = RequestStatus::DONE;
                        $this->request->countable = emptyToNull($this->countable);
                        $this->request->amount = emptyToNull( $this->amount);
                        $this->request->financial = emptyToNull($this->financial);
                        break;
                }
            } else {
                $this->request->status = RequestStatus::tryFrom($this->status);
            }
            $this->request->comments()->create([
                'user_id' => auth()->id(),
                'body' => $this->comment,
                'display_name' => auth()->user()->role?->label() ?? auth()->user()->nama_role?->label()
            ]);
            if ($this->message) {
                $this->request->fill([
                    'message' => $this->message
                ]);
            }
            $this->request->save();
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
            $this->reset(['message','comment','status']);
            redirect()->route('admin.written-requests.index');
        }
    }

    public function render()
    {
        return view('livewire.written-requests.store-request')->extends('livewire.layouts.admin');
    }
}
