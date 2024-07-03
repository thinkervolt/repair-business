@extends('layouts.admin')
@section('page')
    {{ __('repair-business.payments') }}
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('search-payment') }}">
        @csrf


        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search" name="search"
                value="@if (isset($search)) {{ $search }} @endif" aria-label="Search"
                aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
@endsection

@section('mobile-search-buttom')
    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-search fa-fw"></i>
    </a>
@endsection

@section('mobile-search')
    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{ route('search-payment') }}">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search" name="search"
                value="@if (isset($search)) {{ $search }} @endif" aria-label="Search"
                aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.payments') }}</h1>
        </div>

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h5 class="h5 mb-0 text-gray-800">{{ __('repair-business.create-payment') }}</h5>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('create-payment') }}" method="POST">
                    @csrf
                    <div class="form-group row m-0">
                        <label for="amount"
                            class="col-sm-4 col-form-label">{{ __('repair-business.input_amount') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('amount') is-invalid @enderror"
                                id="amount" value="{{ old('amount') }}" name="amount" placeholder="">
                            @error('amount')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="method"
                            class="col-sm-4 col-form-label">{{ __('repair-business.input_method') }}</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm @error('method') is-invalid @enderror"
                                name="method">
                                <option value="cash">{{ __('repair-business.input_cash') }}</option>
                                <option value="card">{{ __('repair-business.input_card') }}</option>
                                <option value="check">{{ __('repair-business.input_check') }}</option>
                                <option value="other">{{ __('repair-business.input_other') }}</option>

                            </select>
                            @error('method')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="ref"
                            class="col-sm-4 col-form-label">{{ __('repair-business.input_reference') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('ref') is-invalid @enderror"
                                id="ref" value="{{ old('ref') }}" name="ref" placeholder="">
                            @error('ref')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn  btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i>
                                {{ __('repair-business.button_create') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @if (!$payments->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_date') }}</th>
                                    <th scope="col">{{ __('repair-business.table_invoice') }}</th>
                                    <th scope="col">{{ __('repair-business.table_amount') }}</th>
                                    <th scope="col">{{ __('repair-business.table_method') }}</th>
                                    <th scope="col">{{ __('repair-business.table_reference') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{ date_format($payment->created_at, 'M d, Y') }}</td>
                                        <td>
                                            @if (isset($payment->invoice))
                                                <a
                                                    href="{{ route('view-invoice', $payment->invoice) }}">{{ $payment->invoice }}</a>
                                            @endif
                                        </td>
                                        <td>$ {{ number_format((float) $payment->amount, 2, '.', ',') }} </td>
                                        <td class="text-uppercase">{{ $payment->method }}</td>
                                        <td>{{ $payment->ref }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-block btn-sm"
                                                href="{{ route('view-payment', $payment) }}"><i
                                                    class="fas fa-binoculars"></i>
                                                {{ __('repair-business.button_view') }}</a>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="alert alert-secondary" role="alert">
                        {{ __('repair-business.no-information-to-show') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
