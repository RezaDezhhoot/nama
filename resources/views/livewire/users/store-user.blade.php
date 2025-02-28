@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'نقش'.(' '.$header ?? '') )
    <x-admin.form-control :deleteAble="false" :store="false" title="نقش"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات نقش کاربر">
                <x-admin.forms.dropdown  id="role" :data="$data['role']" label="نقش" wire:model.defer="role"/>
                <x-admin.forms.dropdown  id="item" :data="$data['items']" label="پروژه" wire:model.defer="item"/>
                <div class="col-12">
                    <button class="btn btn-outline-primary" type="button" wire:click="attachRole">ارسال نقش</button>
                </div>
            </x-admin.form-section>
            <x-admin.form-section label="نقش های کاربر">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>عنوان پروژه</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['items'] as $k => $d)
                            <tr>
                                <td><h2>{{$loop->iteration}}-{{ $d }}</h2></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div >
                                        <table class="table table-info ">
                                            <thead>
                                            <tr>
                                                <th>عنوان نقش</th>
                                                <th>عملیات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($roles[$k] ?? [] as $r)
                                                <tr>
                                                    <td>{{ $r->role->label() }}</td>
                                                    <td><x-admin.delete-btn onclick="deleteRole('{{$r->id}}')"  /></td>
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
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteRole(id) {
            Swal.fire({
                title: 'حذف کردن',
                text: 'آیا از حذف کردن این نقش اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('deleteRole' , id)
                }
            })
        }
    </script>
@endpush
