@extends('layouts.admin')
@section('page'){{ __('repair-business.profile') }}
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('repair-business.profile') }}</div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label
                                class="col-md-4 col-form-label text-md-right p-1">{{ __('repair-business.input_name') }}:</label>
                            <div class="col-md-6">
                                <p class="p-1">{{ $user->name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password"
                                class="col-md-4 col-form-label text-md-right p-1">{{ __('repair-business.input_email') }}:</label>

                            <div class="col-md-6">
                                <p class="p-1">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm"
                                class="col-md-4 col-form-label text-md-right p-1">{{ __('repair-business.input_role') }}:</label>

                            <div class="col-md-6">
                                <p class="p-1">{{ $user->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-1">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('repair-business.password-update') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile-update-password') }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label for="current_password"
                                    class="col-md-4 col-form-label text-md-right">{{ __('repair-business.input_current_password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        name="current_password" required autocomplete="current_password">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-right">{{ __('repair-business.input_password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-right">{{ __('repair-business.input_confirm-password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password"
                                        class="form-control  @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ __('repair-business.button_save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
