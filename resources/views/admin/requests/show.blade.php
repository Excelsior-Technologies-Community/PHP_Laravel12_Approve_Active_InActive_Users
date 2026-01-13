@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-eye me-2"></i> Request Details
        </h2>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Requests
        </a>
    </div>

    <div class="row">
        <!-- Request Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Request Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Request ID</th>
                            <td>#{{ $request->id }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $request->title }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $request->getTypeLabel() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $request->getStatusBadgeClass() }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Submitted</th>
                            <td>{{ $request->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        @if($request->processed_at)
                        <tr>
                            <th>Processed</th>
                            <td>{{ $request->processed_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        @endif
                    </table>
                    
                    <h6 class="mt-4">Description:</h6>
                    <div class="border p-3 rounded bg-light">
                        {{ $request->description }}
                    </div>
                    
                    @if($request->admin_notes)
                    <h6 class="mt-4">Admin Notes:</h6>
                    <div class="border p-3 rounded bg-light">
                        {{ $request->admin_notes }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update Request Status -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Update Request Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ old('admin_notes', $request->admin_notes) }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <td>{{ $request->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $request->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $request->user->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Account Status</th>
                            <td>
                                @if($request->user->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>User Status</th>
                            <td>
                                <span class="badge {{ $request->user->status == 'active' ? 'bg-success' : ($request->user->status == 'inactive' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($request->user->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Registered</th>
                            <td>{{ $request->user->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.customers.show', $request->user) }}" class="btn btn-outline-primary">
                            <i class="bi bi-person me-1"></i> View Customer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($request->status == 'pending')
                            <form action="{{ route('admin.requests.approve', $request) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-check-circle me-1"></i> Approve Request
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.requests.reject', $request) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 mb-2">
                                    <i class="bi bi-x-circle me-1"></i> Reject Request
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.requests.in-progress', $request) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100 mb-2">
                                    <i class="bi bi-gear me-1"></i> Mark as In Progress
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.requests.destroy', $request) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this request?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection