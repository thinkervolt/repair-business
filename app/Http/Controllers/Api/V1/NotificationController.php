<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Lang;

class NotificationController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $notifications = Notification::orderBy('id', 'desc')->take(3)->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'ref' => $notification->ref,
                    'route' => $notification->route,
                    'created_at' => $notification->created_at ? $notification->created_at->format('M d, Y') : null,
                ];
            });

        $count = Notification::count();

        return $this->success(compact('notifications', 'count'));
    }

    public function destroy($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return $this->error(Lang::get('repair-business.error_not-found'), 404);
        }

        $route = $notification->route;
        $ref = $notification->ref;

        $notification->delete();

        return $this->success(compact('route', 'ref'));
    }
}