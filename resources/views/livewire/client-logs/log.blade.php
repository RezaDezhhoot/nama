@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => 'Client logs']) )
    <x-admin.form-control :store="false" title="Client logs"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">

            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('general.id') }}</th>
                            <th>Ip</th>
                            <th>Agent</th>
                            <th>Client version</th>
                            <th>Platform</th>
                            <th>User</th>
                            <th>Headers</th>
                            <th>Context</th>
                            <th>{{ __('general.date') }}</th>
                        </tr>
                        </thead>
                        <tbody >
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->ip }}</td>
                                <td>{{ $item->agent }}</td>
                                <td>{{ $item->client_version }}</td>
                                <td>{{ $item->platform }}</td>
                                <td><span>{{ ($item->user?->name ?? '').' | '.($item->user?->phone ?? '').' #'.($item->user?->id ?? '') }}</span></td>
                                <td class="jdate">{{ persian_date($item->created_at,'%A, %d %B %Y H:i:s') }}</td>
                                <td>{{ $item->context }}</td>
                                <td>
                                    <pre>
                                        {{ json_encode($item->headers) }}
                                    </pre>
                                </td>
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
