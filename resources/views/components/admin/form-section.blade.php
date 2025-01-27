@props(['label'=> null ,'class'=>' row'])
<div class="mb-5" {{$attributes}}>
    <fieldset class="{{$class}} border">
        <legend class="mx-2">
            @if($label)
                <strong> {{ $label }}</strong>
            @endif
        </legend>
        {{ $slot }}
    </fieldset>
</div>
