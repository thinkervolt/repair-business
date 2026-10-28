<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    use ApiResponses;

    protected function systemLocale()
    {
        $setting = Setting::where('group', 'language')->first();
        return in_array($setting ? $setting->data : null, ['en', 'es']) ? $setting->data : 'en';
    }

    protected function businessProfile()
    {
        $settings = Setting::where('group', 'business_profile')->get();
        $profile = [];
        foreach ($settings as $setting) {
            $profile[$setting->name] = $setting->data;
        }
        return $profile;
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->where('active', 'yes')->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(Lang::get('repair-business.error_invalid-login'), 401);
        }

        $token = $user->createToken('react-client')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
            'app_name' => config('app.name'),
            'business_profile' => $this->businessProfile(),
            'locale' => $this->systemLocale(),
        ], Lang::get('repair-business.login-success'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'active' => 'yes',
        ]);

        return $this->success($user, Lang::get('repair-business.user-created'), 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, Lang::get('repair-business.logout-success'));
    }

    public function me(Request $request)
    {
        return $this->success([
            'user' => $request->user(),
            'app_name' => config('app.name'),
            'business_profile' => $this->businessProfile(),
            'locale' => $this->systemLocale(),
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
            'expires' => 'required|integer',
        ]);

        $user = User::find($data['id']);

        if (!$user) {
            return $this->error(Lang::get('repair-business.error_invalid-verification'), 404);
        }

        if ((int) $data['expires'] < now()->getTimestamp()) {
            return $this->error(Lang::get('repair-business.error_verification-expired'), 400);
        }

        if (!hash_equals((string) sha1($user->getEmailForVerification()), (string) $data['hash'])) {
            return $this->error(Lang::get('repair-business.error_invalid-verification'), 400);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $this->success(null, Lang::get('repair-business.verification-success'));
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, Lang::get('repair-business.email-already-verified'));
        }

        $user->sendEmailVerificationNotification();

        return $this->success(null, Lang::get('repair-business.error_email-has-been-sent'));
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
        ]);

        Password::sendResetLink(['email' => $data['email']]);

        return $this->success(null, Lang::get('repair-business.error_email-has-been-sent'));
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset($data, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error(Lang::get('repair-business.error_password-reset-failed'), 422);
        }

        return $this->success(null, Lang::get('repair-business.password-update'));
    }
}