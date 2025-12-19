@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'اکشن پلن ها')
    <x-admin.form-control link="{{ route('admin.plans.store',[PageAction::CREATE] ) }}" title="اکشن پلن ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="6" id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
                <x-admin.forms.dropdown width="6" id="type" :data="$data['types']" label="نوع پلن" wire:model.live="type"/>
                <x-admin.forms.checkbox width="3" id="single_step" label="درخواست تک مرحله ای" wire:model.live="single_step"/>
                <x-admin.forms.checkbox width="3" id="double_step" label="درخواست دو مرحله ای" wire:model.live="double_step"/>
                <x-admin.forms.checkbox width="3" id="staff" label="ستادی" wire:model.live="staff"/>
                <x-admin.forms.checkbox width="3" id="golden" label="طلایی" wire:model.live="golden"/>
                <x-admin.forms.checkbox width="3" id="designated_by_council" label="تعیین هزینه توسط شورا" wire:model.live="designated_by_council"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>عنوان</th>
                            <th>درخواست تک مرحله ای</th>
                            <th>ستادی</th>
                            <th>تعیین هزینه توسط شورا</th>
                            <th>طلایی</th>
                            <th>زیر عنوان</th>
                            <th>وضعیت</th>
                            <th>پروژه</th>
                            <th>نسخه آرمان</th>
                            <th>پیشنیاز ها</th>
                            <th>نوع اکشن پلن</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->single_step ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->staff ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->designated_by_council ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->golden ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->sub_title ?? '-' }}</td>
                                <td>{{ $item->status?->label() }}</td>
                                <td>{{ $item->item?->title ?? "-" }}</td>
                                <td>{{ $item->version?->value ?? "-" }}</td>
                                <td>
                                    @foreach($item->requirements as $requirement)
                                        <a href="{{ route('admin.plans.store',[PageAction::UPDATE , $requirement->id]) }}" target="_blank" class="badge badge-primary">
                                            {{ $requirement->title }}
                                        </a>
                                    @endforeach
                                </td>
                                <td>{{ $item->type?->label() }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.plans.store',[PageAction::UPDATE , $item->id]) }}"/>
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
            }).then((result) => {
                if (result.value) {
                    @this.call('deleteItem' , id)
                }
            })
        }
    </script>
@endpush
