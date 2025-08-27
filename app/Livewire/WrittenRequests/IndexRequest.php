<?php

namespace App\Livewire\WrittenRequests;

use App\Enums\RequestStatus;
use App\Livewire\BaseComponent;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use misterspelik\LaravelPdf\Facades\Pdf;

class IndexRequest extends BaseComponent
{
    use WithPagination;

    public $status , $region;

    public function mount()
    {
        $this->data['status'] = RequestStatus::labels();
        $this->authorize('show_requests_written');
    }

    public function render()
    {
        $items = WrittenRequest::query()
            ->with(['user','unit','unit.city','unit.region'])
            ->withCount('comments')
            ->when($this->region , function (Builder $builder) {
                $builder->whereHas('unit' , function (Builder $builder) {
                    $builder->where('region_id' , $this->region);
                });
            })
            ->latest('updated_at')
            ->when($this->region , function (Builder $builder) {
                $builder->whereHas('unit' , function (Builder $builder) {
                    $builder->where('region_id' , $this->region);
                });
            })
            ->roleFilter()
            ->when($this->status , function (Builder $builder) {
                $builder->where('status' , $this->status);
            })->when($this->search , function (Builder $builder) {
                $builder->search($this->search)->orWhereHas('user' , function (Builder $builder) {
                    $builder->search($this->search);
                });
            })->paginate($this->per_page);

        return view('livewire.written-requests.index-request' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function pdfExport($id)
    {
        $this->authorize('export_requests_written');
        $r = WrittenRequest::query()->with(['sign'])->find($id);
        $pdf = Pdf::loadView('pdf.written-requests', ['r' => $r]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'written-request.pdf');
    }
}
