@props(['id', 'label', 'help', 'required' => false ,'width' => 12 ,'timer' => true])
<div class="form-group col-12 col-md-{{$width}}">
    <label for="{{$id}}"> {{$label}}  <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
     <input id="{{$id}}" {!! $attributes->merge(['class'=> 'form-control p-datepicker']) !!}
    x-data
           x-init="$('#{{$id}}').pDatepicker({
                initialValue: false,
                autoClose: true,
                calendarType: 'gregorian',
                timePicker: {
                    enabled: '{{$timer}}',
                    meridiem: {
                        enabled: true
                    }
                },
                calendar: {
                    'persian': {
                    'locale': 'en',
                    'showHint': true,
                    'leapYearMode': 'algorithmic'
                    },
                    'gregorian': {
                        'locale': 'en',
                        'showHint': true
                    },
                },
                toolbox: {
                    'enabled': true,
                    'calendarSwitch': {
                    'enabled': false,
                    },
                },
                format: 'YYYY-MM-DD {{$timer ? 'HH:mm:ss' : ''}}',
                onSelect: function () {
                    $dispatch('input', $('#{{$id}}').val())
                }
                });">
    @isset($help)
        <small class="text-muted">{{$help}}</small>
    @endisset
</div>
