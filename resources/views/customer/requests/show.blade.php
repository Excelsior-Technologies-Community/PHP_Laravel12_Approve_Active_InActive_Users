@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Request #{{ $request->id }} Details
            </h4>
            <small class="text-muted">Track the progress of your request and communicate with our support staff.</small>
        </div>
        <a href="{{ route('customer.requests.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
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
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $isInProg ? 'In progress' : 'Awaiting review' }}</small>
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
                    <div class="fw-bold small">4. Final Resolution</div>
                    <small class="text-muted d-block" style="font-size: 11px;">{{ $request->processed_at ? $request->processed_at->format('M d, H:i') : 'Pending decision' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Conversation & Request Info -->
        <div class="col-lg-8">
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
                        <strong><i class="bi bi-info-circle me-1"></i>Admin Feedback Note:</strong> {{ $request->admin_notes }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Two-Way Discussion Thread -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-chat-quote-fill text-primary me-2"></i>Communication Thread ({{ $request->messages->count() }})
                    </h6>
                    <small class="text-muted">Staff replies & updates</small>
                </div>
                <div class="card-body p-4" style="background-color: #fafbfc; max-height: 500px; overflow-y: auto;">
                    @forelse($request->messages as $msg)
                    <div class="d-flex mb-3 {{ $msg->is_admin ? 'justify-content-start' : 'justify-content-end' }}">
                        <div class="card border-0 shadow-sm" style="max-width: 80%; background-color: {{ $msg->is_admin ? '#ffffff' : '#e7f1ff' }};">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1 gap-3">
                                    <span class="fw-bold small {{ $msg->is_admin ? 'text-primary' : 'text-dark' }}">
                                        @if($msg->is_admin)
                                            <i class="bi bi-shield-fill-check me-1"></i>Support Team
                                        @else
                                            <i class="bi bi-person-fill me-1"></i>You
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
                        No messages yet. Send a message below if you have any questions or additional details to share with our staff.
                    </div>
                    @endforelse
                </div>

                {{-- Customer Reply Form --}}
                <div class="card-footer bg-white p-3 border-top">
                    <form method="POST" action="{{ route('customer.requests.messages.store', $request) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <textarea name="message" class="form-control" rows="3" placeholder="Type your reply or question here..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label for="custAttachmentInput" class="btn btn-sm btn-outline-secondary mb-0">
                                    <i class="bi bi-paperclip me-1"></i> Attach File
                                </label>
                                <input type="file" name="attachment" id="custAttachmentInput" class="d-none" onchange="document.getElementById('custFileName').innerText = this.files[0]?.name || ''">
                                <span id="custFileName" class="small text-muted ms-2"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark">Request Metadata</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Request ID:</span>
                            <span class="fw-bold">#{{ $request->id }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Status:</span>
                            <span class="badge {{ $request->getStatusBadgeClass() }}">{{ ucfirst($request->status) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Category:</span>
                            <span class="fw-semibold">{{ $request->getTypeLabel() }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Submitted On:</span>
                            <span>{{ $request->created_at->format('M d, Y h:i A') }}</span>
                        </li>
                        @if($request->processed_at)
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Processed On:</span>
                            <span>{{ $request->processed_at->format('M d, Y h:i A') }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
