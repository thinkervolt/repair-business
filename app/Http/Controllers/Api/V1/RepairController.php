<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\MailRepair;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Repair;
use App\Models\RepairItem;
use App\Models\RepairSetting;
use App\Models\Setting;
use App\Models\User;
use App\Traits\ApiResponses;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class RepairController extends Controller
{
    use ApiResponses;

    protected function repairSettings()
    {
        return RepairSetting::where('group', 'priority')->orWhere('group', 'status')->orderBy('group')->get();
    }

    protected function companyProfile()
    {
        $settings = Setting::where('group', 'business_profile')->get();
        $company_profile = (object)[];
        foreach ($settings as $setting) {
            $company_profile->{$setting->name} = $setting->data;
        }
        return $company_profile;
    }

    protected function repairContext()
    {
        return [
            'users' => User::where('active', 'yes')->orderBy('name')->get(),
            'statuses' => RepairSetting::where('group', 'status')->get(),
            'priorities' => RepairSetting::where('group', 'priority')->get(),
            'repair_settings' => $this->repairSettings(),
        ];
    }

    public function index(Request $request)
    {
        $task = $request->query('task');
        $id = $request->query('id');

        if ($task === 'no-invoice') {
            $repair_items = InvoiceItem::select('ref')->where('group', 'repair');
            $repairs = Repair::with(['customer_data', 'status_data', 'priority_data'])
                ->whereNotIn('id', $repair_items)
                ->where('active', 'yes')
                ->orderBy('created_at', 'DESC')
                ->paginate(25);
        } elseif ($task === 'group_by_settings' && $id !== null) {
            $repairs = Repair::with(['customer_data', 'status_data', 'priority_data'])
                ->where('priority', $id)
                ->orWhere(function ($q) use ($id) {
                    $q->where('status', $id)->where('active', 'yes');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate(25);
        } else {
            $query = Repair::with(['customer_data', 'status_data', 'priority_data'])->where('active', 'yes');

            if ($request->filled('search')) {
                $search = '%' . $request->search . '%';
                $customer_ids = Customer::select('id')
                    ->where('last_name', 'LIKE', $search)
                    ->orWhere('first_name', 'LIKE', $search)
                    ->orWhere('id', 'LIKE', $search)
                    ->orWhere('phone', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search)
                    ->orWhere('company', 'LIKE', $search);

                $query->where(function ($q) use ($search, $customer_ids) {
                    $q->where('target', 'LIKE', $search)
                        ->orWhere('request', 'LIKE', $search)
                        ->orWhere('id', 'LIKE', $search)
                        ->orWhereIn('customer', $customer_ids);
                });
            }

            $repairs = $query->orderBy('created_at', 'DESC')->paginate(25);
        }

        return $this->success([
            'repairs' => $repairs,
            'repair_settings' => $this->repairSettings(),
        ]);
    }

    public function store(Request $request, $id = null)
    {
        $data = $request->validate([
            'target' => 'required|min:2|max:250',
            'data_request' => 'required|min:2|max:250',
        ]);

        $repair = new Repair;
        $repair->customer = $id !== null ? $id : ($request->input('customer') ?: null);
        $repair->target = $data['target'];
        $repair->request = $data['data_request'];
        $repair->user = Auth::id();
        $repair->active = 'yes';
        $repair->save();

        Notification::newRepair($repair);

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Created [target] ' . $data['target'] . ' [request] ' . $data['data_request'];
        $log->ref = $repair->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($repair, Lang::get('repair-business.error_repair-has-been-created'), 201);
    }

    public function show($id)
    {
        $repair = Repair::with(['customer_data', 'agent_data', 'status_data', 'priority_data'])->find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $logs = Log::where('table', 'repairs')->where('ref', $id)->with('user_data')->orderBy('created_at', 'DESC')->paginate(25);
        $comments = RepairItem::with('agent_data')->where('group', 'comment')->where('repair', $id)->get();
        $jobs = RepairItem::with('agent_data')->where('group', 'job')->where('repair', $id)->get();
        $transactions = InventoryTransaction::with('product')->where('repair_id', $id)->get();

        $invoice_items = InvoiceItem::select('invoice')->where('ref', $id);
        $invoices = Invoice::with('status_data')->whereIn('id', $invoice_items)->where('active', 'yes')->orderBy('created_at', 'DESC')->paginate(25);

        return $this->success(array_merge([
            'repair' => $repair,
            'logs' => $logs,
            'comments' => $comments,
            'jobs' => $jobs,
            'invoices' => $invoices,
            'transactions' => $transactions,
        ], $this->repairContext()));
    }

    public function update(Request $request, $id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'target' => 'required|min:2|max:250',
            'data_request' => 'required|min:2|max:250',
            'user' => 'nullable|numeric',
            'status' => 'nullable|numeric',
            'priority' => 'nullable|numeric',
            'estimate' => 'nullable|numeric|between:0,99999.99',
        ]);

        $log_update = '';

        if ($repair->target != $data['target']) {
            $log_update .= ' [target][FROM] ' . $repair->target . ' [TO] ' . $data['target'];
        }
        if ($repair->request != $data['data_request']) {
            $log_update .= ' [request][FROM] ' . $repair->request . ' [TO] ' . $data['data_request'];
        }
        if ($repair->user != ($data['user'] ?? null)) {
            $log_update .= ' [user][FROM] ' . $repair->user . ' [TO] ' . ($data['user'] ?? '');
        }
        if ($repair->status != ($data['status'] ?? null)) {
            $log_update .= ' [status][FROM] ' . $repair->status . ' [TO] ' . ($data['status'] ?? '');
        }
        if ($repair->priority != ($data['priority'] ?? null)) {
            $log_update .= ' [priority][FROM] ' . $repair->priority . ' [TO] ' . ($data['priority'] ?? '');
        }
        if ($repair->estimate != ($data['estimate'] ?? null)) {
            $log_update .= ' [estimate][FROM] ' . $repair->estimate . ' [TO] ' . ($data['estimate'] ?? '');
        }

        $repair->target = $data['target'];
        $repair->request = $data['data_request'];
        $repair->status = $data['status'] ?? null;
        $repair->priority = $data['priority'] ?? null;
        $repair->estimate = $data['estimate'] ?? null;
        $repair->user = $data['user'] ?? null;
        $repair->save();

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Updated ' . $log_update;
        $log->ref = $repair->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($repair, Lang::get('repair-business.error_repair-has-been-updated'));
    }

    public function softDelete($id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $repair->active = 'no';
        $repair->save();

        Notification::clearFor('view-repair', $repair->id);

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_repair-has-been-deleted'));
    }

    public function restore($id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $repair->active = 'yes';
        $repair->save();

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Restored';
        $log->ref = $repair->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_repair-has-been-restored'));
    }

    public function destroy($id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $repair->delete();

        Notification::clearFor('view-repair', $repair->id);

        RepairItem::where('repair', $id)->delete();
        Log::where('table', 'repairs')->where('ref', $id)->delete();

        return $this->success(null, Lang::get('repair-business.error_repair-has-been-destroyed'));
    }

    public function assignCustomer($customer, $id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if (!Customer::find($customer)) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair has been Updated [customer][FROM]' . $repair->customer . '[TO]' . $customer;
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        $repair->customer = $customer;
        $repair->save();

        $repair->load('customer_data');

        return $this->success($repair, Lang::get('repair-business.error_repair-has-been-updated'));
    }

    public function printReceipt($id)
    {
        $repair = Repair::with(['customer_data', 'status_data', 'priority_data'])->find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $comments = RepairItem::with('agent_data')->where('group', 'comment')->where('repair', $id)->get();
        $jobs = RepairItem::with('agent_data')->where('group', 'job')->where('repair', $id)->get();

        $pdf = Pdf::loadView('repair.print-repair', [
            'repair' => $repair,
            'users' => User::where('active', 'yes')->get(),
            'statuses' => RepairSetting::where('group', 'status')->get(),
            'priorities' => RepairSetting::where('group', 'priority')->get(),
            'logs' => collect(),
            'comments' => $comments,
            'jobs' => $jobs,
            'company_profile' => $this->companyProfile(),
        ])->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream('repair-receipt-' . $id . '.pdf');
    }

    public function mailReceipt($id)
    {
        $repair = Repair::find($id);

        if (!$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if (File::exists(public_path() . '/repair-receipt.pdf')) {
            File::delete(public_path() . '/repair-receipt.pdf');
        }

        if (!$repair->customer_data) {
            return $this->error(Lang::get('repair-business.error_missing-customer-information'), 422);
        }

        if (empty($repair->customer_data->email)) {
            return $this->error(Lang::get('repair-business.error_missing-customer-email-address'), 422);
        }

        $comments = RepairItem::with('agent_data')->where('group', 'comment')->where('repair', $id)->get();
        $jobs = RepairItem::with('agent_data')->where('group', 'job')->where('repair', $id)->get();

        $pdf = Pdf::loadView('repair.print-repair', [
            'repair' => $repair,
            'users' => User::where('active', 'yes')->get(),
            'statuses' => RepairSetting::where('group', 'status')->get(),
            'priorities' => RepairSetting::where('group', 'priority')->get(),
            'logs' => collect(),
            'comments' => $comments,
            'jobs' => $jobs,
            'company_profile' => $this->companyProfile(),
        ])->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path() . '/repair-receipt.pdf');

        $mail_data = (object)[];
        $mail_data->repair = $repair;

        try {
            Mail::to($repair->customer_data->email)->send(new MailRepair($mail_data));
            if (File::exists(public_path() . '/repair-receipt.pdf')) {
                File::delete(public_path() . '/repair-receipt.pdf');
            }

            $log = new Log;
            $log->table = 'repairs';
            $log->data = 'Email has been Sent';
            $log->ref = $repair->id;
            $log->user = Auth::id();
            $log->save();

            return $this->success(null, Lang::get('repair-business.error_email-has-been-sent'));
        } catch (Exception $ex) {
            if (File::exists(public_path() . '/repair-receipt.pdf')) {
                File::delete(public_path() . '/repair-receipt.pdf');
            }
            return $this->error(Lang::get('repair-business.error_something-went-wrong'), 500);
        }
    }

    /* SETTINGS */

    public function settingsIndex()
    {
        return $this->success($this->repairSettings());
    }

    protected function validateSetting(Request $request)
    {
        return $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);
    }

    public function settingsCreate(Request $request)
    {
        $data = $this->validateSetting($request);

        $repair_setting = new RepairSetting;
        $repair_setting->name = $data['name'];
        $repair_setting->group = $data['group'];
        $repair_setting->color = $data['color'];
        $repair_setting->save();

        $log = new Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Created';
        $log->ref = $repair_setting->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($repair_setting, Lang::get('repair-business.error_setting-has-been-created'), 201);
    }

    public function settingsUpdate(Request $request, $id)
    {
        $repair_setting = RepairSetting::find($id);

        if (!$repair_setting) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $this->validateSetting($request);

        $repair_setting->name = $data['name'];
        $repair_setting->group = $data['group'];
        $repair_setting->color = $data['color'];
        $repair_setting->save();

        $log = new Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($repair_setting, Lang::get('repair-business.error_setting-has-been-updated'));
    }

    public function settingsDelete($id)
    {
        $repair_setting = RepairSetting::find($id);

        if (!$repair_setting) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $repair_setting->delete();

        $log = new Log;
        $log->table = 'repair_settings';
        $log->data = 'Repair Setting has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_setting-has-been-deleted'));
    }

    /* ITEMS */

    public function createItem(Request $request, $id)
    {
        if (!Repair::find($id)) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'data' => 'required|min:2|max:250',
            'group' => 'required|alpha|min:2|max:50',
        ]);

        $repair_item = new RepairItem;
        $repair_item->data = $data['data'];
        $repair_item->repair = $id;
        $repair_item->group = $data['group'];
        $repair_item->user = Auth::id();
        $repair_item->save();

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair Item has been Created [' . $data['data'] . ']';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($repair_item, Lang::get('repair-business.error_item-has-been-created'), 201);
    }

    public function deleteItem($id)
    {
        $repair_item = RepairItem::find($id);

        if (!$repair_item) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $log = new Log;
        $log->table = 'repairs';
        $log->data = 'Repair Item has been Deleted [' . $repair_item->data . ']';
        $log->ref = $repair_item->repair;
        $log->user = Auth::id();
        $log->save();

        $repair_item->delete();

        return $this->success(null, Lang::get('repair-business.error_item-has-been-deleted'));
    }
}