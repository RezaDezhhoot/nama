@props(['id', 'label' , 'disabled' => false ,'dir' => 'ltr' , 'help' => false ,'width' => 12])
<div class="form-group col-12 col-md-{{$width}}">
    <label for="{{$id}}">{{$label}} </label>
    <textarea {{ $attributes->wire('model') }} dir="{{$dir}}" {{ $disabled ? 'disabled' : '' }} id="{{$id}}" class="resizable_textarea form-control"></textarea>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
    @error($id)
    <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
