<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ __('pages.file-manager.file-manager') }}</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/admin/lfm/css/bootstrap.min.css"  >
    <link rel="stylesheet" href="/admin/lfm/css/all.css" >
    <link rel="stylesheet" href="{{asset('lfm/css/file-manager.css')}}">

    <!-- Styles -->
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="fm-main-block">
            <div id="fm"></div>
        </div>
    </div>
</div>

<!-- File manager -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // set fm height
        document.getElementById('fm-main-block').setAttribute('style', 'height:' + window.innerHeight + 'px');

        // Add callback to file manager
        fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
            window.opener.getURL(fileUrl);
            window.close();
        });
    });
</script>
<script src="{{asset('lfm/js/file-manager.js')}}"></script>

</body>
</html>

