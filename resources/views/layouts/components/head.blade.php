<meta name="csrf-token" content="{{ csrf_token() }}">

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="{{ asset('vendor/fontawesome-free/favicon.ico') }}">

<title>{{ config('app.name', 'REPAIR-BUSINESS') }} - @yield('page')</title>

<link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
<style>
    @font-face {
        font-family: 'Nunito';
        font-style: normal;
        font-weight: 400;
        src: url({{ asset('/vendor/google-fonts/nunito/Nunito-Regular.ttf')}}) format('truetype');
    }
</style>
<link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
