<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Courier Portal - 24/7 NHAM')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --driver-primary: #15803d;
            --driver-dark: #0f172a;
            --driver-bg: #f8fafc;
            --driver-surface: #ffffff;
            --driver-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--driver-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .driver-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--driver-border);
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .driver-nav-tab {
            color: #64748b;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 10px 16px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .driver-nav-tab:hover {
            color: var(--driver-primary);
            background: #f1fdf4;
        }

        .driver-nav-tab.active {
            color: #ffffff;
            background: var(--driver-primary);
        }

        .driver-bottom-nav {
            display: none;
        }

        @media (max-width: 768px) {
            .desktop-nav {
                display: none !important;
            }
            .driver-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #ffffff;
                border-top: 1px solid var(--driver-border);
                z-index: 1040;
                box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
            }
            .driver-bottom-tab {
                flex: 1;
                text-align: center;
                padding: 8px 4px;
                color: #64748b;
                font-size: 0.72rem;
                font-weight: 600;
                text-decoration: none;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
            }
            .driver-bottom-tab i {
                font-size: 1.2rem;
            }
            .driver-bottom-tab.active {
                color: var(--driver-primary);
            }
            body {
                padding-bottom: 70px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Driver Portal Header -->
    <header class="driver-header shadow-sm">
        <div class="container py-2 py-md-3">
            <div class="d-flex align-items-center justify-content-between gap-3">
                
                <!-- Brand & Driver Badge -->
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('driver.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                        <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 38px; width: auto; object-fit: contain;">
                        <div>
                            <div class="fw-bolder text-dark lh-1" style="font-size: 1.05rem;">24/7 NHAM</div>
                            <span class="text-success fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Courier Portal</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Nav -->
                <nav class="desktop-nav d-flex align-items-center gap-2">
                    <a href="{{ route('driver.dashboard') }}" class="driver-nav-tab {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('driver.active') }}" class="driver-nav-tab {{ request()->routeIs('driver.active') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i> Active Job
                    </a>
                    <a href="{{ route('driver.earnings') }}" class="driver-nav-tab {{ request()->routeIs('driver.earnings') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Earnings
                    </a>
                    <a href="{{ route('driver.profile') }}" class="driver-nav-tab {{ request()->routeIs('driver.profile') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Profile
                    </a>
                </nav>

                <!-- Status Toggle & Profile Dropdown -->
                <div class="d-flex align-items-center gap-2">
                    
                    <!-- Availability Online/Offline Form -->
                    <form action="{{ route('driver.toggle') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-2 {{ auth()->user()->is_online ? 'btn-success' : 'btn-outline-secondary' }}" 
                                title="Toggle your delivery availability">
                            <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: {{ auth()->user()->is_online ? '#ffffff' : '#94a3b8' }};"></span>
                            <span>{{ auth()->user()->is_online ? 'Online' : 'Offline' }}</span>
                        </button>
                    </form>

                    <!-- User menu -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" 
                                type="button" data-bs-toggle="dropdown" style="width: 36px; height: 36px;">
                            <i class="bi bi-person-fill text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-2">
                            <li class="px-3 py-1 border-bottom mb-1">
                                <div class="fw-bold small text-dark">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->role }}</div>
                            </li>
                            <li><a class="dropdown-item py-1.5 rounded-2 small" href="{{ url('/') }}"><i class="bi bi-shop me-2 text-success"></i> Customer Store</a></li>
                            @if(auth()->user()->role === 'admin')
                                <li><a class="dropdown-item py-1.5 rounded-2 small" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2 text-warning"></i> Admin Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-1.5 rounded-2 small text-danger border-0 bg-transparent w-100 text-start">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <div class="container py-3 py-md-4">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="driver-bottom-nav">
        <a href="{{ route('driver.dashboard') }}" class="driver-bottom-tab {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Orders</span>
        </a>
        <a href="{{ route('driver.active') }}" class="driver-bottom-tab {{ request()->routeIs('driver.active') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Active</span>
        </a>
        <a href="{{ route('driver.earnings') }}" class="driver-bottom-tab {{ request()->routeIs('driver.earnings') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Earnings</span>
        </a>
        <a href="{{ route('driver.profile') }}" class="driver-bottom-tab {{ request()->routeIs('driver.profile') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>
</html>

