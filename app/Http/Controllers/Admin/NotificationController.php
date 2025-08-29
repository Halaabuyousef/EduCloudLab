<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification as DBNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // فلاتر اختيارية: unread / read / all
        $filter = $request->get('filter', 'all');
        $query  = $admin->notifications()->latest();

        if ($filter === 'unread') {
            $query = $admin->unreadNotifications()->latest();
        } elseif ($filter === 'read') {
            $query = $admin->readNotifications()->latest();
        }

        $notifications = $query->paginate(20);
        $unreadCount   = $admin->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function read(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $notification = $admin->notifications()->where('id', $id)->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // إذا في رابط مرفق بالإشعار، حوّل إليه
        $url = data_get($notification->data, 'url');
        return $url ? redirect($url) : back()->with('msg', 'Marked as read')->with('type', 'success');
    }

    public function readAll()
    {
        $admin = Auth::guard('admin')->user();
        $admin->unreadNotifications->markAsRead();
        return back()->with('msg', 'All notifications marked as read')->with('type', 'success');
    }

    // Endpoint بسيط يرجع عدد غير المقروء لتحديث الشارة (badge)
    public function badge()
    {
        $admin = Auth::guard('admin')->user();
        return response()->json([
            'count' => $admin->unreadNotifications()->count(),
        ]);
    }
    public function dropdown()
    {
        $admin = Auth::guard('admin')->user();

        $unreadCount = $admin->unreadNotifications()->count();
        $latest      = $admin->notifications()->latest()->limit(10)->get();

        // نرجّع Blade جزئي (HTML) مع العداد
        $html = view('admin.notifications._list', [
            'notifications' => $latest,
        ])->render();

        return response()->json([
            'count' => $unreadCount,
            'html'  => $html,
        ]);
    }
}