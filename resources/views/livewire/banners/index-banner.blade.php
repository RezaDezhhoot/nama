@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title',  'بنر ها' )
    <x-admin.form-control link="{{ route('admin.banners.store',[PageAction::CREATE] ) }}" title="بنر ها"/>
    <div class="card card-custom">
        <div class="card-body">
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered" id="sortable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>عنوان</th>
                            <th>پروژه</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="sortable-handler" data-index="{{$item->id}}">{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->item?->title ?? '-' }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.banners.store',[PageAction::UPDATE , $item->id]) }}"/>
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
                        <tbody wire:loading >
                            <x-admin.big-loader :table="true" width="20" height="20" />
                        </tbody>
                    </table>
                </div>
            </div>
            {{$items?->links('livewire.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"/>
    <script>
        function deleteItem(id) {
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
                @this.call('deleteItem' , id)
                }
            })
        }

        var fixHelperModified = function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },
            updateIndex = function(e, ui) {
                let newSort = {}
                $('td.sortable-handler', ui.item.parent()).each(function (i) {
                    $(this).html(i+1);
                    newSort[$(this)[0].getAttribute('data-index')] = i
                });
                @this.call('updateFormSort' , newSort)
            };

        $("#sortable tbody").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

        $("tbody").sortable({
            distance: 5,
            delay: 100,
            opacity: 0.6,
            cursor: 'move',
            update: function() {}
        });
    </script>
@endpush
