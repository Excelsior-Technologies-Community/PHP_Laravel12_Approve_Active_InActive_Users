@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-x-circle-fill text-danger me-2"></i>Inactive Customers
            </h3>
            <small class="text-muted">Deactivated or suspended customer accounts.</small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger fs-6 px-3 py-2">{{ $customers->total() }} Inactive</span>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> All Customers
            </a>
        </div>
    </div>

    <!-- Inactive Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-person-check fs-1 text-success d-block mb-2"></i>
                <h5 class="text-muted">No inactive customers found.</h5>
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
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="Reactivate Account">
                                            <i class="bi bi-play-circle"></i> Activate
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