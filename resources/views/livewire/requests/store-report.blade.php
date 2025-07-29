@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'گزارش'.(' '.$header ?? '') )
    <x-admin.form-control :store="false"  title="درخواست"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.nav-tabs-list>
            <x-admin.nav-tabs-item active="{{$tab =='report'}}" title="گزارش" key="tab" value="report" icon="far fa-newspaper"/>
            <x-admin.nav-tabs-item active="{{$tab =='comment'}}" title="گفتوگو" key="tab" value="comment" icon="far fa-comment"/>

        </x-admin.nav-tabs-list>
        <div class="card-body {{$tab != 'report' ? 'd-none' : ''}}">
            <x-admin.form-section label="جزئیات درخواست">
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>اکشن پلن</th>
                            <th>درخواست ستادی</th>
                            <th>درخواست طلایی</th>
                            <th>وضعیت</th>
                            <th>مرحله کار</th>
                            <th>مشخصات کاربر</th>
                            <th>تاریخ ثبت درخواست</th>
                            <th>تاریخ آخرین بروزرسانی</th>
                            <th>واحد</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $request->plan?->title }} #{{ $request->plan?->version?->value }} {{ $request->single_step ? 'درخواست تک مرحله ای' : '' }}</td>
                            <td>{{ $request?->staff ? 'بله' : 'خیر' }}</td>
                            <td>{{ $request?->golden ? 'بله' : 'خیر' }}</td>
                            <td><span class="alert alert-info">{{ $request->status->label() }}</span></td>
                            <td>{{ $request->step->label() }}</td>
                            <td>
                                <ul>
                                    <li>{{ $request->user?->name ?? "-" }}</li>
                                    <li>{{ $request->user?->phone }}</li>
                                    <li>{{ $request->user?->email ?? '-' }}</li>
                                    <li>{{ $request->user?->national_id  ?? '-' }}</li>
                                </ul>
                            </td>
                            <td>{{ persian_date($request->created_at) }}</td>
                            <td>{{ persian_date($request->updated_at) }}</td>
                            <td>
                                {{ $request->unit?->full ?? "-" }}
                                <hr>
                                {{ $request->unit?->parent?->full ?? "-" }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @if($request?->golden && sizeof($request->members) > 0)
                    <div class="col-12">
                        <h4>اعضای حلقه</h4>
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
                                <th>-</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($request->members as $member)
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
                                        @if( $member->trashed())
                                            <span class="badge badge-danger">حذف شده</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>تعداد دانش آموزان نوجوان</th>
                            <th>هزینه کلی عملیات</th>
                            <th>تاریخ برگزاری</th>
                            <th>فایل پیوست نامه امام جماعت</th>
                            <th>فایل نامه رابط منطقه</th>
                            <th>تصاویر پیوست شده</th>
                            <th>هزینه پرداختی توسط آرمان(ثبت سیستمی)</th>
                            <th>هزینه پیشنهادی مرحله اول توسط معاونت اجرایی مساجد</th>
                            <th>هزینه نهایی تایید شده مرحله اول توسط معاونت طرح و برنامه</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $request->students }}</td>
                            <td>{{ number_format($request->amount) }} ریال </td>
                            <td>{{ persian_date($request->date) }}</td>
                            <td>
                                @if($request->imamLetter)
                                    <div class="d-flex">
                                        <button wire:click="download({{ $request->imamLetter->id }})" class="btn btn-sm  btn-outline-success">بارگیری فایل</button>
                                        <a target="_blank" href="{{ $request->imamLetter->url }}" class="btn btn-sm  btn-outline-warning">مشاهده فایل</a>
                                    </div>
                                @endif
                                    @foreach($request->otherImamLetter ?? [] as $f)
                                        <hr>
                                        <div class=" d-flex">
                                            <button wire:click="download({{ $f->id }})" class="btn btn-sm  btn-outline-success">بارگیری فایل</button>
                                            <a target="_blank" href="{{ $f->url }}" class="btn btn-sm  btn-outline-warning">مشاهده فایل</a>
                                        </div>
                                    @endforeach
                            </td>
                            <td>
                                @if($request->areaInterfaceLetter)
                                    <div class="d-flex">
                                        <button wire:click="download({{ $request->areaInterfaceLetter->id }})" class="btn btn-sm btn-outline-success">بارگیری فایل</button>
                                        <a target="_blank" href="{{ $request->areaInterfaceLetter->url }}" class="btn btn-sm  btn-outline-warning">مشاهده فایل</a>
                                    </div>
                                @endif
                                @foreach($request->otherAreaInterfaceLetter ?? [] as $f)
                                    <hr>
                                    <div class=" d-flex">
                                        <button wire:click="download({{ $f->id }})" class="btn btn-sm  btn-outline-success">بارگیری فایل</button>
                                        <a target="_blank" href="{{ $f->url }}" class="btn btn-sm  btn-outline-warning">مشاهده فایل</a>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($request->images ?? [] as $f)
                                    <hr>
                                    <div class=" d-flex">
                                        <button wire:click="download({{ $f->id }})" class="btn btn-sm  btn-outline-success">بارگیری فایل</button>
                                        <a target="_blank" href="{{ $f->url }}" class="btn btn-sm  btn-outline-warning">مشاهده فایل</a>
                                    </div>
                                @endforeach
                            </td>
                            <td><strong>{{ number_format($request->total_amount) }} ریال </strong></td>
                            <td><strong>{{ number_format($request->offer_amount) }} ریال </strong></td>
                            <td><strong>{{ number_format($request->final_amount) }} ریال </strong></td>
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
            </x-admin.form-section>
            <x-admin.form-section label="جزئیات گزارش">
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>وضعیت</th>
                            <th>مرحله کار</th>
                            <th>تاریخ ثبت گزارش</th>
                            <th>تاریخ آخرین بروزرسانی</th>

                            <th>هزینه پیشنهادی مرحله دوم توسط معاونت اجرایی مساجد</th>
                            <th>هزینه نهایی تایید شده مرحله دوم توسط معاونت طرح و برنامه</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span class="alert alert-info">{{ $report->status->label() }}</span></td>
                            <td>{{ $report->step->label() }}</td>
                            <td>{{ persian_date($report->created_at) }}</td>
                            <td>{{ persian_date($report->updated_at) }}</td>

                            <td><strong>{{ number_format($report->offer_amount) }} ریال </strong></td>
                            <td><strong>{{ number_format($report->final_amount) }} ریال </strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>تعداد دانش آموزان نوجوان</th>
                            <th>تاریخ برگزاری</th>
                            <th>تصاویر</th>
                            <th>ویدیو</th>
                            <th>تصاویر بیشتر</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $report->students }}</td>
                            <td>{{ persian_date($report->date) }}</td>
                            <td>
                                @foreach($report->images as $image)
                                    <a target="_blank" href="{{ $image->url }}" class="btn btn-outline-success">
                                        مشاهده تصویر
                                        ({{ $loop->iteration }})
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @if($report->video)
                                    <a target="_blank"  href="{{ $report->video->url }}"  class="btn btn-outline-warning">مشاهده ویدیو</a>
                                @endif
                                @foreach($report->otherVideos ?? [] as $v)
                                        <a target="_blank"  href="{{ $v->url }}"  class="btn btn-outline-warning">مشاهده ویدیو</a>
                                @endforeach
                            </td>
                            <td>
                                @foreach($report->images2 ?? [] as $image)
                                    <a target="_blank" href="{{ $image->url }}" class="btn btn-outline-success">
                                        مشاهده تصویر
                                        ({{ $loop->iteration }})
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @if($report->status !== \App\Enums\RequestStatus::DONE)
                    <hr>
                    <div class="col-12">
                        <x-admin.forms.validation-errors/>
                        @if($report->step === \App\Enums\RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES)
                            <x-admin.forms.input type="number"  id="offer_amount" label="قیمت پیشنهادی توسط {{ \App\Enums\OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES->label() }} مرحله دوم(ریال)" wire:model.defer="offer_amount"/>
                        @elseif($report->step === \App\Enums\RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING)
                            @if($request->single_step)
                                <x-admin.forms.input help="پس از تایید صد درصد هزینه فوق برای درخواست کننده واریز خواهد شد" type="number"  :required="true" id="final_amount" label="قیمت نهایی تایید شده توسط {{ \App\Enums\OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->label() }} مرحله دوم(ریال)" wire:model.defer="final_amount"/>
                            @else
                                <x-admin.forms.input help="پس از تایید پنجاه درصد هزینه فوق برای درخواست کننده واریز خواهد شد" type="number"  :required="true" id="final_amount" label="قیمت نهایی تایید شده توسط {{ \App\Enums\OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->label() }} مرحله دوم(ریال)" wire:model.defer="final_amount"/>
                            @endif
                        @endif
                        <x-admin.forms.text-area dir="rtl" id="comment" :required="true" label="کامنت" wire:model.defer="comment"/>
                        <x-admin.forms.dropdown :data="$data['status']" :required="true" id="status" label="وضعیت درخواست" wire:model.live="status"/>
                        @if($status == \App\Enums\RequestStatus::REJECTED->value || $status == \App\Enums\RequestStatus::ACTION_NEEDED->value)
                            <x-admin.forms.text-area dir="rtl" id="message"  label="علت" wire:model.defer="message"/>
                        @endif
                        <x-admin.forms.dropdown :data="$data['step']" id="step" help="در صورت تایید درخواست به صورت خودکار وارد مراحل بعدی می شود" label="ارجاع به" wire:model.live="step"/>
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
                            @forelse($report->comments as $reply)
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
