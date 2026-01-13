@extends('admin.layouts.app')

@section('content')

<h4 class="mb-4">
    <i class="bi bi-speedometer2"></i> Dashboard
</h4>

<!-- Stats -->
<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card border-start border-primary border-3">
            <div class="card-body">
                <small class="text-muted">Total Customers</small>
                <h4 class="mb-1">{{ $totalCustomers }}</h4>
                <small class="text-muted">{{ $pendingCustomers }} pending</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-start border-warning border-3">
            <div class="card-body">
                <small class="text-muted">Active Customers</small>
                <h4 class="mb-1">{{ $activeCustomers }}</h4>
                <small class="text-muted">Approved users</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-start border-info border-3">
            <div class="card-body">
                <small class="text-muted">Total Requests</small>
                <h4 class="mb-1">{{ $totalRequests }}</h4>
                <small class="text-muted">{{ $pendingRequests }} pending</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-start border-success border-3">
            <div class="card-body">
                <small class="text-muted">Approved Requests</small>
                <h4 class="mb-1">{{ $approvedRequests }}</h4>
                <small class="text-muted">{{ $rejectedRequests }} rejected</small>
            </div>
        </div>
    </div>

</div>

<!-- Recent Activity -->
<div class="row">

    <!-- Requests -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <strong>Recent Requests</strong>
                <a href="{{ route('admin.requests.pending') }}" class="btn btn-sm btn-outline-primary">View</a>
            </div>

            <div class="card-body p-0">
                @forelse($recentRequests as $request)
                    <div class="border-bottom p-3">
                        <strong>{{ $request->title }}</strong><br>
                        <small class="text-muted">
                            {{ $request->user->name }} • {{ $request->created_at->diffForHumans() }}
                        </small>
                        <div class="mt-2">
                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            <a href="{{ route('admin.requests.show', $request) }}"
                               class="btn btn-sm btn-outline-primary float-end">
                                View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4 text-muted">
                        No requests found
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Customers -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <strong>Recent Customers</strong>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary">View</a>
            </div>

            <div class="card-body p-0">
                @forelse($recentCustomers as $customer)
                    <div class="border-bottom p-3">
                        <strong>{{ $customer->name }}</strong><br>
                        <small class="text-muted">
                            {{ $customer->email }} • {{ $customer->created_at->diffForHumans() }}
                        </small>

                        <div class="mt-2">
                            <span class="badge {{ $customer->getStatusBadgeClass() }}">
                                {{ ucfirst($customer->status) }}
                            </span>

                            <span class="badge {{ $customer->getApprovalBadgeClass() }}">
                                {{ $customer->is_approved ? 'Approved' : 'Pending' }}
                            </span>

                            <a href="{{ route('admin.customers.show', $customer) }}"
                               class="btn btn-sm btn-outline-primary float-end">
                                View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4 text-muted">
                        No customers found
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Request Types -->
<div class="card">
    <div class="card-header bg-white">
        <strong>Request Types</strong>
    </div>

    <div class="card-body">
        <div class="row text-center">

            <div class="col-md-3">
                <h6>Account Activation</h6>
                <p class="text-muted">{{ $requestTypes['account_activation'] ?? 0 }}</p>
            </div>

            <div class="col-md-3">
                <h6>Service Request</h6>
                <p class="text-muted">{{ $requestTypes['service_request'] ?? 0 }}</p>
            </div>

            <div class="col-md-3">
                <h6>Info Change</h6>
                <p class="text-muted">{{ $requestTypes['information_change'] ?? 0 }}</p>
            </div>

            <div class="col-md-3">
                <h6>Other</h6>
                <p class="text-muted">{{ $requestTypes['other'] ?? 0 }}</p>
            </div>

        </div>
    </div>
</div>

@endsection
