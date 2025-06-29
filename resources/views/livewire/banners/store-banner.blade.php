@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'بنر'.(' '.$header ?? '') )
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" title="بنر"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات پایه">
                <x-admin.forms.input width="4" type="text" :required="true" id="title" label="عنوان" wire:model.defer="title"/>
                <x-admin.forms.lfm-standalone width="4" :required="true" id="image" label="تصویر" :file="$image" wire:model="image"/>
                <x-admin.forms.lfm-standalone width="4"  id="mobile_image" label="تصویر موبایل" :file="$mobile_image" wire:model="mobile_image"/>
                <x-admin.forms.dropdown  id="item" :data="$data['items']" label="پروژه" wire:model.defer="item"/>
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
