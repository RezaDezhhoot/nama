@props(['id', 'label' => null, 'type' => null, 'required' => false ,'disabled' => false ,'help' => false,'width' => 12 ,'extraClasses' => null])
<div class="form-group col-12 col-md-{{$width}} {{ $extraClasses }}">
    @if(! empty($label))
        <label for="{{$id}}">{{$label}} <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
    @endif
    <input  {!! $attributes->merge(['class'=> 'form-control']) !!} {{ $disabled ? 'disabled' : '' }} type="{{$type}}" id="{{$id}}" {{ $attributes }}>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
    @error($id)
        <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
