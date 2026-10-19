<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\RequestMessage;
use App\Models\Notification;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    // Display all requests
    public function index()
    {
        $requests = Request::with(['user'])
            ->withCount('messages')
            ->latest()
            ->paginate(10);
        
        return view('admin.requests.index', compact('requests'));
    }

    // Show request details with messages
    public function show(Request $request)
    {
        $request->load(['user', 'processor', 'messages.user']);
        return view('admin.requests.show', compact('request'));
    }

    // Post message in conversation thread
    public function storeMessage(HttpRequest $httpRequest, Request $request)
    {
        $validated = $httpRequest->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,zip,txt',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($httpRequest->hasFile('attachment')) {
            $file = $httpRequest->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('request_attachments', 'public');
        }

        RequestMessage::create([
            'request_id' => $request->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_admin' => true,
        ]);

        // Auto move to in_progress if still pending
        if ($request->isPending()) {
            $request->update(['status' => 'in_progress']);
        }

        // Notify customer
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'New Message on Request #' . $request->id,
            'message' => 'Admin replied: ' . \Illuminate\Support\Str::limit($validated['message'], 75),
            'type' => 'request_update',
            'data' => [
                'request_id' => $request->id,
            ]
        ]);

        return back()->with('success', 'Message posted to request timeline.');
    }

    // Update request status
    public function update(HttpRequest $httpRequest, Request $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,approved,rejected,in_progress',
            'admin_notes' => 'nullable|string'
        ]);

        $request->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);
        
        // Send notification to customer
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Request Status Updated',
            'message' => 'Your request "' . $request->title . '" has been ' . $validated['status'],
            'type' => 'request_update',
            'data' => [
                'request_id' => $request->id,
                'status' => $validated['status'],
                'title' => $request->title
            ]
        ]);

        return back()->with('success', 'Request updated successfully!');
    }

    // Delete request
    public function destroy(Request $request)
    {
        $request->delete();
        
        return redirect()->route('admin.requests.index')
            ->with('success', 'Request deleted successfully!');
    }

    // Pending requests
    public function pending()
    {
        $requests = Request::with(['user'])
            ->withCount('messages')
            ->pending()
            ->latest()
            ->paginate(10);
        
        return view('admin.requests.pending', compact('requests'));
    }

    // Approve request
    public function approve(Request $request)
    {
        $request->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);
        
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Request Approved',
            'message' => 'Your request "' . $request->title . '" has been approved.',
            'type' => 'request_update'
        ]);

        return back()->with('success', 'Request approved successfully!');
    }

    // Reject request
    public function reject(Request $request)
    {
        $request->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);
        
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Request Rejected',
            'message' => 'Your request "' . $request->title . '" has been rejected.',
            'type' => 'request_update'
        ]);

        return back()->with('success', 'Request rejected successfully!');
    }

    // Mark as in progress
    public function inProgress(Request $request)
    {
        $request->update([
            'status' => 'in_progress',
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);
        
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Request In Progress',
            'message' => 'Your request "' . $request->title . '" is now being processed.',
            'type' => 'request_update'
        ]);

        return back()->with('success', 'Request marked as in progress!');
    }
}