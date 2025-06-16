@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'حلقه ها')
    <x-admin.form-control exportable="export" :store="false" title="حلقه ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown id="type" :data="$data['type']" label="نوع" wire:model.live="type"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>نوع حلقه</th>
                            <th>عنوان حلقه</th>
                            <th>مشخصات سازنده</th>
                            <th>نام مربی</th>
                            <th>کد ملی مربی</th>
                            <th>تاریخ تولد </th>
                            <th>کد پستی </th>
                            <th>تعداد اعضا</th>
                            <th>تاریخ ثبت</th>
                            <th>تاریخ آخرین بروزرسانی</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody class="border">
                        @foreach($items as $key => $item)
                            <tr data-toggle="collapse" data-target="#row{{$key}}" class="accordion-toggle cursor-pointer">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->item?->type?->label() ?? '-' }}</td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="d-block">نام:<a target="_blank" href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->owner->id]) }}"> {{ $item->owner->name ?? '-' }}</a></span>
                                    <span class="d-block">شماره:<a target="_blank" href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->owner->id]) }}"> {{ $item->owner->phone ?? '-' }}</a></span>
                                    <span class="d-block">کد ملی:<a target="_blank" href="{{ route('admin.users.roles.store',[PageAction::UPDATE , $item->owner->id]) }}"> {{ $item->owner->national_id ?? '-' }}</a></span>
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->national_code }}</td>
                                <td>{{ persian_date($item->birthdate) }}</td>
                                <td>{{ $item->postal_code }}</td>
                                <td><strong>{{ number_format($item->members_count) }}</strong> <sub class="text-info">(برای مشاهده اعضا کلیک کنید)</sub></td>
                                <td>
                                    {{ persian_date($item->created_at) }}
                                </td>
                                <td>
                                    {{ persian_date($item->updated_at) }}
                                </td>
                                <td >
                                    @if($item->trashed())
                                        <span class="badge badge-danger">حذف شده</span>
                                        <button wire:click="restore({{$item->id}})" class="btn btn-sm btn-outline-primary">بازنشانی</button>
                                    @elseif(! $item->role)
                                        <span class="badge badge-warning">مربی حذف شده</span>
                                    @else
                                        <x-admin.delete-btn onclick="deleteItem('{{$item->id}}')"  />
                                    @endif
                                </td>
                            </tr>
                            <tr >
                                <td wire:ignore.self colspan="100" class="hiddenRow">
                                    <div  wire:ignore.self class="accordian-body collapse" id="row{{$key}}">
                                        <h4>سایر اطلاعات حلقه</h4>
                                        <table class="table table-bordered table-info table-striped">
                                            <thead>
                                            <tr>
                                                <th>آدرس </th>
                                                <th>شماره تلفن </th>
                                                <th>سطح تحصیلات </th>
                                                <th>رشته تحصیلی </th>
                                                <th>حوزه عملکردی</th>
                                                <th>حوزه مهارتی</th>
                                                <th>توضیحات</th>
                                                <th>شغل</th>
                                                <th>شماره شبا</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>{{ $item->address }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->level_of_education }}</td>
                                                <td>{{ $item->field_of_study }}</td>
                                                <td>
                                                    @foreach($item->functional_area as $f)
                                                        <span class="badge badge-primary">{{ $f }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach($item->skill_area as $s)
                                                        <span class="badge badge-warning">{{ $s }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->job }}</td>
                                                <td>{{ $item->sheba_number }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <h4>اعضا</h4>
                                        <table class="table table-bordered table-info table-striped">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>شناسه</th>
                                                <th>نام عضو</th>
                                                <th>کد ملی عضو</th>
                                                <th>تاریخ تولد </th>
                                                <th>کد پستی </th>
                                                <th>آدرس </th>
                                                <th>شماره تلفن </th>
                                                <th>نام پدر </th>
                                                <th>تاریخ ثبت</th>
                                                <th>تاریخ آخرین بروزرسانی</th>
                                                <th>اقدامات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($item->members as $member)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $member->id }}</td>
                                                    <td>{{ $member->name }}</td>
                                                    <td>{{ $member->national_code }}</td>
                                                    <td>{{ persian_date($member->birthdate) }}</td>
                                                    <td>{{ $member->postal_code }}</td>
                                                    <td>{{ $member->address }}</td>
                                                    <td>{{ $member->phone }}</td>
                                                    <td>{{ $member->father_name }}</td>
                                                    <td>
                                                        {{ persian_date($member->created_at) }}
                                                    </td>
                                                    <td>
                                                        {{ persian_date($member->updated_at) }}
                                                    </td>
                                                    <td >
                                                        @if(! $member->trashed())
                                                            <x-admin.delete-btn onclick="deleteMember('{{$item->id}}')"  />
                                                        @else
                                                            <span class="badge badge-danger">حذف شده</span>
                                                            <button wire:click="restoreMember({{$member->id}})" class="btn btn-sm btn-outline-primary">بازنشانی</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if(sizeof($items) == 0)
                            <td class="text-center" colspan="100">
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
        function deleteMember(id) {
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
                @this.call('deleteMember' , id)
                }
            })
        }
    </script>
@endpush
