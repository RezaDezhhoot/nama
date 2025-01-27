@props(['id', 'label', 'required' => false])
<div class="form-group col-12" >
    <div wire:ignore>
        <label for="{{$id}}">{{$label}} <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
        <textarea {{ $attributes->wire('model') }} id="{{$id}}" class="resizable_textarea form-control"
                  x-data="{text: @entangle($attributes->wire('model')) }"
                  x-init="CKEDITOR.replace('{{$id}}', {
                            language: '{{ app()->getLocale() }}',
                            allowedContent: true,
                            extraAllowedContent: '*(*);*{*}',
                            extraAllowedContent: 'span;ul;li;table;td;style;*[id];*(*);*{*}',
                            filebrowserImageBrowseUrl: '/file-manager/ckeditor',
                            versionCheck : false
                        });
                        CKEDITOR.dtd.$removeEmpty['span'] = 0;
                        CKEDITOR.instances.{{$id}}.on('change', function () {
                            $dispatch('input', CKEDITOR.instances.{{$id}}.getData())
                        });"
                  x-text="CKEDITOR.instances.{{$id}}.setData(this.text); return this.text">
            </textarea>
    </div>
    @error($id)
    <small class="text-danger d-block">{{ $message }}</small>
    @enderror
    <script src="//cdn.ckeditor.com/4.22.0/full/ckeditor.js"></script>
</div>

