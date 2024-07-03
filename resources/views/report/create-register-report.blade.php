@extends('layouts.admin')
@section('page')
 {{ __('repair-business.create-register-report') }} 
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"> {{ __('repair-business.create-register-report') }} </h1>
        </div>
        <p class="small font-italic"><i class="fas fa-info-circle"></i>  {{ __('repair-business.register-report-message') }} </p>

        <form method="POST" action="{{ route('get-register-report') }}">
            @csrf
            <div class="row align-items-end">
                <div class="col-md">
                    <label>{{ __('repair-business.input_date') }}:</label>
                    <input type="date" class="form-control mb-1" value="{{ date('Y-m-d') }}" name="date"
                        id="from">
                </div>

                <div class="col-md">
                    <label>{{ __('repair-business.input_cash-register-total') }}:</label>
                    <input type="text" class="form-control mb-1" name="cash" placeholder="$ 0.00">
                </div>

                <div class="col-md">
                    <label>{{ __('repair-business.input_credit-debit-total') }}:</label>
                    <input type="text" class="form-control mb-1" name="card" placeholder="$ 0.00">
                </div>


                <div class="col-md">
                    <label></label>
                    <button type="submit" class="btn btn-primary  mb-1 mt-2 "><i class="far fa-file-alt"></i> {{ __('repair-business.button_report') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
