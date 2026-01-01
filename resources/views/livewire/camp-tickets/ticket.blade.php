@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', $header )
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" :title="$header"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات پایه">
                <x-admin.forms.input  type="number" :required="true" id="request" label="شماره درخواست" wire:model.defer="request"/>
            </x-admin.form-section>
        </div>
    </div>
</div>
