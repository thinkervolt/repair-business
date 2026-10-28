<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class LogController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $search = $request->filled('search') ? $request->search : '';

        $users = User::select('id')->where('name', 'LIKE', '%' . $search . '%');

        $logs = Log::with('user_data')
            ->where('data', 'LIKE', '%' . $search . '%')
            ->orWhereIn('user', $users)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);

        return $this->success($logs);
    }
}