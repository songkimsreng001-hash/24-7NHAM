<?php

namespace App\Http\Controllers;

use App\Models\InAppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = InAppNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('target_role', $user->role)
                ->orWhere('target_role', 'all');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('frontend.notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $query = InAppNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('target_role', $user->role)
                ->orWhere('target_role', 'all');
        });

        $count = (clone $query)->where('is_read', false)->count();
        $recent = (clone $query)->orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'count' => $count,
            'notifications' => $recent,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = InAppNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('target_role', $user->role)
                ->orWhere('target_role', 'all');
        })->findOrFail($id);

        $notification->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        InAppNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('target_role', $user->role)
                ->orWhere('target_role', 'all');
        })->where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
