@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Executive Analytics Dashboard
        </h3>
        <p class="text-muted mb-0 small">Real-time customer registration trends, approval metrics, and request SLA performance.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.pending') }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-clock-history me-1"></i> {{ $pendingCustomers }} Pending Approvals
        </a>
        <a href="{{ route('admin.customers.index', ['kyc_status' => 'pending']) }}" class="btn btn-info btn-sm text-white rounded-pill px-3 shadow-sm">
            <i class="bi bi-shield-check me-1"></i> {{ $kycPendingCustomers }} KYC Pending
        </a>
    </div>
</div>

<!-- Key Performance Metrics -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Customers</span>
                <h3 class="fw-bold text-primary mb-0 mt-1">{{ $totalCustomers }}</h3>
                <small class="text-muted"><i class="bi bi-people me-1"></i>Registered</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
            <div class="card-body p-3">
                <span class="text-warning text-dark small fw-semibold text-uppercase">Pending Approval</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $pendingCustomers }}</h3>
                <small class="text-muted"><i class="bi bi-hourglass-split me-1"></i>Awaiting review</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
            <div class="card-body p-3">
                <span class="text-success small fw-semibold text-uppercase">Active Customers</span>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeCustomers }}</h3>
                <small class="text-muted"><i class="bi bi-check-circle me-1"></i>Approved & Live</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-info border-4 h-100">
            <div class="card-body p-3">
                <span class="text-info small fw-semibold text-uppercase">KYC Verified</span>
                <h3 class="fw-bold text-info mb-0 mt-1">{{ $kycVerifiedCustomers }}</h3>
                <small class="text-muted"><i class="bi bi-patch-check-fill me-1"></i>Verified Identity</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-dark border-4 h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Avg Turnaround</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $avgTurnaroundFormatted }}</h3>
                <small class="text-muted"><i class="bi bi-stopwatch me-1"></i>Resolution Speed</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm border-start border-secondary border-4 h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Resolution Rate</span>
                <h3 class="fw-bold text-secondary mb-0 mt-1">{{ $resolutionRate }}%</h3>
                <small class="text-muted"><i class="bi bi-graph-up-arrow me-1"></i>Processed/Total</small>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Chart.js Analytics Grid -->
<div class="row g-4 mb-4">
    <!-- Chart 1: 30-Day Registration & Approval Trends -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-graph-up text-primary me-2"></i>Registration & Approval Growth Trends
                    </h6>
                    <small class="text-muted">Daily volume across the past 30 days</small>
                </div>
                <span class="badge bg-light text-dark border">Past 30 Days</span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Customer Status Distribution Donut -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart-fill text-warning me-2"></i>Customer Status Breakdown
                </h6>
                <small class="text-muted">Active vs Pending vs Inactive</small>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                <div style="position: relative; height: 230px; width: 230px;">
                    <canvas id="statusDonutChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3 small text-muted">
                    <span><i class="bi bi-circle-fill text-success me-1"></i> Active ({{ $activeCustomers }})</span>
                    <span><i class="bi bi-circle-fill text-warning me-1"></i> Pending ({{ $pendingCustomers }})</span>
                    <span><i class="bi bi-circle-fill text-danger me-1"></i> Inactive ({{ $inactiveCustomers }})</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Types & Distribution -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bar-chart-fill text-info me-2"></i>Requests Distribution by Category
                </h6>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 240px; width: 100%;">
                    <canvas id="requestCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-lock-fill text-success me-2"></i>KYC & Compliance Status
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">Identity Verified Users (KYC Approved)</span>
                        <span class="badge bg-success">{{ $kycVerifiedCustomers }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $totalCustomers > 0 ? ($kycVerifiedCustomers / $totalCustomers) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">Pending Document Reviews</span>
                        <span class="badge bg-warning text-dark">{{ $kycPendingCustomers }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $totalCustomers > 0 ? ($kycPendingCustomers / $totalCustomers) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">Active Customers Compliance Ratio</span>
                        <span class="badge bg-primary">{{ $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $totalCustomers > 0 ? ($activeCustomers / $totalCustomers) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="alert alert-light border small text-muted mb-0 mt-4">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    To review submitted identity documents, go to <a href="{{ route('admin.customers.index', ['kyc_status' => 'pending']) }}" class="text-primary fw-semibold">KYC Pending Review</a>.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Tables -->
<div class="row g-4">
    <!-- Recent Requests -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-envelope-paper-fill text-primary me-2"></i>Recent Requests
                </h6>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>Request</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $req->title }}</div>
                                <small class="text-muted">{{ $req->getTypeLabel() }}</small>
                            </td>
                            <td>
                                <div>{{ $req->user->name ?? 'User' }}</div>
                                <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $req->getStatusBadgeClass() }}">{{ ucfirst($req->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.requests.show', $req) }}" class="btn btn-sm btn-light border" title="Open Discussion">
                                    <i class="bi bi-chat-dots-fill text-primary"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">No recent requests recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Customers -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-plus-fill text-success me-2"></i>Recent Customers
                </h6>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>KYC</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCustomers as $customer)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $customer->name }}
                                    @if($customer->isKycVerified())
                                        <i class="bi bi-patch-check-fill text-success" title="Verified Customer"></i>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $customer->email }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $customer->getStatusBadgeClass() }}">{{ ucfirst($customer->status) }}</span>
                                <span class="badge {{ $customer->getApprovalBadgeClass() }}">{{ $customer->is_approved ? 'Approved' : 'Pending' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $customer->getKycBadgeClass() }}">{{ ucfirst($customer->kyc_status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-light border">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">No customers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Trends Line Chart
    const trendCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendDates),
            datasets: [
                {
                    label: 'Registrations',
                    data: @json($registrationsSeries),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5
                },
                {
                    label: 'Approvals',
                    data: @json($approvalsSeries),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Status Donut Chart
    const statusCtx = document.getElementById('statusDonutChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Pending', 'Inactive'],
            datasets: [{
                data: [{{ $activeCustomers }}, {{ $pendingCustomers }}, {{ $inactiveCustomers }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '72%'
        }
    });

    // 3. Request Category Bar Chart
    const reqCtx = document.getElementById('requestCategoryChart').getContext('2d');
    new Chart(reqCtx, {
        type: 'bar',
        data: {
            labels: ['Activation', 'Service', 'Info Change', 'Other'],
            datasets: [{
                label: 'Requests Count',
                data: [
                    {{ $requestTypes['account_activation'] ?? 0 }},
                    {{ $requestTypes['service_request'] ?? 0 }},
                    {{ $requestTypes['information_change'] ?? 0 }},
                    {{ $requestTypes['other'] ?? 0 }}
                ],
                backgroundColor: ['#0d6efd', '#20c997', '#6f42c1', '#fd7e14'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
