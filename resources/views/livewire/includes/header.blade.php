@use('App\Modules\Base\Enums\PageAction')
<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
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
