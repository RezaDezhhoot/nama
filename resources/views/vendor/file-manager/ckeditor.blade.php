<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>مدیریت فایل</title>
    <!-- CSRF Token -->
{{--    <link rel="icon" sizes="16x16" href="{{asset($logo)}}" />--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"  >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" >
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

    // Helper function to get parameters from the query string.
    function getUrlParam(paramName) {
      const reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
      const match = window.location.search.match(reParam);

      return (match && match.length > 1) ? match[1] : null;
    }

    // Add callback to file manager
    fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
      const funcNum = getUrlParam('CKEditorFuncNum');

      window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
      window.close();
    });
  });
</script>
<script src="{{asset('lfm/js/file-manager.js')}}"></script>

</body>
</html>

