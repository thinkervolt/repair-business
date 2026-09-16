    public function index(Request $request)
    {
        $task = $request->query('task');
        $search = $request->filled('search') ? '%' . $request->search . '%' : null;

        $status_ids = InvoiceSetting::select('id')
            ->where('group', 'status')
            ->where('name', 'LIKE', $search)
            ->pluck('id');

        if ($task === 'unpaid') {
            $invoices = Invoice::where('balance', '>', 0)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        } elseif ($task === 'group_by_status' && $request->filled('status')) {
            $invoices = Invoice::where('status', $request->status)->where('active', 'yes')
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

            $invoices = Invoice::whereIn('id', $ids)->where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        } else {
            $invoices = Invoice::where('active', 'yes')
                ->orderBy('created_at', 'DESC')->paginate(25);
        }

        return $this->success([
            'invoices' => $invoices->getCollection()->values(),
            'statuses' => InvoiceSetting::where('group', 'status')->orderBy('name')->get(),
            'pagination' => [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);

        $items = InvoiceItem::where('invoice', $invoice->id)->orderBy('created_at', 'ASC')->get();
        $transactions = InventoryTransaction::where('invoice_id', $invoice->id)
            ->orderBy('created_at', 'DESC')->get();
        $payments = Payment::where('invoice', $invoice->id)->where('active', 'yes')
            ->orderBy('created_at', 'DESC')->get();
        $logs = Log::where('table', 'invoices')->where('ref', $invoice->id)
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
            'logs' => $logs->getCollection()->values(),
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
        $tax_porcentage = $this->taxSetting();

        $invoice = new Invoice;
        $invoice->active = 'yes';
        $invoice->user = Auth::id();
        $invoice->tax_porcentage = $tax_porcentage;

        $invoice->company_name = $profile->name ?? null;
        $invoice->company_phone = $profile->phone ?? null;
        $invoice->company_email = $profile->email ?? null;
        $invoice->company_address = $profile->address ?? null;

        if (isset($data['customer'])) {
            $customer = Customer::find($data['customer']);
            if ($customer) {
                $invoice->customer_id = $customer->id;
                $invoice->customer_name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                $invoice->customer_phone = $customer->phone ?? null;
                $invoice->customer_email = $customer->email ?? null;
                $invoice->customer_address = $customer->address ?? null;
                $invoice->customer_company = $customer->company ?? null;
            }
        }

        $invoice->subtotal = 0;
        $invoice->tax = 0;
        $invoice->total = 0;
        $invoice->balance = 0;
        $invoice->save();
