@extends('layouts.admin')
@section('page')
    {{ __('repair-business.create-customer') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.create-customer') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('new-customer') }}" method="POST">
                    @csrf
                    <div class="form-group row m-0">
                        <label for="first_name" class="col-sm-2 col-form-label">{{ __('repair-business.first-name') }}</label>
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
                        <label for="last_name" class="col-sm-2 col-form-label">{{ __('repair-business.last-name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('last_name') is-invalid @enderror" id="last_name"
                                value="{{ old('last_name') }}" name="last_name" placeholder="">
                            @error('last_name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="phone" class="col-sm-2 col-form-label">{{ __('repair-business.phone') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                id="phone" value="{{ old('phone') }}" name="phone" placeholder="">
                            @error('phone')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="email" class="col-sm-2 col-form-label">{{ __('repair-business.email') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                id="email" value="{{ old('email') }}" name="email" placeholder="">
                            @error('email')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="address" class="col-sm-2 col-form-label">{{ __('repair-business.address') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('address') is-invalid @enderror" id="address"
                                value="{{ old('address') }}" name="address" placeholder="">
                            @error('address')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="city" class="col-sm-2 col-form-label">{{ __('repair-business.city') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('city') is-invalid @enderror"
                                id="city" value="{{ old('city') }}" name="city" placeholder="">
                            @error('city')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="state" class="col-sm-2 col-form-label">{{ __('repair-business.state') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('state') is-invalid @enderror"
                                id="state" value="{{ old('state') }}" name="state" placeholder="">
                            @error('state')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="zip" class="col-sm-2 col-form-label">{{ __('repair-business.zip') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('zip') is-invalid @enderror"
                                id="zip" value="{{ old('zip') }}" name="zip" placeholder="">
                            @error('zip')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="company" class="col-sm-2 col-form-label">{{ __('repair-business.company') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('company') is-invalid @enderror" id="company"
                                value="{{ old('company') }}" name="company" placeholder="">
                            @error('company')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary mt-3 mb-2 btn-sm">
                                <i class="fa fa-save"></i> {{ __('repair-business.create') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
