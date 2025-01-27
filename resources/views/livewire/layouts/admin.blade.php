<!DOCTYPE html>
<html lang="{{str_replace('_', '-', app()->getLocale())}}" dir="rtl" xmlns:livewire="">
@include('livewire.includes.head')
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
@yield('body')
<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
    <div class="d-flex align-items-center">
        <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
            <span></span>
        </button>

    </div>
</div>
<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-row flex-column-fluid page">
        <livewire:sidebar />
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            @include('livewire.includes.header')
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="d-flex flex-column-fluid">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('livewire.includes.foot')
</body>
</html>
