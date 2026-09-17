<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} - Invoice #{{ $invoice->id }}</title>

    <style>
        @page {
            size: letter;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #334155;
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        body {
            margin: 18mm 20mm;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .muted {
            color: #64748b;
        }

        .small {
            font-size: 10px;
        }

        .bold {
            font-weight: bold;
        }

        /* ---------- card ---------- */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 26px 30px;
        }

        /* ---------- header ---------- */
        .header-row td {
            padding-bottom: 18px;
            vertical-align: top;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        .company-line {
            font-size: 10px;
            color: #64748b;
            margin: 0;
            padding: 0;
        }

        .doc-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            margin: 0;
            padding: 0;
            text-align: right;
        }

        .doc-no {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
            padding: 0;
            text-align: right;
        }

        .doc-meta {
            font-size: 10px;
            color: #64748b;
            margin: 4px 0 0;
            padding: 0;
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* ---------- addresses ---------- */
        .address-cell {
            padding: 16px 0 18px;
        }

        .addr-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin: 0 0 5px;
            padding: 0;
        }

        .addr-line {
            margin: 0;
            padding: 0;
            color: #475569;
        }

        .addr-line.strong {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        /* ---------- items table ---------- */
        .items-table {
            margin-top: 4px;
        }

        .items-table th {
            background: #f8fafc;
            color: #64748b;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 9px;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 9px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #475569;
        }

        .items-table .item-name {
            font-weight: 600;
            color: #1e293b;
        }

        .items-table .item-desc {
            font-size: 10px;
            color: #94a3b8;
            margin: 0;
            padding: 0;
        }

        .items-table .num {
            text-align: right;
            white-space: nowrap;
        }

        /* ---------- payments rows (part of the main table) ---------- */
        .payment-row td {
            background: #f8fafc;
        }

        .payment-tag {
            color: #64748b;
        }

        .payment-tag b {
            color: #0f172a;
        }

        /* ---------- totals rows (part of the main table) ---------- */
        .sum-row td {
            padding: 4px 9px;
            border-bottom: none;
            font-size: 11px;
        }

        .sum-row.top td {
            padding-top: 14px;
        }

        .sum-label {
            text-align: right;
            color: #64748b;
        }

        .sum-value {
            text-align: right;
            white-space: nowrap;
            font-weight: 600;
            color: #334155;
        }

        .grand-row td {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        .grand-row .sum-label {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .grand-row .sum-value {
            font-size: 14px;
            color: #0f172a;
        }

        .balance-row td {
            padding: 10px 9px;
            background: #eff6ff;
            border-top: 1px solid #bfdbfe;
            border-bottom: 1px solid #bfdbfe;
            font-weight: 700;
            color: #1e293b;
        }

        .balance-label {
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 11px;
            color: #1e293b;
        }

        .balance-figure {
            font-size: 15px;
            text-align: right;
        }

        /* ---------- footer ---------- */
        .terms {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }

        .terms-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin: 0 0 4px;
            padding: 0;
        }

        .terms-text {
            font-size: 10px;
            color: #64748b;
            text-align: justify;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .qrcode {
            text-align: center;
            margin-top: 20px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    @php
        $badges = [
            'primary' => ['background' => '#dbeafe', 'color' => '#1d4ed8'],
            'success' => ['background' => '#dcfce7', 'color' => '#15803d'],
            'warning' => ['background' => '#fef3c7', 'color' => '#b45309'],
            'danger' => ['background' => '#fee2e2', 'color' => '#b91c1c'],
            'info' => ['background' => '#e0f2fe', 'color' => '#0369a1'],
            'secondary' => ['background' => '#e2e8f0', 'color' => '#475569'],
            'dark' => ['background' => '#e2e8f0', 'color' => '#1e293b'],
            'light' => ['background' => '#f8fafc', 'color' => '#94a3b8'],
        ];
        $status = (isset($invoice->status) && $invoice->status_data) ? $invoice->status_data : null;
        $status_style = $status ? ($badges[$status->color] ?? $badges['secondary']) : null;
        $balance = (float) $invoice->balance;
        $balance_color = $balance < 0 ? '#15803d' : ($balance > 0 ? '#b91c1c' : '#1e293b');
        $item_count = 0;
    @endphp

    <div class="card">
        <table class="header-row">
            <tr>
                <td>
                    <div class="company-name">{{ $invoice->company_name }}</div>
                    <p class="company-line">{{ $invoice->company_phone }}</p>
                    <p class="company-line">{{ $invoice->company_email }}</p>
                    <p class="company-line">{{ $invoice->company_address }}</p>
                </td>
                <td style="width:42%;">
                    <p class="doc-label">{{ __('repair-business.invoice') }}</p>
                    <p class="doc-no">#{{ $invoice->id }}</p>
                    @if ($status && $status_style)
                        <p class="status-badge" style="text-align:right;background:{{ $status_style['background'] }};color:{{ $status_style['color'] }};">{{ $status->name }}</p>
                    @endif
                    <p class="doc-meta">{{ __('repair-business.table_date') }}: {{ date('M d, Y', strtotime($invoice->created_at)) }}</p>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="address-cell">
                    <p class="addr-label">{{ __('repair-business.from') }}</p>
                    <p class="addr-line strong">{{ $invoice->company_name }}</p>
                    <p class="addr-line">{{ $invoice->company_phone }}</p>
                    <p class="addr-line">{{ $invoice->company_email }}</p>
                    <p class="addr-line">{{ $invoice->company_address }}</p>
                </td>
                <td class="address-cell">
                    <p class="addr-label">{{ __('repair-business.to') }}</p>
                    <p class="addr-line strong">{{ $invoice->customer_name }}</p>
                    @if ($invoice->customer_company)
                        <p class="addr-line">{{ $invoice->customer_company }}</p>
                    @endif
                    <p class="addr-line">{{ $invoice->customer_phone }}</p>
                    <p class="addr-line">{{ $invoice->customer_email }}</p>
                    <p class="addr-line">{{ $invoice->customer_address }}</p>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:28px;">#</th>
                    <th>{{ __('repair-business.table_item') }}</th>
                    <th>{{ __('repair-business.table_description') }}</th>
                    <th class="right" style="width:90px;">{{ __('repair-business.table_unit-cost') }}</th>
                    <th class="right" style="width:50px;">{{ __('repair-business.table_quantity') }}</th>
                    <th class="right" style="width:100px;">{{ __('repair-business.table_total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice_items as $item)
                    <tr>
                        <td>{{ $item_count = $item_count + 1 }}</td>
                        <td class="item-name">{{ $item->name }}</td>
                        <td>
                            @if ($item->description)
                                <p class="item-desc">{{ $item->description }}</p>
                            @endif
                            @if ($item->sub_description)
                                <p class="item-desc">{{ $item->sub_description }}</p>
                            @endif
                        </td>
                        <td class="num">$ {{ number_format((float) $item->unit_cost, 2, '.', ',') }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">$ {{ number_format((float) $item->total, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $item_count = $item_count + 1 }}</td>
                        <td class="item-name">{{ $transaction->product->name }}</td>
                        <td>
                            <p class="item-desc">{{ __('repair-business.table_barcode') ?? 'Barcode' }}: {{ $transaction->product->barcode }}</p>
                        </td>
                        <td class="num">$ {{ number_format((float) $transaction->selling_price, 2, '.', ',') }}</td>
                        <td class="num">{{ $transaction->quantity }}</td>
                        <td class="num">$ {{ number_format((float) ($transaction->selling_price * $transaction->quantity), 2, '.', ',') }}</td>
                    </tr>
                @endforeach

                @if (!$payments->isEmpty())
                    @foreach ($payments as $payment)
                        <tr class="payment-row">
                            <td></td>
                            <td class="item-name">{{ __('repair-business.payment') }}</td>
                            <td>
                                <p class="item-desc" style="text-transform:uppercase;font-weight:600;color:#475569;">{{ $payment->method }}</p>
                                <p class="item-desc">{{ date('M d, Y h:iA', strtotime($payment->created_at)) }}</p>
                                @if (isset($payment->ref) && $payment->ref)
                                    <p class="item-desc">{{ __('repair-business.ref') }}: {{ $payment->ref }}</p>
                                @endif
                            </td>
                            <td class="num muted">&mdash;</td>
                            <td></td>
                            <td class="num sum-value">$ {{ number_format((float) $payment->amount, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="sum-row top">
                    <td colspan="4" class="sum-label">{{ __('repair-business.subtotal') }}</td>
                    <td></td>
                    <td class="sum-value">$ {{ number_format((float) $invoice->subtotal, 2, '.', ',') }}</td>
                </tr>
                <tr class="sum-row">
                    <td colspan="4" class="sum-label">{{ __('repair-business.tax') }} ({{ number_format((float) $invoice->tax_porcentage, 2, '.', ',') }}%)</td>
                    <td></td>
                    <td class="sum-value">$ {{ number_format((float) $invoice->tax, 2, '.', ',') }}</td>
                </tr>
                <tr class="sum-row grand-row">
                    <td colspan="4" class="sum-label">{{ __('repair-business.total') }}</td>
                    <td></td>
                    <td class="sum-value">$ {{ number_format((float) $invoice->total, 2, '.', ',') }}</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="5" class="balance-label">{{ __('repair-business.balance') }}</td>
                    <td class="balance-figure" style="color:{{ $balance_color }};">$ {{ number_format($balance, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="terms">
            <p class="terms-label">{{ __('repair-business.terms') }}</p>
            <p class="terms-text">{!! $terms !!}</p>
        </div>

        <div class="qrcode">
            @php echo DNS2D::getBarcodeSVG(route('view-invoice', $invoice->id), 'QRCODE', 4, 4); @endphp
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>