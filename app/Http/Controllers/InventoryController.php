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
            'name' => 'min:2|max:50',
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
}
