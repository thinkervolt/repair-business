<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Setting;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class SettingController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $settings = Setting::orderBy('group', 'asc')->orderBy('name', 'asc')->get()->groupBy('group');

        return $this->success($settings);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'terms' => 'nullable|string',
        ]);

        foreach ($data as $name => $value) {
            $setting = Setting::firstOrCreate(
                ['group' => 'business_profile', 'name' => $name],
                ['data' => '']
            );
            $setting->data = (string)$value;
            $setting->save();
        }

        $log = new Log;
        $log->table = 'settings';
        $log->data = 'Business Profile has been Updated';
        $log->ref = 0;
        $log->user = Auth::id();
        $log->save();

        $profile = $this->businessProfile();
        $profile->phone = $this->formatPhone($profile->phone ?? '') ?: null;

        return $this->success(['business_profile' => $profile], Lang::get('repair-business.error_setting-has-been-updated'));
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $request->validate([
            'data' => 'required|string|max:1000',
        ]);

        $setting->data = $request->data;
        $setting->save();

        $log = new Log;
        $log->table = 'settings';
        $log->data = 'Setting has been Updated';
        $log->ref = $id;
        $log->user = Auth::id();
        $log->save();

        return $this->success(['setting' => $setting], Lang::get('repair-business.error_setting-has-been-updated'));
    }

    protected function businessProfile()
    {
        $settings = Setting::where('group', 'business_profile')->get();
        $profile = (object)[];
        foreach ($settings as $setting) {
            $profile->{$setting->name} = $setting->data;
        }
        return $profile;
    }

    protected function formatPhone($phone)
    {
        return preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $phone);
    }
}