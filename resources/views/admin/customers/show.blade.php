@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-person me-2"></i> Customer Details
        </h2>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Customers
        </a>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Customer ID</th>
                            <td>{{ $customer->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $customer->status == 'active' ? 'bg-success' : ($customer->status == 'inactive' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Account Approval</th>
                            <td>
                                @if($customer->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                    @if($customer->approved_at)
                                        <small class="text-muted"> ({{ $customer->approved_at->format('M d, Y') }})</small>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Pending Approval</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Registered</th>
                            <td>{{ $customer->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Login</th>
                            <td>
                                @if($customer->last_login)
                                    {{ $customer->last_login->format('F d, Y h:i A') }}
                                    <br>
                                    <small class="text-muted">({{ $customer->last_login->diffForHumans() }})</small>
                                @else
                                    Never
                                @endif
                            </td>
                        </tr>
                    </table>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-3">
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-1"></i> Edit Customer
                        </a>
                        @if(!$customer->is_approved)
                            <form action="{{ route('admin.customers.approve', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="bi bi-check-circle me-1"></i> Approve Account
                                </button>
                            </form>
                        @endif
                        @if($customer->status == 'active')
                            <form action="{{ route('admin.customers.deactivate', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning me-2">
                                    <i class="bi bi-x-circle me-1"></i> Deactivate
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.customers.activate', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="bi bi-check-circle me-1"></i> Activate
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this customer?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Customer Requests -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Requests</h5>
                    <span class="badge bg-primary">{{ $requests->total() }} requests</span>
                </div>
                <div class="card-body">
                    @if($requests->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem; color: #dee2e6;"></i>
                            <p class="text-muted mt-2">No requests from this customer</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                    <tr>
                                        <td>#{{ $request->id }}</td>
                                        <td>{{ Str::limit($request->title, 40) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $request->getTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $request->created_at->format('M d') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.requests.show', $request) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Request Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px;">
                            <i class="bi bi-envelope-paper" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    
                    <div class="list-group">
                        @php
                            $total = $customer->requests()->count();
                            $pending = $customer->requests()->where('status', 'pending')->count();
                            $approved = $customer->requests()->where('status', 'approved')->count();
                            $rejected = $customer->requests()->where('status', 'rejected')->count();
                        @endphp
                        
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between">
                                <span>Total Requests</span>
                                <span class="badge bg-primary rounded-pill">{{ $total }}</span>
                            </div>
                        </div>
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between">
                                <span>Pending</span>
                                <span class="badge bg-warning rounded-pill">{{ $pending }}</span>
                            </div>
                        </div>
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between">
                                <span>Approved</span>
                                <span class="badge bg-success rounded-pill">{{ $approved }}</span>
                            </div>
                        </div>
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between">
                                <span>Rejected</span>
                                <span class="badge bg-danger rounded-pill">{{ $rejected }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$customer->is_approved)
                            <form action="{{ route('admin.customers.approve', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-check-circle me-1"></i> Approve Account
                                </button>
                            </form>
                        @endif
                        
                        @if($customer->status == 'active')
                            <form action="{{ route('admin.customers.deactivate', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2">
                                    <i class="bi bi-x-circle me-1"></i> Deactivate Account
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.customers.activate', $customer) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-check-circle me-1"></i> Activate Account
                                </button>
                            </form>
                        @endif
                        
                        <a href="mailto:{{ $customer->email }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-envelope me-1"></i> Send Email
                        </a>
                        
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="bi bi-pencil me-1"></i> Edit Profile
                        </a>
                        
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                              onsubmit="return confirm('Delete this customer permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete Customer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection