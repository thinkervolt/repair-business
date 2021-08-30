<?php

namespace App\Http\Controllers;
use App;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function inventory_index_category()
    {

        $invoices = App\Invoice::orderBy('created_at','DESC')->paginate(25); 

        return view('inventory.index-category',compact('invoices'))->with('search','temp');
    }
}
