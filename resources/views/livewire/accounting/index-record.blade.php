@use('App\Enums\PageAction')
<div wire:init="init">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.index',['item' => 'حسابداری']) )
    <x-admin.form-control :exportable="$batchModel ? 'export' : false" :store="false" title="حسابداری"/>
    <div class="card card-custom ">
        <div class="card-body">
            <div class="row">
                <x-admin.forms.select2
                    id="batch"
                    :data="[]"
                    text="batch"
                    label="دسته"
                    :ajaxUrl="route('admin.feed.batches',[$type , $subType])"
                    wire:model.defer="batch"/>

                <x-admin.forms.dropdown  id="type" :data="$data['type']" label="پروژه" wire:model.live="type"/>
            </div>

            <x-admin.nav-tabs-list>
                @foreach(\App\Enums\UnitType::subTypes($type) as $st)
                    <x-admin.nav-tabs-item :active="$subType === $st->value" :title="$st->label()" key="subType" :value="$st->value" icon="flaticon-list"/>
                @endforeach
            </x-admin.nav-tabs-list>
            <div class="row">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            @if($batchModel)
                                @foreach($batchModel->plans['records'] as $r)
                                    <th class="bg-danger text-white">عنوان برنامه</th>
                                    <th class="bg-primary text-white">مبلغ کل{{ number_format($r['totalFinalAmount']) }} </th>
                                    <th class="bg-warning">{{ number_format($r['count']) }} برنامه</th>
                                    <th class="bg-success">{{ number_format($r['students']) }} نفر</th>
                                @endforeach
                            @endif
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>شبا</th>
                            <th>واحد حقوفی</th>
                            <th>منطقه</th>
                            <th>توضیحات</th>
                            <th>تاریخ</th>
                            @if($batchModel)
                                @foreach($batchModel->plans['records'] as $r)
                                    <th style="min-width: 70px" class="bg-danger text-white">{{ $r['plan'] }}</th>
                                    <th class="bg-primary text-white">مبلغ کل </th>
                                    <th class="bg-warning"> برنامه</th>
                                    <th class="bg-success"> نفرات</th>
                                @endforeach
                            @endif
                            <th>تعداد درخواست و گزارش</th>
                            <th>نفرات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->sheba ?? '-' }}</td>
                                <td>{{ $item->unit?->title ?? '-' }}</td>
                                <td>{{ $item->region?->title ?? '-' }}</td>
                                <td>{{ $item->type->label() }}</td>
                                <td>{{ persian_date($item->created_at, "%A, %d %B %Y H:i:s") }}</td>
                                @foreach($item->records['records'] as $key => $record)
                                    <td class="bg-danger text-white">{{ $record['plan'] }}</td>
                                    <td style="min-width: 70px" class="bg-primary text-white">{{ number_format($record['totalFinalAmount']) }}</td>
                                    <td class="bg-warning">{{ number_format($record['count']) }}</td>
                                    <td class="bg-success">{{ number_format($record['students']) }}</td>
                                @endforeach
                                <td>{{ number_format($item->requests_and_reports) }}</td>
                                <td>{{ number_format($item->students) }}</td>
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
