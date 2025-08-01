<?php

namespace App\Livewire\Accounting;

use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Exports\AccountingRecordExport;
use App\Livewire\BaseComponent;
use App\Models\AccountingBatch;
use App\Models\AccountingRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class IndexRecord extends BaseComponent
{
    use WithPagination;

    public $type , $subType , $batch;
    public $batchModel;

    public function mount()
    {
        $this->data['type'] = UnitType::labels();
        $this->type = UnitType::MOSQUE->value;
        $this->subType = UnitSubType::BROTHERS->value;
    }

    public function updatedType($v): void
    {
        $this->reset('subType');
        $this->reset(['batch','batchModel']);
        $this->dispatch('reloadAjaxURL#batch' ,route('admin.feed.batches',[$v , null]));
        $this->dispatch('clear#batch');
    }

    public function updatedSubType($v): void
    {
        $this->reset(['batch','batchModel']);
        $this->dispatch('reloadAjaxURL#batch' ,route('admin.feed.batches',[$this->type , $v]));
        $this->dispatch('clear#batch');
    }

    public function updatedBatch($v)
    {
        if (! empty($v)) {
            $this->batchModel = AccountingBatch::query()->find($v);
        } else {
            $this->reset('batchModel');
        }
    }

    public function render()
    {
        $items = new LengthAwarePaginator([],0,1);
        if ($this->batchModel instanceof AccountingBatch) {
            $items = AccountingRecord::query()
                ->with(['batch','unit','region'])
                ->whereHas('batch')
                ->where('accounting_batch_id' , $this->batchModel->id)
                ->orderByDesc('id')
                ->when($this->type , function (Builder $builder) {
                    $builder->where('unit_type' , $this->type);
                })
                ->when($this->subType , function (Builder $builder) {
                    $builder->where('unit_sub_type' , $this->subType);
                })->paginate(15);
        }
        return view('livewire.accounting.index-record' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function export()
    {
        return (new AccountingRecordExport($this->batchModel))->download('records.xlsx');
    }
}
