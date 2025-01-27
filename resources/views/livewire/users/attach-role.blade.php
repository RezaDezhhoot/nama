@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'لیست ادمین ها')
    <x-admin.form-control :store="false" link="{{ route('admin.plans.store',[PageAction::CREATE] ) }}" title="لیست ادمین ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="card-body ">
                    <x-admin.forms.validation-errors/>
                    <x-admin.form-section label="نقش در نما">
                        <x-admin.forms.select2
                            :multiple="true"
                            id="users"
                            :data="[]"
                            text="text"
                            label="انتخاب کاربران"
                            ajaxUrl="{{route('admin.feed.users')}}"
                            wire:model.defer="users"/>
                        <x-admin.forms.dropdown  id="role" :data="$data['role']" label="نقش" wire:model.defer="role"/>
                        <div class="col-12">
                            <button class="btn btn-outline-primary" type="button" wire:click="attachRole">ارسال نقش</button>
                        </div>
                    </x-admin.form-section>
                </div>
            </div>
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>کدملی</th>
                            <th>شماره همراه</th>
                            <th>نقش در نما</th>
                            <th>نقش  در ارمان</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->national_id }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>
                                    {{ $item->nama_role?->label() }}
                                    @if($item->nama_role)
                                        <x-admin.delete-btn onclick="deleteItem('{{$item->id}}')"  />
                                    @endif
                                </td>
                                <td>{{ $item->role?->label() }}</td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="17">
                                اطلاعاتی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                        <tbody wire:loading >
                        <x-admin.big-loader :table="true" width="20" height="20" />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'گرفتن نقش',
                text: 'آیا از گرفتن نقش از این کاربر اطمینان دارید؟',
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
