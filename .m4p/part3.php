        if (isset($data['repair'])) {
            $repair = Repair::find($data['repair']);
            if ($repair) {
                $invoice->customer_id = $repair->customer_id;

                if ($repair->customer_data) {
                    $invoice->customer_name = trim(($repair->customer_data->first_name ?? '') . ' ' . ($repair->customer_data->last_name ?? ''));
                    $invoice->customer_phone = $repair->customer_data->phone ?? null;
                    $invoice->customer_email = $repair->customer_data->email ?? null;
                    $invoice->customer_address = $repair->customer_data->address ?? null;
                    $invoice->customer_company = $repair->customer_data->company ?? null;
                }
                $invoice->save();

                $item = new InvoiceItem;
                $item->invoice = $invoice->id;
                $item->name = 'REPAIR #' . $repair->id;
                $item->description = trim(($repair->request ?? '') . ' ' . ($repair->target ?? ''));
                $item->unit_cost = isset($repair->estimate) ? (float)$repair->estimate : 0;
                $item->quantity = 1;
                $item->ref = $repair->id;
                $item->group = 'repair';
                $item->total = (float)$item->unit_cost * $item->quantity;
                $item->save();
            }
        }

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoices';
        $log->data = 'Invoice has been Created';
        $log->ref = $invoice->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['invoice' => $invoice], 'Invoice has been created.', 201);
    }

    public function itemsIndex($id)
    {
        $invoice = Invoice::findOrFail($id);
        return $this->success([
            'items' => InvoiceItem::where('invoice', $invoice->id)
                ->orderBy('created_at', 'ASC')->get(),
        ]);
    }
