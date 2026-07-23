@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-x-circle-fill text-danger me-2"></i>
                Inactive Customers
            </h2>
            <small class="text-muted">
                List of all inactive customers
            </small>
        </div>

        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back to All Customers
            </a>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.customers.inactive') }}">
        <div class="row g-3 mb-4">

            <div class="col-md-5">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search name, email or phone..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary">
                    <i class="bi bi-search"></i>
                    Search
                </button>

                <a href="{{ route('admin.customers.inactive') }}"
                   class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </div>
    </form>

    <div class="card shadow-sm">

        <div class="card-header bg-danger text-white d-flex justify-content-between">

            <strong>
                Inactive Customers
            </strong>

            <span class="badge bg-light text-dark">
                Total : {{ $customers->total() }}
            </span>

        </div>

        <div class="card-body">

            @if($customers->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Registered</th>
                            <th width="220">Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($customers as $customer)

                        <tr>

                            <td>
                                {{ $loop->iteration + ($customers->currentPage()-1)*$customers->perPage() }}
                            </td>

                            <td>
                                <strong>{{ $customer->name }}</strong>
                            </td>

                            <td>{{ $customer->email }}</td>

                            <td>{{ $customer->phone ?? 'N/A' }}</td>

                            <td>
                                <span class="badge bg-danger">
                                    Inactive
                                </span>
                            </td>

                            <td>

                                @if($customer->is_approved)

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                @else

                                    <span class="badge bg-warning">
                                        Pending
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $customer->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <div class="btn-group">

                                    <a href="{{ route('admin.customers.show',$customer) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.customers.edit',$customer) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.customers.activate',$customer) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf

                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>

                                    </form>

                                    <form action="{{ route('admin.customers.destroy',$customer) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this customer?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-danger">
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

            <div class="mt-3">
                {{ $customers->withQueryString()->links() }}
            </div>

            @else

            <div class="text-center py-5">

                <i class="bi bi-people fs-1 text-muted"></i>

                <h5 class="mt-3">
                    No inactive customers found.
                </h5>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection