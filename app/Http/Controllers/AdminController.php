<?php
namespace App\Http\Controllers;
use App;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Repair;

class AdminController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
 
    }

    
    public function index()
    {

        
        $month = date('m');
        $year = date('Y');
        $lyear = date('Y', strtotime('last year'));

        $current_year_income = array( 
            'jan' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '01' )->sum('amount'),
            'feb' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '02' )->sum('amount'),
            'mar' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '03' )->sum('amount'),
            'apr' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '04' )->sum('amount'),
            'may' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '05' )->sum('amount'),
            'jun' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '06' )->sum('amount'),
            'jul' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '07' )->sum('amount'),
            'aug' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '08' )->sum('amount'),
            'sep' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '09' )->sum('amount'),
            'oct' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '10' )->sum('amount'),
            'nov' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '11' )->sum('amount'),
            'dec' => Payment::whereYear('created_at', $year)->whereMonth('created_at', '12' )->sum('amount'),
            'total' =>  Payment::whereYear('created_at', $year)->sum('amount'),
            'current_month' => Payment::whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount'),
            'current_day' => Payment::whereDay('created_at', now()->day)->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount'),
        );

        $past_year_income = array(
            'jan' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '01' )->sum('amount'),
            'feb' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '02' )->sum('amount'),
            'mar' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '03' )->sum('amount'),
            'apr' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '04' )->sum('amount'),
            'may' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '05' )->sum('amount'),
            'jun' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '06' )->sum('amount'),
            'jul' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '07' )->sum('amount'),
            'aug' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '08' )->sum('amount'),
            'sep' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '09' )->sum('amount'),
            'oct' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '10' )->sum('amount'),
            'nov' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '11' )->sum('amount'),
            'dec' => Payment::whereYear('created_at', $lyear)->whereMonth('created_at', '12' )->sum('amount'),
            'total' =>  Payment::whereYear('created_at', $lyear)->sum('amount'),
        );

        $unpaid_invoices = Invoice::where('balance', '>', 0)->where('active','yes')->count();

        $repair_items = InvoiceItem::select('ref')->where('group','repair');
        $repairs_no_invoice = Repair::wherenotin('id',$repair_items)->where('active','yes')->count();
         

        return view('home',compact('current_year_income','past_year_income','unpaid_invoices','repairs_no_invoice'));
    }
}
