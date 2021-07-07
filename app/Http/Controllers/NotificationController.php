<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class NotificationController extends Controller
{
            /* Create a new controller instance.
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


    public function notification($id)
    {
        $notification = App\Notification::findOrFail($id);
        $notification->delete();


        return redirect()->route($notification->route,$notification->ref);



    }



}
