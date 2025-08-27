<?php

namespace App\Livewire\Logs\Activities;

use App\Enums\Events;
use App\Enums\OperatorRole;
use App\Enums\Subjects;
use App\Enums\UnitType;
use App\Livewire\BaseComponent;
use App\Models\LogActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexOtherRolesLogs extends BaseComponent
{
    use WithPagination;

    public $subject, $from_date , $to_data, $causer;
    public $log , $tab = 'table';
    public $boxes = [];
    public $event , $role , $unit , $type;

    public function queryString()
    {
        return [
            'tab' => [
                'as' => 'tab'
            ]
        ];
    }

    public $subjects = [];
    public function mount()
    {
        $this->authorize('show_log_activities');
        $this->subjects = [
            Subjects::REPORT->value => Subjects::REPORT->label(),
            Subjects::REQUEST->value => Subjects::REQUEST->label(),
            Subjects::FILE->value => Subjects::FILE->label(),
            Subjects::FORM_REPORT->value => Subjects::FORM_REPORT->label(),
            Subjects::WRITTEN_REQUEST->value => Subjects::WRITTEN_REQUEST->label(),
            Subjects::COMMENT->value => Subjects::COMMENT->label(),
            Subjects::RING->value => Subjects::RING->label(),
            Subjects::RING_MEMBER->value => Subjects::RING_MEMBER->label(),
        ];
        $this->data['subject'] = $this->subjects;
        $this->data['event'] = Events::labels();
        $this->data['roles'] = OperatorRole::labels();
        $this->data['type'] = UnitType::labels();
    }

    public function render()
    {
        $this->boxes = [];
        $db = config('database.connections.arman.database');
        $q = LogActivity::query()
            ->latest("activity_log.created_at")
            ->with(['causer.roles'])
            ->when($this->event , function (Builder $builder) {
                $builder->where('event' , $this->event);
            })
            ->whereIn('subject_type',array_keys($this->subjects))
            ->select("activity_log.*")
            ->join(sprintf("%s.%s AS u",$db, "users"),'u.id','=','causer_id')
            ->join("user_roles AS ur",'ur.user_id','=','u.id')
            ->leftJoin("units AS un",'un.id','=','ur.unit_id')
            ->when($this->role , function (Builder $builder) {
                $builder->where('ur.role' , $this->role);
            })
            ->when($this->unit , function (Builder $builder) {
                $builder->where('ur.unit_id' , $this->unit);
            })
            ->when($this->type , function (Builder $builder) {
                $builder->where('un.type' , $this->type);
            })
            ->when($this->subject , function (Builder $builder) {
                $builder->where('subject_type' , $this->subject);
            })->when($this->causer , function (Builder $builder) {
                $builder->where('causer_id' , $this->causer);
            })->when($this->from_date , function (Builder $builder) {
                $builder->where('created_at' ,'>=' , dateConverter(convert2english($this->from_date),'g'));
            })->when($this->to_data , function (Builder $builder) {
                $builder->where('created_at' , "<=" ,  dateConverter(convert2english($this->to_data),'g'));
            })->groupBy("activity_log.id");

        $items = match ($this->tab) {
            'table' => $q->paginate($this->per_page),
            'box' => $q->cursor()->each(function ($w) {
                if (! isset( $this->boxes[$w->subject_type][$w->event?->value])) {
                    $this->boxes[$w->subject_type][$w->event?->value] = 0;
                }
                $this->boxes[$w->subject_type][$w->event?->value] += 1;
            }),
        };
        return view('livewire.logs.activities.index-other-roles-logs' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function show($id): void
    {
        $this->reset(['log']);
        $this->log = LogActivity::query()->find($id);
        $this->emitShowModal('properties');
    }
}
