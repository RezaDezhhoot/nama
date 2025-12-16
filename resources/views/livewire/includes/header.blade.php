@use('App\Modules\Base\Enums\PageAction')
@use('App\Models\Request')
@use('App\Models\Report')
@use('App\Enums\UnitType')
<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <!--begin::Header Nav-->
                <ul class="menu-nav">
                    @if(isAdmin() || isOperator())
                        <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-text">درخواست ها / گزارش ها</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                <ul class="menu-subnav">
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::MOSQUE)" :report_counter="Report::counter(UnitType::MOSQUE)" icon="fas fa-mosque" :name="UnitType::MOSQUE->value" :title="UnitType::MOSQUE->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::SCHOOL)" :report_counter="Report::counter(UnitType::SCHOOL)" icon="fas fa-school" :name="UnitType::SCHOOL->value" :title="UnitType::SCHOOL->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::UNIVERSITY)" :report_counter="Report::counter(UnitType::UNIVERSITY)" icon="fas fa-university" :name="UnitType::UNIVERSITY->value" :title="UnitType::UNIVERSITY->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::CENTER)" :report_counter="Report::counter(UnitType::CENTER)" icon="fas fa-ticket-alt" :name="UnitType::CENTER->value" :title="UnitType::CENTER->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::GARDEN)" :report_counter="Report::counter(UnitType::GARDEN)" icon="fas fa-tree" :name="UnitType::GARDEN->value" :title="UnitType::GARDEN->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::HALL)" :report_counter="Report::counter(UnitType::HALL)" icon="fas fa-city" :name="UnitType::HALL->value" :title="UnitType::HALL->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::STADIUM)" :report_counter="Report::counter(UnitType::STADIUM)" icon="fas fa-drum-steelpan" :name="UnitType::STADIUM->value" :title="UnitType::STADIUM->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::DARUL_QURAN)" :report_counter="Report::counter(UnitType::DARUL_QURAN)" icon="fas fa-quran" :name="UnitType::DARUL_QURAN->value" :title="UnitType::DARUL_QURAN->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::CULTURAL_INSTITUTE)" :report_counter="Report::counter(UnitType::CULTURAL_INSTITUTE)" icon="fas fa-dungeon" :name="UnitType::CULTURAL_INSTITUTE->value" :title="UnitType::CULTURAL_INSTITUTE->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::SEMINARY)" :report_counter="Report::counter(UnitType::SEMINARY)" icon="fas fa-kaaba" :name="UnitType::SEMINARY->value" :title="UnitType::SEMINARY->label()" />
                                    <x-admin.menu-item-header :request_counter="Request::counter(UnitType::QURANIC_CENTER)" :report_counter="Report::counter(UnitType::QURANIC_CENTER)" icon="fas fa-quran" :name="UnitType::QURANIC_CENTER->value" :title="UnitType::QURANIC_CENTER->label()" />
                                </ul>
                            </div>
                        </li>
                    @endif
                </ul>
                <!--end::Header Nav-->
            </div>
        </div>
        <div class="topbar">
            <div class="topbar-item">
                <div class="btn jdate btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                    <span class="text-dark-50 jdate font-weight-bolder font-size-base d-none d-md-inline mr-3">{{auth()->user()?->name}} - {{ persian_date(now()) }}</span>
                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
        <h3 class="font-weight-bold m-0">پروفایل
            <small class="text-muted font-size-sm ml-2"></small></h3>
        <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
            <i class="ki ki-close icon-xs text-muted"></i>
        </a>
    </div>
    <div class="offcanvas-content pr-5 mr-n5">
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                <div class="symbol-label rounded-circle" style="background-image:url('{{asset(auth()->user()->avatar?->url ?? null )}}')"></div>
                <i class="symbol-badge bg-success"></i>
            </div>
            <div class="d-flex flex-column">
                <a href="" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">
                    {{ auth()->user()?->name ?? '-' }} #{{ auth()->id() }}
                </a>
                <hr>
                <small  class="font-weight-bold  text-dark-75 text-hover-primary">
                    نقش در سامانه نما :
                    {{ auth()->user()->nama_role?->label() ?? "-" }}
                </small>
                <small  class="font-weight-bold  text-dark-75 text-hover-primary">
                    نقش در سامانه آرمان :
                    {{ auth()->user()->role?->label() ?? "-" }}
                </small>
                <div class="navi mt-2">
                    <a class="navi-item">
                        <span class="navi-text text-muted text-hover-primary">{{ auth()->user()->phone ?? '-' }}</span>
                    </a>
                    <a href="{{ route('admin.auth.logout') }}" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">خروج</a>
                </div>
            </div>
        </div>
        <div class="separator separator-dashed mt-8 mb-5"></div>
    </div>
</div>
