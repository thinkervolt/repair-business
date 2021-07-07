<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
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
    public function profile()
    {
        $user = App\User::select('name','email','role')->where('id', auth()->user()->id)->first();
        return view('user.profile',compact('user'));
    }

    public function update_password(request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|min:8|string',
            'password' => 'required|min:8|string',
            'password_confirmation' => 'required|min:8|string',

        ]);

        if(!\Hash::check($request->current_password, auth()->user()->password)){

            return back()->with('error','Current Password does not Match.')->with('alert', 'alert-danger');
        }else{

            if($request->password == $request->password_confirmation){

                $user = App\User::findOrFail(auth()->user()->id);
                $user->password = Hash::make($request->password);
                $user->save();

                Auth::logout();
                return redirect()->route('home');

            }else{

                return back()->with('error','New Password does not Match.')->with('alert', 'alert-warning');
            }

         
        }


    }

    public function users()
    {
        $users = App\User::select('id','name','email','role')->where('active','yes')->where('id','!=',auth()->user()->id)->get();
        return view('user.users',compact('users'));
    }

    
    public function update_user(request $request, $id)
    {
        $user = App\User::findOrFail($id);

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'name' => 'required|string|min:2|max:50',
            'password' => 'required|min:8|string',
            'role' => 'required',
        ]);

        
        $user->role = $request->role;
        $user->password = Hash::make($request->password);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('error','User has been Updated.')->with('alert', 'alert-warning');

    }

    public function delete_user($id)
    {
        $user = App\User::findOrFail($id);

        $user->delete();

        return back()->with('error','User has been Deleted.')->with('alert', 'alert-danger');

    }


}
