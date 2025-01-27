@props(['id', 'conditions', 'formKey', 'editAble' => true ,'form' => []])

<div class="form-group col-12 d-flex justify-content-between">
    <h6 class="d-inline">{{ __('general.conditions') }}</h6>
    @if($editAble)
        <button type="button" class="btn btn-success" wire:click="addCondition({{$formKey}})">{{ __('general.actions.create') }}</button>
    @endif
</div>
{{--{{dd($conditions)}}--}}
@foreach($conditions as $conditionKey => $condition)
    <div class="form-group col-12 d-flex">
        <div class="flex-fill">
            <label for="{{$id}}condition_value_{{$conditionKey}}">{{ __('general.form_name') }}</label>
            <select id="{{$id}}condition_value_{{$conditionKey}}" class="form-control"  wire:model.defer="formConditions.{{$conditionKey}}.value">
                @foreach(collect($form)->pluck('label','name')->toArray() as $key => $item)
                    <option value="{{ $key }}">{{$item}}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-fill ml-1">
            <label for="{{$id}}condition_target_{{$conditionKey}}">{{ __('general.value') }}</label>
            <input id="{{$id}}condition_target_{{$conditionKey}}" type="text" class="form-control"
                   wire:model.defer="formConditions.{{$conditionKey}}.target">
        </div>
        <div class="flex-fill ml-1">
            <label for="{{$id}}condition_visibility_{{$conditionKey}}"> {{ __('general.visibility') }}</label>
            <select id="{{$id}}condition_visibility_{{$conditionKey}}" class="form-control"
                    wire:model.defer="formConditions.{{$conditionKey}}.visibility">
                <option value="">-</option>
                <option value="hide">{{__('general.hide')}}</option>
                <option value="show">{{__('general.visible')}}</option>
            </select>
        </div>
        @if($editAble)
            <div class="d-flex align-items-end mx-1">
                <button type="button" class="btn btn-danger" wire:click="deleteCondition({{$formKey}},{{$conditionKey}})">{{__('general.delete')}}</button>
            </div>
        @endif
    </div>
@endforeach
