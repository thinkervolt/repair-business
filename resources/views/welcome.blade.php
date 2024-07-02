<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('vendor/fontawesome-free/favicon.ico') }}">

    <style>
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            src: url({{ asset('/vendor/google-fonts/nunito/Nunito-Regular.ttf')}}) format('truetype');
        }
    </style>
    <!-- Styles -->
    <style>
        html,
        body {

            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        body {
            background-image: linear-gradient(rgba(0, 0, 0, .1), rgba(0, 0, 0, .3)), url("{{ asset('vendor/thinkervolt/computer-repair-signed.jpg') }}");
            background-color: #fff;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 50px;
            text-transform: uppercase;
            font-weight: bold;
            color: #f0f0f0;

            text-shadow: 1px 1px black;

        }

        .links>a {
            color: #f0f0f0;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="top-right links">
            <a href="{{ route('customer-signup') }}">Customer Sign-up</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            @endif

        </div>


        <div class="content">
            <div class=" m-b-md ">
                <p class="title">{{ config('app.name', 'REPAIR-HERO') }}</p>
                <p style="font-size:15px;color:black">Powered by <a href="https://thinkervolt.com"
                        style="font-weight: bold; font-family:arial; color:black;text-decoration:none;">thinkervolt<a>
                </p>

            </div>
        </div>
    </div>
</body>

</html>
