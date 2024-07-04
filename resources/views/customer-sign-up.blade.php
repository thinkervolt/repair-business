<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ asset('vendor/fontawesome-free/favicon.ico') }}">
    <title>{{ config('app.name', 'Laravel') }} - {{ __('repair-business.customer-sign-up') }}</title>

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

</head>
<body>
    <div class="container mt-3">

        @if (session()->has('error'))
            <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                <li>{{ session()->get('error') }}</li>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 mt-3 text-gray-800"><a href="/"> {{ __('repair-business.home') }}</a> - {{ __('repair-business.customer-sign-up') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('public-new-customer') }}" method="POST">
                    <div class="form-group row m-0">
                        <label for="first_name" class="col-sm-2 col-form-label">{{ __('repair-business.input_first-name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                id="first_name" value="{{ old('first_name') }}" name="first_name" placeholder="">
                            @error('first_name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="last_name" class="col-sm-2 col-form-label">{{ __('repair-business.input_last-name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                id="last_name" value="{{ old('last_name') }}" name="last_name" placeholder="">
                            @error('last_name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="phone" class="col-sm-2 col-form-label">{{ __('repair-business.input_phone') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone"
                                value="{{ old('phone') }}" name="phone" placeholder="">
                            @error('phone')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>



                    <div class="form-group row m-0">
                        <label for="email" class="col-sm-2 col-form-label">{{ __('repair-business.input_email') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('email') is-invalid @enderror" id="email"
                                value="{{ old('email') }}" name="email" placeholder="">
                            @error('email')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="address" class="col-sm-2 col-form-label">{{ __('repair-business.input_address') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('address') is-invalid @enderror"
                                id="address" value="{{ old('address') }}" name="address" placeholder="">
                            @error('address')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="city" class="col-sm-2 col-form-label">{{ __('repair-business.input_city') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('city') is-invalid @enderror" id="city"
                                value="{{ old('city') }}" name="city" placeholder="">
                            @error('city')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="state" class="col-sm-2 col-form-label">{{ __('repair-business.input_state') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('state') is-invalid @enderror"
                                id="state" value="{{ old('state') }}" name="state" placeholder="">
                            @error('state')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="zip" class="col-sm-2 col-form-label">{{ __('repair-business.input_zip') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('zip') is-invalid @enderror" id="zip"
                                value="{{ old('zip') }}" name="zip" placeholder="">
                            @error('zip')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0 mt-2">
                        <label for="zip" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <p class="text-uppercase">{{ $company_profile->name }} {{ __('repair-business.terms-and-conditions') }}</p>
                            <p class="small">{{ $company_profile->terms }}</p>

                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="zip" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">

                            <h4 class="text-uppercase font-weight-bold text-dark  my-2">
                                {{ __('repair-business.terms-and-conditions-message') }}
                                </h4>

                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn  btn-primary mt-3 mb-2 ">{{ __('repair-business.button_sign-up') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
