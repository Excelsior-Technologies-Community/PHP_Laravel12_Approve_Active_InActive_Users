<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // Display all customers
    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::customers()->withCount('requests');

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Approval Filter
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->approved);
        }

        $customers = $query
            ->oldest()
            ->paginate(5)
            ->withQueryString();

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
            'is_approved' => 'nullable|boolean',
        ]);

        $oldStatus = $customer->status;
        $oldApproval = $customer->is_approved;

        // Handle approval checkbox
        $validated['is_approved'] = $request->has('is_approved');

        // Set approved_at only the first time customer is approved
        if ($validated['is_approved'] && !$customer->approved_at) {
            $validated['approved_at'] = now();
        }

        // Clear approved_at if approval removed (optional)
        if (!$validated['is_approved']) {
            $validated['approved_at'] = null;
        }

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

        // Audit log for approval
        if (!$oldApproval && $customer->is_approved) {
            AuditLog::create([
                'user_id' => $customer->id,
                'admin_id' => auth()->id(),
                'action' => 'Approved',
                'description' => auth()->user()->name . ' approved the customer.',
            ]);
        }

        // Audit log for update
        AuditLog::create([
            'user_id' => $customer->id,
            'admin_id' => auth()->id(),
            'action' => 'Updated',
            'description' => auth()->user()->name . ' updated customer information.',
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    // Delete customer
    public function destroy(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        AuditLog::create([

            'user_id' => $customer->id,

            'admin_id' => auth()->id(),

            'action' => 'Deleted',

            'description' => auth()->user()->name .
                ' deleted customer account.',

        ]);

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

        AuditLog::create([

            'user_id' => $customer->id,

            'admin_id' => auth()->id(),

            'action' => 'Approved',

            'description' => auth()->user()->name .
                ' approved customer account.',

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

        AuditLog::create([

            'user_id' => $customer->id,

            'admin_id' => auth()->id(),

            'action' => 'Activated',

            'description' => auth()->user()->name .
                ' activated customer account.',

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

        AuditLog::create([

            'user_id' => $customer->id,

            'admin_id' => auth()->id(),

            'action' => 'Deactivated',

            'description' => auth()->user()->name .
                ' deactivated customer account.',

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

    public function exportCsv()
    {
        $fileName = 'customers_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        $callback = function () {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Status',
                'Approved',
                'Registered Date'
            ]);

            $customers = User::customers()->get();

            foreach ($customers as $customer) {

                fputcsv($file, [

                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    ucfirst($customer->status),
                    $customer->is_approved ? 'Yes' : 'No',
                    $customer->created_at->format('d-m-Y'),

                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function history(User $customer)
    {
        $logs = AuditLog::with('admin')
            ->where('user_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view(
            'admin.customers.history',
            compact('customer', 'logs')
        );
    }
}
