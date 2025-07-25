@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'گزارش ها')
    <x-admin.form-control :store="false" title="گزارش ها" exportable="exportXLSX"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown  width="3"  id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
                <x-admin.forms.select2
                    id="region"
                    :data="$regionModel ?? []"
                    text="title"
                    label="منطقه"
                    :required="true"
                    width="3"
                    :ajaxUrl="route('admin.feed.regions')"
                    wire:model.defer="region"/>
                <x-admin.forms.select2
                    id="unit"
                    :data="$unitModel ?? []"
                    text="title"
                    label="مرکز"
                    width="3"
                    :ajaxUrl="route('admin.feed.units',[0])"
                    wire:model.defer="unit"/>
                <x-admin.forms.select2
                    id="plan"
                    :data="$planModel ?? []"
                    text="title"
                    label="اکشن پلن"
                    width="3"
                    :ajaxUrl="route('admin.feed.plans',[$type])"
                    wire:model.defer="plan"/>
                <x-admin.forms.select2
                    id="user"
                    :data="[]"
                    text="name"
                    label="مربی"
                    width="4"
                    :ajaxUrl="route('admin.feed.users')"
                    wire:model.defer="user"/>
                <x-admin.forms.dropdown width="3" id="step" :data="$data['step']" label="نقش / مرحله" wire:model.live="step"/>
                <x-admin.forms.dropdown width="3" :data="$data['version']" id="version" label="نسخه آرمان" wire:model.live="version"/>
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
                            <th>وضعیت</th>
                            <th>اکشن پلن</th>
                            <th>درخواست تک مرحله ای</th>
                            <th>درخواست ستادی</th>
                            <th>درخواست طلایی</th>
                            <th>مرحله</th>
                            <th>مرکز</th>
                            <th>شهر/منطقه</th>
                            <th>تاریخ ارسال</th>
                            <th>تاریخ اخرین بروزرسانی</th>
                            <th>تعداد گفتوگو</th>
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
                                        <li>{{ $item->request->user?->name }}</li>
                                        <li>{{ $item->request->user?->phone }}</li>
                                        <li>{{ $item->request->user?->national_id }}</li>
                                        <li>{{ $item->request?->coach?->school_coach_type?->label() }}</li>
                                    </ul>
                                </td>
                                <td>{{ $item->status->label() }}</td>
                                <td>
                                    <ul>
                                        <li>عنوان:{{ $item->request?->plan?->title }}</li>
                                        <li>نسخه: {{ $item->request?->plan?->version?->value }}</li>
                                    </ul>
                                </td>
                                <td>{{ $item->request?->single_step ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->request?->staff ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->request?->golden ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->step->label() }}</td>
                                <td>
                                    {{ $item->request?->unit?->full ?? '-' }}
                                    @if($item->request?->unit?->parent)
                                        <hr>
                                        {{$item->request->unit->parent->full}}
                                    @endif
                                </td>
                                <td>
                                    {{ $item->request?->unit?->city?->title }} / {{ $item->request?->unit?->region?->title }}
                                </td>
                                <td>{{ persian_date($item->created_at) }}</td>
                                <td>{{ persian_date($item->updated_at) }}</td>
                                <td>{{ number_format($item->comments_count) }}</td>
                                <td>
                                    <x-admin.edit-btn target="_blank" href="{{ route('admin.reports.store',[$type,PageAction::UPDATE , $item->id]) }}?version={{$version}}&status={{$status}}&type={{$type}}&region={{$region}}&plan={{$plan}}&unit={{$unit}}&step={{$step}}&search={{$search}}"/>
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
