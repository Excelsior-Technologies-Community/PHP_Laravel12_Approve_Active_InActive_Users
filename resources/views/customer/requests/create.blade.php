@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card">
                <div class="card-header bg-white">
                    <strong>Submit New Request</strong>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('customer.requests.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Request Title</label>
                            <input type="text"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="Enter request title">

                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-label">Request Type</label>
                            <select name="type"
                                    class="form-select @error('type') is-invalid @enderror">
                                <option value="">Select type</option>
                                <option value="account_activation" {{ old('type')=='account_activation'?'selected':'' }}>
                                    Account Activation
                                </option>
                                <option value="service_request" {{ old('type')=='service_request'?'selected':'' }}>
                                    Service Request
                                </option>
                                <option value="information_change" {{ old('type')=='information_change'?'selected':'' }}>
                                    Information Change
                                </option>
                                <option value="other" {{ old('type')=='other'?'selected':'' }}>
                                    Other
                                </option>
                            </select>

                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                      rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Write details about your request">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="alert alert-light border">
                            Your request will be reviewed by the admin team.
                            You will be notified once the status is updated.
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('customer.dashboard') }}"
                               class="btn btn-sm btn-secondary me-2">
                                Back
                            </a>

                            <button type="submit" class="btn btn-sm btn-primary">
                                Submit
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
