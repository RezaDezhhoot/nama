@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => 'بلیط های اردو']) )
    <x-admin.form-control link="{{ route('admin.camp-tickets.store',[PageAction::CREATE] ) }}" title="بلیط های اردو"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown  id="item" :data="$data['type']" label="نوع" wire:model.live="item"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('general.id') }}</th>
                            <th>شماره درخواست</th>
                            <th>{{ __('general.status') }}</th>
                            <th>نتیجه</th>
                            <th>{{ __('general.date') }}</th>
                        </tr>
                        </thead>
                        <tbody >
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->request_id }} - {{ $item->request?->item?->title }}</td>
                                <td>{{ $item->status === 0 ? 'در انتظار' : ($item->status === 1 ? 'خظا' : 'صادر شده') }}</td>
                                <td><pre>{{json_encode($item->result ?? [])}}</pre></td>
                                <td class="jdate">{{ persian_date($item->updated_at,'%A, %d %B %Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tbody wire:loading >
                        <x-admin.big-loader :table="true" width="20" height="20" />
                        </tbody>
                    </table>
                </div>
            </div>
            @if(sizeof($items) > 0)
                {{$items?->links('livewire.layouts.paginate')}}
            @endif
        </div>
    </div>
</div>
