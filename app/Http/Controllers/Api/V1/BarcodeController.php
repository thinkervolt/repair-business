<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Repair;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarcodeController extends Controller
{
    use ApiResponses;

    protected function productStock($productId)
    {
        $purchases = InventoryTransaction::where('product_id', $productId)->where('transaction', 'purchase')->sum('quantity');
        $sells = InventoryTransaction::where('product_id', $productId)->where('transaction', 'sell')->sum('quantity');
        return (int)$purchases - (int)$sells;
    }

    protected function recomputeInvoice(Invoice $invoice)
    {
        $transactions_sum = 0;
        $transactions = InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = Payment::where('invoice', $invoice->id)->sum('amount');

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) * (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) * (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) * (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();
    }

    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required',
        ]);

        $barcode = $request->barcode;

        $product = InventoryProduct::where('barcode', $barcode)->first();

        if ($product) {
            return $this->success([
                'response' => 'product-found',
                'data' => $product,
                'data_response' => $this->productStock($product->id),
            ]);
        }

        if (str_starts_with($barcode, 'INV')) {
            $invoice_id = str_replace('INV', '', $barcode);
            $invoice = Invoice::where('id', $invoice_id)->first();

            if ($invoice) {
                return $this->success([
                    'response' => 'invoice-found',
                    'data' => $invoice,
                    'data_response' => null,
                ]);
            }

            return $this->success([
                'response' => 'barcode-not-found',
                'data' => null,
                'data_response' => $barcode,
            ]);
        }

        if (str_starts_with($barcode, 'REP')) {
            $repair_id = str_replace('REP', '', $barcode);
            $repair = Repair::where('id', $repair_id)->first();

            if ($repair) {
                return $this->success([
                    'response' => 'repair-found',
                    'data' => $repair,
                    'data_response' => null,
                ]);
            }
        }

        return $this->success([
            'response' => 'barcode-not-found',
            'data' => null,
            'data_response' => $barcode,
        ]);
    }

    public function scanInvoice(Request $request)
    {
        $data = $request->validate([
            'invoice' => 'required|numeric',
            'barcode' => 'required',
        ]);

        $invoice = Invoice::where('id', $data['invoice'])->first();
        $product = InventoryProduct::where('barcode', $data['barcode'])->first();

        if (!$invoice) {
            return $this->success(['response' => 'invoice-not-found']);
        }

        if (!$product) {
            return $this->success(['response' => 'barcode-not-found', 'barcode' => $data['barcode']]);
        }

        if ($this->productStock($product->id) <= 0) {
            return $this->success([
                'response' => 'product-out-stock',
                'product' => ['id' => $product->id, 'name' => $product->name, 'barcode' => $product->barcode],
            ]);
        }

        $payments = Payment::where('invoice', $invoice->id)->where('active', 'yes');

        if ($payments->exists() && !$request->boolean('confirm')) {
            return $this->success([
                'response' => 'invoice-has-payments',
                'product' => ['id' => $product->id, 'name' => $product->name, 'barcode' => $product->barcode],
                'payments_count' => (clone $payments)->count(),
                'payments_total' => (float)$payments->sum('amount'),
            ]);
        }

        $inventory_transaction = DB::transaction(function () use ($product, $invoice) {
            $check_transaction = InventoryTransaction::where('product_id', $product->id)
                ->where('invoice_id', $invoice->id)
                ->where('transaction', 'sell')
                ->lockForUpdate()
                ->first();

            if ($check_transaction) {
                $check_transaction->quantity = $check_transaction->quantity + 1;
                $check_transaction->save();
                return $check_transaction;
            }

            $created = new InventoryTransaction;
            $created->product_id = $product->id;
            $created->invoice_id = $invoice->id;
            $created->transaction = 'sell';
            $created->selling_price = $product->selling_price;
            $created->quantity = 1;
            $created->save();
            return $created;
        });

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success([
            'response' => 'new-transaction-created',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'selling_price' => $product->selling_price,
            ],
            'transaction_id' => $inventory_transaction->id,
            'quantity' => $inventory_transaction->quantity,
            'stock' => $this->productStock($product->id),
        ]);
    }

    public function scanRepair(Request $request)
    {
        $data = $request->validate([
            'repair' => 'required|numeric',
            'barcode' => 'required',
        ]);

        $repair = Repair::where('id', $data['repair'])->first();
        $product = InventoryProduct::where('barcode', $data['barcode'])->first();

        if (!$product) {
            return $this->success(['response' => 'barcode-not-found']);
        }

        if ($this->productStock($product->id) <= 0) {
            return $this->success(['response' => 'part-out-stock']);
        }

        $check_transaction = InventoryTransaction::where('product_id', $product->id)->where('repair_id', $repair->id)->first();

        if ($check_transaction) {
            $inventory_transaction = $check_transaction;
            $inventory_transaction->quantity = $inventory_transaction->quantity + 1;
            $inventory_transaction->save();
        } else {
            $inventory_transaction = new InventoryTransaction;
            $inventory_transaction->product_id = $product->id;
            $inventory_transaction->repair_id = $repair->id;
            $inventory_transaction->transaction = 'sell';
            $inventory_transaction->selling_price = $product->selling_price;
            $inventory_transaction->quantity = 1;
            $inventory_transaction->save();
        }

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['response' => 'new-transaction-created']);
    }
}