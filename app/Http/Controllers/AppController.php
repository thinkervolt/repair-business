<?php

namespace App\Http\Controllers;

class AppController extends Controller
{
    public function set_sidebar_position($position)
    {
        session()->put('sidebar-position', $position);
        return redirect()->back();
    }
}
