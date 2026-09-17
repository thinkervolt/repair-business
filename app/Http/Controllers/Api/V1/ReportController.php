<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Repair;
use App\Traits\ApiResponses;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ReportController extends Controller
{
    use ApiResponses;

    protected function invoiceReport($from, $to)
    {
        $query = Invoice::where('active', 'yes')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $data = [
            'total' => (float) $query->clone()->sum('total'),
            'balance' => (float) $query->clone()->sum('balance'),
            'count' => $query->clone()->count(),
        ];
        $data['earnings'] = $data['total'] - $data['balance'];
        $data['items'] = Invoice::with(['status_data', 'items'])
            ->where('active', 'yes')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        return $data;
    }

    protected function repairReport($from, $to)
    {
        $repairs = Repair::with(['customer_data', 'status_data', 'priority_data'])
            ->where('active', 'yes')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        return [
            'count' => $repairs->count(),
            'items' => $repairs,
        ];
    }

    protected function paymentReport($from, $to)
    {
        $payments = Payment::where('active', 'yes')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        return [
            'count' => $payments->count(),
            'total' => (float) $payments->sum('amount'),
            'total_cash' => (float) $payments->where('method', 'cash')->sum('amount'),
            'total_card' => (float) $payments->where('method', 'card')->sum('amount'),
            'total_check' => (float) $payments->where('method', 'check')->sum('amount'),
            'total_other' => (float) $payments->where('method', 'other')->sum('amount'),
            'items' => $payments,
        ];
    }

    protected function registerTotals($date, $cashRegister, $cardRegister)
    {
        $payments = Payment::where('active', 'yes')->whereDate('created_at', '=', $date)->get();

        $totalCash = (float) $payments->where('method', 'cash')->sum('amount');
        $totalCard = (float) $payments->where('method', 'card')->sum('amount');

        return [
            'date' => $date,
            'count' => $payments->count(),
            'total' => (float) $payments->sum('amount'),
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_check' => (float) $payments->where('method', 'check')->sum('amount'),
            'total_other' => (float) $payments->where('method', 'other')->sum('amount'),
            'cash_register' => (float) $cashRegister,
            'card_register' => (float) $cardRegister,
            'cash_diff' => (float) $cashRegister - $totalCash,
            'card_diff' => (float) $cardRegister - $totalCard,
            'items' => $payments,
        ];
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'invoices' => 'boolean',
            'repairs' => 'boolean',
            'payments' => 'boolean',
        ]);

        $withInvoices = $request->boolean('invoices');
        $withRepairs = $request->boolean('repairs');
        $withPayments = $request->boolean('payments');

        return $this->success([
            'report' => [
                'from' => $data['from'],
                'to' => $data['to'],
                'invoices' => $withInvoices,
                'repairs' => $withRepairs,
                'payments' => $withPayments,
            ],
            'invoices' => $withInvoices ? $this->invoiceReport($data['from'], $data['to']) : null,
            'repairs' => $withRepairs ? $this->repairReport($data['from'], $data['to']) : null,
            'payments' => $withPayments ? $this->paymentReport($data['from'], $data['to']) : null,
        ]);
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'invoices' => 'boolean',
            'repairs' => 'boolean',
            'payments' => 'boolean',
        ]);

        $invoices = $request->boolean('invoices');
        $repairs = $request->boolean('repairs');
        $payments = $request->boolean('payments');

        $report_data = [
            'from' => $data['from'],
            'to' => $data['to'],
            'invoices' => $invoices ? 'on' : 'off',
            'repairs' => $repairs ? 'on' : 'off',
            'payments' => $payments ? 'on' : 'off',
        ];

        $invoice_data = $this->invoiceReport($data['from'], $data['to']);
        $repair_data = $this->repairReport($data['from'], $data['to']);
        $payment_data = $this->paymentReport($data['from'], $data['to']);

        $invoices = $invoice_data['items'];
        $repairs = $repair_data['items'];
        $payments = $payment_data['items'];

        $pdf = Pdf::loadView('report.print-report', compact(
            'report_data',
            'invoice_data',
            'repair_data',
            'payment_data',
            'invoices',
            'repairs',
            'payments'
        ))->setOptions(['defaultFont' => 'sans-serif'])->setPaper('letter', 'portrait');

        return $pdf->stream('report-' . $data['from'] . '-' . $data['to'] . '.pdf');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'cash' => 'nullable|numeric',
            'card' => 'nullable|numeric',
        ]);

        $totals = $this->registerTotals($data['date'], $data['cash'] ?? 0, $data['card'] ?? 0);

        return $this->success($totals);
    }

    public function registerInsert(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'cash' => 'nullable|numeric',
            'card' => 'nullable|numeric',
        ]);

        $cash = $data['cash'] ?? 0;
        $card = $data['card'] ?? 0;

        if ($cash != 0) {
            $cash_payment = new Payment;
            $cash_payment->amount = $cash;
            $cash_payment->method = 'cash';
            $cash_payment->ref = 'NON-INVOICED-CASH-TRANSACTIONS-' . $data['date'];
            $cash_payment->active = 'yes';
            $cash_payment->created_at = $data['date'] . ' 00:00:00';
            $cash_payment->save();

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$' . $cash . '][cash][NON-INVOICED-CASH-TRANSACTIONS-' . $data['date'] . ']';
            $log->ref = $cash_payment->id;
            $log->user = Auth::id();
            $log->save();
        }

        if ($card != 0) {
            $card_payment = new Payment;
            $card_payment->amount = $card;
            $card_payment->method = 'card';
            $card_payment->ref = 'NON-INVOICED-CARD-TRANSACTIONS-' . $data['date'];
            $card_payment->active = 'yes';
            $card_payment->created_at = $data['date'] . ' 00:00:00';
            $card_payment->save();

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$' . $card . '][cash][NON-INVOICED-CARD-TRANSACTIONS-' . $data['date'] . ']';
            $log->ref = $card_payment->id;
            $log->user = Auth::id();
            $log->save();
        }

        return $this->success(null, Lang::get('repair-business.error_payments-have-been-created'));
    }
}
