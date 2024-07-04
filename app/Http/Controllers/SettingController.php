<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;
use \Illuminate\Support\Facades\Lang;

class SettingController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

   

    public function index_setting()
    {
        $settings = App\Setting::orderBy('group', 'asc')->get();
        return view('setting.index-setting', compact('settings'));
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


        return back()->with('error', Lang::get('repair-business.error_setting-has-been-updated'))->with('alert', 'alert-warning');
    }

}
