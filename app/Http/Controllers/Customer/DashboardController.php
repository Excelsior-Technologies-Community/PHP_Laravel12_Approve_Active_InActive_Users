<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\Notification;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Update last login
        $user->update(['last_login' => now()]);
        
        // Get user's requests
        $requests = Request::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Get unread notifications
        $notifications = Notification::where('user_id', $user->id)
            ->unread()
            ->latest()
            ->take(5)
            ->get();
        
        // Request statistics
        $totalRequests = Request::where('user_id', $user->id)->count();
        $pendingRequests = Request::where('user_id', $user->id)->pending()->count();
        $approvedRequests = Request::where('user_id', $user->id)->approved()->count();
        
        return view('customer.dashboard', compact(
            'user',
            'requests',
            'notifications',
            'totalRequests',
            'pendingRequests',
            'approvedRequests'
        ));
    }

    // Mark notification as read
    public function markNotificationAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();
        
        return back()->with('success', 'Notification marked as read.');
    }

    // Mark all notifications as read
    public function markAllNotificationsAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['is_read' => true]);
        
        return back()->with('success', 'All notifications marked as read.');
    }

    // View all notifications
    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('customer.notifications', compact('notifications'));
    }
}