@props(['id', 'label' , 'data'  , 'required' => false , 'help' => false , 'value' => false,'with' => 12 ])
<div class="form-group col-md-{{$with}} col-12">
    <label  for="{{$id}}"> {{$label}} </label>
    <input id="{{$id}}" {{ $attributes }} {!! $attributes->merge(['class'=> 'form-control']) !!} list="searchable-dropdown-{{$id}}">
    <datalist multiple id="searchable-dropdown-{{$id}}">
        <option value="">انتخاب</option>
        @foreach($data as $key => $item)
            <option value="{{ $value ? $item : $key }}">{{$item}}</option>
        @endforeach
    </datalist>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
    @error($id)
    <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
