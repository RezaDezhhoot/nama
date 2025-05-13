@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', 'درخواست ها')
    <x-admin.form-control :store="false" title="درخواست ها" exportable="exportXLSX"/>

    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.dropdown width="3" id="status" :data="$data['status']" label="وضعیت" wire:model.live="status"/>
                <x-admin.forms.select2
                    id="region"
                    :data="$region ?? []"
                    text="title"
                    label="منظقه"
                    width="3"
                    :ajaxUrl="route('admin.feed.regions')"
                    wire:model.defer="region"/>
                <x-admin.forms.select2
                    id="unit"
                    :data="[]"
                    text="title"
                    label="مرکز"
                    width="3"
                    :ajaxUrl="route('admin.feed.units',[0])"
                    wire:model.defer="unit"/>
                <x-admin.forms.select2
                    id="plan"
                    :data="[]"
                    text="title"
                    label="اکشن پلن"
                    width="3"
                    :ajaxUrl="route('admin.feed.plans',[$type])"
                    wire:model.defer="plan"/>

                <x-admin.forms.dropdown width="3" id="step" :data="$data['step']" label="نقش" wire:model.live="step"/>

            </div>
            @include('livewire.includes.advance-table')
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>پلن</th>
                            <th>کاربر</th>
                            <th>وضعیت</th>
                            <th>مرحله</th>
                            <th>مرکز</th>
                            <th>شهر/منطقه</th>

                            <th>هزینه پرداختی توسط آرمان(ثبت سیستمی)</th>
                            <th>هزینه پیشنهادی توسط معاونت اجرایی </th>
                            <th>هزینه نهایی تایید شده توسط معاونت طرح و برنامه</th>

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
                                <td>{{ $item->plan->title }}</td>
                                <td>
                                    <ul>
                                        <li>{{ $item->user->name }}</li>
                                        <li>{{ $item->user->phone }}</li>
                                        <li>{{ $item->user->national_id }}</li>
                                    </ul>
                                </td>
                                <td>{{ $item->status->label() }}</td>
                                <td>{{ $item->step->label2() }}</td>
                                <td>
                                    {{ $item->unit->title ?? "-" }}
                                </td>
                                <td>
                                    {{ $item->unit?->city?->title }} / {{ $item->unit?->region?->title }}
                                </td>
                                <td><strong>{{ number_format($item->total_amount) }} ریال </strong></td>
                                <td><strong>{{ number_format($item->offer_amount) }} ریال </strong></td>
                                <td><strong>{{ number_format($item->final_amount) }} ریال </strong></td>

                                <td>{{ persian_date($item->created_at) }}</td>
                                <td>{{ persian_date($item->updated_at) }}</td>
                                <td>{{ number_format($item->comments_count) }}</td>
                                <td>
                                    <x-admin.edit-btn target="_blank" href="{{ route('admin.requests.store',[$type,PageAction::UPDATE , $item->id]) }}"/>
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
