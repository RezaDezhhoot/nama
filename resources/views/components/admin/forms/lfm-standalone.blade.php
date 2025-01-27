@props(['id', 'label', 'required' => false, 'help', 'file' , 'disable'=>false , 'real_size' => false ,'width' => 12 ,'hidden' => false])
<div class="form-group col-12 col-md-{{$width}} {{ $hidden ? 'd-none' : '' }}">
    <label for="{{$id}}">{{$label}} <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
    @if(!$disable)
        <div class="input-group d-flex align-items-center justify-content-center">
            @if($file && gettype($file) == 'string')
                @foreach(explode(',', $file) as $key => $item)
                    @if(isImage($item))
                        @if($real_size)
                            <img src="{{asset($item)}}" class="w-100 p-5"
                                 style="border-radius: 5px" />
                        @else
                            <img src="{{asset($item)}}"  width="60px" height="60px" class="mr-1 mb-1 imglist"
                                 style="border-radius: 5px" />
                        @endif
                    @elseif(isVideo($item))
                        @if($real_size)
                            <video  class="w-100 p-5"
                                    style="border-radius: 5px" controls muted src="{{asset($item)}}"></video>
                        @else
                            <video  width="60px" height="60px" class="mr-1 mb-1 imglist"
                                    style="border-radius: 5px" controls muted src="{{asset($item)}}"></video>
                        @endif
                    @endif
                @endforeach
            @endif
            <input type="text" {{ $attributes->wire('model') }} id="{{$id}}" {!! $attributes->merge(['class'=>
            'form-control']) !!} name="image"
                   aria-label="Image" aria-describedby="button-image"
                   x-data
                   x-init="$('#{{$id}}').on('change', function () {alert(2); $dispatch('input', $(this).val()) })"
            >
            <div class="input-group-append">
                <button onclick="openWindow('{{$id}}')" {{$disable ? 'disabled' : '' }} class="btn btn-outline-secondary" type="button"
                        id="button-{{$id}}">select</button>
            </div>
        </div>
        @error($id)
        <small class="text-danger d-block">{{ $message }}</small>
        @enderror
    @endif
</div>
@push('scripts')
<script>
    var id , input , input_id;
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById('button-{{$id}}').addEventListener('click', (event) => {
                event.preventDefault();
                id = event.target.id;
                input_id = id.replace("button-", '');
                input = document.getElementById(input_id);
                window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
            });
        });


        // set file link

        function fmSetLink($url) {
            input.value = $url;
            @this.set(input_id, $url);
        }
</script>
@endpush
