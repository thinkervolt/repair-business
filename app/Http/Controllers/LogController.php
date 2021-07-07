<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;


class LogController extends Controller
{
                /*
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


    
   public function index_log(request $request)
   {

    $users = App\User::select('id')->where('name','LIKE', '%' . $request->search . '%');

    $logs = App\Log::where('data', 'LIKE', '%' . $request->search . '%')->orwherein('user',$users)->orderby('created_at','DESC')->paginate('50');
       return view('log.index-log',compact('logs'));
       
   }
}
