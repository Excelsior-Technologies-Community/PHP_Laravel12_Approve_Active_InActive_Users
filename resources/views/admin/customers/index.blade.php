@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>Customer Management
            </h3>
            <p class="text-muted small mb-0">Approve registrations, manage account statuses, review KYC, and perform bulk operations.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customers.pending') }}" class="btn btn-outline-warning rounded-pill px-3 shadow-sm">
                <i class="bi bi-clock-history me-1"></i> Pending
            </a>
            <a href="{{ route('admin.customers.active') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> Active
            </a>
            <a href="{{ route('admin.customers.inactive') }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                <i class="bi bi-x-circle-fill me-1"></i> Inactive
            </a>
            <a href="{{ route('admin.customers.index', ['kyc_status' => 'pending']) }}" class="btn btn-outline-info rounded-pill px-3 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> KYC Review
            </a>
            <a href="{{ route('admin.customers.export.csv') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Name, Email, Phone..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="approved" class="form-select form-select-sm">
                            <option value="">All Approvals</option>
                            <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>Approved</option>
                            <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>Not Approved</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="kyc_status" class="form-select form-select-sm">
                            <option value="">All KYC Status</option>
                            <option value="verified" {{ request('kyc_status') === 'verified' ? 'selected' : '' }}>Verified ✓</option>
                            <option value="pending" {{ request('kyc_status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="rejected" {{ request('kyc_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="unsubmitted" {{ request('kyc_status') === 'unsubmitted' ? 'selected' : '' }}>Unsubmitted</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-grow-1" type="submit">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating / Sticky Bulk Action Bar (Feature 1) -->
    <div id="bulkActionBar" class="card border-0 shadow-lg bg-dark text-white mb-3 d-none">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-square text-warning fs-5"></i>
                <span class="fw-semibold"><span id="selectedCount">0</span> Customer(s) Selected</span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="executeBulkAction('approve')">
                    <i class="bi bi-check-all me-1"></i> Bulk Approve
                </button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="executeBulkAction('activate')">
                    <i class="bi bi-toggle-on me-1"></i> Bulk Activate
                </button>
                <button type="button" class="btn btn-warning btn-sm fw-semibold" onclick="executeBulkAction('deactivate')">
                    <i class="bi bi-toggle-off me-1"></i> Bulk Deactivate
                </button>
                <button type="button" class="btn btn-danger btn-sm fw-semibold" onclick="executeBulkAction('delete')">
                    <i class="bi bi-trash me-1"></i> Bulk Delete
                </button>
                <button type="button" class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                    Deselect All
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Action Form -->
    <form id="bulkActionForm" method="POST" action="{{ route('admin.customers.bulk-action') }}" class="d-none">
        @csrf
        <input type="hidden" name="action" id="bulkActionInput">
        <div id="bulkIdsContainer"></div>
    </form>

    <!-- Customers Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-table me-2 text-primary"></i>Customer Directory ({{ $customers->total() }})
            </h6>
            <small class="text-muted">Multi-select rows to trigger bulk actions</small>
        </div>

        <div class="card-body p-0">
            @if($customers->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                    No customers found matching your criteria.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                </th>
                                <th>Customer Details</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>KYC Identity</th>
                                <th>Registered</th>
                                <th class="text-end" style="min-width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input customer-checkbox" value="{{ $customer->id }}" onchange="updateBulkBar()">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $customer->name }}
                                        @if($customer->isKycVerified())
                                            <i class="bi bi-patch-check-fill text-success" title="KYC Verified ✓"></i>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </td>
                                <td>
                                    <small>{{ $customer->phone ?: 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $customer->getStatusBadgeClass() }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($customer->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                        @if($customer->approved_at)
                                            <div class="small text-muted" style="font-size: 11px;">{{ $customer->approved_at->format('M d, Y') }}</div>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->isKycVerified())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Verified ✓
                                        </span>
                                        <div class="small text-muted" style="font-size: 11px;">{{ $customer->id_document_type }}</div>
                                    @elseif($customer->isKycPending())
                                        <button type="button" class="btn btn-sm btn-warning text-dark py-0 px-2 rounded-pill shadow-sm"
                                                onclick="openKycModal({{ json_encode([
                                                    'id' => $customer->id,
                                                    'name' => $customer->name,
                                                    'email' => $customer->email,
                                                    'type' => $customer->id_document_type,
                                                    'url' => $customer->kyc_document_url,
                                                    'is_image' => $customer->isKycDocumentImage(),
                                                    'notes' => $customer->kyc_notes
                                                ]) }})">
                                            <i class="bi bi-file-earmark-person me-1"></i>Review ID
                                        </button>
                                    @elseif($customer->isKycRejected())
                                        <span class="badge bg-danger-subtle text-danger border" title="{{ $customer->kyc_notes }}">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Rejected
                                        </span>
                                        @if($customer->id_document_path)
                                            <a href="javascript:void(0)" class="small d-block text-danger" onclick="openKycModal({{ json_encode([
                                                'id' => $customer->id,
                                                'name' => $customer->name,
                                                'email' => $customer->email,
                                                'type' => $customer->id_document_type,
                                                'url' => $customer->kyc_document_url,
                                                'is_image' => $customer->isKycDocumentImage(),
                                                'notes' => $customer->kyc_notes
                                            ]) }})">View Re-upload</a>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border">Unsubmitted</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $customer->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.history', $customer->id) }}" class="btn btn-outline-info" title="Audit Log">
                                            <i class="bi bi-clock-history"></i>
                                        </a>

                                        @if(!$customer->is_approved)
                                            <form action="{{ route('admin.customers.approve', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Approve Customer">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($customer->status === 'active')
                                            <form action="{{ route('admin.customers.deactivate', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="Deactivate">
                                                    <i class="bi bi-pause-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Activate">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete customer account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
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
                @if($customers->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers</small>
                        {{ $customers->links() }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- MODAL: KYC Document Preview and Decision (Feature 2) --}}
<div class="modal fade" id="kycPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check text-info me-2"></i>KYC Document Verification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-0" id="kycCustomerName">Customer Name</h6>
                        <small class="text-muted" id="kycCustomerEmail">email@example.com</small>
                    </div>
                    <span class="badge bg-primary fs-6 px-3 py-2" id="kycDocumentType">National ID</span>
                </div>

                {{-- Document Preview Container --}}
                <div id="kycViewerContainer" class="border rounded bg-light text-center p-3 mb-3" style="min-height: 350px; max-height: 500px; overflow: auto;">
                    {{-- Dynamically populated --}}
                </div>

                {{-- Rejection Feedback Notes Container --}}
                <div id="rejectionNotesSection" class="border rounded p-3 bg-light d-none">
                    <form id="kycRejectForm" method="POST" action="">
                        @csrf
                        <label class="form-label fw-bold text-danger">Rejection / Re-upload Reason *</label>
                        <textarea name="rejection_reason" class="form-control mb-2" rows="3" placeholder="Explain why this document was rejected (e.g. Blurry photo, expired ID, name mismatch)..." required></textarea>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleRejectionSection(false)">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm">Confirm Rejection & Notify</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light py-2" id="kycDecisionButtons">
                <form id="kycApproveForm" method="POST" action="" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Approve KYC Document
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger fw-semibold" onclick="toggleRejectionSection(true)">
                    <i class="bi bi-x-circle me-1"></i> Reject / Request Re-upload
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select All & Bulk Actions
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const bulkActionBar = document.getElementById('bulkActionBar');
const selectedCountElem = document.getElementById('selectedCount');

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        updateBulkBar();
    });
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.customer-checkbox:checked');
    const count = checked.length;
    selectedCountElem.innerText = count;

    if (count > 0) {
        bulkActionBar.classList.remove('d-none');
    } else {
        bulkActionBar.classList.add('d-none');
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false);
    if (selectAllCheckbox) selectAllCheckbox.checked = false;
    updateBulkBar();
}

function executeBulkAction(action) {
    const checked = document.querySelectorAll('.customer-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one customer.');
        return;
    }

    if (action === 'delete' && !confirm(`Are you sure you want to permanently delete these ${checked.length} customer(s)?`)) {
        return;
    }

    const form = document.getElementById('bulkActionForm');
    const container = document.getElementById('bulkIdsContainer');
    document.getElementById('bulkActionInput').value = action;
    container.innerHTML = '';

    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'customer_ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });

    form.submit();
}

// KYC Modal Handlers
function openKycModal(data) {
    document.getElementById('kycCustomerName').innerText = data.name;
    document.getElementById('kycCustomerEmail').innerText = data.email;
    document.getElementById('kycDocumentType').innerText = data.type || 'Identity Document';

    const viewer = document.getElementById('kycViewerContainer');
    if (!data.url) {
        viewer.innerHTML = '<div class="text-muted py-5">No document file found.</div>';
    } else if (data.is_image) {
        viewer.innerHTML = `<img src="${data.url}" alt="KYC Document" class="img-fluid rounded shadow-sm" style="max-height: 450px;">`;
    } else {
        viewer.innerHTML = `<iframe src="${data.url}" style="width: 100%; height: 450px; border: none;"></iframe>`;
    }

    document.getElementById('kycApproveForm').action = `/admin/customers/${data.id}/kyc-approve`;
    document.getElementById('kycRejectForm').action = `/admin/customers/${data.id}/kyc-reject`;

    toggleRejectionSection(false);

    const modal = new bootstrap.Modal(document.getElementById('kycPreviewModal'));
    modal.show();
}

function toggleRejectionSection(show) {
    const rejSection = document.getElementById('rejectionNotesSection');
    const decButtons = document.getElementById('kycDecisionButtons');
    if (show) {
        rejSection.classList.remove('d-none');
        decButtons.classList.add('d-none');
    } else {
        rejSection.classList.add('d-none');
        decButtons.classList.remove('d-none');
    }
}
</script>
@endpush

@endsection