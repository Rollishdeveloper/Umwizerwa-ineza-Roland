<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['status' => 'read']);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('status', 'unread')
            ->update(['status' => 'read']);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('status', 'unread')
            ->count();
        return response()->json(['count' => $count]);
    }
}
