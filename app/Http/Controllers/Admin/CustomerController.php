<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Display all customers
    public function index()
    {
        $customers = User::customers()
            ->withCount(['requests'])
            ->latest()
            ->paginate(10);
        
        return view('admin.customers.index', compact('customers'));
    }

    // Show customer details
    public function show(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }
        
        $requests = $customer->requests()
            ->latest()
            ->paginate(5);
        
        return view('admin.customers.show', compact('customer', 'requests'));
    }

    // Edit customer
    public function edit(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }
        
        return view('admin.customers.edit', compact('customer'));
    }

    // Update customer
    public function update(Request $request, User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,active,inactive',
        ]);

        $oldStatus = $customer->status;
        
        $customer->update($validated);
        
        // Send notification if status changed
        if ($oldStatus !== $customer->status) {
            Notification::create([
                'user_id' => $customer->id,
                'title' => 'Account Status Updated',
                'message' => 'Your account status has been changed to: ' . ucfirst($customer->status),
                'type' => 'account_update',
                'data' => ['status' => $customer->status]
            ]);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    // Delete customer
    public function destroy(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->delete();
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    // Approve customer
    public function approve(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->update([
            'is_approved' => true,
            'status' => 'active',
            'approved_at' => now()
        ]);
        
        // Send notification to customer
        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Account Approved',
            'message' => 'Congratulations! Your account has been approved. You can now login.',
            'type' => 'account_update',
            'data' => ['approved' => true]
        ]);

        return back()->with('success', 'Customer approved successfully!');
    }

    // Activate customer
    public function activate(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->update(['status' => 'active']);
        
        // Send notification
        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Account Activated',
            'message' => 'Your account has been activated.',
            'type' => 'account_update'
        ]);

        return back()->with('success', 'Customer activated successfully!');
    }

    // Deactivate customer
    public function deactivate(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->update(['status' => 'inactive']);
        
        // Send notification
        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Account Deactivated',
            'message' => 'Your account has been deactivated. Please contact admin for assistance.',
            'type' => 'account_update'
        ]);

        return back()->with('success', 'Customer deactivated successfully!');
    }

    // Pending customers
    public function pending()
    {
        $customers = User::customers()
            ->pending()
            ->where('is_approved', false)
            ->latest()
            ->paginate(10);
        
        return view('admin.customers.pending', compact('customers'));
    }

    // Active customers
    public function active()
    {
        $customers = User::customers()
            ->active()
            ->approved()
            ->latest()
            ->paginate(10);
        
        return view('admin.customers.active', compact('customers'));
    }

    // Inactive customers
    public function inactive()
    {
        $customers = User::customers()
            ->inactive()
            ->latest()
            ->paginate(10);
        
        return view('admin.customers.inactive', compact('customers'));
    }
}