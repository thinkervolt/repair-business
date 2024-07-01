<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Mail\MailRepair;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\File;

class RepairController extends Controller
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

    public function index_repair(request $request, $task = null, $id = null)
    {

        if ($task == 'no-invoice') {

            $repair_items = App\InvoiceItem::select('ref')->where('group', 'repair');
            $repairs = App\Repair::wherenotin('id', $repair_items)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);
        } else {

            $search_customer = App\Customer::select('id')->where('last_name', 'LIKE', '%' . $request->search . '%')
                ->orwhere('first_name', 'LIKE', '%' . $request->search . '%')
                ->orwhere('id', 'LIKE', '%' . $request->search . '%')
                ->orwhere('phone', 'LIKE', '%' . $request->search . '%')
                ->orwhere('email', 'LIKE', '%' . $request->search . '%')
                ->orwhere('company', 'LIKE', '%' . $request->search . '%');


            $search = App\Repair::select('id')->where('target', 'LIKE', '%' . $request->search . '%')
                ->orwhere('request', 'LIKE', '%' . $request->search . '%')->orwhere('id', 'LIKE', '%' . $request->search . '%')->orwhereIn('customer', $search_customer);

            $repairs = App\Repair::whereIn('id', $search)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);
        }

        return view('repair.index-repair', compact('repairs'))->with('search', $request->search)->with('task', $task)->with('id', $id);
    }

    public function create_repair($id = null)
    {

        if ($id == null) {
            $customer = null;
        } else {
            $customer = App\Customer::findOrFail($id);
        }

        return view('repair.create-repair', compact('customer'));
    }

    public function new_repair(request $request, $id = null)
    {

        $validatedData = $request->validate([
            'target' => 'required|min:2|max:250',
            'data_request' => 'required|min:2|max:250',
        ]);

        $repair = new App\Repair;
        if ($id == null) {
            $repair->customer = null;
        } else {
            $repair->customer = $id;
        }

        $repair->target = $request->target;
        $repair->request = $request->data_request;
        $repair->user = Auth::user()->id;
        $repair->active = 'yes';
        $repair->save();



        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Created ' . '[target] ' . $request->target . ' [request] ' . $request->data_request;
        $log->ref = $repair->id;
        $log->user = Auth::user()->id;
        $log->save();


        return redirect()->route('view-repair', $repair->id)->with('error', 'Repair has been Created.')->with('alert', 'alert-success');
    }

    public function view_repair($id)
    {
        $repair = App\Repair::findOrFail($id);
        $users = App\User::where('active', 'yes')->get();
        $statuses = App\RepairSetting::where('group', 'status')->get();
        $priorities = App\RepairSetting::where('group', 'priority')->get();
        $logs =  App\Log::where('table', 'repairs')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $comments = App\RepairItem::where('group', 'comment')->where('repair', $id)->get();
        $jobs = App\RepairItem::where('group', 'job')->where('repair', $id)->get();
        $transactions = App\InventoryTransaction::where('repair_id', $id)->get();

        $invoice_items = App\InvoiceItem::select('invoice')->where('ref', $id);
        $invoices = App\Invoice::wherein('id', $invoice_items)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);

        return view('repair.view-repair', compact('repair', 'users', 'statuses', 'priorities', 'logs', 'comments', 'jobs', 'invoices', 'transactions'));
    }


    public function update_repair(request $request, $id)
    {

        $validatedData = $request->validate([
            'target' => 'required|min:2|max:250',
            'data_request' => 'required|min:2|max:250',
            'user' => 'nullable|numeric',
            'status' => 'nullable|numeric',
            'priority' => 'nullable|numeric',
            'estimate' => 'nullable|numeric|between:0,99999.99',
        ]);

        $repair = App\Repair::findOrFail($id);

        $repair_log_update = '';

        if ($repair->target != $request->target) {
            $repair_log_update  .= ' [target][FROM] ' . $repair->target . ' [TO] ' . $request->target;
        }
        if ($repair->request != $request->data_request) {
            $repair_log_update  .= ' [request][FROM] ' . $repair->request . ' [TO] ' . $request->data_request;
        }
        if ($repair->user != $request->user) {
            $repair_log_update  .= ' [user][FROM] ' . $repair->user . ' [TO] ' . $request->user;
        }
        if ($repair->status != $request->status) {
            $repair_log_update  .= ' [status][FROM] ' . $repair->status . ' [TO] ' . $request->status;
        }
        if ($repair->priority != $request->priority) {
            $repair_log_update  .= ' [priority][FROM] ' . $repair->priority . ' [TO] ' . $request->priority;
        }
        if ($repair->estimate != $request->estimate) {
            $repair_log_update  .= ' [estimate][FROM] ' . $repair->estimate . ' [TO] ' . $request->estimate;
        }

        $repair->target = $request->target;
        $repair->request = $request->data_request;
        $repair->status = $request->status;
        $repair->priority = $request->priority;
        $repair->estimate = $request->estimate;
        $repair->user = $request->user;
        $repair->save();


        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Updated ' . $repair_log_update;
        $log->ref = $repair->id;
        $log->user = Auth::user()->id;
        $log->save();


        return redirect()->route('view-repair', $repair->id)->with('error', 'Repair has been Updated.')->with('alert', 'alert-warning');
    }

    public function delete_repair($id)
    {
        $repair = App\Repair::findOrFail($id);
        $repair->active = 'no';
        $repair->save();

        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Deleted';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('index-repair')->with('error', 'Repair has been Deleted.')->with('alert', 'alert-danger');
    }

    public function restore_repair($id)
    {
        $repair = App\Repair::findOrFail($id);
        $repair->active = 'yes';
        $repair->save();

        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Restored';
        $log->ref = $repair->id;
        $log->user = Auth::user()->id;
        $log->save();

        return redirect()->route('view-repair', $repair->id)->with('error', 'Repair has been Restored.')->with('alert', 'alert-success');
    }

    public function destroy_repair($id)
    {
        $repair = App\Repair::findOrFail($id);
        $repair->delete();

        $repair_items = App\RepairItem::where('repair', $id);
        $repair_items->delete();

        $logs = App\Log::where('table', 'repairs')->where('ref', $id);
        $logs->delete();

        return back()->with('error', 'Repair has been Destroyed.')->with('alert', 'alert-danger');
    }

    public function print_repair($id)
    {
        $repair = App\Repair::findOrFail($id);
        $users = App\User::where('active', 'yes')->get();
        $statuses = App\RepairSetting::where('group', 'status')->get();
        $priorities = App\RepairSetting::where('group', 'priority')->get();
        $logs =  App\Log::where('table', 'repairs')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $comments = App\RepairItem::where('group', 'comment')->where('repair', $id)->get();
        $jobs = App\RepairItem::where('group', 'job')->where('repair', $id)->get();

        /* business-profile */
        $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }


        return view('repair.print-repair', compact('repair', 'users', 'statuses', 'priorities', 'logs', 'comments', 'jobs', 'company_profile'));
    }

    public function mail_repair($id)
    {

        if (File::exists(public_path() . '/repair-receipt.pdf')) {
            File::delete(public_path() . '/repair-receipt.pdf');
        }

        $repair = App\Repair::findOrFail($id);
        $users = App\User::where('active', 'yes')->get();
        $statuses = App\RepairSetting::where('group', 'status')->get();
        $priorities = App\RepairSetting::where('group', 'priority')->get();
        $logs =  App\Log::where('table', 'repairs')->where('ref', $id)->orderBy('created_at', 'DESC')->paginate('25');
        $comments = App\RepairItem::where('group', 'comment')->where('repair', $id)->get();
        $jobs = App\RepairItem::where('group', 'job')->where('repair', $id)->get();
        /* business-profile */
        $business_profile_settings = App\Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($business_profile_settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }



        $pdf = Pdf::loadView('repair.mail-repair', compact('repair', 'users', 'statuses', 'priorities', 'logs', 'comments', 'jobs', 'company_profile'))->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path() . '/repair-receipt.pdf');

        $mail_data = (object)[];
        $mail_data->repair = $repair;

        if (!empty($repair->customer_data)) {
            if (!empty($repair->customer_data->email)) {
                try {
                    Mail::to($repair->customer_data->email)->send(new MailRepair($mail_data));
                    if (File::exists(public_path() . '/repair-receipt.pdf')) {
                        File::delete(public_path() . '/repair-receipt.pdf');
                    }

                    $log = new App\Log;
                    $log->table = 'repairs';
                    $log->data = 'Email has been Sent';
                    $log->ref = $repair->id;
                    $log->user = Auth::user()->id;
                    $log->save();

                    return back()->with('error', 'Email has been Sent.')->with('alert', 'alert-success');
                } catch (Exception $ex) {
                    if (File::exists(public_path() . '/repair-receipt.pdf')) {
                        File::delete(public_path() . '/repair-receipt.pdf');
                    }
                    return back()->with('error', 'Something went Wrong.')->with('alert', 'alert-danger');
                }
            } else {
                if (File::exists(public_path() . '/repair-receipt.pdf')) {
                    File::delete(public_path() . '/repair-receipt.pdf');
                }
                return back()->with('error', 'Missing Customer E-mail Address.')->with('alert', 'alert-danger');
            }
        } else {
            if (File::exists(public_path() . '/repair-receipt.pdf')) {
                File::delete(public_path() . '/repair-receipt.pdf');
            }
            return back()->with('error', 'Missing Customer Information.')->with('alert', 'alert-danger');
        }
    }


    public function update_customer_repair($customer, $id)
    {


        $repair = App\Repair::findOrFail($id);

        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Updated [customer][FROM]' . $repair->customer . '[TO]' . $customer;
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        $repair->customer = $customer;
        $repair->save();




        return redirect()->route('view-repair', $repair)->with('error', 'Repair has been Updated.')->with('alert', 'alert-warning');
    }


    /* SETTINGS */

    public function setting_repair()
    {
        $priority_status = App\RepairSetting::where('group', 'priority')->orWhere('group', 'status')->orderBy('group')->get();
        return view('repair.setting-repair', compact('priority_status'));
    }


    public function create_setting_repair(request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);

        $repair_setting = new App\RepairSetting;
        $repair_setting->name = $request->name;
        $repair_setting->group = $request->group;
        $repair_setting->color = $request->color;
        $repair_setting->save();

        $log = new App\Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Created';
        $log->ref = $repair_setting->id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Setting Repair has been Created.')->with('alert', 'alert-success');
    }

    public function update_setting_repair(request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);

        $repair_setting = App\RepairSetting::findOrFail($id);
        $repair_setting->name = $request->name;
        $repair_setting->group = $request->group;
        $repair_setting->color = $request->color;
        $repair_setting->save();

        $log = new App\Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Setting Repair has been Updated.')->with('alert', 'alert-warning');
    }

    public function delete_setting_repair($id)
    {

        $repair_setting = App\RepairSetting::findOrFail($id);
        $repair_setting->delete();

        $log = new App\Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Deleted';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Setting Repair has been Deleted.')->with('alert', 'alert-danger');
    }

    /* END SETTINGS */


    /* ITMES */

    public function create_item_repair(request $request, $id)
    {

        $validatedData = $request->validate([
            'data' => 'required|min:2|max:250',
            'group' => 'required|alpha|min:2|max:50',
        ]);

        $repair_item = new App\RepairItem;
        $repair_item->data = $request->data;
        $repair_item->repair = $id;
        $repair_item->group = $request->group;
        $repair_item->user = Auth::user()->id;

        $repair_item->save();

        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair Item has been Created [' . $request->data . ']';
        $log->ref = $id;
        $log->user = Auth::user()->id;
        $log->save();


        return back()->with('error', 'Item Repair has been Created.')->with('alert', 'alert-success');
    }


    public function delete_item_repair($id)
    {

        $repair_item = App\RepairItem::findOrFail($id);

        $log = new App\Log;
        $log->table = 'repairs';
        $log->data = 'Repair Item has been Deleted [' . $repair_item->data . ']';
        $log->ref = $repair_item->repair;
        $log->user = Auth::user()->id;
        $log->save();

        $repair_item->delete();


        return back()->with('error', 'Repair Item has been Deleted.')->with('alert', 'alert-danger');
    }


    /* END ITEMS */
}
