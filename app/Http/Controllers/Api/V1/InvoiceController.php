<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\MailInvoice;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceSetting;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\RepairItem;
use App\Models\Setting;
use App\Traits\ApiResponses;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    use ApiResponses;

    protected function companyProfile()
    {
        $settings = Setting::where('group', 'business_profile')->get();
        $profile = (object)[];
        foreach ($settings as $setting) {
            $profile->{$setting->name} = $setting->data;
        }
        return $profile;
    }

    protected function taxSetting()
    {
        $setting = Setting::where('name', 'invoice_tax')->where('group', 'tax')->first();
        return $setting ? (float)$setting->data : 0;
    }

    protected function formatPhone($phone)
    {
        return preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $phone);
    }

    protected function repairJobsText($repairId)
    {
        $jobs = RepairItem::where('repair', $repairId)->where('group', 'job')->get();
        $job_data = 'JOBS: ';
        foreach ($jobs as $job) {
            $job_data .= '[' . $job->data . ']';
        }
        return $job_data;
    }

    protected function applyCompany(Invoice $invoice, $profile)
    {
        $invoice->company_name = $profile->name ?? null;
        $invoice->company_phone = $this->formatPhone($profile->phone ?? '') ?: null;
        $invoice->company_email = $profile->email ?? null;
        $invoice->company_address = $profile->address ?? null;
        return $invoice;
    }

    protected function applyCustomer(Invoice $invoice, Customer $customer)
    {
        $invoice->customer_id = $customer->id;
        $invoice->customer_name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
        $invoice->customer_phone = $this->formatPhone($customer->phone ?? '') ?: null;
        $invoice->customer_email = $customer->email ?? null;
        $invoice->customer_address = trim(($customer->address ?? '') . ' ' . ($customer->city ?? '') . ' ' . ($customer->state ?? '') . ' ' . ($customer->zip ?? ''));
        $invoice->customer_company = $customer->company ?? null;
        return $invoice;
    }

    protected function recomputeInvoice(Invoice $invoice)
    {
        $transactions_sum = 0;
        $transactions = InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum += (float)($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = (float)InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = (float)Payment::where('invoice', $invoice->id)->sum('amount');
        $tax_porcentage = (float)$invoice->tax_porcentage;

        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) * $tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) * $tax_porcentage);
        $invoice->balance = (float)$invoice->total - $payments_sum;
        $invoice->save();
    }

    public function index(Request $request)
    {
        $task = $request->query('task');
        $search = $request->filled('search') ? '%' . $request->search . '%' : null;

        $status_ids = InvoiceSetting::select('id')
            ->where('group', 'status')
            ->where('name', 'LIKE', $search)
            ->pluck('id');

        if ($task === 'unpaid') {
            $invoices = Invoice::with('status_data')->where('balance', '>', 0)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        } elseif ($task === 'group_by_status' && $request->filled('status')) {
            $invoices = Invoice::with('status_data')->where('status', $request->status)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        } elseif ($search !== null) {
            $ids = Invoice::select('id')
                ->where('customer_name', 'LIKE', $search)
                ->orWhere('customer_email', 'LIKE', $search)
                ->orWhere('customer_phone', 'LIKE', $search)
                ->orWhere('customer_company', 'LIKE', $search)
                ->orWhere('id', 'LIKE', $search)
                ->orWhereIn('status', $status_ids)
                ->pluck('id');

            $invoices = Invoice::with('status_data')->whereIn('id', $ids)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        } else {
            $invoices = Invoice::with('status_data')->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        }

        return $this->success([
            'invoices' => $invoices,
            'statuses' => InvoiceSetting::where('group', 'status')->orderBy('name')->get(),
        ]);
    }

    public function show($id)
    {
        $invoice = Invoice::with('status_data')->find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $items = InvoiceItem::where('invoice', $invoice->id)->orderBy('created_at', 'ASC')->get();
        $transactions = InventoryTransaction::with('product')->where('invoice_id', $invoice->id)
            ->orderBy('created_at', 'DESC')->get();
        $payments = Payment::where('invoice', $invoice->id)->where('active', 'yes')
            ->orderBy('created_at', 'DESC')->get();
        $logs = Log::with('user_data')->where('table', 'invoices')->where('ref', $invoice->id)
            ->orderBy('created_at', 'DESC')->paginate(25);

        $repair_ids = InvoiceItem::where('invoice', $invoice->id)->where('group', 'repair')->pluck('ref');
        $repairs = $repair_ids->isNotEmpty()
            ? Repair::with(['customer_data', 'status_data', 'priority_data'])
                ->whereIn('id', $repair_ids)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->get()
            : collect();

        return $this->success([
            'invoice' => $invoice,
            'items' => $items,
            'transactions' => $transactions,
            'payments' => $payments,
            'repairs' => $repairs,
            'logs' => $logs,
            'statuses' => InvoiceSetting::where('group', 'status')->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'repair' => 'nullable|numeric|exists:repairs,id',
            'customer' => 'nullable|numeric|exists:customers,id',
        ]);

        $profile = $this->companyProfile();

        $invoice = new Invoice;
        $invoice->active = 'yes';
        $invoice->user = Auth::id();
        $invoice->tax_porcentage = $this->taxSetting();
        $this->applyCompany($invoice, $profile);

        $invoice->subtotal = 0;
        $invoice->tax = 0;
        $invoice->total = 0;
        $invoice->balance = 0;
        $invoice->save();

        if (!empty($data['customer'])) {
            $customer = Customer::find($data['customer']);
            if ($customer) {
                $this->applyCustomer($invoice, $customer);
                $invoice->save();
            }
        }

        if (!empty($data['repair'])) {
            $repair = Repair::find($data['repair']);
            if ($repair) {
                if ($repair->customer_data) {
                    $this->applyCustomer($invoice, $repair->customer_data);
                }
                $invoice->save();

                $item = new InvoiceItem;
                $item->invoice = $invoice->id;
                $item->name = 'REPAIR #' . $repair->id;
                $item->description = trim(($repair->request ?? '') . ' ' . ($repair->target ?? ''));
                $item->sub_description = $this->repairJobsText($repair->id);
                $item->unit_cost = isset($repair->estimate) ? (float)$repair->estimate : 0;
                $item->quantity = 1;
                $item->ref = $repair->id;
                $item->group = 'repair';
                $item->total = (float)$item->unit_cost * (float)$item->quantity;
                $item->save();
            }
        }

        $this->recomputeInvoice($invoice);

        Notification::syncInvoiceBalance($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['invoice' => $invoice], Lang::get('repair-business.error_invoice-has-been-created'), 201);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|string',
            'customer_address' => 'nullable|string',
            'customer_company' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|string',
            'company_address' => 'nullable|string',
            'status' => 'nullable|numeric|exists:invoice_settings,id',
            'tax_porcentage' => 'nullable|numeric|between:0,100',
        ]);

        $invoice_log_update = '';

        if (isset($data['customer_name']) && $invoice->customer_name != $data['customer_name']) {
            $invoice_log_update .= ' [customer_name][FROM] ' . $invoice->customer_name . ' [TO] ' . $data['customer_name'];
        }
        if (isset($data['customer_phone']) && $invoice->customer_phone != $data['customer_phone']) {
            $invoice_log_update .= ' [customer_phone][FROM] ' . $invoice->customer_phone . ' [TO] ' . $data['customer_phone'];
        }
        if (isset($data['customer_email']) && $invoice->customer_email != $data['customer_email']) {
            $invoice_log_update .= ' [customer_email][FROM] ' . $invoice->customer_email . ' [TO] ' . $data['customer_email'];
        }
        if (isset($data['customer_address']) && $invoice->customer_address != $data['customer_address']) {
            $invoice_log_update .= ' [customer_address][FROM] ' . $invoice->customer_address . ' [TO] ' . $data['customer_address'];
        }
        if (isset($data['customer_company']) && $invoice->customer_company != $data['customer_company']) {
            $invoice_log_update .= ' [customer_company][FROM] ' . $invoice->customer_company . ' [TO] ' . $data['customer_company'];
        }
        if (isset($data['company_name']) && $invoice->company_name != $data['company_name']) {
            $invoice_log_update .= ' [company_name][FROM] ' . $invoice->company_name . ' [TO] ' . $data['company_name'];
        }
        if (isset($data['company_phone']) && $invoice->company_phone != $data['company_phone']) {
            $invoice_log_update .= ' [company_phone][FROM] ' . $invoice->company_phone . ' [TO] ' . $data['company_phone'];
        }
        if (isset($data['company_email']) && $invoice->company_email != $data['company_email']) {
            $invoice_log_update .= ' [company_email][FROM] ' . $invoice->company_email . ' [TO] ' . $data['company_email'];
        }
        if (isset($data['company_address']) && $invoice->company_address != $data['company_address']) {
            $invoice_log_update .= ' [company_address][FROM] ' . $invoice->company_address . ' [TO] ' . $data['company_address'];
        }
        if (array_key_exists('status', $data) && $invoice->status != $data['status']) {
            $invoice_log_update .= ' [status][FROM] ' . $invoice->status . ' [TO] ' . $data['status'];
        }
        if (array_key_exists('tax_porcentage', $data) && $invoice->tax_porcentage != $data['tax_porcentage']) {
            $invoice_log_update .= ' [tax_porcentage][FROM] ' . $invoice->tax_porcentage . ' [TO] ' . $data['tax_porcentage'];
        }

        if (isset($data['customer_name'])) {
            $invoice->customer_name = $data['customer_name'];
        }
        if (isset($data['customer_phone'])) {
            $invoice->customer_phone = $data['customer_phone'];
        }
        if (isset($data['customer_email'])) {
            $invoice->customer_email = $data['customer_email'];
        }
        if (isset($data['customer_address'])) {
            $invoice->customer_address = $data['customer_address'];
        }
        if (isset($data['customer_company'])) {
            $invoice->customer_company = $data['customer_company'];
        }
        if (isset($data['company_name'])) {
            $invoice->company_name = $data['company_name'];
        }
        if (isset($data['company_phone'])) {
            $invoice->company_phone = $data['company_phone'];
        }
        if (isset($data['company_email'])) {
            $invoice->company_email = $data['company_email'];
        }
        if (isset($data['company_address'])) {
            $invoice->company_address = $data['company_address'];
        }
        if (array_key_exists('status', $data)) {
            $invoice->status = $data['status'];
        }
        if (array_key_exists('tax_porcentage', $data)) {
            $invoice->tax_porcentage = $data['tax_porcentage'];
        }

        $this->recomputeInvoice($invoice);

        Notification::syncInvoiceBalance($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Updated' . $invoice_log_update;
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['invoice' => $invoice], Lang::get('repair-business.error_invoice-has-been-updated'));
    }

    public function softDelete($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $invoice->active = 'no';
        $invoice->save();

        Notification::syncInvoiceBalance($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_invoice-has-been-deleted'));
    }

    public function restore($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $invoice->active = 'yes';
        $invoice->save();

        Notification::syncInvoiceBalance($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Restored';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_invoice-has-been-restored'));
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        InvoiceItem::where('invoice', $id)->delete();
        Payment::where('invoice', $id)->delete();
        InventoryTransaction::where('invoice_id', $id)->delete();
        Log::where('table', 'invoices')->where('ref', $id)->delete();

        Notification::clearFor('view-invoice', $id);

        $invoice->delete();

        return $this->success(null, Lang::get('repair-business.error_invoice-has-been-destroyed'));
    }

    public function updateCustomer($id, $customer)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $customer_data = Customer::find($customer);

        if (!$customer_data) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Updated [customer][FROM]' . $invoice->customer_id . '[TO]' . $customer_data->id;
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        $this->applyCustomer($invoice, $customer_data);
        $invoice->save();

        $invoice->load('status_data');

        return $this->success(['invoice' => $invoice], Lang::get('repair-business.error_invoice-has-been-updated'));
    }

    public function print($id, $task)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ($task !== 'print' && $task !== 'receipt') {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $invoice_items = InvoiceItem::where('invoice', $id)->orderBy('created_at', 'ASC')->get();
        $transactions = InventoryTransaction::with('product')->where('invoice_id', $id)->get();
        $invoice_statuses = InvoiceSetting::where('group', 'status')->get();
        $logs = Log::where('table', 'invoices')->where('ref', $id)->orderBy('created_at', 'DESC')->get();
        $payments = Payment::where('invoice', $id)->where('active', 'yes')->get();
        $profile = $this->companyProfile();
        $terms = $profile->terms ?? null;

        $view = $task === 'print' ? 'invoice.print-invoice' : 'invoice.print-invoice-receipt';

        $pdf = Pdf::loadView($view, compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'terms', 'transactions'))
            ->setOptions(['defaultFont' => 'sans-serif']);

        if ($task === 'print') {
            $pdf->setPaper('letter', 'portrait');
        }

        return $pdf->stream('invoice-' . $task . '-' . $id . '.pdf');
    }

    public function email($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if (File::exists(public_path() . '/invoice.pdf')) {
            File::delete(public_path() . '/invoice.pdf');
        }

        if (empty($invoice->customer_email)) {
            return $this->error(Lang::get('repair-business.error_missing-customer-email-address'), 422);
        }

        $invoice_items = InvoiceItem::where('invoice', $id)->orderBy('created_at', 'ASC')->get();
        $transactions = InventoryTransaction::with('product')->where('invoice_id', $id)->get();
        $invoice_statuses = InvoiceSetting::where('group', 'status')->get();
        $logs = Log::where('table', 'invoices')->where('ref', $id)->orderBy('created_at', 'DESC')->get();
        $payments = Payment::where('invoice', $id)->where('active', 'yes')->get();
        $profile = $this->companyProfile();
        $terms = $profile->terms ?? null;

        $pdf = Pdf::loadView('invoice.print-invoice', compact('invoice', 'invoice_items', 'invoice_statuses', 'logs', 'payments', 'terms', 'transactions'))
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->setPaper('letter', 'portrait');
        $pdf->save(public_path() . '/invoice.pdf');

        $mail_data = (object)[];
        $mail_data->invoice = $invoice;

        try {
            Mail::to($invoice->customer_email)->send(new MailInvoice($mail_data));
            if (File::exists(public_path() . '/invoice.pdf')) {
                File::delete(public_path() . '/invoice.pdf');
            }

            $log = new Log;
            $log->table = 'invoices';
            $log->data = 'Email has been Sent';
            $log->ref = $invoice->id;
            $log->user = Auth::id();
            $log->save();

            return $this->success(null, Lang::get('repair-business.error_email-has-been-sent'));
        } catch (Exception $ex) {
            if (File::exists(public_path() . '/invoice.pdf')) {
                File::delete(public_path() . '/invoice.pdf');
            }
            return $this->error(Lang::get('repair-business.error_something-went-wrong'), 500);
        }
    }

    public function itemsIndex($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        return $this->success([
            'items' => InvoiceItem::where('invoice', $invoice->id)
                ->orderBy('created_at', 'ASC')->get(),
        ]);
    }

    public function createItem(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'name' => 'required|min:2|max:250',
            'description' => 'required|min:2|max:250',
            'sub_description' => 'nullable|max:250',
            'unit_cost' => 'required|numeric|between:0,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
            'group' => 'nullable|string',
            'ref' => 'nullable|numeric',
        ]);

        $item = new InvoiceItem;
        $item->invoice = (int)$invoice->id;
        $item->name = $data['name'];
        $item->description = $data['description'];
        $item->sub_description = $data['sub_description'] ?? null;
        $item->unit_cost = (float)$data['unit_cost'];
        $item->quantity = (float)$data['quantity'];
        $item->group = $data['group'] ?? 'no-group';
        $item->ref = $data['ref'] ?? null;
        $item->total = (float)$item->unit_cost * (float)$item->quantity;
        $item->save();

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['item' => $item], Lang::get('repair-business.error_item-has-been-created'), 201);
    }

    public function updateItem(Request $request, $invoiceId, $itemId)
    {
        $invoice = Invoice::find($invoiceId);
        $item = InvoiceItem::find($itemId);

        if (!$invoice || !$item) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ((int)$item->invoice !== (int)$invoice->id) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate([
            'name' => 'required|min:2|max:250',
            'description' => 'required|min:2|max:250',
            'sub_description' => 'nullable|max:250',
            'unit_cost' => 'required|numeric|between:0,99999.99',
            'quantity' => 'required|numeric|between:1,99999',
        ]);

        $item_log_update = '';

        if ($item->name != $data['name']) {
            $item_log_update .= ' [name][FROM] ' . $item->name . ' [TO] ' . $data['name'];
        }
        if ($item->description != $data['description']) {
            $item_log_update .= ' [description][FROM] ' . $item->description . ' [TO] ' . $data['description'];
        }
        if (($item->sub_description ?? '') != ($data['sub_description'] ?? '')) {
            $item_log_update .= ' [sub_description][FROM] ' . $item->sub_description . ' [TO] ' . ($data['sub_description'] ?? '');
        }
        if ((float)$item->unit_cost != (float)$data['unit_cost']) {
            $item_log_update .= ' [unit_cost][FROM] ' . $item->unit_cost . ' [TO] ' . $data['unit_cost'];
        }
        if ((float)$item->quantity != (float)$data['quantity']) {
            $item_log_update .= ' [quantity][FROM] ' . $item->quantity . ' [TO] ' . $data['quantity'];
        }

        $item->name = $data['name'];
        $item->description = $data['description'];
        $item->sub_description = $data['sub_description'] ?? null;
        $item->unit_cost = (float)$data['unit_cost'];
        $item->quantity = (float)$data['quantity'];
        $item->total = (float)$item->unit_cost * (float)$item->quantity;
        $item->save();

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Updated' . $item_log_update;
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['item' => $item], Lang::get('repair-business.error_item-has-been-created'));
    }

    public function deleteItem($invoiceId, $itemId)
    {
        $invoice = Invoice::find($invoiceId);
        $item = InvoiceItem::find($itemId);

        if (!$invoice || !$item) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        if ((int)$item->invoice !== (int)$invoice->id) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Deleted [' . $item->name . '][' . $item->description . ']';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        $item->delete();
        $this->recomputeInvoice($invoice);

        return $this->success(['id' => (int)$itemId], Lang::get('repair-business.error_item-has-been-deleted'));
    }

    public function createRepairItem($repairId, $invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        $repair = Repair::find($repairId);

        if (!$invoice || !$repair) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $invoice_item = new InvoiceItem;
        $invoice_item->invoice = $invoice->id;
        $invoice_item->name = 'REPAIR #' . $repair->id;
        $invoice_item->description = trim(($repair->request ?? '') . ' ' . ($repair->target ?? ''));
        $invoice_item->sub_description = $this->repairJobsText($repair->id);
        $invoice_item->unit_cost = isset($repair->estimate) ? (float)$repair->estimate : 0;
        $invoice_item->quantity = 1;
        $invoice_item->ref = $repair->id;
        $invoice_item->group = 'repair';
        $invoice_item->total = (float)$invoice_item->unit_cost * (float)$invoice_item->quantity;
        $invoice_item->save();

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice Item has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['item' => $invoice_item], Lang::get('repair-business.error_item-has-been-created'), 201);
    }

    /* SETTINGS */

    public function settingsIndex()
    {
        return $this->success([
            'statuses' => InvoiceSetting::where('group', 'status')->orderBy('name')->get(),
            'tax' => $this->taxSetting(),
        ]);
    }

    protected function validateSetting(Request $request)
    {
        return $request->validate([
            'name' => 'required|alpha|min:2|max:50',
            'group' => 'required|alpha|min:2|max:50',
            'color' => 'required|alpha|min:2|max:50',
        ]);
    }

    public function createSetting(Request $request)
    {
        $data = $this->validateSetting($request);

        $setting = new InvoiceSetting;
        $setting->name = $data['name'];
        $setting->group = $data['group'];
        $setting->color = $data['color'];
        $setting->save();

        $log = new Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Created';
        $log->ref = $setting->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['setting' => $setting], Lang::get('repair-business.error_setting-has-been-created'), 201);
    }

    public function updateSetting(Request $request, $id)
    {
        $setting = InvoiceSetting::find($id);

        if (!$setting) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $this->validateSetting($request);

        $setting->name = $data['name'];
        $setting->group = $data['group'];
        $setting->color = $data['color'];
        $setting->save();

        $log = new Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['setting' => $setting], Lang::get('repair-business.error_setting-has-been-updated'));
    }

    public function deleteSetting($id)
    {
        $setting = InvoiceSetting::find($id);

        if (!$setting) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $count = Invoice::where('status', $setting->id)->where('active', 'yes')->count();
        if ($count > 0) {
            return $this->error('This status is in use and cannot be deleted.', 422);
        }

        $setting->delete();

        $log = new Log;
        $log->table = 'invoice_settings';
        $log->data = 'Invoice Setting has been Deleted';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['id' => (int)$id], Lang::get('repair-business.error_setting-has-been-deleted'));
    }

    public function updateTaxSetting(Request $request)
    {
        $data = $request->validate([
            'tax' => 'required|numeric|between:0,100',
        ]);

        $setting = Setting::firstOrCreate(
            ['name' => 'invoice_tax', 'group' => 'tax'],
            ['data' => '0']
        );
        $setting->data = (string)$data['tax'];
        $setting->save();

        return $this->success(['tax' => (float)$data['tax']], 'Tax has been updated.');
    }
}