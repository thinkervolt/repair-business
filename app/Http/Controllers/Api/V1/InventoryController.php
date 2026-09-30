<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\Setting;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class InventoryController extends Controller
{
    use ApiResponses;

    protected function productStock(InventoryProduct $product)
    {
        $purchases = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'purchase')->sum('quantity');
        $sells = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'sell')->sum('quantity');
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

    /* CATEGORIES */

    public function categoriesIndex()
    {
        return $this->success(InventoryCategory::orderBy('created_at', 'DESC')->paginate(25));
    }

    public function categoriesCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category = new InventoryCategory;
        $inventory_category->name = $data['name'];
        $inventory_category->save();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Created';
        $log->ref = $inventory_category->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_category, Lang::get('repair-business.error_inventory-category-has-been-created'), 201);
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $inventory_category = InventoryCategory::find($id);

        if (!$inventory_category) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category->name = $data['name'];
        $inventory_category->save();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Updated';
        $log->ref = $inventory_category->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_category, Lang::get('repair-business.error_inventory-category-has-been-updated'));
    }

    public function categoriesDelete($id)
    {
        $inventory_category = InventoryCategory::find($id);

        if (!$inventory_category) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $inventory_category->delete();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_inventory-category-has-been-deleted'));
    }

    /* PRODUCTS */

    public function productsIndex(Request $request)
    {
        $search = $request->search;
        $barcode = $search;

        $category_search = InventoryCategory::select('id')->where('name', 'LIKE', '%' . $search . '%');

        $query = InventoryProduct::where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('barcode', 'LIKE', '%' . $barcode . '%')
            ->orWhereIn('category_id', $category_search);

        $products = $query->orderBy('created_at', 'DESC')->paginate(25);

        $products->getCollection()->transform(function ($product) {
            $product->stock = $this->productStock($product);
            return $product;
        });

        return $this->success([
            'products' => $products,
            'categories' => InventoryCategory::orderBy('name', 'ASC')->get(),
        ]);
    }

    public function productsCreate(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'nullable|unique:inventory_products,barcode',
            'purchase_price' => 'required|numeric|between:-99999.99,99999.99',
            'selling_price' => 'required|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'supplier' => 'nullable|min:2|max:50',
            'min_stock' => 'nullable|numeric|between:0,99999',
            'max_stock' => 'nullable|numeric|between:0,99999',
            'email_alert' => 'required|alpha|min:2|max:3',
        ]);

        $inventory_product = new InventoryProduct;
        $inventory_product->name = $data['name'];
        $inventory_product->category_id = $data['category'];
        $inventory_product->barcode = $data['barcode'] ?? null;
        $inventory_product->min_stock = $data['min_stock'] ?? null;
        $inventory_product->max_stock = $data['max_stock'] ?? null;
        $inventory_product->email_alert = $data['email_alert'];
        $inventory_product->supplier = $data['supplier'] ?? null;
        $inventory_product->selling_price = $data['selling_price'];
        $inventory_product->save();

        $inventory_transaction = new InventoryTransaction;
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->product_id = $inventory_product->id;
        $inventory_transaction->purchase_price = $data['purchase_price'];
        $inventory_transaction->quantity = $data['quantity'];
        $inventory_transaction->save();

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Created';
        $log->ref = $inventory_product->id;
        $log->user = Auth::id();
        $log->save();

        $inventory_product->stock = $this->productStock($inventory_product);

        Notification::syncProductStock($inventory_product);

        return $this->success($inventory_product, Lang::get('repair-business.error_inventory-product-and-transaction-has-been-created'), 201);
    }

    public function productsShow($id)
    {
        $product = InventoryProduct::with('category', 'transactions')->find($id);

        if (!$product) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $transactions = InventoryTransaction::where('product_id', $id)->orderBy('created_at', 'DESC')->paginate(25);

        return $this->success([
            'product' => $product,
            'stock' => $this->productStock($product),
            'categories' => InventoryCategory::orderBy('name', 'ASC')->get(),
            'transactions' => $transactions,
        ]);
    }

    public function productsUpdate(Request $request, $id)
    {
        $inventory_product = InventoryProduct::find($id);

        if (!$inventory_product) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'nullable|unique:inventory_products,barcode,' . $inventory_product->id,
            'min_stock' => 'nullable|numeric|between:0,99999',
            'max_stock' => 'nullable|numeric|between:0,99999',
            'email_alert' => 'required|alpha|min:2|max:3',
            'supplier' => 'nullable|min:2|max:50',
            'selling_price' => 'required|numeric|between:-99999.99,99999.99',
        ]);

        $inventory_product->name = $data['name'];
        $inventory_product->category_id = $data['category'];
        $inventory_product->barcode = $data['barcode'] ?? null;
        $inventory_product->min_stock = $data['min_stock'] ?? null;
        $inventory_product->max_stock = $data['max_stock'] ?? null;
        $inventory_product->email_alert = $data['email_alert'];
        $inventory_product->supplier = $data['supplier'] ?? null;
        $inventory_product->selling_price = $data['selling_price'];
        $inventory_product->save();

        Notification::syncProductStock($inventory_product);

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Updated';
        $log->ref = $inventory_product->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_product, Lang::get('repair-business.error_inventory-product-has-been-updated'));
    }

    public function productsDelete($id)
    {
        $product = InventoryProduct::find($id);

        if (!$product) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        InventoryTransaction::where('product_id', $product->id)->delete();

        Notification::clearFor('view-product', $product->id);

        $product->delete();

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product and Transactions have been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_product-and-transactions-have-been-deleted'));
    }

    /* TRANSACTIONS */

    public function transactionsIndex(Request $request)
    {
        $product_search = InventoryProduct::select('id')
            ->where('name', 'LIKE', '%' . $request->search . '%')
            ->orWhere('supplier', 'LIKE', '%' . $request->search . '%');

        $search = InventoryTransaction::select('id')
            ->where('transaction', 'LIKE', '%' . $request->search . '%')
            ->orWhere('invoice_id', 'LIKE', '%' . $request->search . '%')
            ->orWhereIn('product_id', $product_search);

        $transactions = InventoryTransaction::with('product')
            ->whereIn('id', $search)
            ->orderBy('created_at', 'DESC')
            ->paginate(25);

        return $this->success($transactions);
    }

    public function transactionsShow($id)
    {
        $transaction = InventoryTransaction::with('product')->find($id);

        if (!$transaction) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        return $this->success($transaction);
    }

    public function transactionsUpdate(Request $request, $id)
    {
        $inventory_transaction = InventoryTransaction::find($id);

        if (!$inventory_transaction) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'purchase_price' => 'nullable|numeric|between:-99999.99,99999.99',
            'selling_price' => 'nullable|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'transaction' => 'required',
        ]);

        $inventory_transaction->transaction = $data['transaction'];
        $inventory_transaction->purchase_price = $data['purchase_price'] ?? null;
        $inventory_transaction->selling_price = $data['selling_price'] ?? null;
        $inventory_transaction->quantity = $data['quantity'];
        $inventory_transaction->save();

        $product = InventoryProduct::find($inventory_transaction->product_id);
        if ($product) {
            Notification::syncProductStock($product);
        }

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Updated';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_transaction, Lang::get('repair-business.error_inventory-transaction-has-been-updated'));
    }

    public function transactionsDelete($id)
    {
        $transaction = InventoryTransaction::find($id);

        if (!$transaction) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $transaction->delete();

        $product = InventoryProduct::find($transaction->product_id);
        if ($product) {
            Notification::syncProductStock($product);
        }

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory ransactions has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_transaction-has-been-deleted'));
    }

    /* STOCK ACTIONS */

    public function restock(Request $request, $id)
    {
        $product = InventoryProduct::find($id);

        if (!$product) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'purchase_price' => 'required|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);

        $inventory_transaction = new InventoryTransaction;
        $inventory_transaction->product_id = $id;
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->purchase_price = $data['purchase_price'];
        $inventory_transaction->quantity = $data['quantity'];
        $inventory_transaction->save();

        Notification::syncProductStock($product);

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_transaction, Lang::get('repair-business.error_transaction-has-been-created'), 201);
    }

    public function sellOnRepair($repairId, $productId)
    {
        $product = InventoryProduct::find($productId);
        $repair = Repair::find($repairId);

        if (!$product || !$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ($this->productStock($product) <= 0) {
            return $this->error(Lang::get('repair-business.error_product-is-out-of-stock'), 422);
        }

        $check_transaction = InventoryTransaction::where('product_id', $productId)->where('repair_id', $repairId)->first();

        if ($check_transaction) {
            $inventory_transaction = $check_transaction;
            $inventory_transaction->quantity = $inventory_transaction->quantity + 1;
            $inventory_transaction->save();
        } else {
            $inventory_transaction = new InventoryTransaction;
            $inventory_transaction->product_id = $productId;
            $inventory_transaction->repair_id = $repairId;
            $inventory_transaction->transaction = 'sell';
            $inventory_transaction->selling_price = $product->selling_price;
            $inventory_transaction->quantity = 1;
            $inventory_transaction->save();
        }

        Notification::syncProductStock($product);

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_transaction, Lang::get('repair-business.transaction-has-been-created'), 201);
    }

    public function sellOnInvoice(Request $request, $invoiceId, $productId)
    {
        $quantity = $request->has('quantity') ? $request->quantity : 1;

        $product = InventoryProduct::find($productId);
        $invoice = Invoice::find($invoiceId);

        if (!$product || !$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ($this->productStock($product) < $quantity) {
            return $this->error(Lang::get('repair-business.error_product-is-out-of-stock'), 422);
        }

        $check_transaction = InventoryTransaction::where('product_id', $productId)->where('invoice_id', $invoiceId)->first();

        if ($check_transaction) {
            $inventory_transaction = $check_transaction;
            $inventory_transaction->quantity = $inventory_transaction->quantity + $quantity;
            $inventory_transaction->save();
        } else {
            $inventory_transaction = new InventoryTransaction;
            $inventory_transaction->product_id = $productId;
            $inventory_transaction->invoice_id = $invoiceId;
            $inventory_transaction->transaction = 'sell';
            $inventory_transaction->selling_price = $product->selling_price;
            $inventory_transaction->quantity = $quantity;
            $inventory_transaction->save();
        }

        $this->recomputeInvoice($invoice);

        Notification::syncProductStock($product);
        Notification::syncInvoiceBalance($invoice);

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($inventory_transaction, Lang::get('repair-business.error_transaction-has-been-created'), 201);
    }

    public function cancelTransaction($task, $id, $transactionId)
    {
        $transaction = InventoryTransaction::find($transactionId);

        if (!$transaction) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $transaction->delete();

        $product = InventoryProduct::find($transaction->product_id);
        if ($product) {
            Notification::syncProductStock($product);
        }

        if ($task === 'invoice') {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->error(Lang::get('repair-business.error_not-found'), 404);
            }
            $this->recomputeInvoice($invoice);
            Notification::syncInvoiceBalance($invoice);
        }

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Deleted';
        $log->ref = $transactionId;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_transaction-has-been-deleted'));
    }

    public function quickSell($productId)
    {
        $product = InventoryProduct::find($productId);

        if (!$product) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ($this->productStock($product) <= 0) {
            return $this->error(Lang::get('repair-business.error_product-is-out-of-stock'), 422);
        }

        $business_profile_settings = Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }

        $invoice = new Invoice;
        $invoice->company_name = $company_profile->name ?? null;
        $invoice->company_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone ?? '') ?: null;
        $invoice->company_email = $company_profile->email ?? null;
        $invoice->company_address = $company_profile->email ?? null;
        $invoice->tax_porcentage = 0;
        $invoice->subtotal = 0;
        $invoice->tax = 0;
        $invoice->total = 0;
        $invoice->balance = 0;
        $invoice->active = 'yes';
        $invoice->user = Auth::id();
        $invoice->save();

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        $inventory_transaction = new InventoryTransaction;
        $inventory_transaction->product_id = $product->id;
        $inventory_transaction->invoice_id = $invoice->id;
        $inventory_transaction->transaction = 'sell';
        $inventory_transaction->selling_price = $product->selling_price;
        $inventory_transaction->quantity = 1;
        $inventory_transaction->save();

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::id();
        $log->save();

        $payment = new Payment;
        $payment->amount = $product->selling_price;
        $payment->method = 'cash';
        $payment->ref = 'Quick Sell Transaction';
        $payment->active = 'yes';
        $payment->invoice = $invoice->id;
        $payment->save();

        $this->recomputeInvoice($invoice);

        Notification::syncProductStock($product);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Payment has been Created [$' . $product->selling_price . '][cash][Quick Sell Transaction]';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['invoice_id' => $invoice->id], Lang::get('repair-business.transaction-has-been-created'), 201);
    }
}