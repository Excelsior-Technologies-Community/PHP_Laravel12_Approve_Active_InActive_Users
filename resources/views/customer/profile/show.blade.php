@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-circle text-primary me-2"></i>My Profile
            </h4>
            <p class="text-muted small mb-0">Manage your account information, KYC verification, and security settings.</p>
        </div>
        <a href="{{ route('customer.profile.edit') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Edit Profile
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px; font-size: 32px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">
                    {{ $user->name }}
                    @if($user->isKycVerified())
                        <i class="bi bi-patch-check-fill text-success" title="Verified Customer"></i>
                    @endif
                </h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge {{ $user->getStatusBadgeClass() }} px-3 py-1">
                        {{ ucfirst($user->status) }}
                    </span>
                    <span class="badge {{ $user->getApprovalBadgeClass() }} px-3 py-1">
                        {{ $user->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>

                <hr>

                <div class="text-start small">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Phone:</span>
                        <span class="fw-semibold">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Member Since:</span>
                        <span class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">KYC Status:</span>
                        <span class="badge {{ $user->getKycBadgeClass() }}">{{ ucfirst($user->kyc_status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- KYC Document Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-shield-check text-primary me-2"></i>KYC / Identity Document Status
                    </h6>
                    <span class="badge {{ $user->getKycBadgeClass() }} px-3 py-1">
                        {{ ucfirst($user->kyc_status) }}
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($user->isKycVerified())
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border border-success">
                            <i class="bi bi-patch-check-fill text-success fs-1"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Identity Verified ✓</h6>
                                <p class="text-muted small mb-0">
                                    Document: <strong>{{ $user->id_document_type }}</strong> approved on {{ $user->kyc_verified_at ? $user->kyc_verified_at->format('M d, Y H:i') : 'recently' }}.
                                </p>
                            </div>
                        </div>
                    @elseif($user->isKycPending())
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border border-warning">
                            <i class="bi bi-hourglass-split text-warning fs-1"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Document Under Review</h6>
                                <p class="text-muted small mb-0">
                                    Your <strong>{{ $user->id_document_type }}</strong> was uploaded on {{ $user->kyc_submitted_at ? $user->kyc_submitted_at->format('M d, Y H:i') : 'recently' }}. Our compliance team is verifying it.
                                </p>
                            </div>
                        </div>
                    @elseif($user->isKycRejected())
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border border-danger">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-1"></i>
                            <div>
                                <h6 class="fw-bold text-danger mb-1">Document Rejected</h6>
                                <p class="text-dark small mb-0">
                                    Feedback: <strong>{{ $user->kyc_notes }}</strong>
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-light rounded border">
                            <p class="text-muted small mb-2">No identity document uploaded yet. Please upload a valid document from your dashboard.</p>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                Go to Dashboard to Upload ID
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Details Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Account Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Full Name</label>
                            <div class="form-control bg-light">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Email Address</label>
                            <div class="form-control bg-light">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Phone Number</label>
                            <div class="form-control bg-light">{{ $user->phone ?? 'None' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Account Role</label>
                            <div class="form-control bg-light">{{ ucfirst($user->user_type) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
