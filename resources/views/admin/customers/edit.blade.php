@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-pencil-square me-2"></i>
            Edit Customer
        </h2>

        <a href="{{ route('admin.customers.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.customers.update', $customer) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $customer->name) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $customer->email) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $customer->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="pending"
                                {{ $customer->status=='pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="active"
                                {{ $customer->status=='active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="inactive"
                                {{ $customer->status=='inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_approved"
                                value="1"
                                {{ $customer->is_approved ? 'checked' : '' }}>

                            <label class="form-check-label">
                                Customer Approved
                            </label>

                        </div>
                    </div>

                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Update Customer
                </button>

            </form>

        </div>
    </div>

</div>
@endsection