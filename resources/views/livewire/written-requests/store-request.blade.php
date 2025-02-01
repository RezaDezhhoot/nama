@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'درخواست های مکتوب'.(' '.$header ?? '') )
    <x-admin.form-control :store="false"  title="درخواست های مکتوب"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.nav-tabs-list>
            <x-admin.nav-tabs-item active="{{$tab =='request'}}" title="درخواست" key="tab" value="request" icon="far fa-newspaper"/>
            <x-admin.nav-tabs-item active="{{$tab =='comment'}}" title="گفتوگو" key="tab" value="comment" icon="far fa-comment"/>

        </x-admin.nav-tabs-list>
        <div class="card-body {{$tab != 'request' ? 'd-none' : ''}}">
            <x-admin.form-section label="جزئیات درخواست">
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>عنوان درخواست</th>
                            <th>وضعیت</th>
                            <th>مرحله کار</th>
                            <th>مشخصات کاربر</th>
                            <th>تاریخ ثبت درخواست</th>
                            <th>تاریخ آخرین بروزرسانی</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $request->title }}</td>
                            <td><span class="alert alert-info">{{ $request->status->label() }}</span></td>
                            <td>{{ $request->step->label() }}</td>
                            <td>
                                <ul>
                                    <li>{{ $request->user->name ?? "-" }}</li>
                                    <li>{{ $request->user->phone }}</li>
                                    <li>{{ $request->user->email ?? '-' }}</li>
                                    <li>{{ $request->user->national_id  ?? '-' }}</li>
                                </ul>
                            </td>
                            <td>{{ persian_date($request->created_at) }}</td>
                            <td>{{ persian_date($request->updated_at) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>فایل پیوست نامه </th>
                            <th>فایل امضا با عکس </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><button wire:click="download({{ $request->letter->id }})" class="btn btn-outline-success">بارگیری فایل</button></td>
                            <td><button wire:click="download({{ $request->sign->id }})" class="btn btn-outline-success">بارگیری فایل</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>توضیحات تکمیلی</th>
                            <th>آخرین پیام ثبت شده از وضعیت درخواست</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $request->body ?? '-' }}</td>
                            <td>{{ $request->message ?? '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @if(! in_array($request->status , [\App\Enums\RequestStatus::DONE,\App\Enums\RequestStatus::REJECTED]))
                    <hr>
                    <div class="col-12">
                        <x-admin.forms.validation-errors/>

                        @if($request->step === \App\Enums\WrittenRequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES)
                        @elseif($request->step === \App\Enums\WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING)
                            <x-admin.forms.checkbox  id="countable" label="جز درخواست های کاربر شمرده شود" wire:model.defer="countable"/>
                        @endif
                        <x-admin.forms.text-area dir="rtl" id="comment" :required="true" label="کامنت" wire:model.defer="comment"/>
                        <x-admin.forms.dropdown :data="$data['status']" :required="true" id="status" label="وضعیت درخواست" wire:model.live="status"/>
                        @if($status == \App\Enums\RequestStatus::REJECTED->value || $status == \App\Enums\RequestStatus::ACTION_NEEDED->value)
                            <x-admin.forms.text-area dir="rtl" id="message"  label="علت" wire:model.defer="message"/>
                        @endif
                        <button class="btn btn-outline-primary" onclick="store()">دخیره تغییرات </button>
                    </div>
                @endif

            </x-admin.form-section>
        </div>
        <div class="card-body {{$tab != 'comment' ? 'd-none' : ''}}">
            <x-admin.form-section label="تاریخچه کامنت ها">
                <div wire:ignore.self class="card p-3 w-100 card-custom">
                    <div wire:ignore.self class="card-body">
                        <div wire:ignore.self id="scroll-pull" class="scroll scroll-pull" data-height="375" data-mobile-height="300">
                            @forelse($request->comments as $reply)
                                <div class="messages infinite-scroll">
                                    <div class="d-flex flex-column mb-5 {{ $reply->user_id == auth()->id() ? " align-items-start" : " align-items-end" }}">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <a class="text-dark-75 text-hover-primary font-weight-bold font-size-h6"><strong>{{$reply->display_name}} :</strong> {{ $reply->user->name }}  </a>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-35 mr-3">
                                                <img alt="avatar" src="{{ asset($reply->user->avatar ?? null) }}" />
                                            </div>
                                            <div>
                                                <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-100%">
                                                    {!! $reply->body !!}
                                                    <br>
                                                    <span class="text-muted font-size-sm">
                                                     {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            @empty
                                <p class="text-center">
                                    پیامی ارسال نشده است
                                </p>
                            @endforelse

                        </div>
                    </div>
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function store() {
            Swal.fire({
                title: 'دخیره سازی',
                text: 'آیا از تعییرات این درخواست اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('store')
                }
            })
        }
    </script>
@endpush
