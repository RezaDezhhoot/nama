@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader  :loading="$loading"/>
    @section('title', 'گزارش گیر'.(' '.$header ?? '') )
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" :store="false"  title="گزارش گیر"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <div class="card-body ">
            <x-admin.form-section label="جزئیات گزارش">
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>وضعیت</th>
                            <th>فرم</th>
                            <th>کاربر</th>
                            <th>تاریخ ثبت درخواست</th>
                            <th>تاریخ آخرین بروزرسانی</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span class="badge badge-info">{{ $report->status->label() }}</span></td>
                            <td>{{ $report->form->title }}</td>
                            <td>
                                <ul>
                                    <li>{{ $report->user?->name ?? "-" }}</li>
                                    <li>{{ $report->user?->phone }}</li>
                                    <li>{{ $report->user?->email ?? '-' }}</li>
                                    <li>{{ $report->user?->national_id  ?? '-' }}</li>
                                </ul>
                            </td>
                            <td>{{ persian_date($report->created_at) }}</td>
                            <td>{{ persian_date($report->updated_at) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        @foreach($report->reports as $r)
                            <tr>
                                <td>{{$r['form']['title']}}</td>
                                <td>
                                    @if(is_array($r['value']))
                                        @foreach($r['value'] as $v)
                                            <span class="badge badge-success">{{ $v }}</span>
                                        @endforeach
                                    @elseif($r['form']['type'] == \App\Enums\FormItemType::FILE->value)
                                        <a target="_blank" class="btn btn-outline-danger" href="{{ asset($r['value']) }}">مشاهده فایل</a>
                                    @else
                                        {{$r['value']}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-12">
                    <x-admin.forms.validation-errors/>
                    <x-admin.forms.text-area dir="rtl" id="message"  label="کامنت" wire:model.defer="message"/>
                    <x-admin.forms.dropdown :data="$data['status']" :required="true" id="status" label="وضعیت درخواست" wire:model.live="status"/>
                    <button class="btn btn-outline-primary" wire:click="store">دخیره تغییرات </button>
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
