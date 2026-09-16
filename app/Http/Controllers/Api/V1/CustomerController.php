<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Repair;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class CustomerController extends Controller
{
    use ApiResponses;

    protected function rules(int $id = null): array
    {
        return [
            'first_name' => 'required|alpha|min:2|max:50',
            'last_name' => 'nullable|alpha|min:2|max:50',
            'phone' => 'required|numeric|digits_between:10,10|unique:customers,phone' . ($id ? ',' . $id : ''),
            'email' => 'nullable|email|unique:customers,email' . ($id ? ',' . $id : ''),
            'address' => 'nullable|min:2|max:250',
            'city' => 'nullable|min:2|max:250',
            'state' => 'nullable|alpha|min:2|max:2',
            'zip' => 'nullable|numeric|digits_between:5,5',
            'company' => 'nullable|min:2|max:50',
        ];
    }

    public function index(Request $request)
    {
        $query = Customer::where('active', 'yes');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'LIKE', $search)
                    ->orWhere('first_name', 'LIKE', $search)
                    ->orWhere('id', 'LIKE', $search)
                    ->orWhere('phone', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search)
                    ->orWhere('company', 'LIKE', $search);
            });
        }

        $customers = $query->orderBy('created_at', 'DESC')->paginate(25);

        return $this->success($customers);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $customer = new Customer;
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'] ?? null;
        $customer->phone = $data['phone'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->city = $data['city'] ?? null;
        $customer->state = $data['state'] ?? null;
        $customer->zip = $data['zip'] ?? null;
        $customer->company = $data['company'] ?? null;
        $customer->active = 'yes';
        $customer->save();

        $log = new Log;
        $log->table = 'customers';
        $log->data = 'Customer has been Created';
        $log->ref = $customer->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($customer, Lang::get('repair-business.error_customer-has-been-created'), 201);
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $repairs = Repair::with(['status_data', 'priority_data'])
            ->where('customer', $id)
            ->where('active', 'yes')
            ->orderBy('created_at', 'DESC')
            ->paginate(25);

        $invoices = Invoice::with('status_data')
            ->where('customer_id', $id)
            ->where('active', 'yes')
            ->orderBy('created_at', 'DESC')
            ->paginate(25);

        return $this->success([
            'customer' => $customer,
            'repairs' => $repairs,
            'invoices' => $invoices,
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $data = $request->validate($this->rules($id));

        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'] ?? null;
        $customer->phone = $data['phone'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->city = $data['city'] ?? null;
        $customer->state = $data['state'] ?? null;
        $customer->zip = $data['zip'] ?? null;
        $customer->company = $data['company'] ?? null;
        $customer->save();

        $log = new Log;
        $log->table = 'customers';
        $log->data = 'Customer has been Updated';
        $log->ref = $customer->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success($customer, Lang::get('repair-business.error_customer-has-been-updated'));
    }

    public function softDelete($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $customer->active = 'no';
        $customer->save();

        $log = new Log;
        $log->table = 'customers';
        $log->data = 'Customer has been Deleted';
        $log->ref = $customer->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_customer-has-been-deleted'));
    }

    public function restore($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $customer->active = 'yes';
        $customer->save();

        $log = new Log;
        $log->table = 'users';
        $log->data = 'Customer has been Restored';
        $log->ref = $customer->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(null, Lang::get('repair-business.error_customer-has-been-restored'));
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $customer->delete();

        Log::where('table', 'customers')->where('ref', $id)->delete();

        return $this->success(null, Lang::get('repair-business.error_customer-has-been-destroyed'));
    }

    public function publicNewCustomer(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|alpha|min:2|max:50',
            'last_name' => 'nullable|alpha|min:2|max:50',
            'phone' => 'required|numeric|digits_between:10,10|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|min:2|max:250',
            'city' => 'nullable|min:2|max:250',
            'state' => 'nullable|alpha|min:2|max:2',
            'zip' => 'nullable|numeric|digits_between:5,5',
        ]);

        $customer = new Customer;
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'] ?? null;
        $customer->phone = $data['phone'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->city = $data['city'] ?? null;
        $customer->state = $data['state'] ?? null;
        $customer->zip = $data['zip'] ?? null;
        $customer->active = 'yes';
        $customer->save();

        $notification = new Notification;
        $notification->message = Lang::get('repair-business.new-customer-signed-up');
        $notification->ref = $customer->id;
        $notification->route = 'view-customer';
        $notification->save();

        return $this->success($customer, Lang::get('repair-business.error_you-have-been-signed-up'), 201);
    }
}