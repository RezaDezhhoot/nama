@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'اینم های داشبورد'.(' '.$header ?? '') )
    <x-admin.form-control :deleteAble="false" title="اینم های داشبورد"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات پایه">
                <x-admin.forms.input width="4" type="text" :required="true" id="title" label="عنوان" wire:model.defer="title"/>
                <x-admin.forms.input width="4" type="url" id="link" label="لینک" wire:model.defer="link"/>
                <x-admin.forms.input width="4" type="color" id="color" label="رنگ" wire:model.defer="color"/>
                <x-admin.forms.lfm-standalone width="6" :required="true" id="image" label="تصویر" :file="$image" wire:model="image"/>
                <x-admin.forms.lfm-standalone width="6" :required="true" id="logo" label="لوگو" :file="$logo" wire:model="logo"/>
                <x-admin.forms.checkbox  id="status" label="منتشر شده" wire:model.defer="status"/>

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
