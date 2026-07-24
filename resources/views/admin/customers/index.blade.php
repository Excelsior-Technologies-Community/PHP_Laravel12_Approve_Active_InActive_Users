@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <h2 class="mb-0">
            <i class="bi bi-people-fill text-primary me-2"></i>
            Customer Management
        </h2>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('admin.customers.pending') }}"
                class="btn btn-outline-warning rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i>
                Pending
            </a>

            <a href="{{ route('admin.customers.active') }}"
                class="btn btn-outline-success rounded-pill px-3">
                <i class="bi bi-check-circle-fill me-1"></i>
                Active
            </a>

            <a href="{{ route('admin.customers.inactive') }}"
                class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-x-circle-fill me-1"></i>
                Inactive
            </a>

            <a href="{{ route('admin.customers.export.csv') }}"
                class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-download me-1"></i>
                Export CSV
            </a>

        </div>

    </div>


    <br><br>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.customers.index') }}">
        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search Name, Email or Phone..."
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="approved" class="form-select">
                    <option value="">All Approval</option>
                    <option value="1" {{ request('approved')==='1' ? 'selected' : '' }}>Approved</option>
                    <option value="0" {{ request('approved')==='0' ? 'selected' : '' }}>Not Approved</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>

            <div class="col-md-2 d-grid">
                <a href="{{ route('admin.customers.index') }}"
                    class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>

        </div>

    </form>
    <br><br>


    <div class="d-flex justify-content-between mb-3">

        <h5>
            Customers List
        </h5>

        <span class="badge bg-primary fs-6">
            Total : {{ $customers->total() }}
        </span>

    </div>



    <!-- Customers Table -->
    <div class="card">
        <div class="card-body">
            @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: #dee2e6;"></i>
                <h5 class="mt-3 text-muted">No customers found</h5>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>


                            <td>
                                {{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}
                            </td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $customer->status == 'active' ? 'bg-success' : ($customer->status == 'inactive' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>
                                @if($customer->is_approved)
                                <span class="badge bg-success">Approved</span>
                                @if($customer->approved_at)
                                <br><small class="text-muted">{{ $customer->approved_at->format('M d, Y') }}</small>
                                @endif
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $customer->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.history', $customer->id) }}"
                                        class="btn btn-outline-info"
                                        title="View History">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    @if(!$customer->is_approved)
                                    <form action="{{ route('admin.customers.approve', $customer) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($customer->status == 'active')
                                    <form action="{{ route('admin.customers.deactivate', $customer) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.customers.activate', $customer) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.customers.destroy', $customer) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $customers->withQueryString()->links() }}
            </div>
            @endif

        </div>
    </div>
</div>



@endsection