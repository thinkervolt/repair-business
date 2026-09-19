<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class UserController extends Controller
{
    use ApiResponses;

    public function profile(Request $request)
    {
        $user = User::select('name', 'email', 'role')->where('id', $request->user()->id)->firstOrFail();

        return $this->success(['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|min:8|string',
            'password' => 'required|min:8|string',
            'password_confirmation' => 'required|min:8|string',
        ]);

        if (!Hash::check($data['current_password'], $request->user()->password)) {
            return $this->error(Lang::get('repair-business.error_current-password-does-not-match'), 422);
        }

        if ($data['password'] !== $data['password_confirmation']) {
            return $this->error(Lang::get('repair-business.error_new-password-does-not-match'), 422);
        }

        $user = User::findOrFail($request->user()->id);
        $user->password = Hash::make($data['password']);
        $user->save();

        $request->user()->currentAccessToken()->delete();

        return $this->success(null, Lang::get('repair-business.error_password-has-been-updated'));
    }

    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->where('active', 'yes')
            ->where('id', '!=', $request->user()->id)
            ->get();

        return $this->success(['users' => $users]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|string|min:2|max:50',
            'password' => 'nullable|min:8|string',
            'role' => 'required',
        ]);

        $user->role = $data['role'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return $this->success($user, Lang::get('repair-business.error_user-has-been-updated'));
    }

    public function resetPassword(Request $request, $id)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($data['password']);
        $user->save();

        return $this->success(null, Lang::get('repair-business.error_user-password-reset'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $this->success(null, Lang::get('repair-business.error_user-has-been-deleted'));
    }
}