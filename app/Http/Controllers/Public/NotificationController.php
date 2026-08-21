<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get JSON list of unread notifications & count for logged-in user.
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = Notification::where('id_user', $user->id)
            ->where('is_read', false)
            ->count();

        $notifications = Notification::where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'data' => $n->data,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->diffForHumans(),
                    'url' => isset($n->data['url']) ? $n->data['url'] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark single notification as read and redirect to target URL.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notification = Notification::where('id_user', $user->id)->findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        $targetUrl = isset($notification->data['url']) ? $notification->data['url'] : url('/');
        return redirect($targetUrl);
    }

    /**
     * Mark all notifications as read for logged-in user.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        Notification::where('id_user', $user->id)->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }
}