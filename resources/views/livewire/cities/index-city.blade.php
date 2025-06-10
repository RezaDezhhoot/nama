@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.loader :loading="$loading" />
    @section('title', 'شهر ها و مناطق' )
    <x-admin.form-control :store="false" title="شهر ها و مناطق"/>

    <div class="card card-custom">
        <div class="card-header d-flex align-items-center justify-content-end" id="headingOne">
            <button wire:click="newCity" class="btn btn-primary">
                شهر جدید
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.forms.select2
                    id="region"
                    :data="$region ?? []"
                    text="title"
                    label="منطقه"
                    :required="true"
                    width="6"
                    :ajaxUrl="route('admin.feed.regions')"
                    wire:model.defer="region"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered" id="myTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>تعداد منطقه</th>
                            <th>تعداد محله</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($items as $item)
                            <tr style="cursor: grab">
                                <td class="sortable-handler" data-index="{{$item->id}}">{{ $loop->iteration }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->regions_count }}</td>
                                <td>{{ $item->neighborhoods_count }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.cities.store',[PageAction::UPDATE , $item->id]) }}"/>
                                    <x-admin.delete-btn onclick="deleteItem('{{$item->id}}')"  />
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
            </div>
            {{$items->links('livewire.layouts.paginate')}}
        </div>
    </div>
    <x-admin.modal-page id="city" title="شهر جدید" wire:click="storeCity">
        <x-admin.forms.validation-errors />
        <div class="row">
            <x-admin.forms.input type="text"  :required="true" id="title" label="عنوان" wire:model.defer="title" />
        </div>
    </x-admin.modal-page>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف این مورد',
                text: 'آیا از حذف این مورد اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush
