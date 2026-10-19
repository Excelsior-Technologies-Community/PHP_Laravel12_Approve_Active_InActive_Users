@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-check-circle-fill text-success me-2"></i>Active Customers
            </h3>
            <small class="text-muted">Approved and active customer accounts currently using the platform.</small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success fs-6 px-3 py-2">{{ $customers->total() }} Active</span>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> All Customers
            </a>
        </div>
    </div>

    <!-- Active Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-secondary d-block mb-2"></i>
                <h5 class="text-muted">No active customers found.</h5>
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
                            <th>Approved Date</th>
                            <th>Last Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                                @if($customer->isKycVerified())
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified ✓"></i>
                                @endif
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $customer->getKycBadgeClass() }}">{{ ucfirst($customer->kyc_status) }}</span>
                            </td>
                            <td>
                                @if($customer->approved_at)
                                <small>{{ $customer->approved_at->format('M d, Y') }}</small>
                                @else
                                <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                @if($customer->last_login)
                                <small>{{ $customer->last_login->diffForHumans() }}</small>
                                @else
                                <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.deactivate', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning" title="Deactivate Account">
                                            <i class="bi bi-pause-circle"></i> Deactivate
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