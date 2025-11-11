@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'لیست ادمین ها')
    <x-admin.form-control :store="false" exportable="export" link="{{ route('admin.plans.store',[PageAction::CREATE] ) }}" title="لیست ادمین ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="12" id="role" :data="$data['role']" label="فیلتر نقش" wire:model.live="role"/>
                <x-admin.forms.select2
                    id="region"
                    :data="$region ?? []"
                    text="title"
                    label="منطقه"
                    width="3"
                    :ajaxUrl="route('admin.feed.regions')"
                    wire:model.defer="region"/>
                <x-admin.forms.select2
                    id="unit"
                    :data="[]"
                    text="title"
                    label="مرکز "
                    width="3"
                    :ajaxUrl="route('admin.feed.units',0)"
                    wire:model.defer="unit"/>
                <x-admin.forms.input width="3" type="number"  id="min_roles" label="حداقل تعداد نقش" wire:model.live="min_roles"/>
                <x-admin.forms.input width="3" type="number"  id="max_roles" label="حداکثر تعداد نقش" wire:model.live="max_roles"/>
                <x-admin.forms.dropdown width="12" id="item" :data="$data['items']" label="پروژه" wire:model.live="item"/>

            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>کدملی</th>
                            <th>شماره همراه</th>
                            <th>تعداد نقش</th>
                            <th>نقش  در ارمان</th>
                            <th>نقش ها</th>
                            <th>درسترسی های ادمین</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->national_id }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ sizeof($item->roles2 ?? []) }}</td>
                                <td>{{ $item->role?->label() }}</td>
                                <td>
                                    <ul>
                                        @foreach($item->roles2 as $role)
                                            <li>
                                                <span class="badge my-1 badge-{{ $role->role->badge() }}">
                                                   {{ $role->role?->label() }} - {{ $role?->region?->title }} : {{ $role?->unit?->full }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($item->roles as $r)
                                            <li>
                                                <span class="badge my-1 badge-primary">
                                                   {{ $r->name }}
                                                </span>
                                            </li>
                                        @endforeach
                                            <hr>
                                        @foreach($item->permissions as $p)
                                            <li>
                                                <span class="badge my-1 badge-info">
                                                   {{ $p->title }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <x-admin.edit-btn target="_blank" href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->id]) }}"/>
                                    @if(auth()->user()->hasAnyRole(['super_admin','administrator']))
                                        <a target="_blank" href="{{ route('admin.users.permissions.store' , $item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-key"></i>
                                            مدیریت دسترسی ها
                                        </a>
                                    @endif
                                    <button wire:click="generateToken('{{$item->id}}')" class="btn btn-sm btn-outline-danger">ایجاد توکن</button>
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
            {{$items?->links('livewire.layouts.paginate')}}
        </div>
    </div>
</div>
