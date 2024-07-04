<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;
use \Illuminate\Support\Facades\Lang;

class CustomerController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index_customer(request $request,$task = null, $id = null)
    {
        $search = App\Customer::select('id')->where('last_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('first_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('id', 'LIKE', '%' . $request->search . '%')
        ->orwhere('phone', 'LIKE', '%' . $request->search . '%')
        ->orwhere('email', 'LIKE', '%' . $request->search . '%')
        ->orwhere('company', 'LIKE', '%' . $request->search . '%');

        $customers = App\Customer::whereIn('id',$search)->where('active','yes')->orderBy('created_at','DESC')->paginate(25); 

        return view('customer.index-customer',compact('customers'))->with('search',$request->search)->with('task',$task)->with('id',$id);
    }
    public function create_customer()
    {
        return view('customer.create-customer');
    }

    public function new_customer(request $request)
    {
        $validatedData = $request->validate([

            'first_name' => 'required|alpha|min:2|max:50',
            'last_name' => 'nullable|alpha|min:2|max:50',
            'phone' => 'required|numeric|digits_between:10,10|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|min:2|max:250',
            'city' => 'nullable|min:2|max:250',
            'state' => 'nullable|alpha|min:2|max:2',
            'zip' => 'nullable|numeric|digits_between:5,5',
            'company' => 'nullable|min:2|max:50',


        ]);

        $customer = new App\Customer;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->zip = $request->zip;
        $customer->company = $request->company;
        $customer->active = 'yes';

        $customer->save();

        $log = new App\Log; 
        $log->table = 'customers';
        $log->data = 'Customer has been Created';
        $log->ref = $customer->id;
        $log->user = Auth::user()->id;
        $log->save(); 

        return redirect()->route('view-customer',$customer->id)->with('error',Lang::get('repair-business.error_customer-has-been-created'))->with('alert', 'alert-success');
    }

    public function view_customer($id)
    {
        $customer = App\Customer::findOrFail($id);
        $repairs = App\Repair::where('customer',$id)->where('active','yes')->orderBy('created_at','DESC')->paginate(25); 
        $invoices = App\Invoice::where('customer_id',$id)->where('active','yes')->orderBy('created_at','DESC')->paginate(25); 


        return view('customer.view-customer',compact('customer','repairs','invoices'));
    }

    public function update_customer(request $request, $id)
    {
        $customer = App\Customer::findOrFail($id);

        $validatedData = $request->validate([

            'first_name' => 'required|alpha|min:2|max:50',
            'last_name' => 'nullable|alpha|min:2|max:50',
            'phone' => 'required|numeric|digits_between:10,10|unique:customers,phone,'.$customer->id,
            'email' => 'nullable|email|unique:customers,email,'.$customer->id,
            'address' => 'nullable|min:2|max:250',
            'city' => 'nullable|min:2|max:250',
            'state' => 'nullable|alpha|min:2|max:2',
            'zip' => 'nullable|numeric|digits_between:5,5',
            'company' => 'nullable|min:2|max:50',


        ]);

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->zip = $request->zip;
        $customer->company = $request->company;
        $customer ->save();

        $log = new App\Log; 
        $log->table = 'customers';
        $log->data = 'Customer has been Updated';
        $log->ref = $customer->id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error',Lang::get('repair-business.error_customer-has-been-updated') )->with('alert', 'alert-warning');
    }

    public function delete_customer($id)
    {
        $customer = App\Customer::findOrFail($id);
        $customer->active = 'no';
        $customer ->save();

        $log = new App\Log; 
        $log->table = 'customers';
        $log->data = 'Customer has been Deleted';
        $log->ref = $customer->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('index-customer')->with('error',Lang::get('repair-business.error_customer-has-been-deleted') )->with('alert', 'alert-danger');
    }

    public function restore_customer($id)
    {
        $customer = App\Customer::findOrFail($id);
        $customer->active = 'yes';
        $customer ->save();

        $log = new App\Log; 
        $log->table = 'users';
        $log->data = 'Customer has been Restored';
        $log->ref = $customer->id;
        $log->user = Auth::user()->id;
        $log->save();
        return redirect()->route('view-customer',$customer->id)->with('error',Lang::get('repair-business.error_customer-has-been-restored'))->with('alert', 'alert-success');
    }

    public function destroy_customer($id)
    {
        $customer = App\Customer::findOrFail($id);
        $customer ->delete();

        $logs = App\Log::where('table','customers')->where('ref',$id);
        $logs->delete();
        return back()->with('error',Lang::get('repair-business.error_customer-has-been-destroyed'))->with('alert', 'alert-danger');
    }



}
