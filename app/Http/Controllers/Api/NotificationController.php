<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/v1/notifications?unread=1
    public function index(Request $request)
    {
        $me = $request->user();
        $onlyUnread = $request->boolean('unread', false);

        $q = $onlyUnread ? $me->unreadNotifications() : $me->notifications();
        $pag = $q->latest()->paginate(15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'items' => $pag->through(function ($n) {
                    return [
                        'id'         => $n->id,
                        'type'       => class_basename($n->type),
                        'title'      => data_get($n->data, 'title', 'Notification'),
                        'body'       => data_get($n->data, 'body'),
                        'read_at'    => optional($n->read_at)?->toIso8601String(),
                        'created_at' => $n->created_at?->toIso8601String(),
                    ];
                }),
                'meta' => [
                    'current_page' => $pag->currentPage(),
                    'per_page'     => $pag->perPage(),
                    'total'        => $pag->total(),
                    'last_page'    => $pag->lastPage(),
                ]
            ]
        ]);
    }

    // POST /api/v1/notifications/{id}/read
    public function markRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        if (is_null($n->read_at)) $n->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notification marked as read', 'data' => null]);
    }

    // POST /api/v1/notifications/read-all
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read', 'data' => null]);
    }
}
