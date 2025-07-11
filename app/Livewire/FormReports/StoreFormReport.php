<?php

namespace App\Livewire\FormReports;

use App\Enums\FormReportEnum;
use App\Livewire\BaseComponent;
use App\Models\FormReport;
use Illuminate\Validation\Rule;

class StoreFormReport extends BaseComponent
{
    public $qsearch , $quser , $qstatus;


    public $report , $status , $message = null;

    public function queryString()
    {
        return [
            'qstatus' => [
                'as' => 'status'
            ],
            'qsearch' => [
                'as' => 'search'
            ],
            'quser' => [
                'as' => 'user'
            ]
        ];
    }

    public function mount($action , $id)
    {
        $this->setMode($action);
        $this->report = FormReport::query()->with(['user','form'])->findOrFail($id);
        $this->status = $this->report->status->value;
        $this->header = $this->report->id;
        $this->message = $this->report->message;
        $this->data['status'] = FormReportEnum::labels();
    }

    public function store()
    {
        $this->validate([
            'message' => ['nullable','string','max:200'],
            'status' => ['required',Rule::enum(FormReportEnum::class)]
        ]);
        $data = [
            'message' => $this->message,
            'status' => $this->status
        ];
        $this->report->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        return redirect()->route('admin.form-reports.index', [
            'search' => $this->qsearch,
            'user' => $this->quser,
            'status' => $this->qstatus
        ]);
    }

    public function deleteItem()
    {
        $this->report->delete();
        return redirect()->route('admin.form-reports.index', [
            'search' => $this->qsearch,
            'user' => $this->quser,
            'status' => $this->qstatus
        ]);
    }

    public function render()
    {
        return view('livewire.form-reports.store-form-report')->extends('livewire.layouts.admin');
    }
}
