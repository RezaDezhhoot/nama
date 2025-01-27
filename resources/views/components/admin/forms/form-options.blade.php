@props(['options', 'formKey','formDataType' => 'text'])

<div class="form-group col-12 d-flex justify-content-between">
    <h6 class="d-inline">{{ __('general.values') }}</h6>
    <button type="button" class="btn btn-success" wire:click="addOption({{$formKey}})">{{ __('general.actions.create') }}</button>
</div>

@foreach($options as $optionKey => $option)
    <div class="form-group col-lg-12" style="display: flex;align-items: center">
        <div class="flex-fill" style="width: 100%;">
            <label for="option_value_{{$optionKey}}">{{__('general.title')}}</label>
            <input id="option_value_{{$optionKey}}" type="text" class="form-control"
                   wire:model.defer="formOptions.{{$optionKey}}.name">
        </div>
        <div class="flex-fill ml-1" style="width: 100%;">
            <label for="option_price_{{$optionKey}}">{{__('general.value')}}</label>
            @if($formDataType == 'color')
                <input id="option_price_{{$optionKey}}" type="color" class="form-control"
                       wire:model.defer="formOptions.{{$optionKey}}.value">
            @else
                <input id="option_price_{{$optionKey}}" type="text" class="form-control"
                       wire:model.defer="formOptions.{{$optionKey}}.value">
            @endif
        </div>
        <div class="flex-fill ml-1" style="width: 100%;">
            <label for="option_price_{{$optionKey}}">{{__('general.amount')}}</label>
            <input id="option_price_{{$optionKey}}" type="text" class="form-control"
                   wire:model.defer="formOptions.{{$optionKey}}.price">
        </div>
        <div class="d-flex align-items-end mx-1" style="margin-top: 24px;">
            <button type="button" class="btn btn-danger" wire:click="deleteOption({{$formKey}},{{$optionKey}})">{{__('general.delete')}}</button>
        </div>
    </div>
@endforeach
