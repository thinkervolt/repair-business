@extends('layouts.admin')
@section('page')
{{ __('repair-business.create-report') }} 
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.create-report') }} </h1>
        </div>
        <form method="POST" action="{{ route('get-report') }}">
            @csrf
            <div class="row">
                <div class="col-md">

                    <label for="from">{{ __('repair-business.from') }}:</label>
                    <input type="date" class="form-control mb-1" value="{{ date('Y-m-d') }}" name="from"
                        id="from">
                </div>
                <div class="col-md">
                    <label for="to">{{ __('repair-business.to') }}:</label>
                    <input type="date" class="form-control mb-1" value="{{ date('Y-m-d') }}" name="to"
                        id="to">
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="checkbox" id="invoices" name="invoices" checked>
                                <label class="form-check-label" for="invoices">
                                  {{ __('repair-business.input_invoices') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="checkbox" id="repairs" name="repairs" checked>
                                <label class="form-check-label" for="repairs">
                                  {{ __('repair-business.input_repairs') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="checkbox" id="payments" name="payments" checked>
                                <label class="form-check-label" for="payments">
                                  {{ __('repair-business.input_payments') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm "><i class="far fa-file-alt"></i>
              {{ __('repair-business.button_report') }}</button>
        </form>
    </div>
@endsection
