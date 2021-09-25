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


    public function inventory_index_product()
    {
        $inventory_products = App\InventoryProduct::orderBy('created_at','DESC')->paginate(25); 
        $inventory_categories = App\InventoryCategory::orderBy('name','ASC')->get(); 
        return view('inventory.index-product',compact('inventory_products','inventory_categories'));
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
        $inventory_product->min_stock = $request->max_stock;
        $inventory_product->max_stock = $request->max_stock;
        $inventory_product->email_alert = $request->email_alert;
        $inventory_product->save();


        $inventory_transaction = new App\InventoryTransaction();
        $inventory_transaction->transaction = 'purchase';
        $inventory_transaction->product_id = $inventory_product->id;
        $inventory_transaction->purchase_price = $request->purchase_price;
        $inventory_transaction->selling_price = $request->selling_price;
        $inventory_transaction->quantity = $request->quantity;
        $inventory_transaction->supplier = $request->supplier;
        $inventory_transaction->save();


        $log = new App\Log; 
        $log->table = 'inventory_transaction';
        $log->data = 'Inventory Transaction has been Created';
        $log->ref = $inventory_transaction->id;
        $log->user = Auth::user()->id;
        $log->save();

        $log = new App\Log; 
        $log->table = 'inventory_products';
        $log->data = 'Inventory Category has been Created';
        $log->ref = $inventory_product->id;
        $log->user = Auth::user()->id;
        $log->save();
        
        return back()->with('error','Inventory Product and Transaction has been Created.')->with('alert', 'alert-success');

        
    }

    public function inventory_view_product($id)
    {
        $product = App\InventoryProduct::findOrFail($id); 
        $inventory_categories = App\InventoryCategory::orderBy('name','ASC')->get(); 
        $transactions = App\InventoryTransaction::where('product_id',$product->id)->orderBy('created_at','DESC')->paginate(25); 
        return view('inventory.view-product',compact('product','inventory_categories','transactions'));
    }
}
