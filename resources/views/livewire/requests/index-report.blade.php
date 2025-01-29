@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'گزارش ها')
    <x-admin.form-control :store="false" title="گزارش ها"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown  id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>کاربر</th>
                            <th>وضعیت</th>
                            <th>مرحله</th>
                            <th>تاریخ ارسال</th>
                            <th>تاریخ اخرین بروزرسانی</th>
                            <th>تعداد گفتوگو</th>
                            <th>اقدامات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <ul>
                                        <li>{{ $item->request->user->name }}</li>
                                        <li>{{ $item->request->user->phone }}</li>
                                        <li>{{ $item->request->user->national_id }}</li>
                                    </ul>
                                </td>
                                <td>{{ $item->status->label() }}</td>
                                <td>{{ $item->step->label() }}</td>
                                <td>{{ persian_date($item->created_at) }}</td>
                                <td>{{ persian_date($item->updated_at) }}</td>
                                <td>{{ number_format($item->comments_count) }}</td>
                                <td>
                                    <x-admin.edit-btn target="_blank" href="{{ route('admin.reports.store',[PageAction::UPDATE , $item->id]) }}"/>
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
            @if(sizeof($items) > 0)
                {{$items->links('livewire.layouts.paginate')}}
            @endif
        </div>
    </div>
</div>
