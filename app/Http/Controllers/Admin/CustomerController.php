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
    public function index(Request $request)
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

        // KYC Status Filter
        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        $customers = $query
            ->latest()
            ->paginate(10)
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
            'kyc_status' => 'nullable|in:unsubmitted,pending,verified,rejected',
        ]);

        $oldStatus = $customer->status;
        $oldApproval = $customer->is_approved;

        // Handle approval checkbox
        $validated['is_approved'] = $request->has('is_approved');

        // Set approved_at only the first time customer is approved
        if ($validated['is_approved'] && !$customer->approved_at) {
            $validated['approved_at'] = now();
        }

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
            'description' => auth()->user()->name . ' deleted customer account.',
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
            'description' => auth()->user()->name . ' approved customer account.',
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
            'description' => auth()->user()->name . ' activated customer account.',
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
            'description' => auth()->user()->name . ' deactivated customer account.',
        ]);

        return back()->with('success', 'Customer deactivated successfully!');
    }

    // Feature 1: Bulk / Batch Actions
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,activate,deactivate,delete',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:users,id',
        ]);

        $action = $validated['action'];
        $customerIds = $validated['customer_ids'];
        $admin = auth()->user();
        $count = count($customerIds);

        DB::transaction(function () use ($action, $customerIds, $admin) {
            foreach ($customerIds as $id) {
                $customer = User::find($id);
                if (!$customer || !$customer->isCustomer()) continue;

                if ($action === 'approve') {
                    $customer->update([
                        'is_approved' => true,
                        'status' => 'active',
                        'approved_at' => now(),
                    ]);
                    Notification::create([
                        'user_id' => $customer->id,
                        'title' => 'Account Approved',
                        'message' => 'Your account has been approved and activated.',
                        'type' => 'account_update',
                    ]);
                    AuditLog::create([
                        'user_id' => $customer->id,
                        'admin_id' => $admin->id,
                        'action' => 'Bulk Approved',
                        'description' => "{$admin->name} approved this customer via bulk action.",
                    ]);
                } elseif ($action === 'activate') {
                    $customer->update(['status' => 'active']);
                    Notification::create([
                        'user_id' => $customer->id,
                        'title' => 'Account Activated',
                        'message' => 'Your account has been activated.',
                        'type' => 'account_update',
                    ]);
                    AuditLog::create([
                        'user_id' => $customer->id,
                        'admin_id' => $admin->id,
                        'action' => 'Bulk Activated',
                        'description' => "{$admin->name} activated this customer via bulk action.",
                    ]);
                } elseif ($action === 'deactivate') {
                    $customer->update(['status' => 'inactive']);
                    Notification::create([
                        'user_id' => $customer->id,
                        'title' => 'Account Deactivated',
                        'message' => 'Your account has been deactivated.',
                        'type' => 'account_update',
                    ]);
                    AuditLog::create([
                        'user_id' => $customer->id,
                        'admin_id' => $admin->id,
                        'action' => 'Bulk Deactivated',
                        'description' => "{$admin->name} deactivated this customer via bulk action.",
                    ]);
                } elseif ($action === 'delete') {
                    AuditLog::create([
                        'user_id' => $customer->id,
                        'admin_id' => $admin->id,
                        'action' => 'Bulk Deleted',
                        'description' => "{$admin->name} deleted this customer via bulk action.",
                    ]);
                    $customer->delete();
                }
            }
        });

        $actionWord = match ($action) {
            'approve' => 'approved',
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'delete' => 'deleted',
        };

        return back()->with('success', "Bulk action completed! Successfully {$actionWord} {$count} customer(s).");
    }

    // Feature 2: KYC Document Approval
    public function approveKyc(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->update([
            'kyc_status' => 'verified',
            'kyc_verified_at' => now(),
            'kyc_verified_by' => auth()->id(),
            'kyc_notes' => null,
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Identity Document Verified',
            'message' => 'Your submitted identity document (' . ($customer->id_document_type ?: 'ID') . ') has been verified successfully.',
            'type' => 'account_update',
        ]);

        AuditLog::create([
            'user_id' => $customer->id,
            'admin_id' => auth()->id(),
            'action' => 'KYC Verified',
            'description' => auth()->user()->name . ' verified customer identity document.',
        ]);

        return back()->with('success', 'Customer KYC document approved and marked as Verified.');
    }

    // Feature 2: KYC Document Rejection / Request Re-upload
    public function rejectKyc(Request $request, User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $customer->update([
            'kyc_status' => 'rejected',
            'kyc_notes' => $validated['rejection_reason'],
            'kyc_verified_at' => null,
            'kyc_verified_by' => auth()->id(),
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Identity Document Rejected',
            'message' => 'Your identity document verification was rejected: ' . $validated['rejection_reason'] . '. Please re-upload a clear document.',
            'type' => 'account_update',
        ]);

        AuditLog::create([
            'user_id' => $customer->id,
            'admin_id' => auth()->id(),
            'action' => 'KYC Rejected',
            'description' => auth()->user()->name . ' rejected KYC document. Reason: ' . $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Customer KYC document rejected and notification sent.');
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
                'KYC Status',
                'ID Document Type',
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
                    ucfirst($customer->kyc_status),
                    $customer->id_document_type ?? 'N/A',
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

        return view('admin.customers.history', compact('customer', 'logs'));
    }
}
