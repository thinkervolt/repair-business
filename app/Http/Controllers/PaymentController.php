<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

class PaymentController extends Controller
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

    public function index_payment(request $request)
    {
        $search = App\Payment::select('id')->where('id', 'LIKE', '%' . $request->search . '%')
        ->orwhere('invoice', 'LIKE', '%' . $request->search . '%')
        ->orwhere('amount', 'LIKE', '%' . $request->search . '%')
        ->orwhere('method', 'LIKE', '%' . $request->search . '%')
        ->orwhere('ref', 'LIKE', '%' . $request->search . '%');

        $payments = App\Payment::whereIn('id',$search)->where('active','yes')->orderBy('created_at','DESC')->paginate(25); 

        return view('payment.index-payment',compact('payments'))->with('search',$request->search);
    }

    public function new_payment()
    {
        return view('payment.create-payment');
    }

    public function create_payment(request $request, $id = null)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|between:-99999.99,99999.99',
            'method' => 'required|min:2|max:250',
            'ref' => 'nullable|min:2|max:250',
            
        ]); 

        if($id === null){
            $payment =  new App\Payment;
            $payment->amount = $request->amount;
            $payment->method = $request->method;
            $payment->ref = $request->ref;
            $payment->active = 'yes';
            $payment->save();

            $log = new App\Log; 
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$'.$request->amount.']['.$request->method.']['.$request->ref.']';
            $log->ref = $payment->id;
            $log->user = Auth::user()->id;
            $log->save();
        }else{
            $invoice = App\Invoice::findOrFail($id);

            $payment =  new App\Payment;
            $payment->amount = $request->amount;
            $payment->method = $request->method;
            $payment->ref = $request->ref;
            $payment->active = 'yes';
            $payment->invoice = $id;
            $payment->save();

            $items_sum = App\InvoiceItem::where('invoice',$id)->sum('total');
            $payments_sum = App\Payment::where('invoice',$id)->sum('amount');

            $invoice->subtotal = (float)$items_sum;
            $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
            $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
            $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
            $invoice->save();

            $log = new App\Log; 
            $log->table = 'invoices';
            $log->data = 'Payment has been Created [$'.$request->amount.']['.$request->method.']['.$request->ref.']';
            $log->ref = $id;
            $log->user = Auth::user()->id;
            $log->save();

            

        }
        return back()->with('error','Payment has been Created')->with('alert', 'alert-success');
    }


    public function delete_payment($id)
    {
        $payment = App\Payment::findOrFail($id);

        if($payment->invoice === null){

            $log = new App\Log; 
            $log->table = 'payments';
            $log->data = 'Payment has been Deleted [$'.$payment->amount.']['.$payment->method.']['.$payment->ref.']' ;
            $log->ref = $payment->id;
            $log->user = Auth::user()->id;
            $log->save();
            $payment->delete();

            return redirect()->route('index-payment')->with('error','Payment has been Deleted.')->with('alert', 'alert-danger');


        }else{
        $invoice = App\Invoice::findOrFail($payment->invoice);

        $log = new App\Log; 
        $log->table = 'invoices';
        $log->data = 'Payment has been Deleted [$'.$payment->amount.']['.$payment->method.']['.$payment->ref.']' ;
        $log->ref = $invoice->id;
        $log->user = Auth::user()->id;
        $log->save();
        $payment->delete();

        
        $items_sum = App\InvoiceItem::where('invoice',$invoice->id)->sum('total');
        $payments_sum = App\Payment::where('invoice',$invoice->id)->sum('amount');

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();

        return back()->with('error','Payment has been Deleted')->with('alert', 'alert-danger');
        }


    }

    public function view_payment($id)
    {
        $payment = App\Payment::findOrFail($id);
        return view('payment.view-payment',compact('payment'));
    }

    public function update_payment(request $request, $id)
    {
        $payment = App\Payment::findOrFail($id);

        $validatedData = $request->validate([
            'amount' => 'required|numeric|between:-99999.99,99999.99',
            'method' => 'required|min:2|max:250',
            'ref' => 'nullable|min:2|max:250',
        ]);



        $log = new App\Log; 
        $log->table = 'payments';
        $log->data = 'Payment has been Updated [FROM]'.$payment->amount.'[TO]'.$request->amount.'[FROM]'.$payment->method.'[TO]'.$request->method.'[FROM]'.$payment->ref.'[TO]'.$request->ref;
        $log->ref = $payment->id;
        $log->user = Auth::user()->id;
        $log->save();

        $payment->amount = $request->amount;
        $payment->method = $request->method;
        $payment->ref = $request->ref;
        $payment->save();

        return back()->with('error','Payment has been Updated.')->with('alert', 'alert-warning');
    }



}
