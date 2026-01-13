@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-clock-history me-2"></i> Pending Customers
        </h2>
        <div>
            <span class="badge bg-warning">{{ $customers->total() }} pending</span>
        </div>
    </div>

    <!-- Pending Customers Table -->
    <div class="card">
        <div class="card-body">
            @if($customers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                    <h5 class="mt-3 text-success">No pending customers!</h5>
                    <p class="text-muted">All customers have been approved.</p>
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
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>
                                    <small>{{ $customer->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.customers.show', $customer) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.customers.approve', $customer) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success">
                                                <i class="bi bi-check"></i> Approve
                                            </button>
                                        </form>
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
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection