<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\Repair;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $search = $request->filled('search') ? $request->search : '';

        $search_customer = Customer::select('id')
            ->where('last_name', 'LIKE', '%' . $search . '%')
            ->orWhere('first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('id', 'LIKE', '%' . $search . '%')
            ->orWhere('phone', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');

        $customers = Customer::whereIn('id', $search_customer)
            ->where('active', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        $search_repair = Repair::select('id')
            ->where('target', 'LIKE', '%' . $search . '%')
            ->orWhere('request', 'LIKE', '%' . $search . '%')
            ->orWhereIn('customer', $search_customer);

        $repairs = Repair::with('customer_data')
            ->whereIn('id', $search_repair)
            ->where('active', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        $search_priority = InvoiceSetting::select('id')
            ->where('name', 'LIKE', '%' . $search . '%');

        $search_invoices = Invoice::select('id')
            ->where('customer_name', 'LIKE', '%' . $search . '%')
            ->orWhere('customer_email', 'LIKE', '%' . $search . '%')
            ->orWhere('customer_phone', 'LIKE', '%' . $search . '%')
            ->orWhere('id', 'LIKE', '%' . $search . '%')
            ->orWhereIn('status', $search_priority);

        $invoices = Invoice::whereIn('id', $search_invoices)
            ->where('active', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->success([
            'customers' => $customers,
            'repairs' => $repairs,
            'invoices' => $invoices,
            'search' => $search,
        ]);
    }
}