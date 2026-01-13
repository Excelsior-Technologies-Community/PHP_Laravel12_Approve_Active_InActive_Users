<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    // Display customer's requests
    public function index()
    {
        $requests = Request::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('customer.requests.index', compact('requests'));
    }

    // Show create request form
    public function create()
    {
        return view('customer.requests.create');
    }

    // Store new request
    public function store(HttpRequest $httpRequest)
    {
        $validated = $httpRequest->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:account_activation,service_request,information_change,other',
        ]);

        $request = Request::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('customer.requests.index')
            ->with('success', 'Request submitted successfully!');
    }

    // Show request details
    public function show(Request $request)
    {
        // Ensure the request belongs to the logged-in customer
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('customer.requests.show', compact('request'));
    }

    // Cancel request
    public function destroy(Request $request)
    {
        // Ensure the request belongs to the logged-in customer
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Only allow cancellation if request is still pending
        if (!$request->isPending()) {
            return back()->with('error', 'Cannot delete request that is already processed.');
        }
        
        $request->delete();
        
        return redirect()->route('customer.requests.index')
            ->with('success', 'Request deleted successfully!');
    }

    // Special request for account activation
    public function createActivationRequest()
    {
        // Check if user already has a pending activation request
        $existingRequest = Request::where('user_id', Auth::id())
            ->where('type', 'account_activation')
            ->pending()
            ->exists();
        
        if ($existingRequest) {
            return redirect()->route('customer.requests.index')
                ->with('info', 'You already have a pending account activation request.');
        }
        
        return view('customer.requests.activation');
    }

    // Submit activation request
    public function storeActivationRequest(HttpRequest $httpRequest)
    {
        // Check if user already has a pending activation request
        $existingRequest = Request::where('user_id', Auth::id())
            ->where('type', 'account_activation')
            ->pending()
            ->exists();
        
        if ($existingRequest) {
            return redirect()->route('customer.requests.index')
                ->with('info', 'You already have a pending account activation request.');
        }

        $validated = $httpRequest->validate([
            'description' => 'required|string|min:10',
        ]);

        $request = Request::create([
            'user_id' => Auth::id(),
            'title' => 'Account Activation Request',
            'description' => $validated['description'],
            'type' => 'account_activation',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('customer.requests.index')
            ->with('success', 'Account activation request submitted successfully!');
    }
}