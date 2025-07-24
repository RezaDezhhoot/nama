<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <meta charset="utf-8" />

    <title>نامه PDF</title>
    <style>
        @font-face {
            font-family: 'Vazir';
            src: url("{{ public_path('admin/fonts2/ttf/Vazirmatn-Regular.ttf') }}") format('truetype');
        }

        body {
            font-family: 'XB Riyaz', serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 2;
            color: #000;
        }

        .card {
            width: 100%;
            padding: 0;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 20mm;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo {
            max-width: 40mm;
            margin-bottom: 5mm;
        }

        .title {
            font-weight: bold;
            color: #a28028;
            font-size: 18px;
        }

        .content {
            margin-top: 10mm;
            padding: 0 10mm;
        }

        .content p {
            margin: 4mm 0;
        }

        .signature {
            margin-top: 30mm;
            padding: 0 10mm;
            text-align: left;
        }

        .signature img {
            height: 20mm;
        }

        .sign-name {
            display: block;
            margin-top: 3mm;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
       <div class="logo-container">
           <img src="https://tasks.armaniran.app/Images/masajed/maktob/logo1.svg" class="logo" alt="لوگو">
           <img src="https://tasks.armaniran.app/Images/masajed/maktob/logo2.svg" class="logo" alt="لوگو">
       </div>
    </div>

    <div class="content">
        <p>به نام خدا</p>
        <p><strong>عنوان:</strong> {{ $r->title }}</p>
        <p><strong>ارجاع:</strong> {{ $r->step->label()  }}</p>
        {!! $r->body !!}
    </div>

    <div class="signature">
        @if($r->sign)
            <img src="{{ asset($r->sign->url) }}" alt="">
        @endif
        <span class="sign-name">{{ $r->user->name }}</span>
    </div>
    <hr>
    {{ persian_date($r->created_at) }}
</div>
</body>
</html>
