@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-card-checklist text-primary me-2"></i>My Requests
            </h4>
            <p class="text-muted small mb-0">View all your submitted service, activation, and support requests.</p>
        </div>
        <a href="{{ route('customer.requests.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> New Request
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($requests->isEmpty())
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-inbox text-muted fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No requests yet</h5>
                    <p class="text-muted small mb-3">You haven't submitted any service requests yet.</p>
                    <a href="{{ route('customer.requests.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Create Your First Request
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Title & Description</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#{{ $request->id }}</td>
                                <td>
                                    <a href="{{ route('customer.requests.show', $request) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ $request->title }}
                                    </a>
                                    <div class="text-muted small">{{ Str::limit($request->description, 50) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $request->getTypeLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success text-white',
                                            'rejected' => 'bg-danger text-white',
                                            'in_progress' => 'bg-info text-dark'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusBadges[$request->status] ?? 'bg-secondary' }} px-3 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $request->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('customer.requests.show', $request) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        @if($request->status == 'pending')
                                            <form action="{{ route('customer.requests.destroy', $request) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                        onclick="return confirm('Cancel this request?')">
                                                    <i class="bi bi-trash me-1"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $requests->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection