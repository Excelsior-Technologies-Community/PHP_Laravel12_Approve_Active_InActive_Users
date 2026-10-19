<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\Notification;
use App\Models\User;
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
            ->withCount('messages')
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

    // Upload KYC Document
    public function uploadKyc(HttpRequest $request)
    {
        $validated = $request->validate([
            'id_document_type' => 'required|string|in:National ID,Passport,Driving License,Business Registration',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = Auth::user();

        $path = $request->file('id_document')->store('kyc_documents', 'public');

        $user->update([
            'id_document_type' => $validated['id_document_type'],
            'id_document_path' => $path,
            'kyc_status' => 'pending',
            'kyc_notes' => null,
        ]);

        // Notify Admins
        $admins = User::admins()->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New KYC Document Submitted',
                'message' => "{$user->name} uploaded a {$validated['id_document_type']} for identity verification.",
                'type' => 'account_update',
            ]);
        }

        return back()->with('success', 'Your identity document has been submitted for verification.');
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
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return back()->with('success', 'All notifications marked as read.');
    }

    // View all notifications
    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        
        return view('customer.notifications', compact('notifications'));
    }
}