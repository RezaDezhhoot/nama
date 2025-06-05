@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'مراکز')
    <x-admin.form-control exportable="export" link="{{ route('admin.units.store',[PageAction::CREATE] ) }}" title="مراکز"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="4"  id="type" :data="$data['type']" label="نوع" wire:model.live="type"/>
                <x-admin.forms.select2
                    id="region"
                    :data="$regionModel ?? []"
                    text="title"
                    label="منظقه"
                    width="4"
                    :ajaxUrl="route('admin.feed.regions')"
                    wire:model.defer="region"/>
                <x-admin.forms.select2
                    id="unit"
                    :data="$unitModel ?? []"
                    text="title"
                    label="مرکز محوری"
                    width="4"
                    :ajaxUrl="route('admin.feed.units')"
                    wire:model.defer="unit"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>عنوان</th>
                            <th>نوع</th>
                            <th>نوع فرعی</th>
                            <th>مرکز بالادست</th>
                            <th>نقش ها</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }} - {{ $item->text }}</td>
                                <td>{{ $item->type->label() }}</td>
                                <td>{{ $item->sub_type?->label() ?? '-' }}</td>
                                <td>{{ $item->parent?->title ?? "مرکز محوری" }}</td>
                                <td>
                                   <ul>
                                       @foreach($item->roles as $role)
                                           <li>
                                               <span class="badge my-1 badge-{{ $role->role->badge() }}">{{ $role->user?->name }} : {{ $role->role?->label() }}</span>
                                           </li>
                                       @endforeach
                                   </ul>
                                </td>
                                <td >
                                    <x-admin.edit-btn href="{{ route('admin.units.store',[PageAction::UPDATE , $item->id]) }}?type={{$type}}&region={{$region}}&unit={{$unit}}"/>
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
