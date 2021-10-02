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
        /* SYMBOL LS1208 BARCODE SCANNER (Slash Removal)*/ 
            /* $barcode = str_replace('/', '', $request->search); */

        $barcode = $request->search;
    
        $category_search = App\InventoryCategory::select('id')->where('name', 'LIKE', '%' . $request->search . '%');

        $search = App\InventoryProduct::select('id')->where('name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('barcode', 'LIKE', '%' . $barcode  . '%')
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
            'barcode' => 'nullable|unique:inventory_products,barcode',
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
        $inventory_product = App\InventoryProduct::findOrFail($id);

        $request->validate([
            'category' => 'required|numeric',
            'name' => 'required|min:2|max:50',
            'barcode' => 'nullable|unique:inventory_products,barcode,'.$inventory_product->id,
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

    public function inventory_sell_transaction($task,$id,$product_id)
    {

        if($task == 'invoice'){

            $product = App\InventoryProduct::findOrFail($product_id);
            $invoice = App\Invoice::findOrFail($id);
    
            $purchases = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
            $sells = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
            $stock = $purchases - $sells;
    
            if($stock > 0 ){
    
                $check_transaction = App\InventoryTransaction::where('product_id',$product_id)->where('invoice_id',$id)->first();
    
                if($check_transaction){
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
                    $inventory_transaction->save();
                }else{
    
                $inventory_transaction = new App\InventoryTransaction();
                $inventory_transaction->product_id = $product_id;
                $inventory_transaction->invoice_id = $id;
                $inventory_transaction->transaction = 'sell';
                $inventory_transaction->selling_price = $product->selling_price;
                $inventory_transaction->quantity = 1;
                $inventory_transaction->save();
    
                }
    
                $transactions_sum = 0;
                $transactions = App\InventoryTransaction::where('invoice_id',$id)->get();
                foreach($transactions as $transaction){
                    $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
                }
    
                $items_sum = App\InvoiceItem::where('invoice',$id)->sum('total') + $transactions_sum;
                $payments_sum = App\Payment::where('invoice',$id)->sum('amount');
    
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
    
                return redirect()->route('view-invoice',$id)->with('error','Transactions has been Created.')->with('alert', 'alert-success');
            }else{
    
                return redirect()->route('view-invoice',$id)->with('error','Product is out of Stock')->with('alert', 'alert-danger');
    
            }


        }

        if($task == 'repair'){

            $product = App\InventoryProduct::findOrFail($product_id);
            $repair = App\Repair::findOrFail($id);
    
            $purchases = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
            $sells = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
            $stock = $purchases - $sells;
    
            if($stock > 0 ){
    
                $check_transaction = App\InventoryTransaction::where('product_id',$product_id)->where('repair_id',$id)->first();
    
                if($check_transaction){
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
                    $inventory_transaction->save();
                }else{
    
                $inventory_transaction = new App\InventoryTransaction();
                $inventory_transaction->product_id = $product_id;
                $inventory_transaction->repair_id = $id;
                $inventory_transaction->transaction = 'sell';
                $inventory_transaction->selling_price = $product->selling_price;
                $inventory_transaction->quantity = 1;
                $inventory_transaction->save();
    
                }
        
                $log = new App\Log; 
                $log->table = 'inventory_transactions';
                $log->data = 'Inventory Transaction has been Created';
                $log->ref = $inventory_transaction->id;
                $log->user = Auth::user()->id;
                $log->save();
    
                return redirect()->route('view-repair',$id)->with('error','Transactions has been Created.')->with('alert', 'alert-success');
            }else{
    
                return redirect()->route('view-repair',$id)->with('error','Product is out of Stock')->with('alert', 'alert-danger');
    
            }


        }

        
 

        
    }

    public function inventory_cancel_transaction($task,$id,$transaction)
    {

        if($task == 'invoice'){
            $invoice = App\Invoice::findOrFail($id);
            $transaction = App\InventoryTransaction::findOrFail($transaction);
            $transaction->delete();

            $transactions_sum = 0;
            $transactions = App\InventoryTransaction::where('invoice_id',$id)->get();
            foreach($transactions as $transaction){
                $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
            }

            $items_sum = App\InvoiceItem::where('invoice',$id)->sum('total') + $transactions_sum;
            $payments_sum = App\Payment::where('invoice',$id)->sum('amount');

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

            return redirect()->route('view-invoice',$id)->with('error','Transactions has been Deleted.')->with('alert', 'alert-danger');
        }

        if($task == 'repair'){

            $transaction = App\InventoryTransaction::findOrFail($transaction);
            $transaction->delete();

            $log = new App\Log; 
            $log->table = 'inventory_transactions';
            $log->data = 'Inventory Transaction has been Deleted';
            $log->ref = $transaction->id;
            $log->user = Auth::user()->id;
            $log->save();

            return redirect()->route('view-repair',$id)->with('error','Transactions has been Deleted.')->with('alert', 'alert-danger');
        }




        
    }


    public function inventory_quick_sell_transaction($product_id)
    {
        //STOCK CHECK
        $product = App\InventoryProduct::findOrFail($product_id);
        $purchases = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
        $sells = App\InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
        $stock = $purchases - $sells;

        //END STOCK CHECK

        if($stock > 0 ){

        //NEW INVOICE
        
        $company_profile = App\CompanyProfile::first();
        $invoice_tax_string = App\Setting::where('name','invoice_tax')->where('group','tax')->firstOrFail();
        $invoice_tax = (float)$invoice_tax_string->data;

        $invoice = new App\Invoice;
        $invoice->company_name = $company_profile->name;
        $invoice->company_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone);
        $invoice->company_email = $company_profile->email;
        $invoice->company_address = $company_profile->email;
        $invoice->tax_porcentage = 0;
        $invoice->subtotal = 0;
        $invoice->tax = 0;
        $invoice->total = 0;
        $invoice->balance = 0;
        $invoice->active='yes';
        $invoice->user = Auth::user()->id;
        $invoice->save();
      
        $log = new App\Log; 
        $log->table = 'invoices';
        $log->data = 'Invoice has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::user()->id;
        $log->save();

        //END NEW INVOICE

            $check_transaction = App\InventoryTransaction::where('product_id',$product_id)->where('invoice_id',$invoice->id)->first();

            if($check_transaction){
                $inventory_transaction = $check_transaction;
                $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
                $inventory_transaction->save();
            }else{

            $inventory_transaction = new App\InventoryTransaction();
            $inventory_transaction->product_id = $product_id;
            $inventory_transaction->invoice_id = $invoice->id;
            $inventory_transaction->transaction = 'sell';
            $inventory_transaction->selling_price = $product->selling_price;
            $inventory_transaction->quantity = 1;
            $inventory_transaction->save();

            }

            $log = new App\Log; 
            $log->table = 'inventory_transactions';
            $log->data = 'Inventory Transaction has been Created';
            $log->ref = $inventory_transaction->id;
            $log->user = Auth::user()->id;
            $log->save();

            // NEW PAYMENT

            $payment =  new App\Payment;
            $payment->amount = $product->selling_price;
            $payment->method = 'cash';
            $payment->ref = 'Quick Sell Transaction';
            $payment->active = 'yes';
            $payment->invoice = $invoice->id;
            $payment->save();

            $items_sum = App\InvoiceItem::where('invoice',$invoice->id)->sum('total');

            $transactions_sum = 0;
            $transactions = App\InventoryTransaction::where('invoice_id',$invoice->id)->get();
            foreach($transactions as $transaction){
                $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
            }

            $items_sum = App\InvoiceItem::where('invoice',$invoice->id)->sum('total') + $transactions_sum;
            $payments_sum = App\Payment::where('invoice',$invoice->id)->sum('amount');

            $invoice->subtotal = (float)$items_sum;
            $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
            $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
            $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
            $invoice->save();

            $log = new App\Log; 
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$'.$product->selling_pice.'][cash][Quick Sell Transaction]';
            $log->ref = $invoice->id;
            $log->user = Auth::user()->id;
            $log->save();

            //END NEW PAYMENT


            return redirect()->route('view-invoice',$invoice->id)->with('error','Quick Transactions has been Created.')->with('alert', 'alert-success');
        }else{

            return back()->with('error','Product is out of Stock')->with('alert', 'alert-danger');

        }

        
    }

    
}
