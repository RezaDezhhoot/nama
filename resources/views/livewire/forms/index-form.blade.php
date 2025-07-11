@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'اکشن پلن ها')
    <x-admin.form-control link="{{ route('admin.forms.store',[PageAction::CREATE] ) }}" title="اکشن پلن ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown  width="6" id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
                <x-admin.forms.dropdown  width="6"  id="item" :data="$data['items']" label="پروژه" wire:model.live="item"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>اجباری</th>
                            <th>عنوان</th>
                            <th>پروژه</th>
                            <th>وضعیت</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->required ? 'بله' : 'خیر' }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->item?->title }}</td>
                                <td>{{ $item->status?->label() }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.forms.store',[PageAction::UPDATE , $item->id]) }}?status={{$status}}&item={{$item?->id}}&search={{$search}}"/>
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
    </script>
@endpush
