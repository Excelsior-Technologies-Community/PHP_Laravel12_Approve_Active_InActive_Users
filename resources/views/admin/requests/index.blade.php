@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        <i class="bi bi-envelope"></i> Requests
    </h4>

    <div class="btn-group btn-group-sm">
        <a href="{{ route('admin.requests.pending') }}" class="btn btn-outline-warning">
            Pending
        </a>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-primary">
            All
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.requests.index') }}">
            <div class="row g-2">

                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search title or customer"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="account_activation" {{ request('type') == 'account_activation' ? 'selected' : '' }}>
                            Account Activation
                        </option>
                        <option value="service_request" {{ request('type') == 'service_request' ? 'selected' : '' }}>
                            Service Request
                        </option>
                        <option value="information_change" {{ request('type') == 'information_change' ? 'selected' : '' }}>
                            Info Change
                        </option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>
                            Other
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-sm btn-primary w-100">
                        Filter
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">

        @if($requests->isEmpty())
            <div class="text-center p-5 text-muted">
                No requests found
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>

                            <td>
                                <strong>{{ $request->title }}</strong><br>
                                <small class="text-muted">
                                    {{ Str::limit($request->description, 40) }}
                                </small>
                            </td>

                            <td>
                                {{ $request->user->name }}<br>
                                <small class="text-muted">{{ $request->user->email }}</small>
                            </td>

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
                                <small>{{ $request->created_at->format('d M Y') }}</small>
                            </td>

                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.requests.show', $request) }}"
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($request->isPending())
                                        <form method="POST" action="{{ route('admin.requests.approve', $request) }}">
                                            @csrf
                                            <button class="btn btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.requests.reject', $request) }}">
                                            @csrf
                                            <button class="btn btn-outline-danger">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.requests.in-progress', $request) }}">
                                            @csrf
                                            <button class="btn btn-outline-info">
                                                <i class="bi bi-gear"></i>
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

            <!-- Pagination -->
            <div class="p-3">
                {{ $requests->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>

@endsection
