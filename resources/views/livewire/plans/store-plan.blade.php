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
                <x-admin.forms.checkbox width="6" id="golden" label="طلایی" wire:model.live="golden"/>

                @if($golden)
                    <div class="col-6">
                        <button wire:click="showLimits" class="btn mb-2 btn-sm btn-outline-success">مشاهده کد ملی های مجاز</button>
                    </div>
                @endif
                <div class="col-12 row table-responsive">
                    @if($golden)
                        <table class="table table-bordered">
                            <tr>
                                <td> <x-admin.forms.checkbox  id="ring_member_required" label="اعضای حلقه اجباری باشد(درخواست)" wire:model.defer="ring_member_required"/></td>
                            </tr>
                            <tr>
                                <td><x-admin.forms.checkbox  id="show_ring_member" label="نمایش اعضای حلقه(درخواست)" wire:model.defer="show_ring_member"/></td>
                            </tr>
                        </table>
                    @endif
                    <table class="table table-bordered">
                        <tr>
                            <td> <x-admin.forms.checkbox id="letter_required" label="فایل نامه امام اجباری باشد(درخواست)" wire:model.defer="letter_required"/></td>
                            <td> <x-admin.forms.checkbox  id="letter2_required" label="فایل نامه رابط منطقه اجباری باشد(درخواست)" wire:model.defer="letter2_required"/></td>
                            <td> <x-admin.forms.checkbox id="images_required" label="تصاویر بیشتر اجباری باشد(درخواست)" wire:model.defer="images_required"/></td>
                        </tr>
                        <tr>
                            <td><x-admin.forms.checkbox  id="show_letter" label="نمایش فایل نامه امام(درخواست)" wire:model.defer="show_letter"/></td>
                            <td><x-admin.forms.checkbox  id="show_area_interface" label="نمایش فایل نامه رابط منطقه(درخواست)" wire:model.defer="show_area_interface"/></td>
                            <td><x-admin.forms.checkbox  id="show_images" label="نمایش تصاویر بیشتر (درخواست)" wire:model.defer="show_images"/></td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <td><x-admin.forms.checkbox id="report_video_required" label="فایل ویدئویی اجباری باشد(گزارش)" wire:model.defer="report_video_required"/></td>
                            <td><x-admin.forms.checkbox id="report_other_video_required" label="فایل های ویدئویی بیشتر اجباری باشد(گزارش)" wire:model.defer="report_other_video_required"/></td>
                            <td><x-admin.forms.checkbox  id="report_images_required" label="فایل های پیوست تصویری اجباری باشد(گزارش)" wire:model.defer="report_images_required"/></td>
                            <td><x-admin.forms.checkbox  id="report_images2_required" label="فایل  های پیوست تصویری بیشتر اجباری باشد(گزارش)" wire:model.defer="report_images2_required"/></td>
                        </tr>
                        <tr>
                            <td><x-admin.forms.checkbox  id="show_report_video" label="نمایش فایل ویدئویی (گزارش)" wire:model.defer="show_report_video"/></td>
                            <td><x-admin.forms.checkbox  id="show_report_other_video" label="نمایش فایل های ویدئویی بیشتر(گزارش)" wire:model.defer="show_report_other_video"/></td>
                            <td><x-admin.forms.checkbox  id="show_report_images" label="نمایش فایل های پیوست تصویری(گزارش)" wire:model.defer="show_report_images"/></td>
                            <td><x-admin.forms.checkbox  id="show_report_images2" label="نمایش فایل های پیوست تصویری بیشتر(گزارش)" wire:model.defer="show_report_images2"/></td>
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
    <x-admin.modal-page id="limits" title="محدودیت ها" wire:click="addLimit">
        <x-admin.forms.validation-errors/>
        @include('livewire.includes.advance-table')
        <hr>
        <div class="row d-flex justify-content-between mb-5">
            <div class="col-6">
                <label for="search">شماره ملی جدید</label>
                <input id="limitValue" type="text" class="form-control"  wire:model.defer="limitValue">
                <span>یا</span>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="validatedCustomFile" wire:model.defer="limitFile">
                    <label class="custom-file-label" for="validatedCustomFile">
                        {{ $limitFile ? 'فایل پیوست شد' : 'اپلود فایل اکسل...' }}
                    </label>
                    <small class="text-info">
                        <a href="/samples/plan-limits.xlsx">دریافت فایل نمونه</a>
                    </small>
                </div>
            </div>
            <div class="col-12">
                <hr>
                <button wire:click="addLimit" class="btn btn-outline-primary">ارسال</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12  table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شناسه</th>
                        <th>مقدار</th>
                        <th>اقدامات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($limits as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->value }}</td>
                            <td>
                                <x-admin.delete-btn onclick="deleteLimit('{{$item->id}}')"  />
                            </td>
                        </tr>
                    @endforeach
                    @if(sizeof($limits) == 0)
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
        {{$limits?->links('livewire.layouts.paginate')}}
    </x-admin.modal-page>
</div>
@push('scripts')
    <script>
        function deleteLimit(id) {
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
                    @this.call('deleteLimit' , id)
                }
            })
        }

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
