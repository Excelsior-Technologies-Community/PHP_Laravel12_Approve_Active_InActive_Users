@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Welcome Banner Card -->
    <div class="card border-0 shadow-sm mb-4 bg-white">
        <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    Welcome back, {{ $user->name }}
                    @if($user->isKycVerified())
                        <i class="bi bi-patch-check-fill text-success" title="Identity Verified ✓"></i>
                    @endif
                </h4>
                <p class="text-muted small mb-2">{{ $user->email }} • Member since {{ $user->created_at->format('M Y') }}</p>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge {{ $user->getStatusBadgeClass() }} px-3 py-1">
                        Status: {{ ucfirst($user->status) }}
                    </span>
                    <span class="badge {{ $user->getApprovalBadgeClass() }} px-3 py-1">
                        Approval: {{ $user->is_approved ? 'Approved' : 'Pending Approval' }}
                    </span>
                    <span class="badge {{ $user->getKycBadgeClass() }} px-3 py-1">
                        KYC: {{ ucfirst($user->kyc_status) }}
                    </span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kycUploadModal">
                    <i class="bi bi-shield-check me-1"></i> {{ $user->id_document_path ? 'Update ID Document' : 'Upload ID Document' }}
                </button>
                <a href="{{ route('customer.requests.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Request
                </a>
            </div>
        </div>
    </div>

    <!-- Feature 2: KYC Identity Verification Status Banner -->
    @if($user->isKycVerified())
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-patch-check-fill fs-2 text-success"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-success">Identity Verified (KYC Completed)</h6>
                    <small class="text-muted">Your {{ $user->id_document_type }} was approved on {{ $user->kyc_verified_at ? $user->kyc_verified_at->format('M d, Y') : 'recently' }}. Your account is fully verified.</small>
                </div>
            </div>
            <span class="badge bg-success px-3 py-2">Verified ✓</span>
        </div>
    @elseif($user->isKycPending())
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Identity Verification In Progress</h6>
                    <small class="text-muted">You have submitted your {{ $user->id_document_type }}. Our compliance team is currently reviewing your document.</small>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#kycUploadModal">Replace Document</button>
        </div>
    @elseif($user->isKycRejected())
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-octagon-fill fs-2 text-danger"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-danger">Identity Verification Required (Document Rejected)</h6>
                    <small class="text-dark">Reason from staff: <strong>{{ $user->kyc_notes }}</strong>. Please re-upload a clear, valid identity document.</small>
                </div>
            </div>
            <button class="btn btn-sm btn-danger px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kycUploadModal">Re-upload ID</button>
        </div>
    @else
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-shield-exclamation fs-2 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Identity Verification Pending</h6>
                    <small class="text-muted">Upload a valid ID (National ID, Passport, or Driving License) to verify your account and unlock all platform features.</small>
                </div>
            </div>
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kycUploadModal">
                <i class="bi bi-upload me-1"></i> Verify Now
            </button>
        </div>
    @endif

    <!-- Notifications Section -->
    @if($notifications->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <strong class="text-dark"><i class="bi bi-bell-fill text-warning me-2"></i>Recent Notifications</strong>
                <form method="POST" action="{{ route('customer.notifications.read-all') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Mark all read</button>
                </form>
            </div>
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <div class="fw-bold text-dark">{{ $notification->title }}</div>
                            <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                            <small class="text-muted" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('customer.notifications.read', $notification) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary py-0 px-2">Read</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Request Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center border-start border-primary border-4">
                <div class="card-body p-3">
                    <i class="bi bi-envelope fs-3 text-primary"></i>
                    <h3 class="fw-bold text-primary mt-1 mb-0">{{ $totalRequests }}</h3>
                    <small class="text-muted">Total Requests Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center border-start border-warning border-4">
                <div class="card-body p-3">
                    <i class="bi bi-clock-history fs-3 text-warning"></i>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $pendingRequests }}</h3>
                    <small class="text-muted">Awaiting Processing</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center border-start border-success border-4">
                <div class="card-body p-3">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                    <h3 class="fw-bold text-success mt-1 mb-0">{{ $approvedRequests }}</h3>
                    <small class="text-muted">Approved & Completed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests with Messages Thread links -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-task text-primary me-2"></i>My Recent Requests
            </h6>
            <a href="{{ route('customer.requests.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
        </div>
        <div class="list-group list-group-flush">
            @forelse($requests as $req)
                <div class="list-group-item p-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-bold text-dark">{{ $req->title }}</div>
                            <small class="text-muted">
                                {{ $req->getTypeLabel() }} • Submitted {{ $req->created_at->format('d M Y') }}
                            </small>
                        </div>
                        <div class="text-end d-flex align-items-center gap-2">
                            <span class="badge {{ $req->getStatusBadgeClass() }} px-3 py-1">
                                {{ ucfirst($req->status) }}
                            </span>
                            <a href="{{ route('customer.requests.show', $req) }}" class="btn btn-sm btn-outline-primary px-3 shadow-sm">
                                <i class="bi bi-chat-dots me-1"></i> Open Discussion ({{ $req->messages_count }})
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                    You have not submitted any requests yet.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- MODAL: Customer KYC Upload Modal (Feature 2) --}}
<div class="modal fade" id="kycUploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('customer.kyc.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-shield-check text-info me-2"></i>Upload Identity Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Upload a clear image or PDF scan of your identity proof. Accepted documents: National ID, Passport, Driving License, or Business Registration.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Type *</label>
                        <select name="id_document_type" class="form-select" required>
                            <option value="National ID" {{ $user->id_document_type === 'National ID' ? 'selected' : '' }}>National Identity Card (ID)</option>
                            <option value="Passport" {{ $user->id_document_type === 'Passport' ? 'selected' : '' }}>International Passport</option>
                            <option value="Driving License" {{ $user->id_document_type === 'Driving License' ? 'selected' : '' }}>Driver's License</option>
                            <option value="Business Registration" {{ $user->id_document_type === 'Business Registration' ? 'selected' : '' }}>Business Registration Certificate</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose Document File (PDF, JPG, PNG - Max 5MB) *</label>
                        <input type="file" name="id_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>

                    @if($user->id_document_path)
                    <div class="alert alert-light border small text-muted">
                        <i class="bi bi-info-circle me-1"></i> You currently have a document uploaded. Uploading a new file will replace your existing submission.
                    </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">Submit for Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
