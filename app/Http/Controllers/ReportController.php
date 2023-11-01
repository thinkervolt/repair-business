<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

class ReportController extends Controller
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

     
    public function create_report()
    {
        

        return view('report.create-report');
    }

    public function create_register_report()
    {
        

        return view('report.create-register-report');
    }

    public function get_register_report(request $request)
    {
        $cash_register = $request->cash;
        $card_register = $request->card;

        $payments = App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->get();

        $payment_data = array(
            'date' => $request->date,
            'count' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->count(),
            'total' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->sum('amount'), 
            'total_cash' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->where('method','cash')->sum('amount'),
            'total_card' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->where('method','card')->sum('amount'),
            'total_check' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->where('method','check')->sum('amount'),
            'total_other' => App\Payment::where('active','yes')->whereDate('created_at','=',$request->date)->where('method','other')->sum('amount'), 
            'cash_register' => $cash_register,
            'card_register' => $card_register,
            );

        return view('report.get-register-report',compact('payments','payment_data'));
    }

    

    public function register_report_insert(request $request)
    {

        if($request->cash != 0 ){
            $cash_payment =  new App\Payment;
            $cash_payment->amount = $request->cash;
            $cash_payment->method = 'cash';
            $cash_payment->ref = 'NON-INVOICED-CASH-TRANSACTIONS-'.$request->date;
            $cash_payment->active = 'yes';
            $cash_payment->created_at = $request->date.' 00:00:00';
            $cash_payment->save();

            $log = new App\Log; 
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$'.$request->cash.'][cash][NON-INVOICED-CASH-TRANSACTIONS-'.$request->date.']';
            $log->ref = $cash_payment->id;
            $log->user = Auth::user()->id;
            $log->save();
        }

        if($request->card != 0){
            $card_payment =  new App\Payment;
            $card_payment->amount = $request->card;
            $card_payment->method = 'card';
            $card_payment->ref = 'NON-INVOICED-CARD-TRANSACTIONS-'.$request->date;
            $card_payment->active = 'yes';
            $card_payment->created_at = $request->date.' 00:00:00';
            $card_payment->save();

            $log = new App\Log; 
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$'.$request->card.'][cash][NON-INVOICED-CARD-TRANSACTIONS-'.$request->date.']';
            $log->ref = $card_payment->id;
            $log->user = Auth::user()->id;
            $log->save();
        }

            
        return redirect()->route('index-payment')->with('error','Repair has been Created.')->with('error','Payments have been Created')->with('alert', 'alert-success');

        

    }

         
    public function get_report(request $request)
    {

        $report_data= array(
            'invoices' => $request->invoices,
            'repairs' => $request->repairs,
            'payments' => $request->payments,
            'from' => $request->from,
            'to' => $request->to,
        );

        if($request->invoices == 'on'){

            $invoices = App\Invoice::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->get();
            $invoice_data = array(
            'total' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->sum('total'),
            'balance' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->sum('balance'),
            'count' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->count(),
            );
        }else{

            $invoices = null;
            $invoice_data = null;
        }

        if($request->repairs == 'on'){

            $repairs = App\Repair::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->get();
            $repair_data = array(
                'count' => App\Repair::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->count(),
            );

        }else{

            $repairs = null;
            $repair_data = null;
        }

        if($request->payments == 'on'){
            $payments = App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->get(); 
            $payment_data = array(
                'count' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->count(),
                'total' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->sum('amount'), 
                'total_cash' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->where('method','cash')->sum('amount'),
                'total_card' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->where('method','card')->sum('amount'),
                'total_check' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->where('method','check')->sum('amount'),
                'total_other' => App\Payment::where('active','yes')->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to)->where('method','other')->sum('amount'), 
            );

        }else{

            $payments = null;
            $payment_data = null;

            
        }


        
        

        return view('report.get-report',compact('invoices','invoice_data','repairs','repair_data','payments','payment_data','report_data'));
        
    }


    public function print_report($report_from,$report_to,$report_invoices,$report_repairs,$report_payments)
    {

        $report_data= array(
            'invoices' => $report_invoices,
            'repairs' => $report_repairs,
            'payments' => $report_payments,
            'from' => $report_from,
            'to' => $report_to,
        );

        $invoices = App\Invoice::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->get();

        $invoice_data = array(
        'total' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->sum('total'),
        'balance' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->sum('balance'),
        'count' => App\Invoice::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->count(),
        );
        $repair_data = array(
            'count' => App\Repair::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->count(),
        );
        $payment_data = array(
            'count' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->count(), 
            'total' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->sum('amount'), 
            'total_cash' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->where('method','cash')->sum('amount'),
            'total_card' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->where('method','card')->sum('amount'),
            'total_check' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->where('method','check')->sum('amount'),
            'total_other' => App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->where('method','other')->sum('amount'), 
        );




        $repairs = App\Repair::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->get();
        $payments = App\Payment::where('active','yes')->whereDate('created_at','>=',$report_from)->whereDate('created_at','<=',$report_to)->get(); 

        return view('report.print-report',compact('invoices','invoice_data','repairs','repair_data','payments','payment_data','report_data'));
        
    }


}
