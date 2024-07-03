@extends('layouts.admin')
@section('page')
    {{ __('repair-business.payment') }} #{{ $payment->id }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.payment') }} #{{ $payment->id }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('update-payment', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row m-0">
                        <label for="amount" class="col-sm-4 col-form-label">Amount</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('amount') is-invalid @enderror"
                                id="amount" value="{{ number_format((float) $payment->amount, 2, '.', ',') }}"
                                name="amount" placeholder="">
                            @error('amount')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="method" class="col-sm-4 col-form-label">{{ __('repair-business.input_method') }}</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm @error('method') is-invalid @enderror"
                                name="method">
                                <option @if ($payment->method == 'cash') selected @endif value="cash">{{ __('repair-business.input_cash') }}</option>
                                <option @if ($payment->method == 'card') selected @endif value="card">{{ __('repair-business.input_card') }}</option>
                                <option @if ($payment->method == 'check') selected @endif value="check">{{ __('repair-business.input_check') }}</option>
                                <option @if ($payment->method == 'other') selected @endif value="other">{{ __('repair-business.input_other') }}</option>

                            </select>
                            @error('method')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="ref" class="col-sm-4 col-form-label">{{ __('repair-business.input_reference') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('ref') is-invalid @enderror"
                                id="ref" value="{{ $payment->ref }}" name="ref" placeholder="">
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
                            <button type="submit" class="btn btn-warning mt-3 mb-2 btn-sm"><i
                                    class="fa fa-edit"></i> {{ __('repair-business.button_update') }}</button>
                        </div>
                    </div>
                </form>
                @if ($payment->invoice === null)
                    <form action="{{ route('delete-payment', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group row m-0">
                            <div class="col-sm-4"> </div>
                            <div class="col-sm-8">
                                <button type="submit" class="btn  btn-danger mt-1 mb-2 btn-sm"><i
                                        class="fa fa-trash"></i> {{ __('repair-business.button_delete') }}</button>

                            </div>
                        </div>
                    </form>
                @else
                    <div class="form-group row m-0">
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <a href="{{ route('view-invoice', $payment->invoice) }}"
                                class="btn  btn-primary mt-1 mb-2 btn-sm"><i class="fas fa-binoculars"></i> {{ __('repair-business.button_view-invoice') }}</a>
                        </div>
                    </div>
                @endif

                <p class="d-block d-sm-inline m-0 small">id: {{ $payment->id }}</p>
                <p class="d-block d-sm-inline m-0 small">created_at: {{ $payment->created_at }}</p>
                <p class="d-block d-sm-inline m-0 small">updated_at: {{ $payment->updated_at }}</p>
            </div>
        </div>
    </div>
@endsection
