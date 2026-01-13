<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6f8;
            font-size: 14px;
        }

        /* Navbar */
        .navbar {
            background-color: #212529;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar .nav-link {
            color: #ced4da;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #495057;
            color: #ffffff;
        }

        .sidebar small {
            color: #adb5bd;
            padding-left: 15px;
        }

        /* Content */
        .content-area {
            background-color: #ffffff;
            border-radius: 6px;
            padding: 20px;
        }

        /* Alerts */
        .alert {
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark px-3">
    <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
        Admin Panel
    </a>

    <div class="d-flex align-items-center text-white">
        <span class="me-3">
            <i class="bi bi-person"></i> {{ Auth::user()->name }}
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-light">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                       href="{{ route('admin.customers.index') }}">
                        <i class="bi bi-people me-2"></i> Customers
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}"
                       href="{{ route('admin.requests.index') }}">
                        <i class="bi bi-envelope me-2"></i> Requests
                    </a>
                </li>

                <li class="mt-3">
                    <small>CUSTOMER MANAGEMENT</small>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.pending') ? 'active' : '' }}"
                       href="{{ route('admin.customers.pending') }}">
                        Pending Approval
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.active') ? 'active' : '' }}"
                       href="{{ route('admin.customers.active') }}">
                        Active Customers
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.inactive') ? 'active' : '' }}"
                       href="{{ route('admin.customers.inactive') }}">
                        Inactive Customers
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="content-area">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                @yield('content')

            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
