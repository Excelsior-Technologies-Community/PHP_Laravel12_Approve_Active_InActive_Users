@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Welcome back, {{ $user->name }}</h5>
                <small class="text-muted">{{ $user->email }}</small>

                <div class="mt-2">
                    <span class="badge {{ $user->getStatusBadgeClass() }} me-1">
                        {{ ucfirst($user->status) }}
                    </span>
                    <span class="badge {{ $user->getApprovalBadgeClass() }}">
                        {{ $user->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
            </div>

            <a href="{{ route('customer.requests.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Request
            </a>
        </div>
    </div>

    <!-- Notifications -->
    @if($notifications->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>
                    <i class="bi bi-bell me-1"></i> Notifications
                </strong>

                <form method="POST" action="{{ route('customer.notifications.read-all') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">
                        Mark all read
                    </button>
                </form>
            </div>

            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <strong>{{ $notification->title }}</strong>
                            <p class="mb-1">{{ $notification->message }}</p>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>

                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('customer.notifications.read', $notification) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">
                                    Read
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-envelope fs-3 text-primary"></i>
                    <h4 class="mt-2">{{ $totalRequests }}</h4>
                    <small class="text-muted">Total Requests</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-clock-history fs-3 text-warning"></i>
                    <h4 class="mt-2">{{ $pendingRequests }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                    <h4 class="mt-2">{{ $approvedRequests }}</h4>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>
                <i class="bi bi-list-ul me-1"></i> Recent Requests
            </strong>
            <a href="{{ route('customer.requests.index') }}" class="btn btn-sm btn-outline-primary">
                View All
            </a>
        </div>

        <div class="list-group list-group-flush">
            @forelse($requests as $request)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $request->title }}</strong><br>
                            <small class="text-muted">
                                {{ $request->created_at->format('d M Y') }}
                            </small>
                        </div>

                        <div class="text-end">
                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            <br>
                            <a href="{{ route('customer.requests.show', $request) }}"
                               class="btn btn-sm btn-outline-primary mt-1">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No requests yet
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions & Info -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <strong>
                        <i class="bi bi-lightning me-1"></i> Quick Actions
                    </strong>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('customer.requests.create') }}" class="btn btn-outline-primary btn-sm">
                            New Request
                        </a>

                        @if(!$user->is_approved)
                            <a href="{{ route('customer.requests.activation.create') }}"
                               class="btn btn-outline-warning btn-sm">
                                Request Activation
                            </a>
                        @endif

                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-outline-info btn-sm">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <strong>
                        <i class="bi bi-person-lines-fill me-1"></i> Account Info
                    </strong>

                    <ul class="list-unstyled mt-3 mb-0">
                        <li><strong>Registered:</strong> {{ $user->created_at->format('d M Y') }}</li>
                        <li><strong>Status:</strong> {{ ucfirst($user->status) }}</li>
                        <li><strong>Total Requests:</strong> {{ $totalRequests }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
