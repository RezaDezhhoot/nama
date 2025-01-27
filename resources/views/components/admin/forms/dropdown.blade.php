@props(['id', 'label' , 'data' , 'required' => false , 'help' => false , 'value' => false,'width' => 12 ,'disabled' =>false ])
<div class="form-group col-md-{{$width}} col-12">
    <label for="{{$id}}"> {{$label}}  <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
    <select {{ $attributes }}  {{  $disabled ? "disabled" : "" }}  id="{{$id}}"  {!! $attributes->merge(['class'=> 'form-control']) !!}>
        <option value="">select</option>
        @foreach($data as $key => $item)
            <option value="{{ $value ? $item : $key }}">{{$item}}</option>
        @endforeach
    </select>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
    @error($id)
        <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
