<?php

namespace App\Livewire\Logs\Activities;

use App\Enums\Events;
use App\Enums\Subjects;
use App\Livewire\BaseComponent;
use App\Models\LogActivity;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexLogs extends BaseComponent
{
    use WithPagination;

    public $subject, $from_date , $to_data, $causer;
    public $log;

    public $event;

    public function mount()
    {
        $this->authorize('show_log_activities');
        $this->data['subject'] = Subjects::labels();
        $this->data['event'] = Events::labels();
    }

    public function render()
    {
        $items = LogActivity::query()
            ->latest()
            ->when($this->event , function (Builder $builder) {
                $builder->where('event' , $this->event);
            })
            ->when($this->subject , function (Builder $builder) {
                $builder->where('subject_type' , $this->subject);
            })->when($this->causer , function (Builder $builder) {
                $builder->where('causer_id' , $this->causer);
            })->when($this->from_date , function (Builder $builder) {
                $builder->where('created_at' ,'>=' , dateConverter(convert2english($this->from_date),'g'));
            })->when($this->to_data , function (Builder $builder) {
                $builder->where('created_at' , "<=" ,  dateConverter(convert2english($this->to_data),'g'));
            })->paginate($this->per_page);
        return view('livewire.logs.activities.index-logs' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function show($id): void
    {
        $this->reset(['log']);
        $this->log = LogActivity::query()->find($id);
        $this->emitShowModal('properties');
    }
}
