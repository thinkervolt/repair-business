<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class TrashController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index_trash(request $request)
    {
        $search_customer = App\Customer::select('id')->where('last_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('first_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('id', 'LIKE', '%' . $request->search . '%')
        ->orwhere('phone', 'LIKE', '%' . $request->search . '%')
        ->orwhere('email', 'LIKE', '%' . $request->search . '%');

        $customers = App\Customer::whereIn('id',$search_customer)->where('active','no')->orderBy('created_at','DESC')->get(); 

        $search_repair = App\Repair::select('id')->where('target', 'LIKE', '%' . $request->search . '%')
        ->orwhere('request', 'LIKE', '%' . $request->search . '%')->orwhereIn('customer',$search_customer);
        
        $repairs = App\Repair::whereIn('id',$search_repair)->where('active','no')->orderBy('created_at','DESC')->get();
        
        $search_priority = App\InvoiceSetting::select('id')->where('name', 'LIKE', '%' . $request->search . '%');
        $search_invoices = App\Invoice::select('id')->where('customer_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('customer_email', 'LIKE', '%' . $request->search . '%')
        ->orwhere('customer_phone', 'LIKE', '%' . $request->search . '%')
        ->orwhere('id', 'LIKE', '%' . $request->search . '%')
        ->orwherein('status',$search_priority);
        $invoices = App\Invoice::whereIn('id',$search_invoices)->where('active','no')->orderBy('created_at','DESC')->get(); 

 

        return view('trash.index-trash',compact('customers','repairs','invoices'))->with('search',$request->search);
    }
}
