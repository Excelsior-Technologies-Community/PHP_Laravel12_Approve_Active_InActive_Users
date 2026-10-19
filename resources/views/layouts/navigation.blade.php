<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="{{ url('/') }}">
            <x-application-logo style="width: 32px; height: 32px;" />
            <span class="fs-5">Request Portal</span>
        </a>

        <!-- Hamburger Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.dashboard.index') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.customers.index') }}">
                                <i class="bi bi-people me-1"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.requests*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.requests.index') }}">
                                <i class="bi bi-inbox me-1"></i> Requests
                            </a>
                        </li>
                    @elseif(Auth::user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active fw-bold text-primary' : '' }}" href="{{ route('customer.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.requests*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('customer.requests.index') }}">
                                <i class="bi bi-card-checklist me-1"></i> My Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.profile*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('customer.profile.show') }}">
                                <i class="bi bi-person me-1"></i> Profile
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Auth / User Dropdown -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 fw-medium" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-size: 14px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            @if(Auth::user()->isCustomer() && Auth::user()->isKycVerified())
                                <i class="bi bi-patch-check-fill text-success" title="Verified Customer"></i>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarUserDropdown">
                            <li class="px-3 py-2 text-muted small border-bottom">
                                Signed in as <br>
                                <strong class="text-dark">{{ Auth::user()->email }}</strong>
                                <br>
                                <span class="badge bg-light text-dark border mt-1">{{ ucfirst(Auth::user()->user_type) }}</span>
                            </li>
                            @if(Auth::user()->isCustomer())
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.profile.show') }}">
                                        <i class="bi bi-person-circle me-2 text-primary"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.requests.index') }}">
                                        <i class="bi bi-list-check me-2 text-primary"></i> My Requests
                                    </a>
                                </li>
                            @elseif(Auth::user()->isAdmin())
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.dashboard.index') }}">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm rounded-pill px-3" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>