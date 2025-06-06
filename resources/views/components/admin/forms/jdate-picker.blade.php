@props(['id', 'label' ,'help' => false,'width' => 12,'sign' => '-','timer' => false])
<div class="form-group col-12 col-md-{{$width}}">
    <label for="{{$id}}">{{$label}}</label>
    <input id="{{$id}}" {{ $attributes }} {!! $attributes->merge(['class'=> 'form-control p-datepicker']) !!}
    x-data
           x-init="$('#{{$id}}').persianDatepicker({
           initialValue: false,
           autoClose: true,
           format: 'YYYY{{$sign}}MM{{$sign}}DD{{$timer ? ' HH:mm:ss' : ''}}',
           timePicker: {
              enabled: '{{$timer}}',
           },
           onSelect: function () {
                    $dispatch('input', $('#{{$id}}').val())
                },

           });">
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
    @error($id)
    <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
