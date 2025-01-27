@props(['id', 'label' , 'required' => false,'help' => false ,'width' => 12])
    <div class="form-group col-12 col-md-{{$width}}">
        <label for="{{ $id }}">
            <input type="checkbox" id="{{$id}}" {{ $attributes }}>
            {{ $label }}
        </label>
        <br>
        @if($help)
            <small class="text-info">{{$help}}</small>
        @endif
    </div>
