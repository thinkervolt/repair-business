@extends('layouts.admin')
@section('page')
    {{ __('repair-business.register-report') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.register-report') }}</h1>
            <h5 class="mb-0 text-gray-800">{{ __('repair-business.date') }}: {{ date('M d, Y', strtotime($payment_data['date'])) }} </h5>
        </div>
        @if (!empty($payments))
            <h3>PAYMENTS</h3>
            <div class="row">
                <div class="col-md text-left"> {{ __('repair-business.payments') }}: {{ $payment_data['count'] }}</div>
                <div class="col-md text-center"> {{ __('repair-business.total') }}: $ {{ number_format((float) $payment_data['total'], 2, '.', ',') }}
                </div>
                <div class="col-md text-right">
                    <p class="muted font-italic small m-0 p-0">{{ __('repair-business.cash') }}: $
                        {{ number_format((float) $payment_data['total_cash'], 2, '.', ',') }}</p>
                    <p class="muted font-italic small m-0 p-0">{{ __('repair-business.card') }}: $
                        {{ number_format((float) $payment_data['total_card'], 2, '.', ',') }}</p>
                    <p class="muted font-italic small m-0 p-0">{{ __('repair-business.check') }}: $
                        {{ number_format((float) $payment_data['total_check'], 2, '.', ',') }}</p>
                    <p class="muted font-italic small m-0 p-0">{{ __('repair-business.other') }}: $
                        {{ number_format((float) $payment_data['total_other'], 2, '.', ',') }}</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_id') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_invoice') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_amount') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_method') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_reference') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_date') }}</p>
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    <p class="m-0 p-0 small">{{ $payment->id }}</p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ $payment->invoice ?? '' }}</p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small"> $ {{ number_format((float) $payment->amount, 2, '.', ',') }}
                                    </p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small text-uppercase">{{ $payment->method }}</p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ $payment->ref }}</p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ date_format($payment->created_at, 'M d, Y') }}</p>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="row">
            <div class="col">
                <p class="mb-0 pb-0">{{ __('repair-business.total-cash-regiter') }}: $
                    {{ number_format((float) $payment_data['cash_register'], 2, '.', ',') }}</p>
                <p>{{ __('repair-business.total-cash-invoices') }}: $ {{ number_format((float) $payment_data['total_cash'], 2, '.', ',') }}</p>
                <hr>
                <p class="font-weight-bold">{{ __('repair-business.no-invoice-cash-transactions') }}: $
                    {{ number_format((float) ($payment_data['cash_register'] - $payment_data['total_cash']), 2, '.', ',') }}
                </p>
            </div>
            <div class="col">
                <p class="mb-0 pb-0">{{ __('repair-business.total-card-register') }}: $
                    {{ number_format((float) $payment_data['card_register'], 2, '.', ',') }}</p>
                <p>{{ __('repair-business.total-card-invoices') }}: $ {{ number_format((float) $payment_data['total_card'], 2, '.', ',') }}</p>
                <hr>
                <p class="font-weight-bold">{{ __('repair-business.no-invoice-card-transaction') }}: $
                    {{ number_format((float) ($payment_data['card_register'] - $payment_data['total_card']), 2, '.', ',') }}
                </p>
            </div>




        </div>
        <form method="POST" action="{{ route('register-report-insert') }}">
            @method('POST')

            @csrf
            <input type="hidden" name="cash" value={{ $payment_data['cash_register'] - $payment_data['total_cash'] }}>
            <input type="hidden" name="card" value={{ $payment_data['card_register'] - $payment_data['total_card'] }}>
            <input type="hidden" name="date" value={{ $payment_data['date'] }}>
            <button class="btn btn-primary" type="submit"><i class="far fa-file-alt"></i> {{ __('repair-business.button_insert') }}</button>
        </form>
    </div>
@endsection
