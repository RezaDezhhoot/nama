<head>
    <meta charset="utf-8" />
    <title> @yield('title') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex,follow">
    <link rel="icon" type="image/x-icon" href="{{ asset(getSetting('logo')) }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" >
    <link href="{{asset('admin/plugins/global/plugins.bundle.css?v=7.0.6')}}" rel="stylesheet" type="text/css"/>
    <link href="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/css/style.bundle.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/css/themes/layout/header/base/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/css/themes/layout/header/menu/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>
    <link href="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/css/themes/layout/aside/dark.rtl.css?v=7.0.6" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="{{asset('admin/plugins/custom/datepicker/persian-datepicker.min.css')}}"/>

    <link rel="stylesheet" href="/admin/persianDatepicker-master/css/persianDatepicker-default.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script src="{{asset('admin/js/jquery.min.js')}}" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('admin/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    @stack('head')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.css" />
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.1/ckeditor5-premium-features.css" />
    <script src="//cdn.ckeditor.com/4.22.0/full/ckeditor.js"></script>

</head>
