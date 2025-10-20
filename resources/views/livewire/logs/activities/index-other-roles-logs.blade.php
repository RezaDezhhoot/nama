@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => __('general.sidebar.log_activity')]) )
    <x-admin.form-control :store="false" title="{{__('general.sidebar.log_activity')}}"/>
    <div class="card card-custom ">

        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown  id="event" :data="$data['event']" label="رویداد" wire:model.live="event"/>
                <x-admin.forms.dropdown width="3" id="subject" :data="$data['subject']" label="موجودیت" wire:model.live="subject"/>
                <x-admin.forms.select2 width="3"  id="causer" label="{{__('general.user')}}" ajaxUrl="{{route('admin.feed.users',true)}}" wire:model.live="causer"/>
                <x-admin.forms.jdate-picker width="3" :timer="false" id="from_date" label="{{ __('general.from_date') }}" wire:model.live="from_date"/>
                <x-admin.forms.jdate-picker width="3" :timer="false" id="to_date" label="{{ __('general.to_date') }}" wire:model.live="to_date"/>

                <x-admin.forms.dropdown width="4" id="role" :data="$data['roles']" label="نقش" wire:model.live="role"/>
                <x-admin.forms.dropdown width="4" id="type" :data="$data['type']" label="نوع واحد حقوقی" wire:model.live="type"/>
                <x-admin.forms.select2
                    id="unit"
                    :data="[]"
                    text="title"
                    label="مرکز"
                    width="4"
                    :ajaxUrl="route('admin.feed.units',[0])"
                    wire:model.defer="unit"/>

            </div>
            @include('livewire.includes.advance-table')

            <x-admin.nav-tabs-list>
                <x-admin.nav-tabs-item :active="$tab === 'table'" title="جدول" key="tab" value="table" icon="flaticon-list"/>
                <x-admin.nav-tabs-item :active="$tab === 'box'" title="خلاصه اطلاعات" key="tab" value="box" icon=" flaticon2-box"/>
            </x-admin.nav-tabs-list>
           <div class="{{ $tab == "table" ? '' : 'd-none' }}">
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
                               <th>نقش ها</th>
                               <th>{{ __('general.causer') }}</th>
                               <th>{{ __('general.actions.actions') }}</th>
                           </tr>
                           </thead>
                           <tbody >
                           @foreach($items as $item)
                               <tr>
                                   <td>{{ $loop->iteration }}</td>
                                   <td>{{ $item->id }}</td>
                                   <td>{{ $item->event?->label() }}</td>
                                   <td>{{ $data['subject'][$item->subject_type] ?? $item->subject_type }} # {{ $item->subject_id }}</td>
                                   <td>{{ $item->description }}</td>
                                   <td class="jdate">{{ persian_date($item->created_at) }}</td>
                                   <td>
                                       <ul>
                                           @foreach($item->causer->roles as $role)
                                               <li>
                                                <span class="badge my-1 badge-{{ $role->role?->badge() }}">
                                                   {{ $role->role?->label() }} - {{ $role?->region?->title }} : {{ $role?->unit?->full }}
                                                </span>
                                               </li>
                                           @endforeach
                                       </ul>
                                   </td>
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
               @if(sizeof($items) > 0 &&  $tab == "table")
                   {{$items?->links('livewire.layouts.paginate')}}
               @endif
           </div>
            <div class="{{ $tab == "box" ? '' : 'd-none' }}">
                @foreach($data['subject'] as $k => $s)
                    <fieldset class="border mb-4 p-4 w-100">
                        <legend>{{ $s }}</legend>
                        <div class="row">
                            @foreach(\App\Enums\Events::cases() as $k2 => $l)
                                <div class="col-md-3 col-6">
                                    <div class="card card-custom bg-white shadow-sm bgi-no-repeat card-stretch gutter-b" >
                                        <div class="card-body">
                                            <span class="svg-icon svg-icon-white svg-icon-5x">
                                               <i class="{{ $l->icon() }} fa-4x "></i>
                                            </span>
                                            <span class="card-title font-weight-bolder font-size-h2 mb-0 text-hover-primary d-block">
                                                {{ number_format($boxes[$k][$l->value] ?? 0) }}
                                            </span>
                                            <span class="font-weight-bold  font-size-sm">{{ $l->label() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>
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
                            <td>{{ $log->event?->label() }}</td>
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
                      {{ json_encode($log?->properties['old'] ?? [] , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE ) }}
                    </pre>
                </div>
                <div class="col-12">
                    <h6>
                        به
                    </h6>
                    <pre style="text-align: left" class="text-white p-2 bg-dark">
                      {{ json_encode($log?->properties['attributes'] ?? [] , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE) }}
                    </pre>
                </div>
            </div>
        @endif
    </x-admin.modal-page>
</div>
