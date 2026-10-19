@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-clock-history text-warning me-2"></i>Pending Approval Customers
            </h3>
            <small class="text-muted">Review, approve, or reject new customer registration requests.</small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-warning text-dark fs-6 px-3 py-2">{{ $customers->total() }} Pending</span>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> All Customers
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                <h5 class="text-success fw-bold">No pending customers!</h5>
                <p class="text-muted small">All customer accounts have been processed.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Email Address</th>
                            <th>Phone</th>
                            <th>KYC Status</th>
                            <th>Registered</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $customer->getKycBadgeClass() }}">{{ ucfirst($customer->kyc_status) }}</span>
                            </td>
                            <td>
                                <small>{{ $customer->created_at->format('M d, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.approve', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="Approve Customer">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
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
            @if($customers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $customers->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection