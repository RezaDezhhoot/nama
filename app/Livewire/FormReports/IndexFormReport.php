<?php

namespace App\Livewire\FormReports;

use App\Enums\FormReportEnum;
use App\Exports\ExportFormReports;
use App\Livewire\BaseComponent;
use App\Models\Form;
use App\Models\FormReport;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class IndexFormReport extends BaseComponent
{
    use WithPagination;

    public $status , $form , $user , $userModel;

    public function queryString()
    {
        return [
            'status' => [
                'as' => 'status'
            ],
            'form' => [
                'as' => 'form'
            ],
            'user' => [
                'as' => 'user'
            ]
        ];
    }

    public function mount()
    {
        $this->data['status'] = FormReportEnum::labels();
        $this->data['forms'] = Form::query()->whereNotNull('title')->withTrashed()->get()->map(function ($v){
            $v->text = sprintf("#%d-%s" , $v->id,$v->title);
            return $v;
        })->pluck('title','id')->toArray();

        if ($this->user) {
            $this->userModel = User::query()->find($this->user)->toArray();
        }
    }

    public function render()
    {
        $items = FormReport::query()
            ->with(['form','user'])
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->when($this->status , function ($q) {
                $q->where('status' , $this->status);
            })->when($this->form , function ($q) {
                $q->where('form_id' , $this->form);
            })->when($this->user , function ($q) {
                $q->where("user_id" , $this->user);
            })->paginate($this->per_page);

        return view('livewire.form-reports.index-form-report' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function exportXLSX()
    {
        return (new ExportFormReports($this->user,$this->form,$this->status,$this->search))->download('reports.xlsx');
    }

    public function deleteItem($id)
    {
        FormReport::destroy($id);
    }
}
