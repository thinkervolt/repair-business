<html>

<head>
    <style>
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            src: url({{ asset('/vendor/google-fonts/nunito/Nunito-Regular.ttf')}}) format('truetype');
        }
    </style>
</head>
<style>
    @media print {
        @page {
            margin: 0;
        }
    }

    body,
    html {

        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Nunito', sans-serif;
    }

    .print-area-container {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
    }




    .data-block {
        padding-left: 30px;
        padding-right: 30px;
        padding-bottom: 15px;
    }


    .small-data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 8px;
    }

    .data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 12px;
    }

    .big-data-line {

        text-align: left;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 18px;


    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .justify {

        text-align: justify;

    }

    .barcode {

        text-align: center;
        padding-top: 5px;
    }

    .items-table {

        width: 100%;
    }
</style>

<body>
    @php $item_count = 0 @endphp

    <div class="print-area-container">
        <div class="data-block">
            <p class="big-data-line center">INVOICE RECEIPT</p>
        </div>
        <div class="data-block">

            <p class="data-line left">{{ $invoice->company_name }}</p>
            <p class="data-line left">{{ $invoice->company_phone }}</p>
            <p class="data-line left">{{ $invoice->company_email }}</p>
            <p class="data-line left">{{ $invoice->company_address }}</p>
        </div>
        <div class="data-block">
            <p class="big-data-line center">INVOICE #{{ $invoice->id }}</p>
            <p class="data-line center">{{ date_format($invoice->created_at, 'M d, Y') }}</p>
        </div>

        <div class="data-block">
            <p class="big-data-line">CUSTOMER</p>
            <p class="data-line">{{ $invoice->customer_company }}</p>
            <p class="data-line">{{ $invoice->customer_name }}</p>
            <p class="data-line">{{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $invoice->customer_phone) }}
            </p>
            <p class="data-line">{{ $invoice->customer_email }}</p>
            <p class="data-line">{{ $invoice->customer_address }}</p>
        </div>
        <div class="data-block">
            <table class="items-table">
                @if (!$invoice_items->isEmpty())
                    @foreach ($invoice_items as $item)
                        <tr>
                            <td>
                                <p class="small-data-line">{{ $item_count = $item_count + 1 }}</p>
                            </td>
                            <td>
                                <p class="data-line">{{ $item->name }}</p>
                            </td>
                            <td>
                                <p class="small-data-line"> ({{ $item->unit_cost }} X {{ $item->quantity }})</p>
                            </td>
                            <td>
                                <p class="data-line right">${{ number_format((float) $item->total, 2, '.', ',') }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td></td>

                            <td colspan="2">
                                <p class="small-data-line">{{ $item->description }}</p>
                                <p class="small-data-line">{{ $item->sub_description }}</p>
                            </td>
                            <td>
                            </td>
                        </tr>
                    @endforeach

                @endif

                @if (!$transactions->isEmpty())
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>
                                <p class="small-data-line">{{ $item_count = $item_count + 1 }}</p>
                            </td>
                            <td>
                                <p class="data-line">{{ $transaction->product->barcode }}</p>
                            </td>
                            <td>
                                <p class="small-data-line"> ({{ $transaction->selling_price }} X
                                    {{ $transaction->quantity }})</p>
                            </td>
                            <td>
                                <p class="data-line right">
                                    ${{ number_format((float) ($transaction->selling_price * $transaction->quantity), 2, '.', ',') }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td></td>

                            <td colspan="2">
                                <p class="small-data-line">{{ $transaction->product->name }}</p>
                            </td>
                            <td>
                            </td>
                        </tr>
                    @endforeach

                @endif




                <tr>
                    <td></td>
                    <td colspan="2">
                        <p class="data-line right">Subtotal</p>
                    </td>
                    <td>
                        <p class="data-line right">${{ number_format((float) $invoice->subtotal, 2, '.', ',') }}</p>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td colspan="2">
                        <p class="data-line right">TAX (
                            {{ number_format((float) $invoice->tax_porcentage, 2, '.', ',') }}%)</p>
                    </td>
                    <td>
                        <p class="data-line right">${{ number_format((float) $invoice->tax, 2, '.', ',') }}</p>
                    </td>
                </tr>
                <tr>

                    <td colspan="2">
                        <p class="big-data-line right">Total</p>
                    </td>
                    <td colspan="2">
                        <p class="big-data-line right">${{ number_format((float) $invoice->total, 2, '.', ',') }}</p>
                    </td>
                </tr>

                @if (!$payments->isEmpty())

                    @foreach ($payments as $payment)
                        <tr>
                            <td></td>
                            <td colspan="2">
                                <p class="data-line right">Payment</p>
                                <p class="small-data-line right">{{ $payment->method }}</p>
                                <p class="small-data-line right">{{ date_format($payment->created_at, 'M d, Y h:iA') }}
                                </p>

                            </td>
                            <td>


                                <p class="data-line right">${{ number_format((float) $payment->amount, 2, '.', ',') }}
                                </p>
                            </td>



                        </tr>
                    @endforeach

                @endif
                <tr>
                    <td></td>
                    <td colspan="2">
                        <p class="data-line right">Balance</p>
                    </td>

                    <td>
                        <p class="data-line right">${{ number_format((float) $invoice->balance, 2, '.', ',') }}</p>
                    </td>


                </tr>



            </table>
        </div>





        <div class="data-block">
            <div class="barcode">
                @php echo DNS1D::getBarcodeSVG('INV'.$invoice->id, 'C39',2,50,'black' ,false); @endphp
            </div>
        </div>

        <div class="data-block">
            <p class="small-data-line justify">{{ $terms }}</p>

        </div>





        <script>
            window.print();
        </script>

</body>

</html>
