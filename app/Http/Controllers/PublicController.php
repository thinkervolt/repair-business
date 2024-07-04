<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use \Illuminate\Support\Facades\Lang;

class PublicController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }
    public function customer_signup()
    {

        /* business-profile */
        $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }

        return view('customer-sign-up', compact('company_profile'));
    }

    public function public_new_customer(request $request)
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
        $customer->active = 'yes';

        $customer->save(); 

        $notification = new App\Notification;
        $notification->message = Lang::get('repair-business.new-customer-signed-up') ;
        $notification->ref = $customer->id;
        $notification->route = 'view-customer';
        $notification->save();



        return back()->with('error', Lang::get('repair-business.error_you-have-been-signed-up') )->with('alert', 'alert-success');
    }
}
