<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InventoryTransaction;
use App\Models\InvoiceItem;
use App\Models\Log;
use App\Models\Payment;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class PaymentController extends Controller
{
    use ApiResponses;

    protected function rules(): array
    {
        return [
            'amount' => 'required|numeric|between:-99999.99,99999.99',
            'method' => 'required|min:2|max:250',
            'ref' => 'nullable|min:2|max:250',
        ];
    }

    protected function recomputeInvoice(Invoice $invoice)
    {
        $transactions_sum = 0;
        $transactions = InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum += (float)($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = (float)InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = (float)Payment::where('invoice', $invoice->id)->sum('amount');
        $tax_porcentage = (float)$invoice->tax_porcentage;

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) * $tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) * $tax_porcentage);
        $invoice->balance = (float)$invoice->total - $payments_sum;
        $invoice->save();
    }

    public function index(Request $request)
    {
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $ids = Payment::select('id')
                ->where('id', 'LIKE', $search)
                ->orWhere('invoice', 'LIKE', $search)
                ->orWhere('amount', 'LIKE', $search)
                ->orWhere('method', 'LIKE', $search)
                ->orWhere('ref', 'LIKE', $search);

            $payments = Payment::whereIn('id', $ids)
                ->where('active', 'yes')
                ->orderBy('created_at', 'DESC')
                ->paginate(25);
        } else {
            $payments = Payment::where('active', 'yes')
                ->orderBy('created_at', 'DESC')
                ->paginate(25);
        }

        return $this->success($payments);
    }

    public function store(Request $request, $id = null)
    {
        $data = $request->validate($this->rules());

        $payment = new Payment;
        $payment->amount = $data['amount'];
        $payment->method = $data['method'];
        $payment->ref = $data['ref'] ?? null;
        $payment->active = 'yes';

        if ($id === null) {
            $payment->save();

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$' . $data['amount'] . '][' . $data['method'] . '][' . ($data['ref'] ?? '') . ']';
            $log->ref = $payment->id;
            $log->user = Auth::id();
            $log->save();

            return $this->success(['payment' => $payment], Lang::get('repair-business.error_payment-has-been-created'), 201);
        }

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $payment->invoice = (int)$id;
        $payment->save();

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Payment has been Created [$' . $data['amount'] . '][' . $data['method'] . '][' . ($data['ref'] ?? '') . ']';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['payment' => $payment], Lang::get('repair-business.error_payment-has-been-created'), 201);
    }

    public function show($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $invoice = $payment->invoice ? Invoice::find($payment->invoice) : null;

        return $this->success([
            'payment' => $payment,
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate($this->rules());

        $log = new Log;
        $log->table = 'payments';
        $log->data = 'Payment has been Updated [FROM]' . $payment->amount . '[TO]' . $data['amount']
            . '[FROM]' . $payment->method . '[TO]' . $data['method']
            . '[FROM]' . $payment->ref . '[TO]' . ($data['ref'] ?? '');
        $log->ref = $payment->id;
        $log->user = Auth::id();
        $log->save();

        $payment->amount = $data['amount'];
        $payment->method = $data['method'];
        $payment->ref = $data['ref'] ?? null;
        $payment->save();

        if ($payment->invoice) {
            $invoice = Invoice::find($payment->invoice);
            if ($invoice) {
                $this->recomputeInvoice($invoice);
            }
        }

        return $this->success(['payment' => $payment], Lang::get('repair-business.error_payment-has-been-updated'));
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $log = new Log;
        $log->table = $payment->invoice ? 'invoices' : 'payments';
        $log->data = 'Payment has been Deleted [$' . $payment->amount . '][' . $payment->method . '][' . $payment->ref . ']';
        $log->ref = $payment->invoice ?: $payment->id;
        $log->user = Auth::id();
        $log->save();

        $invoice = $payment->invoice ? Invoice::find($payment->invoice) : null;

        $payment->delete();

        if ($invoice) {
            $this->recomputeInvoice($invoice);
        }

        return $this->success(null, Lang::get('repair-business.error_payment-has-been-deleted'));
    }
}