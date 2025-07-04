@props(['id', 'label','hidden' => false,'data' => null,'width' => 12, 'value' => false ,'ajaxUrl' => '' ,'multiple' => false , 'key' => 'id' , 'text' => 'text','required' => false,'options' => []])
<div {{ $hidden ? 'hidden' : '' }} class="form-group  col-12 col-md-{{$width}}">
    <div  >
        <label for="{{$id}}"> {{$label}} <span {{ ! $required ? 'hidden' : '' }} class="text-danger">*</span></label>
        <div class=" d-flex align-items-center" wire:ignore>
            <select  id="{{$id}}" multiple class="form-control select2"  x-init="
                    Livewire.on('attachSelect2#{{$id}}' , function ([data], text = 'title') {
                        console.log(data)
                        var newOption = new Option(data[text], data.id, true, true);
                        $('#{{$id}}').append(newOption).trigger('change')
                    })
                    Livewire.on('clear#{{$id}}' , function (ids, text = 'title') {
                       $('#{{$id}}').empty();
                    })
                    Livewire.on('reloadSelect2#{{$id}}' , function (data , text = 'title' , multiple = true) {
                        $('#{{$id}}').empty();
                        if (multiple) {
                            data.forEach(item => {
                                var newOption = new Option(item[text], item.id, true, true);
                                $('#{{$id}}').append(newOption).trigger('change')
                            })
                        } else {
                            var newOption = new Option(data[text], data.id, true, true);
                            $('#{{$id}}').append(newOption).trigger('change')
                        }
                    })
                    $(document).ready(() => {
                        $('#{{$id}}').select2({
                            placeholder: 'select',
                            allowClear: true,
                            multiple: '{{$multiple}}',
                            data: JSON.parse('{{ json_encode($options) }}'),
                            ajax: JSON.parse('{{ json_encode($ajaxUrl) }}') ? {
                                url:  '{{$ajaxUrl}}',
                                data: function (params) {
                                    var query = {
                                        search: params.term,
                                        type: 'public'
                                    }
                                    return query;
                                },
                                processResults: function (data) {
                                    return {
                                        results: data
                                    };
                                }
                            } : null
                        })
                        $('#{{$id}}').on('change', function (e) {
                            var data = $('#{{$id}}').select2('val');
                            @this.set('{{$attributes->wire("model")->value}}', data);
                        });
                        $('#{{$id}}').val($('#{{$id}}').select2('val'));
                        $('#{{$id}}').trigger('change');
                        $('#clear{{$id}}').on('click', function (e) {
                            $('#{{$id}}').empty();
                            @this.set('{{$attributes->wire("model")->value}}', null);
                        });
                        Livewire.on('reloadData#{{$id}}' , function ([data]) {
                            $('#{{$id}}').empty();
                            $('#{{$id}}').select2({
                                placeholder: 'select',
                                allowClear: true,
                                multiple: '{{$multiple}}',
                                data: data,
                            })
                        })
                        Livewire.on('reloadAjaxURL#{{$id}}' , function ([url]) {
                            $('#{{$id}}').select2({
                                placeholder: 'select',
                                allowClear: true,
                                multiple: '{{$multiple}}',
                                ajax:{
                                    url,
                                    data: function (params) {
                                        var query = {
                                            search: params.term,
                                            type: 'public'
                                        }
                                        return query;
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data
                                        };
                                    }
                                }
                            })
                        })
                    })
            ">
                <option value="">-</option>
                @if($data && is_array($data) && sizeof($data) > 0)
                    @if($multiple)
                        @foreach($data as $item)
                            <option selected value="{{ $item[$key] ?? null }}">{{$item[$text] ?? '' }}</option>
                        @endforeach
                    @else
                        <option selected value="{{ $data[$key] }}">{{$data[$text] ?? '' }}</option>
                    @endif
                @endif

            </select>
        </div>
    </div>
    @error($attributes->wire("model")->value)
    <small class="text-danger d-block">{{ $message }}</small>
    @enderror
</div>
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('updateSelect2' , function ([data]) {
                $(`#${data['id']}`).empty();
                if (data['multiple']) {
                    data['data'].forEach(item => {
                        var newOption = new Option(item[data['text'] ?? 'text'], item['id'], true, true);
                        $(`#${data['id']}`).append(newOption).trigger('change')
                    })
                } else {
                    var newOption = new Option(data['data'][data['text'] ?? 'text'], data['data']['id'], true, true);
                    $(`#${data['id']}`).append(newOption).trigger('change')
                }
            })
        })

    </script>
@endpush
