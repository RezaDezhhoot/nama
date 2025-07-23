@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => __('general.sidebar.log_activity')]) )
    <x-admin.form-control :store="false" title="{{__('general.sidebar.log_activity')}}"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="3" id="subject" :data="$data['subject']" label="موجودیت" wire:model.live="subject"/>
                <x-admin.forms.select2 width="3"  id="causer" label="{{__('general.user')}}" ajaxUrl="{{route('admin.feed.users')}}" wire:model.live="causer"/>
                <x-admin.forms.jdate-picker width="3" :timer="false" id="from_date" label="{{ __('general.from_date') }}" wire:model.live="from_date"/>
                <x-admin.forms.jdate-picker width="3" :timer="false" id="to_date" label="{{ __('general.to_date') }}" wire:model.live="to_date"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.event') }}</th>
                            <th>{{ __('general.subject') }}</th>
                            <th>{{ __('general.description') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th>{{ __('general.causer') }}</th>
                            <th>{{ __('general.actions.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody >
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->event }}</td>
                                <td>{{ $data['subject'][$item->subject_type] ?? $item->subject_type }} # {{ $item->subject_id }}</td>
                                <td>{{ $item->description }}</td>
                                <td class="jdate">{{ persian_date($item->created_at) }}</td>
                                <td><span>{{ ($item->causer->name ?? '').' | '.($item->causer->phone ?? '').' #'.($item->causer->id ?? '') }}</span></td>
                                <td>
                                    <x-admin.edit-btn wire:click="show({{$item->id}})"/>
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
    <x-admin.modal-page id="properties" :saveAction="false" title="{{__('general.properties')}}" >
        @if($log)
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.event') }}</th>
                            <th>{{ __('general.subject') }}</th>
                            <th>{{ __('general.description') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th>{{ __('general.causer') }}</th>
                        </tr>
                        </thead>
                        <tbody >
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->event }}</td>
                            <td>{{ $data['subject'][$log->subject_type] ?? $log->subject_type }} # {{ $log->subject_id }}</td>
                            <td>{{ $log->description }}</td>
                            <td class="jdate">{{ persian_date($log->created_at) }}</td>
                            <td><span>{{ ($log->causer->name ?? '').' | '.($log->causer->phone ?? '').' | '.($log->causer->email ?? '') }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <h5>
                تغییرات:
            </h5>
            <div class="row">
                <div class=" col-12">
                    <h6>
                        از
                    </h6>
                    <pre style="text-align: left" class="text-white p-2 bg-dark ">
                      {{ json_encode($log?->properties['old'] , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE ) }}
                    </pre>
                </div>
                <div class="col-12">
                    <h6>
                        به
                    </h6>
                    <pre style="text-align: left" class="text-white p-2 bg-dark">
                      {{ json_encode($log?->properties['attributes'] , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE) }}
                    </pre>
                </div>
            </div>
        @endif
    </x-admin.modal-page>
</div>
