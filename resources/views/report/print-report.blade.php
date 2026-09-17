<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} - {{ __('repair-business.report') }}</title>

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

        .muted {
            color: #64748b;
        }

        .small {
            font-size: 10px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
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
            padding-bottom: 16px;
            vertical-align: top;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: #0f172a;
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

        .doc-meta {
            font-size: 10px;
            color: #64748b;
            margin: 4px 0 0;
            padding: 0;
            text-align: right;
        }

        .hr {
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 8px;
        }

        /* ---------- sections ---------- */
        .section {
            margin-top: 24px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #475569;
            margin: 0 0 12px;
            padding: 0;
        }

        .stat-strip {
            width: 100%;
            margin-bottom: 12px;
        }

        .stat-strip td {
            padding: 9px 12px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 4px;
            font-size: 11px;
            color: #64748b;
        }

        .stat-strip .stat-value {
            font-weight: bold;
            color: #0f172a;
        }

        .stat-strip .stat-sub {
            font-size: 9px;
            color: #94a3b8;
        }

        .no-data {
            background: #fafbfc;
            border: 1px dashed #e2e8f0;
            border-radius: 4px;
            color: #94a3b8;
            font-size: 11px;
            text-align: center;
            padding: 18px 10px;
        }

        /* ---------- data tables ---------- */
        .data-table th {
            background: #f8fafc;
            color: #64748b;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 9px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table td {
            padding: 9px 8px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #475569;
        }

        .data-table tr.alt td {
            background: #fafbfc;
        }

        .data-table .num {
            text-align: right;
            white-space: nowrap;
        }

        .data-table .sub-line {
            font-size: 10px;
            color: #94a3b8;
            margin: 0;
            padding: 0;
        }

        .data-table .cell-strong {
            font-weight: 600;
            color: #1e293b;
        }

        /* ---------- soft badges ---------- */
        .badge-inline {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 8.5px;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* ---------- footer ---------- */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
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
        $fmt = function ($v) {
            return number_format((float) $v, 2, '.', ',');
        };
    @endphp

    <div class="card">
        <table class="header-row">
            <tr>
                <td>
                    <div class="company-name">{{ config('app.name', 'Laravel') }}</div>
                </td>
                <td style="width:42%;">
                    <p class="doc-label">{{ __('repair-business.report') }}</p>
                    <p class="doc-meta">
                        {{ __('repair-business.from') }}: {{ date('M d, Y', strtotime($report_data['from'])) }}
                        &nbsp;&middot;&nbsp;
                        {{ __('repair-business.to') }}: {{ date('M d, Y', strtotime($report_data['to'])) }}
                    </p>
                    <p class="doc-meta">{{ date('M d, Y') }}</p>
                </td>
            </tr>
        </table>
        <div class="hr"></div>

        @if ($report_data['invoices'] == 'on')
            <div class="section">
                <p class="section-title">{{ __('repair-business.invoices') }}</p>
                @if (!$invoices->isEmpty())
                    <table class="stat-strip">
                        <tr>
                            <td class="center">{{ __('repair-business.invoices') }}<br><span class="stat-value">{{ $invoice_data['count'] }}</span></td>
                            <td class="center">{{ __('repair-business.unpaid-amount') }}<br><span class="stat-value">$ {{ $fmt($invoice_data['balance']) }}</span></td>
                            <td class="center">{{ __('repair-business.earnings') }}<br><span class="stat-value">$ {{ $fmt($invoice_data['total'] - $invoice_data['balance']) }}</span></td>
                        </tr>
                    </table>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:30px;">{{ __('repair-business.table_id') }}</th>
                                <th>{{ __('repair-business.table_customer') }}</th>
                                <th>{{ __('repair-business.table_items') }}</th>
                                <th>{{ __('repair-business.table_status') }}</th>
                                <th class="num" style="width:90px;">{{ __('repair-business.table_balance') }}</th>
                                <th class="num" style="width:90px;">{{ __('repair-business.table_total') }}</th>
                                <th style="width:80px;">{{ __('repair-business.table_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                @php $ivb = $invoice->status_data ? ($badges[$invoice->status_data->color] ?? $badges['secondary']) : null; @endphp
                                <tr class="{{ $loop->iteration % 2 === 0 ? 'alt' : '' }}">
                                    <td>{{ $invoice->id }}</td>
                                    <td>
                                        <div class="cell-strong">{{ $invoice->customer_name }}</div>
                                        <p class="sub-line">{{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $invoice->customer_phone) }}</p>
                                        <p class="sub-line">{{ $invoice->customer_email }}</p>
                                    </td>
                                    <td>
                                        @foreach ($invoice->items as $item)
                                            <div>{{ $item->name }}</div>
                                            @if ($item->description)
                                                <p class="sub-line">{{ $item->description }}</p>
                                            @endif
                                            @if ($item->sub_description)
                                                <p class="sub-line">{{ $item->sub_description }}</p>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($ivb)
                                            <span class="badge-inline" style="background:{{ $ivb['background'] }};color:{{ $ivb['color'] }};">{{ $invoice->status_data->name }}</span>
                                        @endif
                                    </td>
                                    <td class="num" style="color:{{ $invoice->balance < 0 ? '#15803d' : ($invoice->balance > 0 ? '#b91c1c' : '#1e293b') }};">$ {{ $fmt($invoice->balance) }}</td>
                                    <td class="num">$ {{ $fmt($invoice->total) }}</td>
                                    <td>{{ date('M d, Y', strtotime($invoice->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">{{ __('repair-business.no-information-to-show') }}</div>
                @endif
            </div>
        @endif

        @if ($report_data['repairs'] == 'on')
            <div class="section">
                <p class="section-title">{{ __('repair-business.repairs') }}</p>
                @if (!$repairs->isEmpty())
                    <table class="stat-strip">
                        <tr>
                            <td class="center">{{ __('repair-business.repairs') }}<br><span class="stat-value">{{ $repair_data['count'] }}</span></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:30px;">{{ __('repair-business.table_id') }}</th>
                                <th>{{ __('repair-business.table_customer') }}</th>
                                <th>{{ __('repair-business.table_target') }}</th>
                                <th>{{ __('repair-business.table_request') }}</th>
                                <th>{{ __('repair-business.table_status') }}</th>
                                <th>{{ __('repair-business.table_priority') }}</th>
                                <th style="width:80px;">{{ __('repair-business.table_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repairs as $repair)
                                @php
                                    $rsb = $repair->status_data ? ($badges[$repair->status_data->color] ?? $badges['secondary']) : null;
                                    $rpb = $repair->priority_data ? ($badges[$repair->priority_data->color] ?? $badges['secondary']) : null;
                                @endphp
                                <tr class="{{ $loop->iteration % 2 === 0 ? 'alt' : '' }}">
                                    <td>{{ $repair->id }}</td>
                                    <td>
                                        @if ($repair->customer_data)
                                            <div class="cell-strong">{{ $repair->customer_data->first_name }} {{ $repair->customer_data->last_name }}</div>
                                            <p class="sub-line">{{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone) }}</p>
                                            <p class="sub-line">{{ $repair->customer_data->email }}</p>
                                        @endif
                                    </td>
                                    <td>{{ $repair->target }}</td>
                                    <td>{{ $repair->request }}</td>
                                    <td>
                                        @if ($rsb)
                                            <span class="badge-inline" style="background:{{ $rsb['background'] }};color:{{ $rsb['color'] }};">{{ $repair->status_data->name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rpb)
                                            <span class="badge-inline" style="background:{{ $rpb['background'] }};color:{{ $rpb['color'] }};">{{ $repair->priority_data->name }}</span>
                                        @endif
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($repair->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">{{ __('repair-business.no-information-to-show') }}</div>
                @endif
            </div>
        @endif

        @if ($report_data['payments'] == 'on')
            <div class="section">
                <p class="section-title">{{ __('repair-business.payments') }}</p>
                @if (!$payments->isEmpty())
                    <table class="stat-strip">
                        <tr>
                            <td class="center">{{ __('repair-business.payments') }}<br><span class="stat-value">{{ $payment_data['count'] }}</span></td>
                            <td class="center">{{ __('repair-business.total') }}<br><span class="stat-value">$ {{ $fmt($payment_data['total']) }}</span></td>
                            <td class="center">{{ __('repair-business.breakdown') }}<br>
                                <span class="stat-sub">
                                    {{ __('repair-business.cash') }}: $ {{ $fmt($payment_data['total_cash']) }} &middot;
                                    {{ __('repair-business.card') }}: $ {{ $fmt($payment_data['total_card']) }}
                                </span>
                                <br>
                                <span class="stat-sub">
                                    {{ __('repair-business.check') }}: $ {{ $fmt($payment_data['total_check']) }} &middot;
                                    {{ __('repair-business.other') }}: $ {{ $fmt($payment_data['total_other']) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:30px;">{{ __('repair-business.table_id') }}</th>
                                <th style="width:60px;">{{ __('repair-business.table_invoice') }}</th>
                                <th class="num" style="width:90px;">{{ __('repair-business.table_amount') }}</th>
                                <th>{{ __('repair-business.table_method') }}</th>
                                <th>{{ __('repair-business.table_reference') }}</th>
                                <th style="width:80px;">{{ __('repair-business.table_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr class="{{ $loop->iteration % 2 === 0 ? 'alt' : '' }}">
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->invoice ?? '' }}</td>
                                    <td class="num">$ {{ $fmt($payment->amount) }}</td>
                                    <td class="cell-strong" style="text-transform:uppercase;">{{ $payment->method }}</td>
                                    <td>{{ $payment->ref }}</td>
                                    <td>{{ date('M d, Y', strtotime($payment->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">{{ __('repair-business.no-information-to-show') }}</div>
                @endif
            </div>
        @endif

        <div class="footer">
            {{ config('app.name', 'Laravel') }} &middot; {{ __('repair-business.report') }} &middot;
            {{ date('M d, Y') }}
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>