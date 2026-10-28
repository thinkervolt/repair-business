    public function settingsIndex()
    {
        return $this->success([
            'statuses' => InvoiceSetting::where('group', 'status')->orderBy('name')->get(),
            'tax' => $this->taxSetting(),
        ]);
    }

    public function createSetting(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'group' => 'required|string',
            'color' => 'nullable|string',
        ]);

        $setting = new InvoiceSetting;
        $setting->name = $data['name'];
        $setting->group = $data['group'];
        $setting->color = $data['color'] ?? '#000000';
        $setting->save();

        return $this->success(['setting' => $setting], 'Setting has been created.', 201);
    }

    public function updateSetting(Request $request, $id)
    {
        $setting = InvoiceSetting::findOrFail($id);
        $data = $request->validate([
            'name' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        if (isset($data['name'])) {
            $setting->name = $data['name'];
        }
        if (isset($data['color'])) {
            $setting->color = $data['color'];
        }
        $setting->save();

        return $this->success(['setting' => $setting], 'Setting has been updated.');
    }

    public function deleteSetting($id)
    {
        $setting = InvoiceSetting::findOrFail($id);
        $count = Invoice::where('status', $setting->id)->where('active', 'yes')->count();
        if ($count > 0) {
            return $this->error('This status is in use and cannot be deleted.', 422);
        }
        $setting->delete();
        return $this->success(['id' => (int)$id], 'Setting has been deleted.');
    }

    public function updateTaxSetting(Request $request)
    {
        $data = $request->validate([
            'tax' => 'required|numeric',
        ]);

        $setting = Setting::firstOrCreate(
            ['name' => 'invoice_tax', 'group' => 'tax'],
            ['data' => '0']
        );
        $setting->data = (string)$data['tax'];
        $setting->save();

        return $this->success(['tax' => (float)$data['tax']], 'Tax has been updated.');
    }
