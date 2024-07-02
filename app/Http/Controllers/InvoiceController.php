<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\File;
use App\Mail\MailInvoice;

class InvoiceController extends Controller
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



    public function index_invoice(request $request, $task = null)
    {


        if ($task == 'unpaid') {
            $invoices = App\Invoice::where('balance', '>', 0)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);
        } else {


            $search_priority = App\InvoiceSetting::select('id')->where('name', 'LIKE', '%' . $request->search . '%');

            $search = App\Invoice::select('id')->where('customer_name', 'LIKE', '%' . $request->search . '%')
                ->orwhere('customer_email', 'LIKE', '%' . $request->search . '%')
                ->orwhere('customer_phone', 'LIKE', '%' . $request->search . '%')
                ->orwhere('customer_company', 'LIKE', '%' . $request->search . '%')
                ->orwhere('id', 'LIKE', '%' . $request->search . '%')
                ->orwherein('status', $search_priority);

            $invoices = App\Invoice::whereIn('id', $search)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);
        }

        return view('invoice.index-invoice', compact('invoices'))->with('search', $request->search);
    }

    public function view_invoice($id)
    {

        $invoice = App\Invoice::findOrFail($id);
        $invoice_items = App\InvoiceItem::where('invoice', $id)->get();
        $invoice_statuses = App\InvoiceSetting::where('group', 'status')->get();
        $logs =  App\Log::where('table', 'invoices')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $payments = App\Payment::where('invoice', $id)->where('active', 'yes')->get();
        $transactions = App\InventoryTransaction::where('invoice_id', $id)->get();
        return view('invoice.view-invoice', compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'transactions'));
    }


    public function create_invoice($id, $task)
    {

        if ($task == 'view_repair') {

            $repair = App\Repair::findOrFail($id);

            /* business-profile */
            $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
            $company_profile = (object)[];
            foreach ($business_profile_settings as $setting) {
                $company_profile->{$setting->name} = $setting->data;
            }


            $invoice_tax_string = App\Setting::where('name', 'invoice_tax')->where('group', 'tax')->firstOrFail();
            $invoice_tax = (float)$invoice_tax_string->data;

            $jobs = App\RepairItem::where('repair', $id)->where('group', 'job')->get();
            $job_data = 'JOBS: ';
            if ($jobs) {
                foreach ($jobs as $job) {
                    $job_data .= '[' . $job->data . ']';
                }
            }

            $invoice = new App\Invoice;

            if (isset($repair->customer)) {

                $invoice->customer_id = $repair->customer_data->id;
                $invoice->customer_name = $repair->customer_data->first_name . ' ' . $repair->customer_data->last_name;
                $invoice->customer_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone);
                $invoice->customer_email = $repair->customer_data->email;
                $invoice->customer_address = $repair->customer_data->address . ' ' . $repair->customer_data->city . ' ' . $repair->customer_data->state . ' ' . $repair->customer_data->zip;
                $invoice->customer_company = $repair->customer_data->company;
            }

            $invoice->company_name = $company_profile->name;
            $invoice->company_phone =  preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone);
            $invoice->company_email = $company_profile->email;
            $invoice->company_address = $company_profile->address;

            $invoice->active = 'yes';
            $invoice->user = Auth::user()->id;
            $invoice->save();


            $invoice_item = new App\InvoiceItem;
            $invoice_item->invoice = $invoice->id;
            $invoice_item->name = 'REPAIR #' . $repair->id;
            $invoice_item->description = $repair->request . ' ' . $repair->target;
            $invoice_item->sub_description = $job_data;
            if (isset($repair->estimate)) {
                $invoice_item->unit_cost = $repair->estimate;
            } else {
                $invoice_item->unit_cost = 0;
            }
            $invoice_item->quantity = 1;
            $invoice_item->ref = $id;
            $invoice_item->total = $repair->estimate * 1;
            $invoice_item->group = 'repair';
            $invoice_item->save();

            $invoice->tax_porcentage = $invoice_tax;
            $invoice->subtotal = (float)$invoice_item->total;
            $invoice->tax = (float)($invoice_item->total / 100) *  $invoice_tax;
            $invoice->total = (float)$invoice_item->total + (($invoice_item->total / 100) *  $invoice_tax);
            $invoice->balance = (float)$invoice_item->total + (($invoice_item->total / 100) *  $invoice_tax);
            $invoice->save();



            $log = new App\Log;
            $log->table = 'invoices';
            $log->data = 'Invoice has been Created';
            $log->ref = $invoice->id;
            $log->user = Auth::user()->id;
            $log->save();
        }

        if ($task == 'empty') {

            /* business-profile */
            $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
            $company_profile = (object)[];
            foreach ($business_profile_settings as $setting) {
                $company_profile->{$setting->name} = $setting->data;
            }





            $invoice_tax_string = App\Setting::where('name', 'invoice_tax')->where('group', 'tax')->firstOrFail();
            $invoice_tax = (float)$invoice_tax_string->data;

            $invoice = new App\Invoice;
            $invoice->company_name = $company_profile->name;
            $invoice->company_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone);
            $invoice->company_email = $company_profile->email;
            $invoice->company_address = $company_profile->address;
            $invoice->tax_porcentage = $invoice_tax;
            $invoice->subtotal = 0;
            $invoice->tax = 0;
            $invoice->total = 0;
            $invoice->balance = 0;
            $invoice->active = 'yes';
            $invoice->user = Auth::user()->id;
            $invoice->save();


            $log = new App\Log;
            $log->table = 'invoices';
            $log->data = 'Invoice has been Created';
            $log->ref = $invoice->id;
            $log->user = Auth::user()->id;
            $log->save();
        }

        return redirect()->route('view-invoice', $invoice->id)->with('error', 'Invoice has been Created.')->with('alert', 'alert-success');
    }

    public function update_invoice(request $request, $id)
    {


        $invoice = App\Invoice::findOrFail($id);
        $items_sum = App\InvoiceItem::where('invoice', $id)->sum('total');
        $payments_sum = App\Payment::where('invoice', $id)->sum('amount');

        $transactions_sum =  0;

        $transactions = App\InventoryTransaction::where('invoice_id', $id)->get();

        foreach($transactions as $transaction){

            $transactions_sum = $transactions_sum + ($transaction->quantity * $transaction->selling_price);
        }

        $invoice_log_update = '';

        if ($invoice->customer_name != $request->customer_name) {
            $invoice_log_update  .= ' [customer_name][FROM] ' . $invoice->customer_name . ' [TO] ' . $request->customer_name;
        }

        if ($invoice->customer_phone != $request->customer_phone) {
            $invoice_log_update  .= ' [customer_phone][FROM] ' . $invoice->customer_phone . ' [TO] ' . $request->customer_phone;
        }

        if ($invoice->customer_email != $request->customer_email) {
            $invoice_log_update  .= ' [customer_email][FROM] ' . $invoice->customer_email . ' [TO] ' . $request->customer_email;
        }

        if ($invoice->customer_address != $request->customer_address) {
            $invoice_log_update  .= ' [customer_address][FROM] ' . $invoice->customer_address . ' [TO] ' . $request->customer_address;
        }

        if ($invoice->customer_company != $request->customer_company) {
            $invoice_log_update  .= ' [customer_company][FROM] ' . $invoice->customer_company . ' [TO] ' . $request->customer_company;
        }

        if ($invoice->company_name != $request->company_name) {
            $invoice_log_update  .= ' [company_name][FROM] ' . $invoice->company_name . ' [TO] ' . $request->company_name;
        }

        if ($invoice->company_phone != $request->company_phone) {
            $invoice_log_update  .= ' [company_phone][FROM] ' . $invoice->company_phone . ' [TO] ' . $request->company_phone;
        }

        if ($invoice->company_email != $request->company_email) {
            $invoice_log_update  .= ' [company_email][FROM] ' . $invoice->company_email . ' [TO] ' . $request->company_email;
        }

        if ($invoice->company_address != $request->company_address) {
            $invoice_log_update  .= ' [company_address][FROM] ' . $invoice->company_address . ' [TO] ' . $request->company_address;
        }

        if ($invoice->status != $request->status) {
            $invoice_log_update  .= ' [status][FROM] ' . $invoice->status . ' [TO] ' . $request->status;
        }

        if ($invoice->tax_porcentage != $request->tax_porcentage) {
            $invoice_log_update  .= ' [tax_porcentage][FROM] ' . $invoice->tax_porcentage . ' [TO] ' . $request->tax_porcentage;
        }


        $invoice->customer_name = $request->customer_name;
        $invoice->customer_phone = $request->customer_phone;
        $invoice->customer_email = $request->customer_email;
        $invoice->customer_address = $request->customer_address;
        $invoice->customer_company = $request->customer_company;
        $invoice->company_name = $request->company_name;
        $invoice->company_phone =  $request->company_phone;
        $invoice->company_email = $request->company_email;
        $invoice->company_address = $request->company_address;
        $invoice->status = $request->status;
        $invoice->tax_porcentage = $request->tax_porcentage;


        $invoice_items = $items_sum + $transactions_sum; 

        $invoice->subtotal = (float)$invoice_items;
        $invoice->tax = (float)($invoice_items / 100) *  (float)$request->tax_porcentage;
        $invoice->total = (float)$invoice_items + (($invoice_items / 100) *  (float)$request->tax_porcentage);
        $invoice->balance = ((float)$invoice_items + (($invoice_items / 100) *  (float)$request->tax_porcentage)) - $payments_sum;
        $invoice->save();

        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Updated' . $invoice_log_update;
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();

        return back()->with('error', 'Invoice has been Updated.')->with('alert', 'alert-warning');
    }


    public function delete_invoice($id)
    {
        $invoice = App\Invoice::findOrFail($id);
        $invoice->active = 'no';
        $invoice->save();

        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Deleted';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('index-invoice')->with('error', 'Invoice has been Deleted.')->with('alert', 'alert-danger');
    }

    public function restore_invoice($id)
    {
        $invoice = App\Invoice::findOrFail($id);
        $invoice->active = 'yes';
        $invoice->save();

        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Restored';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('view-invoice', $id)->with('error', 'Invoice has been Restored.')->with('alert', 'alert-success');
    }

    public function destroy_invoice($id)
    {
        $invoice = App\Invoice::findOrFail($id);
        $invoice->delete();

        $invoice_items = App\InvoiceItem::where('invoice', $id);
        $invoice_items->delete();

        $payments = App\Payment::where('invoice', $id);
        $payments->delete();

        $payments = App\InventoryTransaction::where('invoice_id', $id);
        $payments->delete();

        $logs = App\Log::where('table', 'invoices')->where('ref', $id);
        $logs->delete();

        return back()->with('error', 'Invoice has been Destroyed.')->with('alert', 'alert-danger');
    }

    public function print_invoice($id, $task)
    {

        $invoice = App\Invoice::findOrFail($id);
        $invoice_items = App\InvoiceItem::where('invoice', $id)->get();
        $transactions = App\InventoryTransaction::where('invoice_id', $id)->get();
        $invoice_statuses = App\InvoiceSetting::where('group', 'status')->get();
        $logs =  App\Log::where('table', 'invoices')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $payments = App\Payment::where('invoice', $id)->where('active', 'yes')->get();

        /* business-profile */
        $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }

        $terms = $company_profile->terms;

        if ($task == 'print') {
            return view('invoice.print-invoice', compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'terms', 'transactions'));
        }

        if ($task == 'receipt') {
            return view('invoice.print-invoice-receipt', compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'terms', 'transactions'));
        }
    }

    public function email_invoice($id)
    {
        if (File::exists(public_path() . '/invoice-receipt.pdf')) {
            File::delete(public_path() . '/invoice-receipt.pdf');
        }

        $invoice = App\Invoice::findOrFail($id);
        $invoice_items = App\InvoiceItem::where('invoice', $id)->get();
        $transactions = App\InventoryTransaction::where('invoice_id', $id)->get();
        $invoice_statuses = App\InvoiceSetting::where('group', 'status')->get();
        $logs =  App\Log::where('table', 'invoices')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $payments = App\Payment::where('invoice', $id)->where('active', 'yes')->get();

        /* business-profile */
        $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }

        $terms = $company_profile->terms;

        /* return view('invoice.email-invoice',compact('invoice','invoice_items','invoice_statuses','logs','payments','terms','transactions')); */

        $pdf = Pdf::loadView('invoice.print-invoice-receipt', compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'terms', 'transactions'))->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path() . '/invoice-receipt.pdf');

        $mail_data = (object)[];
        $mail_data->invoice = $invoice;


        if (!empty($invoice->customer_email)) {
            try {
                Mail::to($invoice->customer_email)->send(new MailInvoice($mail_data));
                if (File::exists(public_path() . '/invoice-receipt.pdf')) {
                    File::delete(public_path() . '/invoice-receipt.pdf');
                }

                $log = new App\Log;
                $log->table = 'invoices';
                $log->data = 'Email has been Sent';
                $log->ref = $invoice->id;
                $log->user = Auth::user()->id;
                $log->save();

                return back()->with('error', 'Email has been Sent.')->with('alert', 'alert-success');
            } catch (Exception $ex) {
                if (File::exists(public_path() . '/invoice-receipt.pdf')) {
                    File::delete(public_path() . '/invoice-receipt.pdf');
                }
                return back()->with('error', 'Something went Wrong.')->with('alert', 'alert-danger');
            }
        } else {
            if (File::exists(public_path() . '/invoice-receipt.pdf')) {
                File::delete(public_path() . '/invoice-receipt.pdf');
            }
            return back()->with('error', 'Missing Customer E-mail Address.')->with('alert', 'alert-danger');
        }
    }


    public function update_customer_invoice($customer, $id)
    {


        $invoice = App\Invoice::findOrFail($id);
        $customer_data = App\Customer::findOrFail($customer);

        $invoice->customer_id = $customer_data->id;
        $invoice->customer_name = $customer_data->first_name . ' ' . $customer_data->last_name;
        $invoice->customer_phone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $customer_data->phone);
        $invoice->customer_email = $customer_data->email;
        $invoice->customer_address = $customer_data->address . ' ' . $customer_data->city . ' ' . $customer_data->state . ' ' . $customer_data->zip;
        $invoice->customer_company = $customer_data->company;

        $invoice->save();

        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Updated [customer][FROM]' . $invoice->customer_id . '[TO]' . $customer;
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return redirect()->route('view-invoice', $id)->with('error', 'Invoice has been Updated.')->with('alert', 'alert-warning');
    }



    /* SETTINGS */

    public function setting_invoice()
    {
        $statuses = App\InvoiceSetting::where('group', 'status')->orderBy('group')->get();
        return view('invoice.setting-invoice', compact('statuses'));
    }


    public function create_setting_invoice(request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);

        $invoice_setting = new App\InvoiceSetting;
        $invoice_setting->name = $request->name;
        $invoice_setting->group = $request->group;
        $invoice_setting->color = $request->color;
        $invoice_setting->save();

        $log = new App\Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Created';
        $log->ref = $invoice_setting->id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Setting Invoice has been Created.')->with('alert', 'alert-success');
    }

    public function update_setting_invoice(request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);

        $invoice_setting = App\InvoiceSetting::findOrFail($id);
        $invoice_setting->name = $request->name;
        $invoice_setting->group = $request->group;
        $invoice_setting->color = $request->color;
        $invoice_setting->save();

        $log = new App\Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();





        return back()->with('error', 'Setting Invoice has been Updated.')->with('alert', 'alert-warning');
    }

    public function delete_setting_invoice($id)
    {

        $invoice_setting = App\InvoiceSetting::findOrFail($id);
        $invoice_setting->delete();

        $log = new App\Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Deleted';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Setting Invoice has been Deleted.')->with('alert', 'alert-danger');
    }

    /* END SETTINGS */

    /* ITMES */



    public function create_item_repair_invoice($repair, $invoice)
    {
        $invoice_data = App\Invoice::findOrFail($invoice);
        $repair = App\Repair::findOrFail($repair);

        $jobs = App\RepairItem::where('repair', $repair->id)->where('group', 'job')->get();
        $job_data = 'JOBS: ';
        if ($jobs) {
            foreach ($jobs as $job) {
                $job_data .= '[' . $job->data . ']';
            }
        }

        $invoice_item = new App\InvoiceItem;
        $invoice_item->invoice = $invoice;
        $invoice_item->name = 'REPAIR #' . $repair->id;
        $invoice_item->description = $repair->request . ' ' . $repair->target;
        $invoice_item->sub_description = $job_data;
        if (isset($repair->estimate)) {
            $invoice_item->unit_cost = $repair->estimate;
        } else {
            $invoice_item->unit_cost = 0;
        }
        $invoice_item->quantity = 1;
        $invoice_item->ref = $invoice;
        $invoice_item->total = $repair->estimate * 1;
        $invoice_item->group = 'repair';
        $invoice_item->save();



        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id', $invoice)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }


        $items_sum = App\InvoiceItem::where('invoice', $invoice)->sum('total') + $transactions_sum;
        $payments_sum = App\Payment::where('invoice', $invoice)->sum('amount');



        $invoice_data->subtotal = (float)$items_sum;
        $invoice_data->tax = (float)($items_sum / 100) *  (float)$invoice_data->tax_porcentage;
        $invoice_data->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice_data->tax_porcentage);
        $invoice_data->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice_data->tax_porcentage)) - $payments_sum;
        $invoice_data->save();


        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Created';
        $log->ref = $invoice;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('view-invoice', $invoice)->with('error', 'Item Invoice has been Created.')->with('alert', 'alert-success');
    }

    public function create_item_invoice(request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|min:2|max:250',
            'description' => 'required|min:2|max:250',
            'sub_description' => 'nullable|min:2|max:250',
            'unit_cost' => 'required|numeric|between:0,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);


        $invoice_item = new App\InvoiceItem;

        $invoice_item->invoice = $id;
        $invoice_item->name = $request->name;
        $invoice_item->description = $request->description;
        $invoice_item->sub_description = $request->sub_description;
        $invoice_item->unit_cost = $request->unit_cost;
        $invoice_item->quantity = $request->quantity;
        $invoice_item->total = $request->unit_cost * $request->quantity;
        $invoice_item->group = 'no-group';
        $invoice_item->save();



        $invoice = App\Invoice::findOrFail($id);

        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id', $id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }


        $items_sum = App\InvoiceItem::where('invoice', $id)->sum('total') + $transactions_sum;

        $payments_sum = App\Payment::where('invoice', $id)->sum('amount');



        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();


        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Created';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Item Invoice has been Created.')->with('alert', 'alert-success');
    }


    public function update_item_invoice(request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|min:2|max:250',
            'description' => 'required|min:2|max:250',
            'sub_description' => 'nullable|min:2|max:250',
            'unit_cost' => 'required|numeric|between:0,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);



        $invoice_item = App\InvoiceItem::findOrFail($id);

        $invoice_item_log_update = '';

        if ($invoice_item->name != $request->name) {
            $invoice_item_log_update  .= ' [name][FROM] ' . $invoice_item->name . ' [TO] ' . $request->name;
        }
        if ($invoice_item->description != $request->description) {
            $invoice_item_log_update  .= ' [description][FROM] ' . $invoice_item->description . ' [TO] ' . $request->description;
        }
        if ($invoice_item->sub_description != $request->sub_description) {
            $invoice_item_log_update  .= ' [sub_description][FROM] ' . $invoice_item->sub_description . ' [TO] ' . $request->sub_description;
        }

        if ($invoice_item->unit_cost != $request->unit_cost) {
            $invoice_item_log_update  .= ' [unit_cost][FROM] ' . $invoice_item->unit_cost . ' [TO] ' . $request->unit_cost;
        }

        if ($invoice_item->quantity != $request->quantity) {
            $invoice_item_log_update  .= ' [quantity][FROM] ' . $invoice_item->quantity . ' [TO] ' . $request->quantity;
        }


        $invoice_item->name = $request->name;
        $invoice_item->description = $request->description;
        $invoice_item->sub_description = $request->sub_description;
        $invoice_item->unit_cost = $request->unit_cost;
        $invoice_item->quantity = $request->quantity;
        $invoice_item->total = $request->unit_cost * $request->quantity;
        $invoice_item->save();



        $invoice = App\Invoice::findOrFail($invoice_item->invoice);

        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = App\InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = App\Payment::where('invoice', $invoice->id)->sum('amount');



        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();


        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Updated' . $invoice_item_log_update;
        $log->ref = $invoice_item->invoice;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Item Invoice has been Created.')->with('alert', 'alert-success');
    }


    public function delete_item_invoice($id)
    {

        $invoice_item = App\InvoiceItem::findOrFail($id);

        $log = new App\Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Deleted [' . $invoice_item->name . '][' . $invoice_item->description . '][' . $invoice_item->sub_description . '][' . $invoice_item->unit_cost . '][' . $invoice_item->quantity . '][' . $invoice_item->total . ']';
        $log->ref = $invoice_item->invoice;
        $log->user = Auth::user()->id;
        $log->save();


        $invoice_item->delete();


        $invoice = App\Invoice::findOrFail($invoice_item->invoice);

        $transactions_sum = 0;
        $transactions = App\InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
        }




        $items_sum = App\InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = App\Payment::where('invoice', $invoice->id)->sum('amount');
        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
        $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
        $invoice->save();


        return back()->with('error', 'Invoice Item has been Deleted.')->with('alert', 'alert-danger');
    }


    /* END ITEMS */
}
