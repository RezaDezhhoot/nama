<!DOCTYPE html>
<html lang="en" dir="rtl">
<!--begin::Head-->
<head><base href="../../../../">
    <meta charset="utf-8" />
    <title> @yield('title') </title>
    <meta name="description" content="{{__('pages.auth.login')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex,follow">
    <link href="{{asset('admin/css/pages/login/classic/login-2.rtl.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/css/style.bundle.rtl.css?v=7.2.9" rel="stylesheet" type="text/css"/>
    <link href="{{asset('admin/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    @livewireStyles
</head>
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
<div class="d-flex flex-column flex-root">
    <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
        <div class="login-aside col-md-5 col-12 d-flex flex-column flex-row-auto" style="background-color: #b9e6fb;">
            <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url({{asset('admin/media/login.png')}});background-size: contain;background-position: bottom"></div>
        </div>
        @yield('content')
    </div>
</div>
@livewireScripts
@stack('scripts')
</body>
<!--end::Body-->
</html>
