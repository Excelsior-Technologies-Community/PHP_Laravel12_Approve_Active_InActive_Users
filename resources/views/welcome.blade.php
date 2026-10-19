@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="text-center py-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm mb-3" style="width: 72px; height: 72px;">
            <i class="bi bi-shield-lock-fill fs-1"></i>
        </div>
        <h1 class="fw-bold display-5 text-dark mb-3">
            Customer Request & Approval System
        </h1>
        <p class="text-muted fs-5 mx-auto" style="max-width: 680px;">
            A modern, enterprise-grade portal for customer onboarding, KYC document verification, interactive request resolution, and real-time operational analytics.
        </p>

        <div class="d-flex justify-content-center gap-3 mt-4">
            @if(!auth()->check())
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-person-plus me-1"></i> Register Account
                </a>
            @else
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="bi bi-speedometer2 me-1"></i> Open Admin Dashboard
                    </a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="bi bi-speedometer2 me-1"></i> Open Customer Portal
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="row g-4 justify-content-center my-4">
        <!-- For Customers -->
        <div class="col-md-5">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">For Customers</h4>
                        <small class="text-muted">Self-service account & service tickets</small>
                    </div>
                </div>
                <p class="text-muted small mb-4">
                    Register your account, submit identity documents for instant KYC verification, and track service requests with live discussion threads.
                </p>
                <ul class="list-unstyled small mb-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Submit and track service requests</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> KYC Identity document upload & verification</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Two-way live discussion thread with staff</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Step-by-step lifecycle timeline progress</li>
                </ul>
                <div class="mt-auto">
                    @if(!auth()->check())
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 rounded-pill">
                            Register as Customer
                        </a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-primary w-100 rounded-pill">
                            Go to Customer Portal
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- For Admins -->
        <div class="col-md-5">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-dark bg-opacity-10 text-dark d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">For Administrators</h4>
                        <small class="text-muted">Enterprise management & compliance</small>
                    </div>
                </div>
                <p class="text-muted small mb-4">
                    Batch approve/activate customers with selection bars, inspect KYC documents in the modal viewer, and view live growth analytics.
                </p>
                <ul class="list-unstyled small mb-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Bulk approve, activate & deactivate users</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> KYC document inspection & verification workflow</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Interactive Chart.js analytics & trend charts</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> CSV export and complete batch audit logging</li>
                </ul>
                <div class="mt-auto">
                    @if(!auth()->check())
                        <a href="{{ route('login') }}" class="btn btn-dark w-100 rounded-pill">
                            Admin Login
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-dark w-100 rounded-pill">
                            Go to Admin Console
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-5">
        <h3 class="fw-bold text-center mb-4">Streamlined Workflow</h3>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 bg-white shadow-sm p-4 h-100">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold">1. Registration & KYC</h5>
                    <p class="text-muted small mb-0">Customers register and upload identity documents for verification.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-white shadow-sm p-4 h-100">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold">2. Staff Verification</h5>
                    <p class="text-muted small mb-0">Admins review documents in the previewer and bulk-approve accounts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-white shadow-sm p-4 h-100">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-chat-square-text-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold">3. Service & Collaboration</h5>
                    <p class="text-muted small mb-0">Two-way message threads and step-by-step resolution tracking.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer info -->
    <div class="text-center text-muted small pt-4 border-top">
        &copy; {{ date('Y') }} Customer Request & Approval System. All rights reserved.
    </div>
</div>
@endsection