@extends('layouts.admin')
@section('page')
    {{ __('repair-business.report') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.report') }}</h1>
            <h5 class="mb-0 text-gray-800">{{ __('repair-business.from') }}: {{ date('M d, Y', strtotime($report_data['from'])) }} {{ __('repair-business.to') }}:
                {{ date('M d, Y', strtotime($report_data['to'])) }} </h5>

            @php
                $report_from = $report_data['from'];
                $report_to = $report_data['to'];

                if (isset($report_data['invoices'])) {
                    $report_invoices = $report_data['invoices'];
                } else {
                    $report_invoices = 'off';
                }
                if (isset($report_data['repairs'])) {
                    $report_repairs = $report_data['repairs'];
                } else {
                    $report_repairs = 'off';
                }
                if (isset($report_data['payments'])) {
                    $report_payments = $report_data['payments'];
                } else {
                    $report_payments = 'off';
                }

            @endphp
            <a href="{{ route('print-report', [$report_from, $report_to, $report_invoices, $report_repairs, $report_payments]) }}"
                class="btn btn-primary btn-sm mb-1" target="popup"
                onclick="window.open('{{ route('print-report', [$report_from, $report_to, $report_invoices, $report_repairs, $report_payments]) }}','popup','width=600,height=600'); return false;">

                <i class="fas fa-print"></i> {{ __('repair-business.button_print') }}
            </a>
        </div>
        @if (!empty($invoices))
            <h3 class="text-uppercase">{{ __('repair-business.invoices') }}</h3>
            <div class="row">
                <div class="col-md text-left"> {{ __('repair-business.invoices') }}: {{ $invoice_data['count'] }}</div>
                <div class="col-md text-center"> {{ __('repair-business.unpaid-amount') }}: $
                    {{ number_format((float) $invoice_data['balance'], 2, '.', ',') }}</div>
                <div class="col-md text-right"> {{ __('repair-business.earnings') }}: $
                    {{ number_format((float) ($invoice_data['total'] - $invoice_data['balance']), 2, '.', ',') }} </div>
            </div>
            <div class="table-responsive">


                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_id') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_customer') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_items') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_status') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_balance') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_total') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_date') }}</p>
                            </th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    <p class="m-0 p-0 small">{{ $invoice->id }}</p>
                                </td>
                                <td>

                                    <p class="m-0 p-0 small">{{ $invoice->customer_name }}</p>
                                    <p class="m-0 p-0 small">
                                        {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $invoice->customer_phone) }}
                                    </p>
                                    <p class="m-0 p-0 small">{{ $invoice->customer_email }}</p>
                                </td>

                                <td>

                                    @foreach ($invoice->items as $item)
                                        <div class="p-3">
                                            <p class="m-0 p-0 small">{{ $item->name }}</p>
                                            <p class="m-0 p-0 small">{{ $item->description }}</p>
                                            <p class="m-0 p-0 small">{{ $item->sub_description }}</p>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @if (isset($invoice->status))
                                        <p
                                            class="font-weight-bold text-uppercase m-0 p-0 small text-{{ $invoice->status_data->color ?? '' }}">
                                            {{ $invoice->status_data->name ?? '' }}</p>
                                    @endif
                                </td>
                                <td>
                                    <p
                                        class=" m-0 p-0 small @if ($invoice->balance < 0) text-success @endif @if ($invoice->balance > 0) text-danger @endif">
                                        $ {{ number_format((float) $invoice->balance, 2, '.', ',') }} </p>
                                </td>
                                <td>
                                    <p class=" m-0 p-0 small"> $ {{ number_format((float) $invoice->total, 2, '.', ',') }}
                                    </p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ date_format($invoice->created_at, 'M d, Y') }}</p>
                                </td>

                                <td class="text-right"><a class="btn btn-primary btn-sm" href="{{ route('view-invoice', $invoice) }}"><i
                                            class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (!empty($repairs))
            <h3 class="text-uppercase">{{ __('repair-business.repairs') }}</h3>
            <div class="row">
                <div class="col-md text-left"> {{ __('repair-business.repairs') }}: {{ $repair_data['count'] }}</div>
                <div class="col-md text-center"></div>
                <div class="col-md text-right"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_id') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_customer') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_target') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_request') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_status') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_priority') }}</p>
                            </th>
                            <th scope="col">
                                <p class="m-0 p-0 small">{{ __('repair-business.table_date') }}</p>
                            </th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($repairs as $repair)
                            <tr>
                                <td>
                                    <p class="m-0 p-0 small">{{ $repair->id }}</p>
                                </td>
                                <td>
                                    @if (isset($repair->customer))
                                        <p class="m-0 p-0 small">{{ $repair->customer_data->first_name }}
                                            {{ $repair->customer_data->last_name }}</p>
                                        <p class="m-0 p-0 small">
                                            {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone) }}
                                        </p>
                                        <p class="m-0 p-0 small">{{ $repair->customer_data->email }}</p>
                                    @endif
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ $repair->target }}</p>
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ $repair->request }}</p>
                                </td>
                                <td>
                                    @if (isset($repair->status))
                                        <p
                                            class="font-weight-bold text-uppercase small m-0 p-0 text-{{ $repair->status_data->color ?? '' }}">
                                            {{ $repair->status_data->name ?? '' }} </p>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($repair->priority))
                                        <p
                                            class="font-weight-bold text-uppercase small m-0 p-0 text-{{ $repair->priority_data->color ?? '' }}">
                                            {{ $repair->priority_data->name ?? '' }} </p>
                                    @endif
                                </td>
                                <td>
                                    <p class="m-0 p-0 small">{{ date_format($repair->created_at, 'M d, Y') }}</p>
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-primary small btn-sm" href="{{ route('view-repair', $repair) }}"><i
                                            class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }}</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        @endif
        @if (!empty($payments))
            <h3 class="text-uppercase">{{ __('repair-business.payments') }}</h3>

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
    </div>
@endsection
