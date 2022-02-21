<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class AdminController extends Controller
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
    public function index()
    {

        
        $month = date('m');
        $year = date('Y');
        $lyear = date('Y', strtotime('last year'));



        $current_year_income = array( 
            'jan' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '01' )->sum('amount'),
            'feb' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '02' )->sum('amount'),
            'mar' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '03' )->sum('amount'),
            'apr' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '04' )->sum('amount'),
            'may' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '05' )->sum('amount'),
            'jun' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '06' )->sum('amount'),
            'jul' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '07' )->sum('amount'),
            'aug' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '08' )->sum('amount'),
            'sep' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '09' )->sum('amount'),
            'oct' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '10' )->sum('amount'),
            'nov' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '11' )->sum('amount'),
            'dec' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', '12' )->sum('amount'),
            'total' =>  App\Payment::whereYear('created_at', $year)->sum('amount'),
            'current_month' => App\Payment::whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount'),
            'current_day' => App\Payment::whereDay('created_at', now()->day)->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount'),
        );

        $past_year_income = array(
            'jan' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '01' )->sum('amount'),
            'feb' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '02' )->sum('amount'),
            'mar' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '03' )->sum('amount'),
            'apr' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '04' )->sum('amount'),
            'may' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '05' )->sum('amount'),
            'jun' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '06' )->sum('amount'),
            'jul' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '07' )->sum('amount'),
            'aug' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '08' )->sum('amount'),
            'sep' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '09' )->sum('amount'),
            'oct' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '10' )->sum('amount'),
            'nov' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '11' )->sum('amount'),
            'dec' => App\Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '12' )->sum('amount'),
            'total' =>  App\Payment::whereYear('created_at', $lyear)->sum('amount'),
        );

        $unpaid_invoices = App\Invoice::where('balance', '>', 0)->where('active','yes')->count();

        $repair_items = App\InvoiceItem::select('ref')->where('group','repair');
        $repairs_no_invoice = App\Repair::wherenotin('id',$repair_items)->where('active','yes')->count();
         

        return view('home',compact('current_year_income','past_year_income','unpaid_invoices','repairs_no_invoice'));
    }
}
