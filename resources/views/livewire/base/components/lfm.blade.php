<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => __('general.sidebar.lfm')]) )
    <x-admin.form-control :store="false"  title="{{__('general.sidebar.lfm')}}"/>
    <div wire:ignore class="card card-custom h-100 gutter-b example example-compact">

        <div class="card-body ">
            <iframe class="w-100 h-100" src="{{ route('fm.fm-button') }}" frameborder="0"></iframe>
        </div>
    </div>
</div>
