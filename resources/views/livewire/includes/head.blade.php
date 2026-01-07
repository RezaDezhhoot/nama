<head>
    <meta charset="utf-8" />
    <title> @yield('title') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex,follow">
    <link rel="icon" type="image/x-icon" href="{{ asset(getSetting('logo')) }}">
    <link rel="stylesheet" href="/admin/lib/all.css" >
    <link href="{{asset('admin/plugins/global/plugins.bundle.css?v=7.0.6')}}" rel="stylesheet" type="text/css"/>
    <link href="/admin/css/style.bundle.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="/admin/css/themes/layout/header/base/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="/admin/css/themes/layout/header/menu/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="/admin/css/themes/layout/aside/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="{{asset('admin/plugins/custom/datepicker/persian-datepicker.min.css')}}"/>

{{--    <link rel="stylesheet" href="/admin/persianDatepicker-master/css/persianDatepicker-default.css" />--}}

    <script src="/admin/lib/select2.min.js" defer></script>
{{--    <script src="{{asset('admin/js/jquery.min.js')}}" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('admin/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    @stack('head')
    <link rel="stylesheet" href="/admin/lib/ckeditor5.css" />
    <link rel="stylesheet" href="/admin/lib/ckeditor5-premium-features.css" />
    <script src="/admin/lib/ckeditor.js"></script>
    <script src="/admin/lib/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI -->
    <link rel="stylesheet" href="/admin/lib/jquery-ui.css">
    <script src="/admin/lib/jquery-ui.min.js"></script>

</head>
