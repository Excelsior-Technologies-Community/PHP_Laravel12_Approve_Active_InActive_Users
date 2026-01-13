@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-check-circle me-2"></i> Active Customers
        </h2>
        <div>
            <span class="badge bg-success">{{ $customers->total() }} active</span>
        </div>
    </div>

    <!-- Active Customers Table -->
    <div class="card">
        <div class="card-body">
            @if($customers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-person-x" style="font-size: 3rem; color: #dee2e6;"></i>
                    <h5 class="mt-3 text-muted">No active customers</h5>
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
                                <th>Approved On</th>
                                <th>Last Login</th>
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
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.customers.show', $customer) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.customers.deactivate', $customer) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning">
                                                <i class="bi bi-x-circle"></i>
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