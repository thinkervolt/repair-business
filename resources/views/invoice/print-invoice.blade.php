<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name', 'Laravel') }} - @yield('page')</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            src: url({{ asset('/vendor/google-fonts/nunito/Nunito-Regular.ttf') }}) format('truetype');
        }
    </style>
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="card  border-0">
            @php $item_count = 0 @endphp

            <div class="card-header">
                {{ __('repair-business.invoice') }}
                <strong>#{{ $invoice->id }}</strong>
                <span class="float-right">
                    @if (isset($invoice->status))
                        <p class="font-weight-bold text-uppercase m-0 p-0 text-{{ $invoice->status_data->color }}">
                            {{ $invoice->status_data->name }}</p>
                    @endif
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="mb-3">{{ __('repair-business.from') }}:</h6>
                        <div>
                            <div>{{ $invoice->company_name }}</div>
                            <div>{{ $invoice->company_phone }}</div>
                            <div>{{ $invoice->company_email }}</div>
                            <div>{{ $invoice->company_address }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="mb-3">{{ __('repair-business.to') }}:</h6>
                        <div>{{ $invoice->customer_company }}</div>
                        <div>{{ $invoice->customer_name }}</div>
                        <div>{{ $invoice->customer_phone }}</div>
                        <div>{{ $invoice->customer_email }}</div>
                        <div>{{ $invoice->customer_address }}</div>
                    </div>
                </div>
            </div>
            <div class="table-responsive-sm">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ __('repair-business.table_item') }}</th>
                            <th>{{ __('repair-business.table_description') }}</th>

                            <th class="text-right">{{ __('repair-business.table_unit-cost') }}</th>
                            <th class="text-center">{{ __('repair-business.table_quantity') }}</th>
                            <th class="text-right">{{ __('repair-business.table_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$invoice_items->isEmpty())
                            @foreach ($invoice_items as $item)
                                <tr>
                                    <td class="center col-auto">{{ $item_count = $item_count + 1 }}</td>
                                    <td class="text-left strong">
                                        {{ $item->name }}
                                    </td>
                                    <td class="left">
                                        <p>{{ $item->description }}</p>
                                        <p class="small">{{ $item->sub_description }}</p>
                                    </td>
                                    <td class="text-right">
                                        {{ $item->unit_cost }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="text-right">$ {{ number_format((float) $item->total, 2, '.', ',') }}

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        @if (!$transactions->isEmpty())
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td class="center col-auto">{{ $item_count = $item_count + 1 }}</td>
                                    <td class="text-left strong">
                                        {{ $transaction->product->barcode }}
                                    </td>
                                    <td class="text-left">
                                        <p>{{ $transaction->product->name }}</p>
                                    </td>
                                    <td class="text-right">
                                        {{ $transaction->selling_price }}
                                    </td>
                                    <td class="text-center">
                                        {{ $transaction->quantity }}
                                    </td>
                                    <td class="text-right">$
                                        {{ number_format((float) ($transaction->selling_price * $transaction->quantity), 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-5 text-center align-items-middle">
                    
                </div>
                <div class="col-lg-4 col-sm-5 ml-auto">
                    <table class="table table-clear">
                        <tbody>
                            <tr>
                                <td class="text-left">
                                    <strong>{{ __('repair-business.subtotal') }}</strong>
                                </td>
                                <td class="text-right">$ {{ number_format((float) $invoice->subtotal, 2, '.', ',') }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-left">
                                    <strong>{{ __('repair-business.tax') }} (
                                        {{ number_format((float) $invoice->tax_porcentage, 2, '.', ',') }}%)</strong>
                                </td>
                                <td class="text-right">$ {{ number_format((float) $invoice->tax, 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left">
                                    <strong>{{ __('repair-business.total') }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>$ {{ number_format((float) $invoice->total, 2, '.', ',') }}</strong>
                                </td>
                            </tr>

                            @if (!$payments->isEmpty())

                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="text-left">
                                            <strong>{{ __('repair-business.payment') }}</strong>

                                        </td>
                                        <td class="text-right">
                                            <p class="text-muted small text-uppercase m-0 p-0">{{ $payment->method }}
                                            </p>
                                            @if (isset($payment->ref))
                                                <p class="text-muted small text-uppercase m-0 p-0">
                                                    {{ __('repair-business.ref') }}: {{ $payment->ref }}</p>
                                            @endif
                                            <p class="text-muted small text-uppercase m-0 p-0">
                                                {{ date_format($payment->created_at, 'M d, Y h:iA') }}</p>
                                            <p class="font-weight-bold ">$
                                                {{ number_format((float) $payment->amount, 2, '.', ',') }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td class="text-left">
                                    <strong>{{ __('repair-business.balance') }}</strong>
                                </td>
                                <td class="text-right">
                                    <h3 class="font-weight-bold">$
                                        {{ number_format((float) $invoice->balance, 2, '.', ',') }}</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-center p-4 my-2">
            @php echo DNS2D::getBarcodeSVG(route('view-invoice',$invoice->id), 'QRCODE',5,5) @endphp
        </div>

        <div class="data-block">
            <p class="small text-justify">{{ $terms }}</p>
        </div>
    </div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>
