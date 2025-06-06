@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'لیست ادمین ها')
    <x-admin.form-control :store="false" link="{{ route('admin.plans.store',[PageAction::CREATE] ) }}" title="لیست ادمین ها"/>

    <div class="card card-custom">
        <div class="card-body">
{{--            <div class="row">--}}
{{--                <x-admin.forms.dropdown width="4" id="role" :data="$data['role']" label="فیلتر نقش" wire:model.live="role"/>--}}
{{--                <x-admin.forms.select2--}}
{{--                    id="region"--}}
{{--                    :data="$region ?? []"--}}
{{--                    text="title"--}}
{{--                    label="منظقه"--}}
{{--                    width="4"--}}
{{--                    :ajaxUrl="route('admin.feed.regions')"--}}
{{--                    wire:model.defer="region"/>--}}
{{--                <x-admin.forms.select2--}}
{{--                    id="unit"--}}
{{--                    :data="[]"--}}
{{--                    text="title"--}}
{{--                    label="مرکز "--}}
{{--                    width="4"--}}
{{--                    :ajaxUrl="route('admin.feed.units',0)"--}}
{{--                    wire:model.defer="unit"/>--}}
{{--            </div>--}}
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
{{--                            <th>تعداد نقش در نما</th>--}}
                            <th>نقش  در ارمان</th>
                            <th>نقش ها</th>
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
{{--                                <td>{{ $item->roles_count }}</td>--}}
                                <td>{{ $item->role?->label() }}</td>
                                <td>
                                    <ul>
                                        @foreach($item->roles as $role)
                                            <li>
                                                <span class="badge my-1 badge-{{ $role->role->badge() }}">{{ $role->user?->name }} : {{ $role->role?->label() }} - {{ $role?->unit?->full }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->id]) }}"/>
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
