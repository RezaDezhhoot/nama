@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    @section('title', 'اکشن پلن'.(' '.$header ?? '') )
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" title="اکشن پلن"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات پایه">
                <x-admin.forms.input width="3" type="text" :required="true" id="title" label="عنوان" wire:model.defer="title"/>
                <x-admin.forms.dropdown width="2" :data="$data['status']" :required="true" id="status" label="وضعیت" wire:model.defer="status"/>
                <x-admin.forms.dropdown width="2" :data="$data['version']" :required="true" id="version" label="نسخه آرمان" wire:model.defer="version"/>
                <x-admin.forms.input width="5" type="text" :required="true" id="sub_title" label="زیر عنوان" wire:model.defer="sub_title"/>
                <x-admin.forms.dropdown  :data="$data['items']" :required="true" id="item" label="پروژه" wire:model.live="item"/>

                <x-admin.forms.lfm-standalone :required="true" id="image" label="لوگو" :file="$image" wire:model="image"/>
                <x-admin.forms.checkbox  id="bold" label="اولین پلن نمایش داده شود" wire:model.defer="bold"/>

                <x-admin.forms.input width="4" type="number" :required="true" id="max_number_people_supported" label="سقف تعداد نفرات مورد حمایت" wire:model.defer="max_number_people_supported"/>
                <x-admin.forms.input width="4" type="number" help="ریال" :required="true" id="support_for_each_person_amount" label="سرانه حمایتی هر نفر" wire:model.defer="support_for_each_person_amount"/>
                <x-admin.forms.input width="4" type="number"  :required="true" id="max_allocated_request" label="تعداد دفعات مجاز برای این پلن" wire:model.defer="max_allocated_request"/>

                <x-admin.forms.jdate-picker :timer="true" help="در صورت خالی رها کردن محدودیتی اعمال نمی شود" width="6" id="starts_at" label="تاریخ شروع مهلت زمانی این پلن" wire:model.defer="starts_at"/>
                <x-admin.forms.jdate-picker :timer="true" help="در صورت خالی رها کردن محدودیتی اعمال نمی شود" width="6" id="expires_at" label="تاریخ پایان مهلت زمانی این پلن" wire:model.defer="expires_at"/>
                <x-admin.forms.checkbox  id="single_step" label="درخواست تک مرحله ای" wire:model.defer="single_step"/>
                <x-admin.forms.checkbox id="staff" label="ستادی" wire:model.live="staff"/>
                @if($staff)
                    <x-admin.forms.input type="number" :required="true" id="staff_amount" label="مبلغ ثابت اکشن پلن" wire:model.defer="staff_amount"/>
                @endif
                <x-admin.forms.checkbox  id="golden" label="طلایی" wire:model.defer="golden"/>
                <div class="col-12 row table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td> <x-admin.forms.checkbox width="4" id="letter_required" label="فایل نامه امام اجباری باشد(درخواست)" wire:model.defer="letter_required"/></td>
                            <td> <x-admin.forms.checkbox width="4" id="letter2_required" label="فایل نامه رابط منطقه اجباری باشد(درخواست)" wire:model.defer="letter2_required"/></td>
                            <td> <x-admin.forms.checkbox width="4" id="images_required" label="تصاویر بیشتر اجباری باشد(درخواست)" wire:model.defer="images_required"/></td>
                        </tr>
                        <tr>
                            <td><x-admin.forms.checkbox width="4" id="show_letter" label="نمایش فایل نامه امام(درخواست)" wire:model.defer="show_letter"/></td>
                            <td><x-admin.forms.checkbox width="4" id="show_area_interface" label="نمایش فایل نامه رابط منطقه(درخواست)" wire:model.defer="show_area_interface"/></td>
                            <td><x-admin.forms.checkbox width="4" id="show_images" label="نمایش تصاویر بیشتر (درخواست)" wire:model.defer="show_images"/></td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <td><x-admin.forms.checkbox width="3" id="report_video_required" label="فایل ویدئویی اجباری باشد(گزارش)" wire:model.defer="report_video_required"/></td>
                            <td><x-admin.forms.checkbox width="3" id="report_other_video_required" label="فایل های ویدئویی بیشتر اجباری باشد(گزارش)" wire:model.defer="report_other_video_required"/></td>
                            <td><x-admin.forms.checkbox width="3" id="report_images_required" label="فایل های پیوست تصویری اجباری باشد(گزارش)" wire:model.defer="report_images_required"/></td>
                            <td><x-admin.forms.checkbox width="3" id="report_images2_required" label="فایل  های پیوست تصویری بیشتر اجباری باشد(گزارش)" wire:model.defer="report_images2_required"/></td>
                        </tr>
                        <tr>
                            <td><x-admin.forms.checkbox width="3" id="show_report_video" label="نمایش فایل ویدئویی (گزارش)" wire:model.defer="show_report_video"/></td>
                            <td><x-admin.forms.checkbox width="3" id="show_report_other_video" label="نمایش فایل های ویدئویی بیشتر(گزارش)" wire:model.defer="show_report_other_video"/></td>
                            <td><x-admin.forms.checkbox width="3" id="show_report_images" label="نمایش فایل های پیوست تصویری(گزارش)" wire:model.defer="show_report_images"/></td>
                            <td><x-admin.forms.checkbox width="3" id="show_report_images2" label="نمایش فایل های پیوست تصویری بیشتر(گزارش)" wire:model.defer="show_report_images2"/></td>
                        </tr>
                    </table>
                </div>

                <div wire:ignore class="{{ $item ? '' : 'd-none' }} col-12 row">
                    <x-admin.forms.select2
                        id="requirements"
                        :data="$requirements ?? []"
                        text="text"
                        :multiple="true"
                        label="پیشنیاز ها"
                        :ajaxUrl="route('admin.feed.plans',[$model?->item?->type,$model?->id])"
                        wire:model.defer="requirements"/>
                </div>


                <x-admin.forms.text-area dir="rtl" id="body" label="توضیحات" wire:model.defer="body"/>
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem() {
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
                @this.call('deleteItem')
                }
            })
        }
    </script>
@endpush
