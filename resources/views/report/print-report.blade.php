<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ config('app.name', 'Laravel') }} </title>

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

    <style>
        html,
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        table,
        td,
        th,
        p,
        tr {
            color: black !important;
            font-weight: 500 !important;
            text-transform: uppercase;

        }
    </style>
</head>

<body>
    <div class="container-fluid pt-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.report') }}</h1>
            <h5 class="mb-0 text-gray-800">{{ __('repair-business.from:') }} {{ date('M d, Y', strtotime($report_data['from'])) }} {{ __('repair-business.to') }}:
                {{ date('M d, Y', strtotime($report_data['to'])) }} </h5>
        </div>
        @if ($report_data['invoices'] == 'on')
            <h5 class="h5 text-uppercase">{{ __('repair-business.invoices') }}</h5>
            @if (!$invoices->isEmpty())
                <div class="row">
                    <div class="col-md text-right"> {{ __('repair-business.invoices') }}: {{ $invoice_data['count'] }}</div>
                    <div class="col-md text-center"> {{ __('repair-business.unpaid-amount') }}: $
                        {{ number_format((float) $invoice_data['balance'], 2, '.', ',') }}</div>
                    <div class="col-md text-left"> {{ __('repair-business.earnings') }}: $
                        {{ number_format((float) ($invoice_data['total'] - $invoice_data['balance']), 2, '.', ',') }}
                    </div>
                </div>
                <div class="table-responsive ">

                    <table class="table table-sm mt-3 table-hover  table-bordered ">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <p class="m-0 p-0 small">{{ __('repair-business.table_id') }}</p>
                                </th>
                                <th scope="col">
                                    <p class="m-0 p-0 small">{{ __('repair-business.table_customer') }} </p>
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
                                                class="font-weight-bold text-uppercase m-0 p-0 small text-{{ $invoice->status_data->color }}">
                                                {{ $invoice->status_data->name }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <p
                                            class=" m-0 p-0 small @if ($invoice->balance < 0) text-success @endif @if ($invoice->balance > 0) text-danger @endif">
                                            $ {{ number_format((float) $invoice->balance, 2, '.', ',') }} </p>
                                    </td>
                                    <td>
                                        <p class=" m-0 p-0 small"> $
                                            {{ number_format((float) $invoice->total, 2, '.', ',') }} </p>
                                    </td>
                                    <td>
                                        <p class="m-0 p-0 small">{{ date_format($invoice->created_at, 'M d, Y') }}</p>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    {{ __('repair-business.no-information-to-show') }}
                </div>
            @endif
        @endif

        @if ($report_data['repairs'] == 'on')
            <h3>{{ __('repair-business.repairs') }}</h3>
            @if (!$repairs->isEmpty())
                <div class="row">
                    <div class="col-md text-right">{{ __('repair-business.repairs') }}: {{ $repair_data['count'] }}</div>
                    <div class="col-md text-center"></div>
                    <div class="col-md text-left"></div>
                </div>
                <div class="table-responsive ">
                    <table class="table table-sm mt-3 table-hover table-bordered ">
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
                                                class="font-weight-bold text-uppercase small m-0 p-0 text-{{ $repair->status_data->color }}">
                                                {{ $repair->status_data->name }} </p>
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($repair->priority))
                                            <p
                                                class="font-weight-bold text-uppercase small m-0 p-0 text-{{ $repair->priority_data->color }}">
                                                {{ $repair->priority_data->name }} </p>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="m-0 p-0 small">{{ date_format($repair->created_at, 'M d, Y') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    {{ __('repair-business.no-information-to-show') }}
                </div>
            @endif
        @endif

        @if ($report_data['payments'] == 'on')
            <h3>{{ __('repair-business.payments') }}</h3>
            @if (!$payments->isEmpty())
                <div class="row">
                    <div class="col-md text-left">{{ __('repair-business.payments') }}: {{ $payment_data['count'] }}</div>
                    <div class="col-md text-center"> {{ __('repair-business.total') }}: $
                        {{ number_format((float) $payment_data['total'], 2, '.', ',') }}</div>
                    <div class="col-md text-right">
                        <p class="m-0 p-0">{{ __('repair-business.input_cash') }}: $
                            {{ number_format((float) $payment_data['total_cash'], 2, '.', ',') }}
                        </p>
                        <p class="m-0 p-0">{{ __('repair-business.input_card') }}: $
                            {{ number_format((float) $payment_data['total_card'], 2, '.', ',') }}
                        </p>
                        <p class="m-0 p-0">{{ __('repair-business.input_check') }}: $
                            {{ number_format((float) $payment_data['total_check'], 2, '.', ',') }}</p>
                        <p class="m-0 p-0">{{ __('repair-business.input_other') }}: $
                            {{ number_format((float) $payment_data['total_other'], 2, '.', ',') }}</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mt-3 table-hover  table-bordered ">
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
                                        <p class="m-0 p-0 small">{{ $payment->invoice }}</p>
                                    </td>
                                    <td>
                                        <p class="m-0 p-0 "> $
                                            {{ number_format((float) $payment->amount, 2, '.', ',') }} </p>
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
            @else
                <div class="alert alert-secondary" role="alert">
                    {{ __('repair-business.no-information-to-show') }}
                </div>
            @endif
        @endif
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
