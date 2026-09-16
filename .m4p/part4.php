    public function createItem(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $data = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'unit_cost' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
            'group' => 'nullable|string',
            'ref' => 'nullable|numeric',
        ]);

        $item = new InvoiceItem;
        $item->invoice = (int)$invoice->id;
        $item->name = $data['name'] ?? '';
        $item->description = $data['description'] ?? '';
        $item->unit_cost = (float)($data['unit_cost'] ?? 0);
        $item->quantity = (float)($data['quantity'] ?? 1);
        $item->group = $data['group'] ?? 'item';
        $item->ref = $data['ref'] ?? null;
        $item->total = (float)$item->unit_cost * (float)$item->quantity;
        $item->save();

        $this->recomputeInvoice($invoice);

        $log = new Log;
        $log->table = 'invoice_items';
        $log->data = 'Item has been added to the invoice';
        $log->ref = $item->id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['item' => $item], 'Item has been added.', 201);
    }

    public function updateItem(Request $request, $invoiceId, $itemId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::findOrFail($itemId);
        $data = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'unit_cost' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
            'group' => 'nullable|string',
        ]);

        if (isset($data['name'])) {
            $item->name = $data['name'];
        }
        if (isset($data['description'])) {
            $item->description = $data['description'];
        }
        if (isset($data['unit_cost'])) {
            $item->unit_cost = (float)$data['unit_cost'];
        }
        if (isset($data['quantity'])) {
            $item->quantity = (float)$data['quantity'];
        }
        if (isset($data['group'])) {
            $item->group = $data['group'];
        }
        $item->total = (float)$item->unit_cost * (float)$item->quantity;
        $item->save();

        $this->recomputeInvoice($invoice);

        return $this->success(['item' => $item], 'Item has been updated.');
    }

    public function deleteItem($invoiceId, $itemId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::findOrFail($itemId);

        if ((int)$item->invoice !== (int)$invoice->id) {
            return $this->error('Item does not belong to this invoice.', 422);
        }

        $item->delete();
        $this->recomputeInvoice($invoice);

        return $this->success(['id' => (int)$itemId], 'Item has been deleted.');
    }
