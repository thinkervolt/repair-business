<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

class SettingController extends Controller
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


     
    public function index_setting()
    {
        $settings = App\Setting::all();
        $company_profile = App\CompanyProfile::first();
        return view('setting.index-setting',compact('settings','company_profile'));
        
    }

    public function update_setting(request $request, $id)
    {
        $setting = App\Setting::findOrFail($id);
        $setting->data = $request->data;
        $setting->save();

        $log = new App\Log; 
        $log->table = 'settings';
        $log->data = 'Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error','Setting has been Updated.')->with('alert', 'alert-warning');
        
    }

    public function update_company_setting(request $request, $id)
    {

        $validatedData = $request->validate([

            'name' => 'required|min:2|max:50',
            'phone' => 'required|numeric|digits_between:10,10',
            'email' => 'required|email',
            'address' => 'required|min:2|max:250',
            'terms' => 'required',


        ]);

        $setting = App\CompanyProfile::findOrFail($id);
        $setting->name = $request->name;
        $setting->phone = $request->phone;
        $setting->email = $request->email;
        $setting->address = $request->address;
        $setting->terms = $request->terms;
        $setting->save();


        $log = new App\Log; 
        $log->table = 'settings';
        $log->data = 'Company Profile has been Updated';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error','Company Profile has been Updated.')->with('alert', 'alert-warning');
    }

}
