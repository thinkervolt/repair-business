<?php

namespace App\Http\Controllers;
use App;
use Auth;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Lang;

use App\Models\InventoryCategory;
use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\Setting;


class InventoryController extends Controller
{
    public function inventory_index_category()
    {

        $inventory_categories = InventoryCategory::orderBy('created_at', 'DESC')->paginate(25);
        return view('inventory.index-category', compact('inventory_categories'));
    }

    public function inventory_create_category(request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category = new InventoryCategory();
        $inventory_category->name = $request->name;
        $inventory_category->save();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Created';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error', Lang::get('repair-business.error_inventory-category-has-been-created') )->with('alert', 'alert-success');
    }

    public function inventory_update_category(request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category =  InventoryCategory::findOrFail($id);
        $inventory_category->name = $request->name;
        $inventory_category->save();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Updated';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error', Lang::get('repair-business.error_inventory-category-has-been-updated') )->with('alert', 'alert-success');
    }

    public function inventory_delete_category($id)
    {


        $inventory_category =  InventoryCategory::findOrFail($id);
        $inventory_category->delete();

        $log = new Log;
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Deleted';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error',Lang::get('repair-business.error_inventory-category-has-been-deleted'))->with('alert', 'alert-danger');
    }


    public function inventory_index_product(request $request, $task = null, $id = null)
    {
        /* SYMBOL LS1208 BARCODE SCANNER (Slash Removal)*/
        /* $barcode = str_replace('/', '', $request->search); */

        $barcode = $request->search;

        $category_search = InventoryCategory::select('id')->where('name', 'LIKE', '%' . $request->search . '%');

        $search = InventoryProduct::select('id')->where('name', 'LIKE', '%' . $request->search . '%')
            ->orwhere('barcode', 'LIKE', '%' . $barcode  . '%')
            ->orwherein('category_id', $category_search);

        $inventory_products = InventoryProduct::whereIn('id', $search)->orderBy('created_at', 'DESC')->paginate(25);


        $inventory_categories = InventoryCategory::orderBy('name', 'ASC')->get();
        return view('inventory.index-product', compact('inventory_products', 'inventory_categories'))->with('search', $request->search)->with('task', $task)->with('id', $id);
    }



    public function inventory_create_product(request $request)
    {
        $request->validate([
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

        $inventory_product = new InventoryProduct();
        $inventory_product->name = $request->name;
        $inventory_product->category_id = $request->category;
        $inventory_product->barcode = $request->barcode;
        $inventory_product->min_stock = $request->min_stock;
        $inventory_product->max_stock = $request->max_stock;
        $inventory_product->email_alert = $request->email_alert;
        $inventory_product->supplier = $request->supplier;
        $inventory_product->selling_price = $request->selling_price;
        $inventory_product->save();


        $inventory_transaction = new InventoryTransaction();
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->product_id = $inventory_product->id;
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Created';
        $log->ref = $inventory_product->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error',Lang::get('repair-business.error_inventory-product-and-transaction-has-been-created'))->with('alert', 'alert-success');
    }

    public function inventory_view_product($id)
    {
        $product = InventoryProduct::findOrFail($id);
        $inventory_categories = InventoryCategory::orderBy('name', 'ASC')->get();
        $transactions = InventoryTransaction::where('product_id', $product->id)->orderBy('created_at', 'DESC')->paginate(25);
        return view('inventory.view-product', compact('product', 'inventory_categories', 'transactions'));
    }

    public function inventory_update_product(request $request, $id)
    {
        $inventory_product = InventoryProduct::findOrFail($id);

        $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'nullable|unique:inventory_products,barcode,' . $inventory_product->id,
            'min_stock' => 'nullable|numeric|between:0,99999',
            'max_stock' => 'nullable|numeric|between:0,99999',
            'email_alert' => 'required|alpha|min:2|max:3',
            'supplier' => 'nullable|min:2|max:50',
            'selling_price' => 'required|numeric|between:-99999.99,99999.99',
        ]);


        $inventory_product->name = $request->name;
        $inventory_product->category_id = $request->category;
        $inventory_product->barcode = $request->barcode;
        $inventory_product->min_stock = $request->min_stock;
        $inventory_product->max_stock = $request->max_stock;
        $inventory_product->email_alert = $request->email_alert;
        $inventory_product->supplier = $request->supplier;
        $inventory_product->selling_price = $request->selling_price;
        $inventory_product->save();

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Updated';
        $log->ref = $inventory_product->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error',Lang::get('repair-business.error_inventory-product-has-been-updated')  )->with('alert', 'alert-warning');
    }

    public function inventory_delete_product($id)
    {
        $product = InventoryProduct::findOrFail($id);
        $transactions = InventoryTransaction::where('product_id', $product->id);

        $product->delete();
        $transactions->delete();

        $log = new Log;
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product and Transactions have been Deleted';
        $log->ref = $product->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-index-product')->with('error',Lang::get('repair-business.error_product-and-transactions-have-been-deleted'))->with('alert', 'alert-danger');
    }

    public function inventory_index_transaction(request $request)
    {


        $product_search = InventoryProduct::select('id')
            ->where('name', 'LIKE', '%' . $request->search . '%')
            ->orwhere('supplier', 'LIKE', '%' . $request->search . '%');

        $search = InventoryTransaction::select('id')->where('transaction', 'LIKE', '%' . $request->search . '%')
            ->orwhere('invoice_id', 'LIKE', '%' . $request->search . '%')
            ->orwherein('product_id', $product_search);




        $transactions = InventoryTransaction::whereIn('id', $search)->orderBy('created_at', 'DESC')->paginate(25);

        return view('inventory.index-transaction', compact('transactions'))->with('search', $request->search);
    }

    public function inventory_view_transaction($id)
    {
        $transaction = InventoryTransaction::findOrFail($id);
        return view('inventory.view-transaction', compact('transaction'));
    }

    public function inventory_update_transaction(request $request, $id)
    {
        $request->validate([
            'purchase_price' => 'numeric|between:-99999.99,99999.99',
            'selling_price' => 'numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'transaction' => 'required',
        ]);

        $inventory_transaction = InventoryTransaction::findOrFail($id);
        $inventory_transaction->transaction = $request->transaction;
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->selling_price = $request->selling_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Updated';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error', Lang::get('repair-business.error_inventory-transaction-has-been-updated') )->with('alert', 'alert-warning');
    }

    public function inventory_delete_transaction($id)
    {
        $transaction = InventoryTransaction::findOrFail($id);

        $transaction->delete();

        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory ransactions has been Deleted';
        $log->ref = $transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-index-transaction')->with('error',Lang::get('repair-business.error_transaction-has-been-deleted'))->with('alert', 'alert-danger');
    }

    public function inventory_restock_transaction($id)
    {
        $product = InventoryProduct::findOrFail($id);
        return view('inventory.restock-transaction', compact('product'));
    }

    public function inventory_create_transaction(request $request, $id)
    {
        $request->validate([
            'purchase_price' => 'required|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);

        $inventory_transaction = new InventoryTransaction();
        $inventory_transaction->product_id = $id;
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new Log;
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-view-product', $id)->with('error',Lang::get('repair-business.error_transaction-has-been-created') )->with('alert', 'alert-success');
    }

    public function inventory_sell_transaction(request $request, $task, $id, $product_id)
    {

        if ($task == 'invoice') {

            if(isset($request->quantity)){

                $quantity = $request->quantity;
            }else{
                $quantity = 1;
            }



            $product = InventoryProduct::findOrFail($product_id);
            $invoice = Invoice::findOrFail($id);

            $purchases = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'purchase')->sum('quantity');
            $sells = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'sell')->sum('quantity');
            $stock = $purchases - $sells;

            if ($stock >= $quantity) {

                $check_transaction = InventoryTransaction::where('product_id', $product_id)->where('invoice_id', $id)->first();

                if ($check_transaction) {
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction->quantity + $quantity;
                    $inventory_transaction->save();
                } else {

                    $inventory_transaction = new InventoryTransaction();
                    $inventory_transaction->product_id = $product_id;
                    $inventory_transaction->invoice_id = $id;
                    $inventory_transaction->transaction = 'sell';
                    $inventory_transaction->selling_price = $product->selling_price;
                    $inventory_transaction->quantity = $quantity;
                    $inventory_transaction->save();
                }

                $transactions_sum = 0;
                $transactions = InventoryTransaction::where('invoice_id', $id)->get();
                foreach ($transactions as $transaction) {
                    $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
                }

                $items_sum = InvoiceItem::where('invoice', $id)->sum('total') + $transactions_sum;
                $payments_sum = Payment::where('invoice', $id)->sum('amount');

                $invoice->subtotal = (float)$items_sum;
                $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
                $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
                $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
                $invoice->save();

                $log = new Log;
                $log->table = 'inventory_transactions';
                $log->data = 'Inventory Transaction has been Created';
                $log->ref = $inventory_transaction->id;
                $log->user = Auth::user()->id;
                $log->save();

                return redirect()->route('view-invoice', $id)->with('error', Lang::get('repair-business.error_transaction-has-been-created'))->with('alert', 'alert-success');
            } else {

                return redirect()->route('view-invoice', $id)->with('error', Lang::get('repair-business.error_product-is-out-of-stock'))->with('alert', 'alert-danger');
            }
        }

        if ($task == 'repair') {

            $product = InventoryProduct::findOrFail($product_id);
            $repair = Repair::findOrFail($id);

            $purchases = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'purchase')->sum('quantity');
            $sells = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'sell')->sum('quantity');
            $stock = $purchases - $sells;

            if ($stock > 0) {

                $check_transaction = InventoryTransaction::where('product_id', $product_id)->where('repair_id', $id)->first();

                if ($check_transaction) {
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction->quantity + 1;
                    $inventory_transaction->save();
                } else {

                    $inventory_transaction = new InventoryTransaction();
                    $inventory_transaction->product_id = $product_id;
                    $inventory_transaction->repair_id = $id;
                    $inventory_transaction->transaction = 'sell';
                    $inventory_transaction->selling_price = $product->selling_price;
                    $inventory_transaction->quantity = 1;
                    $inventory_transaction->save();
                }

                $log = new Log;
                $log->table = 'inventory_transactions';
                $log->data = 'Inventory Transaction has been Created';
                $log->ref = $inventory_transaction->id;
                $log->user = Auth::user()->id;
                $log->save();

                return redirect()->route('view-repair', $id)->with('error', Lang::get('repair-business.transaction-has-been-created'))->with('alert', 'alert-success');
            } else {

                return redirect()->route('view-repair', $id)->with('error', Lang::get('repair-business.error_product-is-out-of-stock'))->with('alert', 'alert-danger');
            }
        }
    }

    public function inventory_cancel_transaction($task, $id, $transaction)
    {

        if ($task == 'invoice') {
            $invoice = Invoice::findOrFail($id);
            $transaction = InventoryTransaction::findOrFail($transaction);
            $transaction->delete();

            $transactions_sum = 0;
            $transactions = InventoryTransaction::where('invoice_id', $id)->get();
            foreach ($transactions as $transaction) {
                $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
            }

            $items_sum = InvoiceItem::where('invoice', $id)->sum('total') + $transactions_sum;
            $payments_sum = Payment::where('invoice', $id)->sum('amount');

            $invoice->subtotal = (float)$items_sum;
            $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
            $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
            $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
            $invoice->save();

            $log = new Log;
            $log->table = 'inventory_transactions';
            $log->data = 'Inventory Transaction has been Deleted';
            $log->ref = $transaction->id;
            $log->user = Auth::user()->id;
            $log->save();

            return redirect()->route('view-invoice', $id)->with('error',Lang::get('repair-business.error_transaction-has-been-deleted'))->with('alert', 'alert-danger');
        }

        if ($task == 'repair') {

            $transaction = InventoryTransaction::findOrFail($transaction);
            $transaction->delete();

            $log = new Log;
            $log->table = 'inventory_transactions';
            $log->data = 'Inventory Transaction has been Deleted';
            $log->ref = $transaction->id;
            $log->user = Auth::user()->id;
            $log->save();

            return redirect()->route('view-repair', $id)->with('error',Lang::get('repair-business.error_transaction-has-been-deleted'))->with('alert', 'alert-danger');
        }
    }


    public function inventory_quick_sell_transaction($product_id)
    {
        //STOCK CHECK
        $product = InventoryProduct::findOrFail($product_id);
        $purchases = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'purchase')->sum('quantity');
        $sells = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'sell')->sum('quantity');
        $stock = $purchases - $sells;

        //END STOCK CHECK

        if ($stock > 0) {

            //NEW INVOICE

            /* business-profile */
            $business_profile_settings = Setting::where('group', 'business_profile')->get();
            $company_profile = (object)[];
            foreach ($business_profile_settings as $setting) {
                $company_profile->{$setting->name} = $setting->data;
            }

            $invoice_tax_string = Setting::where('name', 'invoice_tax')->where('group', 'tax')->firstOrFail();
            $invoice_tax = (float)$invoice_tax_string->data;

            $invoice = new Invoice;
            $invoice->company_name = $company_profile->name;
            $invoice->company_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone);
            $invoice->company_email = $company_profile->email;
            $invoice->company_address = $company_profile->email;
            $invoice->tax_porcentage = 0;
            $invoice->subtotal = 0;
            $invoice->tax = 0;
            $invoice->total = 0;
            $invoice->balance = 0;
            $invoice->active = 'yes';
            $invoice->user = Auth::user()->id;
            $invoice->save();

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Invoice has been Created';
            $log->ref = $invoice->id;
            $log->user = Auth::user()->id;
            $log->save();

            //END NEW INVOICE

            $check_transaction = InventoryTransaction::where('product_id', $product_id)->where('invoice_id', $invoice->id)->first();

            if ($check_transaction) {
                $inventory_transaction = $check_transaction;
                $inventory_transaction->quantity = $inventory_transaction->quantity + 1;
                $inventory_transaction->save();
            } else {

                $inventory_transaction = new InventoryTransaction();
                $inventory_transaction->product_id = $product_id;
                $inventory_transaction->invoice_id = $invoice->id;
                $inventory_transaction->transaction = 'sell';
                $inventory_transaction->selling_price = $product->selling_price;
                $inventory_transaction->quantity = 1;
                $inventory_transaction->save();
            }

            $log = new Log;
            $log->table = 'inventory_transactions';
            $log->data = 'Inventory Transaction has been Created';
            $log->ref = $inventory_transaction->id;
            $log->user = Auth::user()->id;
            $log->save();

            // NEW PAYMENT

            $payment =  new Payment;
            $payment->amount = $product->selling_price;
            $payment->method = 'cash';
            $payment->ref = 'Quick Sell Transaction';
            $payment->active = 'yes';
            $payment->invoice = $invoice->id;
            $payment->save();

            $items_sum = InvoiceItem::where('invoice', $invoice->id)->sum('total');

            $transactions_sum = 0;
            $transactions = InventoryTransaction::where('invoice_id', $invoice->id)->get();
            foreach ($transactions as $transaction) {
                $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
            }

            $items_sum = InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
            $payments_sum = Payment::where('invoice', $invoice->id)->sum('amount');

            $invoice->subtotal = (float)$items_sum;
            $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
            $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
            $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
            $invoice->save();

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$' . $product->selling_pice . '][cash][Quick Sell Transaction]';
            $log->ref = $invoice->id;
            $log->user = Auth::user()->id;
            $log->save();

            //END NEW PAYMENT


            return redirect()->route('view-invoice', $invoice->id)->with('error',Lang::get('repair-business.transaction-has-been-created'))->with('alert', 'alert-success');
        } else {

            return back()->with('error', Lang::get('repair-business.error_product-is-out-of-stock'))->with('alert', 'alert-danger');
        }
    }
}
