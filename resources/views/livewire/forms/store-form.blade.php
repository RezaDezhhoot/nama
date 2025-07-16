@use('App\Enums\PageAction')
@use('App\Enums\FormItemType')
<div  class="h-100">
{{--    <x-admin.big-loader :loading="$loading"/>--}}
    @section('title', $header )
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" store="store" title="فرم ها"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات پایه">
                <x-admin.forms.input width="4" type="text" :required="true" id="title" label="عنوان" wire:model.defer="title"/>
                <x-admin.forms.dropdown width="4" :data="$data['items']" :required="true" id="item" label="پروژه" wire:model.defer="item"/>
                <x-admin.forms.dropdown width="4" :data="$data['status']" :required="true" id="status" label="وضعیت" wire:model.defer="status"/>
                <x-admin.forms.checkbox  id="required" label="اجباری " wire:model.defer="required"/>
                <x-admin.forms.text-area dir="rtl" id="body" label="توضیحات" wire:model.defer="body"/>
            </x-admin.form-section>
            <x-admin.form-section label="سوالات">
                <div class="col-12  table-responsive">
                    <button wire:click="openItem" class="btn btn-sm btn-outline-warning">سوال جدید</button>
                    <hr>
                    <table id="items" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>عنوان</th>
                            <th>اجباری</th>
                            <th>نوع</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $key => $item)
                            <tr style="cursor: grab">
                                <td data-index="{{$item->id}}" class="sortable-handler">{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->required ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->type?->label() }}</td>
                                <td>
                                    <x-admin.edit-btn wire:click="openItem('{{$item->id}}')" />
                                    <x-admin.delete-btn onclick="deleteQ('{{$item->id}}')"  />
                                </td>
                            </tr>
                        @endforeach
                        @if(sizeof($items) == 0)
                            <td class="text-center" colspan="17">
                                اطلاعاتی برای نمایش وجود ندارد
                            </td>
                        @endif
                        </tbody>
                    </table>
                </div>
            </x-admin.form-section>
        </div>
    </div>
    <x-admin.modal-page id="item" title="" wire:click="storeItem">
        <x-admin.forms.validation-errors/>
        <div class="row">
            <x-admin.forms.input width="4" type="text" :required="true" id="iTitle" label="عنوان" wire:model.defer="iTitle"/>
            <x-admin.forms.dropdown width="4" :data="$data['types']" :required="true" id="iType" label="نوع فیلد" wire:model.live="iType"/>
            <x-admin.forms.input width="4" type="text" id="iPlaceHolder" label="متن پیشفرض" wire:model.defer="iPlaceHolder"/>
            <x-admin.forms.input type="text" id="iHelp" label="متن کمکی" wire:model.defer="iHelp"/>
            <x-admin.forms.checkbox  id="iRequired" label="اجباری " wire:model.defer="iRequired"/>

            @if($iType == FormItemType::FILE->value)
                <x-admin.forms.input type="text" id="iMimeTypes" help="فرمت هارا با کاما (,) از هم جدا کنید" label="فرمت های مجاز" wire:model.defer="iMimeTypes"/>
            @endif
            @if(in_array($iType,[FormItemType::TEXT->value,FormItemType::NUMBER->value,FormItemType::TEXTAREA->value,FormItemType::FILE->value]))
                <x-admin.forms.input width="6" type="number" id="iMax" label="حداکثر مقدار" wire:model.defer="iMax"/>
                <x-admin.forms.input width="6" type="number" id="iMin" label="حداقل مقدار" wire:model.defer="iMin"/>
            @endif
            @if(in_array($iType,[FormItemType::CHECKBOX->value,FormItemType::RADIO->value,FormItemType::SELECT->value,FormItemType::SELECT2->value]))
                <div class="col-12">
                    <h4>گزینه ها</h4>
                    <hr>
                    <button wire:click="addOption" class="btn btn-sm btn-outline-warning">گزینه جدید</button>
                    <div class="row mt-4 pt-2 border">
                        @foreach($iOptions as $k => $o)
                            <div class="d-flex align-items-center justify-content-between row col-12">
                                <x-admin.forms.input type="text" width="11" :required="true" id="iOptions.{{$k}}" label="عنوان گزینه" wire:model.defer="iOptions.{{$k}}"/>
                                <button wire:click="deleteOption({{$k}})" class="btn p-1 col-1 btn-danger">حذف گزینه</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="col-12 mt-2">
                <h4>شرط ها</h4>
                <hr>
                <button wire:click="addCondition" class="btn btn-sm btn-outline-warning">گزینه شرط</button>
                <div class="row mt-4 pt-2 border">
                    @foreach($iConditions as $k => $c)
                        <div class="d-flex align-items-center justify-content-between row col-12">
                            <x-admin.forms.dropdown width="3" :data="$forms" :required="true" id="iConditions.{{$k}}.form" label="فرم هدف" wire:model.defer="iConditions.{{$k}}.form"/>
                            <x-admin.forms.input type="text" width="3" :required="true" id="iConditions.{{$k}}.target" label="مقدار هدف" wire:model.defer="iConditions.{{$k}}.target"/>
                            <x-admin.forms.dropdown width="3" :data="$data['actions']" :required="true" id="iConditions{{$k}}.action" label="رویداد" wire:model.defer="iConditions.{{$k}}.action"/>
                            <button wire:click="deleteCondition({{$k}})" class="btn p-1 col-1 btn-danger">حذف شرط</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-admin.modal-page>

    <script>
        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        }
        var updateIndex = function(e, ui) {
            let newSort = {}
            $('td.sortable-handler', ui.item.parent()).each(function (i) {
                $(this).html(i+1);
                newSort[$(this)[0].getAttribute('data-index')] = i
            });

            @this.call('updateFormSort' , newSort)
        };
        $("#items tbody").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

    </script>
</div>
@push('scripts')
    <script>
        function deleteQ(id) {
            Swal.fire({
                title: 'حذف کردن',
                text: 'آیا از حذف کردن این مورد اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('deleteQ' , id)
                }
            })
        }
        function deleteItem() {
            Swal.fire({
                title: 'حذف کردن',
                text: 'آیا از حذف کردن این مورد اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('deleteItem')
                }
            })
        }
    </script>
@endpush
