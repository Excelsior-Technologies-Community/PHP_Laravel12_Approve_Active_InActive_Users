@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-chat-left-text-fill text-primary me-2"></i>Request #{{ $request->id }} - {{ $request->title }}
            </h3>
            <small class="text-muted">Interactive request review, two-way communication thread, and status lifecycle.</small>
        </div>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Requests
        </a>
    </div>

    <!-- Visual Lifecycle Timeline Stepper -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row text-center position-relative">
                <div class="col-3">
                    <div class="rounded-circle bg-success text-white d-inline-flex justify-content-center align-items-center mb-2 shadow-sm" style="width: 42px; height: 42px;">
                        <i class="bi bi-send-check"></i>
                    </div>
                    <div class="fw-bold small">1. Submitted</div>
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $request->submitted_at ? $request->submitted_at->format('M d, H:i') : $request->created_at->format('M d, H:i') }}</small>
                </div>

                <div class="col-3">
                    @php $isInProg = $request->isInProgress() || $request->isApproved() || $request->isRejected() || $request->messages->count() > 0; @endphp
                    <div class="rounded-circle {{ $isInProg ? 'bg-primary text-white' : 'bg-light text-muted border' }} d-inline-flex justify-content-center align-items-center mb-2 shadow-sm" style="width: 42px; height: 42px;">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="fw-bold small">2. Under Review</div>
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $isInProg ? 'In progress' : 'Pending review' }}</small>
                </div>

                <div class="col-3">
                    @php $hasMsgs = $request->messages->count() > 0; @endphp
                    <div class="rounded-circle {{ $hasMsgs ? 'bg-info text-white' : 'bg-light text-muted border' }} d-inline-flex justify-content-center align-items-center mb-2 shadow-sm" style="width: 42px; height: 42px;">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="fw-bold small">3. Discussion</div>
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $request->messages->count() }} message(s)</small>
                </div>

                <div class="col-3">
                    @php $isDone = $request->isApproved() || $request->isRejected(); @endphp
                    <div class="rounded-circle {{ $request->isApproved() ? 'bg-success text-white' : ($request->isRejected() ? 'bg-danger text-white' : 'bg-light text-muted border') }} d-inline-flex justify-content-center align-items-center mb-2 shadow-sm" style="width: 42px; height: 42px;">
                        <i class="bi {{ $request->isApproved() ? 'bi-check-all' : ($request->isRejected() ? 'bi-x-lg' : 'bi-flag') }}"></i>
                    </div>
                    <div class="fw-bold small">4. Final Decision</div>
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $request->processed_at ? $request->processed_at->format('M d, H:i') : 'Awaiting decision' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Request Body & Conversation Thread (8 cols) -->
        <div class="col-lg-8">
            <!-- Request Detail Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="badge bg-secondary px-2 py-1">{{ $request->getTypeLabel() }}</span>
                    <span class="badge {{ $request->getStatusBadgeClass() }} px-3 py-1 fs-6">{{ ucfirst($request->status) }}</span>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-2">{{ $request->title }}</h5>
                    <div class="p-3 bg-light rounded border mb-3">
                        {!! nl2br(e($request->description)) !!}
                    </div>

                    @if($request->admin_notes)
                    <div class="alert alert-info py-2 small mb-0">
                        <strong><i class="bi bi-sticky me-1"></i>Official Admin Note:</strong> {{ $request->admin_notes }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Two-Way Message Thread (Feature 6) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-chat-quote-fill text-primary me-2"></i>Discussion & Attachments Thread ({{ $request->messages->count() }})
                    </h6>
                    <small class="text-muted">Direct communication with customer</small>
                </div>
                <div class="card-body p-4" style="background-color: #fafbfc; max-height: 500px; overflow-y: auto;">
                    @forelse($request->messages as $msg)
                    <div class="d-flex mb-3 {{ $msg->is_admin ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="card border-0 shadow-sm" style="max-width: 80%; background-color: {{ $msg->is_admin ? '#e7f1ff' : '#ffffff' }};">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1 gap-3">
                                    <span class="fw-bold small {{ $msg->is_admin ? 'text-primary' : 'text-dark' }}">
                                        @if($msg->is_admin)
                                            <i class="bi bi-shield-fill-check me-1"></i>{{ $msg->user->name ?? 'Admin' }} (Staff)
                                        @else
                                            <i class="bi bi-person-fill me-1"></i>{{ $msg->user->name ?? 'Customer' }}
                                        @endif
                                    </span>
                                    <small class="text-muted" style="font-size: 11px;">{{ $msg->created_at->format('M d, h:i A') }}</small>
                                </div>
                                <p class="mb-2 text-dark small" style="white-space: pre-wrap;">{{ $msg->message }}</p>

                                {{-- Attachment --}}
                                @if($msg->attachment_path)
                                <div class="mt-2 pt-2 border-top">
                                    @if($msg->isImage())
                                        <a href="{{ $msg->attachment_url }}" target="_blank">
                                            <img src="{{ $msg->attachment_url }}" alt="Attachment" class="img-fluid rounded mb-1" style="max-height: 160px;">
                                        </a>
                                    @endif
                                    <div>
                                        <a href="{{ $msg->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size: 11px;">
                                            <i class="bi bi-paperclip me-1"></i>{{ $msg->attachment_name ?: 'Download Attachment' }}
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-chat-dots fs-2 d-block mb-1 text-secondary"></i>
                        No messages yet in this request. Reply below to initiate communication with the customer.
                    </div>
                    @endforelse
                </div>

                {{-- Post Reply Input Form --}}
                <div class="card-footer bg-white p-3 border-top">
                    <form method="POST" action="{{ route('admin.requests.messages.store', $request) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <textarea name="message" class="form-control" rows="3" placeholder="Write a response or question for the customer..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label for="adminAttachmentInput" class="btn btn-sm btn-outline-secondary mb-0">
                                    <i class="bi bi-paperclip me-1"></i> Attach File
                                </label>
                                <input type="file" name="attachment" id="adminAttachmentInput" class="d-none" onchange="document.getElementById('adminFileName').innerText = this.files[0]?.name || ''">
                                <span id="adminFileName" class="small text-muted ms-2"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Status Box -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark">Update Request Status & Resolution Notes</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $request->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $request->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Official Admin Note</label>
                                <input type="text" name="admin_notes" class="form-control" value="{{ old('admin_notes', $request->admin_notes) }}" placeholder="Optional note visible to customer">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Save Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Details & Customer Info (4 cols) -->
        <div class="col-lg-4">
            <!-- Customer Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark">Customer Profile</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $request->user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $request->user->email }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge {{ $request->user->getStatusBadgeClass() }}">{{ ucfirst($request->user->status) }}</span>
                        <span class="badge {{ $request->user->getApprovalBadgeClass() }}">{{ $request->user->is_approved ? 'Approved' : 'Pending' }}</span>
                        <span class="badge {{ $request->user->getKycBadgeClass() }}">{{ ucfirst($request->user->kyc_status) }}</span>
                    </div>

                    <a href="{{ route('admin.customers.show', $request->user) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Full Customer Profile
                    </a>
                </div>
            </div>

            <!-- Quick Action Triggers -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark">One-Click Actions</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        @if($request->status !== 'approved')
                        <form action="{{ route('admin.requests.approve', $request) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-check-circle-fill me-1"></i> 1-Click Approve Request
                            </button>
                        </form>
                        @endif

                        @if($request->status !== 'rejected')
                        <form action="{{ route('admin.requests.reject', $request) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-x-circle-fill me-1"></i> 1-Click Reject Request
                            </button>
                        </form>
                        @endif

                        @if($request->status !== 'in_progress')
                        <form action="{{ route('admin.requests.in-progress', $request) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                <i class="bi bi-play-fill me-1"></i> Mark In Progress
                            </button>
                        </form>
                        @endif

                        <hr class="my-2">

                        <form action="{{ route('admin.requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Permanently delete this request?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-danger">
                                <i class="bi bi-trash me-1"></i> Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection