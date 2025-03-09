@use(App\Enums\PageAction)
<div wire:init="init">
    <x-admin.loader :loading="$loading" />
    @section('title',$header)
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" :title="$header"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <div class="row">
                <x-admin.forms.input type="text"  :required="true" id="title" label="عنوان شهر" wire:model.defer="title" />
            </div>
            <x-admin.form-section label="مناطق">
                <div class="row w-100">
                    <div class="col-12 table-responsive">
                        <div >
                            <div class="d-flex align-items-center justify-content-end">
                                <x-admin.button class="btn btn-outline-primary font-weight-bolder btn-sm" content="منطقه جدید" wire:click="regionForm()"/>
                            </div>
                            <table class="table table-striped table-sm table-bordered" id="kt_datatable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان منطقه</th>
                                    <th>تعداد محله</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($regions as $key => $item)
                                    <tr data-toggle="collapse" data-target="#row{{$key}}" class="accordion-toggle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->neighborhoods_count }}</td>
                                        <td class="d-flex">
                                            <x-admin.edit-btn wire:click="regionForm('{{$item->id}}')"/>
                                            <x-admin.delete-btn onclick="deleteRegion('{{$item->id}}')"/>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td wire:ignore.self colspan="12" class="hiddenRow">
                                            <div  wire:ignore.self class="accordian-body collapse" id="row{{$key}}">
                                                <table class="table table-bordered table-primary table-striped">
                                                    <thead>
                                                    <tr class="info">
                                                        <th>#</th>
                                                        <th>عنوان محله</th>
                                                        <th>عملیات</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($item->neighborhoods as $key2 => $item2)
                                                        <tr data-toggle="collapse" data-target="#n-row{{$key2}}" class="accordion-toggle">
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item2->title }}</td>
                                                            <td class="d-flex">
                                                                <x-admin.edit-btn wire:click="neighborhoodForm('{{$item2->id}}')"/>
                                                                <x-admin.delete-btn onclick="deleteNeighborhood('{{$item2->id}}')"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td wire:ignore.self colspan="12" class="hiddenRow">
                                                                <div  wire:ignore.self class="accordian-body collapse" id="n-row{{$key2}}">
                                                                    <table class="table table-info table-striped">
                                                                        <thead>
                                                                        <tr class="info">
                                                                            <th>#</th>
                                                                            <th>عنوان ناحیه</th>
                                                                            <th>عملیات</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach($item2->areas as $key3 => $item3)
                                                                            <tr>
                                                                                <td>{{ $loop->iteration }}</td>
                                                                                <td>{{ $item3->title }}</td>
                                                                                <td class="d-flex">
                                                                                    <x-admin.edit-btn wire:click="areaForm('{{$item3->id}}')"/>
                                                                                    <x-admin.delete-btn onclick="deleteArea('{{$item3->id}}')"/>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <td class="text-center" colspan="7">
                                        داده ثبت نشده است
                                    </td>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-admin.form-section>
        </div>
    </div>
    <x-admin.modal-page id="region" title="" wire:click="storeRegion">
        <x-admin.forms.validation-errors />
        <div class="row">
            <x-admin.forms.input type="text"  :required="true" id="rTitle" label="عنوان منطقه" wire:model.defer="rTitle" />
            <div class="input-box col-12 my-2">
                <h6 class="label-text"> فایل محله ها xlsx </h6>
                <div class="input-box col-12 p-0 m-0">
                    <div class="form-group w-100">
                        <div class="custom-file"
                             x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false"
                             x-on:livewire-upload-cancel="uploading = false"
                             x-on:livewire-upload-error="uploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            <input id="neighborhoods" type="file" class="custom-file-input" wire:model.live="neighborhoods">
                            <div x-show="uploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>
                            <label class="custom-file-label"  for="excel">
                                {{ is_object($neighborhoods) ? 'فایل پیوست شد' : '' }}
                            </label>
                            <br>
                            @error('excel')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.modal-page>
    <x-admin.modal-page id="neighborhood" title="" wire:click="storeNeighborhood">
        <x-admin.forms.validation-errors />
        <div class="row">
            <x-admin.forms.input type="text"  :required="true" id="nTitle" label="عنوان محله" wire:model.defer="nTitle" />
            <div class="input-box col-12 my-2">
                <h6 class="label-text"> نواحی </h6>
                <div class="input-box col-12 p-0 m-0">
                    <div class="form-group w-100">
                        <div class="custom-file"
                             x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false"
                             x-on:livewire-upload-cancel="uploading = false"
                             x-on:livewire-upload-error="uploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            <input id="areas" type="file" class="custom-file-input" wire:model.live="areas">
                            <div x-show="uploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>
                            <label class="custom-file-label"  for="excel">
                                {{ is_object($areas) ? 'فایل پیوست شد' : '' }}
                            </label>
                            <br>
                            @error('excel')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.modal-page>
    <x-admin.modal-page id="area" title="" wire:click="storeArea">
        <x-admin.forms.validation-errors />
        <div class="row">
            <x-admin.forms.input type="text"  :required="true" id="aTitle" label="عنوان ناحیه" wire:model.defer="aTitle" />
        </div>
    </x-admin.modal-page>
</div>
@push('scripts')
    <script>
        function deleteRegion(id) {
            Swal.fire({
                title: 'حذف منطقه!',
                text: 'آیا از حذف این منطقه اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                @this.call('deleteRegion', id)
                }
            })
        }
        function deleteNeighborhood(id) {
            Swal.fire({
                title: 'حذف محله!',
                text: 'آیا از حذف این محله اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                @this.call('deleteNeighborhood', id)
                }
            })
        }
        function deleteArea(id) {
            Swal.fire({
                title: 'حذف ناحیه!',
                text: 'آیا از حذف این ناحیه اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                @this.call('deleteArea', id)
                }
            })
        }
        function deleteItem() {
            Swal.fire({
                title: 'حذف شهر!',
                text: 'آیا از حذف این شهر اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush
