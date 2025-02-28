@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'لیست ادمین ها')
    <x-admin.form-control :store="false" link="{{ route('admin.plans.store',[PageAction::CREATE] ) }}" title="لیست ادمین ها"/>

    <div class="card card-custom">
        <div class="card-body">
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
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->national_id }}</td>
                                <td>{{ $item->phone }}</td>
{{--                                <td>{{ $item->roles_count }}</td>--}}
                                <td>{{ $item->role?->label() }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->id]) }}"/>
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="17">
                                اطلاعاتی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                        <tbody wire:loading >
                        <x-admin.big-loader :table="true" width="20" height="20" />
                        </tbody>
                    </table>
                </div>
            </div>
            @if(sizeof($items) > 0)
                {{$items->links('livewire.layouts.paginate')}}
            @endif
        </div>
    </div>
</div>
