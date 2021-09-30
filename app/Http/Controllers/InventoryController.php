<?php

namespace App\Http\Controllers;
use App;
use Auth;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function inventory_index_category()
    {

        $inventory_categories = App\InventoryCategory::orderBy('created_at','DESC')->paginate(25); 
        return view('inventory.index-category',compact('inventory_categories'));
    }

    public function inventory_create_category(request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category = new App\InventoryCategory();
        $inventory_category->name = $request->name;
        $inventory_category->save();

        $log = new App\Log; 
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Created';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Category has been Created.')->with('alert', 'alert-success');

        
    }

    public function inventory_update_category(request $request , $id)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
        ]);

        $inventory_category =  App\InventoryCategory::findOrFail($id);
        $inventory_category->name = $request->name;
        $inventory_category->save();

        $log = new App\Log; 
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Updated';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Category has been Updated.')->with('alert', 'alert-success');

        
    }

    public function inventory_delete_category( $id)
    {
   

        $inventory_category =  App\InventoryCategory::findOrFail($id);
        $inventory_category->delete();

        $log = new App\Log; 
        $log->table = 'inventory_categories';
        $log->data = 'Inventory Category has been Deleted';
        $log->ref = $inventory_category->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Category has been Deleted.')->with('alert', 'alert-danger');

        
    }


    public function inventory_index_product(request $request, $task = null, $id = null)
    {

    
        $category_search = App\InventoryCategory::select('id')->where('name', 'LIKE', '%' . $request->search . '%');

        $search = App\InventoryProduct::select('id')->where('name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('barcode', 'LIKE', '%' . $request->search . '%')
        ->orwherein('category_id',$category_search);

        $inventory_products = App\InventoryProduct::whereIn('id',$search)->orderBy('created_at','DESC')->paginate(25); 


        $inventory_categories = App\InventoryCategory::orderBy('name','ASC')->get(); 
        return view('inventory.index-product',compact('inventory_products','inventory_categories'))->with('search',$request->search)->with('task',$task)->with('id',$id);
    }


    
    public function inventory_create_product(request $request)
    {
        $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'required',
            'purchase_price' => 'required|numeric|between:-99999.99,99999.99',
            'selling_price' => 'required|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'supplier' => 'nullable|min:2|max:50',
            'min_stock' => 'nullable|numeric|between:0,99999',
            'max_stock' => 'nullable|numeric|between:0,99999',
            'email_alert' => 'required|alpha|min:2|max:3',
        ]);

        $inventory_product = new App\InventoryProduct();
        $inventory_product->name = $request->name;
        $inventory_product->category_id = $request->category;
        $inventory_product->barcode = $request->barcode;
        $inventory_product->min_stock = $request->min_stock;
        $inventory_product->max_stock = $request->max_stock;
        $inventory_product->email_alert = $request->email_alert;
        $inventory_product->supplier = $request->supplier;
        $inventory_product->selling_price = $request->selling_price;
        $inventory_product->save();


        $inventory_transaction = new App\InventoryTransaction();
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->product_id = $inventory_product->id;
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        $log = new App\Log; 
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Created';
        $log->ref = $inventory_product->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Product and Transaction have been Created.')->with('alert', 'alert-success');

        
    }

    public function inventory_view_product($id)
    {
        $product = App\InventoryProduct::findOrFail($id); 
        $inventory_categories = App\InventoryCategory::orderBy('name','ASC')->get(); 
        $transactions = App\InventoryTransaction::where('product_id',$product->id)->orderBy('created_at','DESC')->paginate(25); 
        return view('inventory.view-product',compact('product','inventory_categories','transactions'));
    }

    public function inventory_update_product(request $request, $id)
    {
        $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'required',
            'min_stock' => 'nullable|numeric|between:0,99999',
            'max_stock' => 'nullable|numeric|between:0,99999',
            'email_alert' => 'required|alpha|min:2|max:3',
            'supplier' => 'nullable|min:2|max:50',
            'selling_price' => 'required|numeric|between:-99999.99,99999.99',
        ]);

        $inventory_product = App\InventoryProduct::findOrFail($id);
        $inventory_product->name = $request->name;
        $inventory_product->category_id = $request->category;
        $inventory_product->barcode = $request->barcode;
        $inventory_product->min_stock = $request->min_stock;
        $inventory_product->max_stock = $request->max_stock;
        $inventory_product->email_alert = $request->email_alert;
        $inventory_product->supplier = $request->supplier;
        $inventory_product->selling_price = $request->selling_price;
        $inventory_product->save();

        $log = new App\Log; 
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product has been Updated';
        $log->ref = $inventory_product->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Product has been Updated.')->with('alert', 'alert-warning');

        
    }

    public function inventory_delete_product($id)
    {
        $product = App\InventoryProduct::findOrFail($id); 
        $transactions = App\InventoryTransaction::where('product_id',$product->id); 

        $product->delete();
        $transactions->delete();

        $log = new App\Log; 
        $log->table = 'inventory_products';
        $log->data = 'Inventory Product and Transactions have been Deleted';
        $log->ref = $product->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-index-product')->with('error','Product and Transactions have been Deleted.')->with('alert', 'alert-danger');


    }

    public function inventory_index_transaction(request $request)
    {

            
        $product_search = App\InventoryProduct::select('id')
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('supplier', 'LIKE', '%' . $request->search . '%');

        $search = App\InventoryTransaction::select('id')->where('transaction', 'LIKE', '%' . $request->search . '%')
        ->orwhere('invoice_id', 'LIKE', '%' . $request->search . '%')
        ->orwherein('product_id',$product_search);




        $transactions = App\InventoryTransaction::whereIn('id',$search)->orderBy('created_at','DESC')->paginate(25); 

        return view('inventory.index-transaction',compact('transactions'))->with('search',$request->search);


    }

    public function inventory_view_transaction($id)
    {
        $transaction = App\InventoryTransaction::findOrFail($id); 
        return view('inventory.view-transaction',compact('transaction'));
    }

    public function inventory_update_transaction(request $request, $id)
    {
        $request->validate([
            'purchase_price' => 'numeric|between:-99999.99,99999.99',
            'selling_price' => 'numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'transaction' => 'required',
        ]);

        $inventory_transaction = App\InventoryTransaction::findOrFail($id);
        $inventory_transaction->transaction = $request->transaction;
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->selling_price = $request->selling_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Updated';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Transaction has been Updated.')->with('alert', 'alert-warning');

        
    }

    public function inventory_delete_transaction($id)
    {
        $transaction = App\InventoryTransaction::findOrFail($id); 

        $transaction->delete();

        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory ransactions has been Deleted';
        $log->ref = $transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-index-transaction')->with('error','Transactions has been Deleted.')->with('alert', 'alert-danger');


    }

    public function inventory_restock_transaction($id)
    {
        $product = App\InventoryProduct::findOrFail($id); 
        return view('inventory.restock-transaction',compact('product'));
    }

    public function inventory_create_transaction(request $request, $id)
    {
        $request->validate([
            'purchase_price' => 'required|numeric|between:-99999.99,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);

        $inventory_transaction = new App\InventoryTransaction();
        $inventory_transaction->product_id = $id;
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->save();


        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('inventory-view-product',$id)->with('error','Transactions has been Created.')->with('alert', 'alert-success');


        
    }

    public function inventory_sell_transaction($invoice_id,$product_id)
    {

        
        $product = App\InventoryProduct::findOrFail($product_id);
        $invoice = App\Invoice::findOrFail($invoice_id);

        $check_transaction = App\InventoryTransaction::where('product_id',$product_id)->where('invoice_id',$invoice_id)->first();

        if($check_transaction){

            $inventory_transaction = $check_transaction;

            $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
            $inventory_transaction->save();


        }else{

        $inventory_transaction = new App\InventoryTransaction();
        $inventory_transaction->product_id = $product_id;
        $inventory_transaction->invoice_id = $invoice_id;
        $inventory_transaction->transaction = 'sell';
        $inventory_transaction->selling_price = $product->selling_price;
        $inventory_transaction->quantity = 1;
        $inventory_transaction->save();

        }

        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id',$invoice_id)->get();
        foreach($transactions as $transaction){
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = App\InvoiceItem::where('invoice',$invoice_id)->sum('total') + $transactions_sum;
        $payments_sum = App\Payment::where('invoice',$invoice_id)->sum('amount');

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();
   
        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('view-invoice',$invoice_id)->with('error','Transactions has been Created.')->with('alert', 'alert-success');


        
    }

    public function inventory_cancel_transaction($invoice_id,$transaction)
    {
        $invoice = App\Invoice::findOrFail($invoice_id);
 

        $transaction = App\InventoryTransaction::findOrFail($transaction);
   
        $transaction->delete();



        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id',$invoice_id)->get();
        foreach($transactions as $transaction){
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = App\InvoiceItem::where('invoice',$invoice_id)->sum('total') + $transactions_sum;
        $payments_sum = App\Payment::where('invoice',$invoice_id)->sum('amount');

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();
   
        $log = new App\Log; 
        $log->table = 'inventory_transactions';
        $log->data = 'Inventory Transaction has been Deleted';
        $log->ref = $transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('view-invoice',$invoice_id)->with('error','Transactions has been Deleted.')->with('alert', 'alert-danger');


        
    }

    
}
