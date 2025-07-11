@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'گزارش گیر')
    <x-admin.form-control :store="false" title="گزارش گیر" exportable="exportXLSX"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="3" id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
                <x-admin.forms.dropdown width="3" id="form" :data="$data['forms']" label="فرم" wire:model.live="form"/>
                <x-admin.forms.select2
                    id="user"
                    :data="$userModel ?? []"
                    text="name"
                    label="مربی"
                    width="6"
                    :ajaxUrl="route('admin.feed.users')"
                    wire:model.defer="user"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>کاربر</th>
                            <th>فرم</th>
                            <th>وضعیت</th>
                            <th>تاریخ ارسال</th>
                            <th>تاریخ اخرین بروزرسانی</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <ul>
                                        <li>{{ $item->user?->name }}</li>
                                        <li>{{ $item->user?->phone }}</li>
                                        <li>{{ $item->user?->national_id }}</li>
                                        <li>{{ $item->coach?->school_coach_type?->label() }}</li>
                                    </ul>
                                </td>
                                <td>{{ $item->form?->title }}</td>
                                <td>{{ $item->status->label() }}</td>
                                <td>{{ persian_date($item->created_at) }}</td>
                                <td>{{ persian_date($item->updated_at) }}</td>
                                <td>
                                    <x-admin.edit-btn target="_blank" href="{{ route('admin.form-reports.store',[PageAction::UPDATE , $item->id]) }}?status={{$status}}&user={{$user}}&search={{$search}}"/>
                                    <x-admin.delete-btn onclick="deleteItem('{{$item->id}}')"  />
                                </td>
                            </tr>
                        @endforeach
                        @if(sizeof($items) == 0)
                            <td class="text-center" colspan="17">
                                اطلاعاتی برای نمایش وجود ندارد
                            </td>
                        @endif
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
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"/>
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف کردن',
                text: 'آیا از حذف کردن این مورد اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
                showLoaderOnConfirm: true,
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                preConfirm: async (login) => {
                    if (! login || login === "") {
                        Swal.showValidationMessage('رمز عبور اجباری می باشد');
                        return
                    }
                    if (! ["1234"].includes(login)) {
                        Swal.showValidationMessage('رمز عبور اشتباه می باشد');
                        return
                    }
                    return login
                },
            }).then((result) => {
                if (result.value) {
                    @this.call('deleteItem' , id)
                }
            })
        }

    </script>
@endpush
