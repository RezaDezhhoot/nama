

<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading" />
    @section('title', __('pages.pages.dashboard'))
    @if(auth()->user()->hasAnyPermission('show_dashboard'))
        <div class="subheader py-2 py-lg-6 subheader-solid" >
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <h5 class="text-dark font-weight-bold my-1 mr-5">{{  __('pages.pages.dashboard') }}</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center pt-4 justify-content-start">
                    <p class="m-0">{{ __('general.from_date') }}</p>
                    <div class="d-flex align-items-center-center justify-content-between">
                        <x-admin.forms.jdate-picker direction="ltr" id="from_date" :timer="true" label="" wire:model.defer="from_date"/>
                    </div>
                    <p class="m-0">{{ __('general.to_date') }}</p>
                    <div class="d-flex align-items-center-center justify-content-between">
                        <x-admin.forms.jdate-picker direction="ltr" id="to_date" :timer="true" label="" wire:model.defer="to_date"/>
                    </div>
                    <div>
                        <button wire:loading.attr="disabled" class="btn btn-light-primary font-weight-bolder btn-sm" wire:click.prevent="runFilterableCharts">اعمال کردن</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom h-100 gutter-b example example-compact">
            <div class="card-header">
                <h3 class="card-title">{{  __('pages.pages.dashboard') }}</h3>
            </div>
            <div class="card-body row" >
                <div class="row w-100">
                    <div class="col-12">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">درخواست ها/گزارش ها</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="data" class="d-flex justify-content-center"></div>
                            </div>
                            <x-admin.big-loader :table="true" width="20" height="20" />
                        </div>
                    </div>
                </div>
                <div class="row w-100">
                    <div class="col-6 row">
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-mosque fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_MOSQUE_REQUESTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل درخواست های مساجد</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-university fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_UNIVERSITY_REQUESTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل درخواست های دانشگاه</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-school fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_SCHOOL_REQUESTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل درخواست های مدارس</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-ticket-alt fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_CENTER_REQUESTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل درخواست های مرکز تعالی</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-custom bg-dark bgi-no-repeat card-stretch gutter-b shadow" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="flaticon2-document fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['root'][\App\Enums\StatisticType::TOTAL_REQUESTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل درخواست ها</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-mosque fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_MOSQUE_REPORTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل گزارش های مساجد</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-university fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_UNIVERSITY_REPORTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل گزارش های دانشگاه</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-school fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_SCHOOL_REPORTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل گزارش های مدارس</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card card-custom bg-dark shadow bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="fas fa-ticket-alt fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['sub'][\App\Enums\StatisticType::TOTAL_CENTER_REPORTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل گزارش های مرکز تعالی</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-custom bg-dark bgi-no-repeat card-stretch gutter-b shadow" style="background-position: right top; background-size: 30% auto; background-image: url({{asset('admin/media/svg/shapes/abstract-1.svg')}})">
                                <div class="card-body">
                                    <span class="svg-icon svg-icon-white svg-icon-5x">
                                       <i class="flaticon2-document fa-4x text-white"></i>
                                    </span>
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ number_format($box['root'][\App\Enums\StatisticType::TOTAL_REPORTS->value] ?? 0) }}</span>
                                    <span class="font-weight-bold text-white font-size-sm">تعداد کل گزارش ها</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <script>
        const primary = '#6993FF';
        const success = '#1BC5BD';
        const info = '#8950FC';
        const warning = '#FFA800';
        const danger = '#F64E60';
        const dark = '#3F4254';
        document.addEventListener('livewire:init', () => {
            Livewire.on('DataChart' , function ([data]) {
                console.log(data)
                var KTApexChartsDemo = function () {
                    var _demo3 = function () {
                        const apexChart = "#data";
                        var options = {
                            series: [{
                                name: 'درخواست ها',
                                data: data.data['requests'],
                            },{
                                name: 'گزارش ها',
                                data: data.data['reports'],
                            }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                stacked: true,
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                },
                            },
                            dataLabels: {
                                enabled: true
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: data.labels,
                            },
                            yaxis: {
                                title: {
                                    text: ''
                                }
                            },
                            fill: {
                                opacity: 1
                            },
                            tooltip: {
                                x: {
                                    format: 'dd/MM/yy HH:mm'
                                },
                                y: {
                                    formatter: function (val) {
                                        return val.toLocaleString()
                                    }
                                }
                            },
                            colors: [warning, dark, warning]
                        };

                        var chart = new ApexCharts(document.querySelector(apexChart), options);
                        chart.render();
                    }
                    return {
                        // public functions
                        init: function () {
                            _demo3();
                        }
                    };
                }();

                jQuery(document).ready(function () {
                    KTApexChartsDemo.init();
                });
            })
        })
    </script>
@endpush
