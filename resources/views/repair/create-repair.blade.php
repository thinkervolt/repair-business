@extends('layouts.admin')
@section('page')
    {{ __('repair-business.create-repair') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.create-repair') }}</h1>
        </div>
        <div class="row mt-2 justify-content-center">
            <div class="col-md-8">
                @if (isset($customer))
                    <div class="card shadow py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        {{ __('repair-business.customer') }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customer->first_name }}
                                        {{ $customer->last_name }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $customer->phone) }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customer->email }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customer->address }}
                                        {{ $customer->city }} {{ $customer->state }} {{ $customer->zip }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customer->company }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mt-2 justify-content-center">
            <div class="col-md-8 mb-2">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">{{ __('repair-business.repair') }}
                                </div>
                                <form action="{{ route('new-repair', $customer) }}" method="POST">
                                    @csrf
                                    <div class="form-group row m-0">
                                        <label for="target" class="col-sm-4 col-form-label">{{ __('repair-business.input_repair-target') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text"
                                                class="form-control form-control-sm @error('target') is-invalid @enderror"
                                                id="target" value="{{ old('target') }}" name="target" placeholder="">
                                            @error('target')
                                                <span class="invalid-feedback mb-1" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row m-0">
                                        <label for="data_request" class="col-sm-4 col-form-label">{{ __('repair-business.input_repair-request') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text"
                                                class="form-control form-control-sm @error('data_request') is-invalid @enderror"
                                                id="data_request" value="{{ old('data_request') }}" name="data_request"
                                                placeholder="">
                                            @error('data_request')
                                                <span class="invalid-feedback mb-1" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row m-0">
                                        <div class="col-sm-4"> </div>
                                        <div class="col-sm-8">
                                            <button type="submit" class="btn btn-primary mt-2 mb-2 btn-sm"><i
                                                    class="fa fa-save"></i> {{ __('repair-business.button_create') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
 
@endsection
