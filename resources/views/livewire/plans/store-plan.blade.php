@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
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

                <x-admin.forms.checkbox  id="letter_required" label="فایل نامه امام اجباری باشد" wire:model.defer="letter_required"/>
                <x-admin.forms.checkbox  id="letter2_required" label="فایل نامه رابط منطقه اجباری باشد" wire:model.defer="letter2_required"/>

                @if($item)
                    <x-admin.forms.select2
                        id="requirements"
                        :data="$requirements ?? []"
                        text="title"
                        :multiple="true"
                        label="پیشنیاز ها"
                        :ajaxUrl="route('admin.feed.plans',[$model?->item?->type,$model?->id])"
                        wire:model.defer="requirements"/>
                @endif


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
