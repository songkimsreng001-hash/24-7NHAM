<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light dark">
    <title>24/7 NHAM</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script>
        (function () {
            const saved = localStorage.getItem('nham_theme');
            const theme = (saved === 'dark' || saved === 'light') 
                ? saved 
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-base: #16a34a;
            --green-dark: #166534;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
        }
        .navbar-nham {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .navbar-brand-custom {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--green-dark) !important;
            display: flex; align-items: center; gap: 8px;
        }
        .brand-icon {
            width: 30px; height: 30px;
            background: var(--green-base);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .85rem;
        }
        .nav-link { color: #475569 !important; font-size: .88rem; font-weight: 500; }
        .nav-link:hover { color: var(--green-base) !important; }
        .btn-nav-primary {
            background: var(--green-base); color: #fff !important;
            border-radius: 8px; padding: 6px 16px;
            font-size: .85rem; font-weight: 600;
            transition: background .18s;
        }
        .btn-nav-primary:hover { background: var(--green-dark) !important; }

        /* Auth card */
        .auth-wrap { min-height: calc(100vh - 65px); display: flex; align-items: center; }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 32px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .auth-card-header {
            background: linear-gradient(135deg, var(--green-dark), var(--green-base));
            padding: 28px 28px 24px;
            text-align: center;
        }
        .auth-card-header .auth-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,.2);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: #fff;
            margin: 0 auto 12px;
        }
        .auth-card-header h4 { color: #fff; font-weight: 700; font-size: 1.2rem; margin: 0; }
        .auth-card-header p  { color: rgba(255,255,255,.75); font-size: .83rem; margin: 5px 0 0; }
        .auth-card-body { padding: 28px; }

        .form-label { font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .input-group-text {
            background: #f8fafc; border-right: none;
            border-color: #e2e8f0; color: #94a3b8;
        }
        .form-control {
            border-left: none; border-color: #e2e8f0;
            font-size: .88rem;
        }
        .form-control:focus {
            border-color: var(--green-base);
            box-shadow: 0 0 0 3px rgba(22,163,74,.12);
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--green-base);
        }
        .btn-submit {
            background: var(--green-base); color: #fff;
            border: none; border-radius: 10px;
            padding: 10px 24px; font-size: .88rem; font-weight: 600;
            width: 100%; transition: background .18s;
        }
        .btn-submit:hover { background: var(--green-dark); color: #fff; }
        .auth-link { color: var(--green-base); font-weight: 500; font-size: .83rem; }
        .auth-link:hover { color: var(--green-dark); }

        /* Dark Theme Support */
        .theme-toggle-btn { cursor: pointer; transition: transform .18s ease; background: transparent; }
        .theme-toggle-btn:hover { transform: scale(1.08); }
        html[data-bs-theme="dark"] .theme-icon-dark, html[data-theme="dark"] .theme-icon-dark { display: none !important; }
        html[data-bs-theme="dark"] .theme-icon-light, html[data-theme="dark"] .theme-icon-light { display: inline-block !important; }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .theme-icon-light { display: none !important; }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .theme-icon-dark { display: inline-block !important; }

        html[data-bs-theme="dark"], html[data-theme="dark"] { color-scheme: dark; }
        html[data-bs-theme="dark"] body, html[data-theme="dark"] body { background: #0b1320; color: #e2e8f0; }
        html[data-bs-theme="dark"] .navbar-nham, html[data-theme="dark"] .navbar-nham { background: #131c2e; border-bottom: 1px solid #223048; }
        html[data-bs-theme="dark"] .auth-card, html[data-theme="dark"] .auth-card { background: #131c2e; border-color: #223048; color: #e2e8f0; }
        html[data-bs-theme="dark"] .card, html[data-theme="dark"] .card { background-color: #131c2e !important; border-color: #223048 !important; color: #e2e8f0 !important; }
        html[data-bs-theme="dark"] .card-header, html[data-theme="dark"] .card-header { background-color: #101928 !important; border-color: #223048 !important; color: #e2e8f0 !important; }
        html[data-bs-theme="dark"] .form-control, html[data-theme="dark"] .form-control { background-color: #0b1320; border-color: #223048; color: #f8fafc; }
        html[data-bs-theme="dark"] .input-group-text, html[data-theme="dark"] .input-group-text { background-color: #1a2538; border-color: #223048; color: #94a3b8; }
        html[data-bs-theme="dark"] .dropdown-menu, html[data-theme="dark"] .dropdown-menu { background-color: #131c2e; border-color: #223048; color: #e2e8f0; }
        html[data-bs-theme="dark"] .dropdown-item, html[data-theme="dark"] .dropdown-item { color: #cbd5e1; }
        html[data-bs-theme="dark"] .dropdown-item:hover, html[data-theme="dark"] .dropdown-item:hover { background-color: #1e293b; color: #4ade80; }
        html[data-bs-theme="dark"] .dropdown-divider, html[data-theme="dark"] .dropdown-divider { border-color: #223048; }
        html[data-bs-theme="dark"] .nav-link, html[data-theme="dark"] .nav-link { color: #cbd5e1 !important; }
        html[data-bs-theme="dark"] .text-dark, html[data-theme="dark"] .text-dark { color: #f8fafc !important; }
        html[data-bs-theme="dark"] .bg-white, html[data-theme="dark"] .bg-white { background-color: #131c2e !important; }
        html[data-bs-theme="dark"] .form-label, html[data-theme="dark"] .form-label { color: #cbd5e1; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-nham">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="{{ url('/') }}">
                <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 38px; width: auto; object-fit: contain;">
                <span style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.3px; color: var(--green-dark);">24/7 NHAM</span>
            </a>
            <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item me-lg-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 p-1 theme-toggle-btn d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Toggle Light / Dark Mode" aria-label="Toggle theme">
                            <i class="bi bi-moon-stars-fill theme-icon-dark text-warning"></i>
                            <i class="bi bi-sun-fill theme-icon-light text-warning"></i>
                        </button>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-nav-primary ms-1" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @else
                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-th-large me-1"></i> Dashboard
                            </a>
                        </li>
                        @elseif(auth()->user()->isDriver())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('driver.dashboard') }}">
                                <i class="fas fa-bicycle me-1"></i> Courier Portal
                            </a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.orders.index') }}">
                                <i class="fas fa-clock me-1"></i> My Orders
                            </a>
                        </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                               id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover border" style="width: 22px; height: 22px;">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                                @if(auth()->user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-th-large me-2 text-success"></i> Dashboard</a></li>
                                @elseif(auth()->user()->isDriver())
                                <li><a class="dropdown-item" href="{{ route('driver.dashboard') }}">
                                    <i class="fas fa-bicycle me-2 text-success"></i> Courier Portal</a></li>
                                @else
                                <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}">
                                    <i class="fas fa-clock me-2 text-success"></i> My Orders</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.edit', auth()->user()->id) }}">
                                    <i class="fas fa-user-edit me-2 text-primary"></i> Edit Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('form.password') }}">
                                    <i class="fas fa-key me-2 text-warning"></i> Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="auth-wrap py-4">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>Swal.fire({toast:true,position:'top-end',icon:'success',title:@json(session('success')),showConfirmButton:false,timer:2600});</script>
    @endif
    @if(session('error'))
    <script>Swal.fire({toast:true,position:'top-end',icon:'error',title:@json(session('error')),showConfirmButton:false,timer:3200});</script>
    @endif
    @if($errors->any())
    <script>Swal.fire({icon:'error',title:'Please check the form',html:@json(implode('<br>', $errors->all()))});</script>
    @endif

    <script>
        (function () {
            function getPreferredTheme() {
                const stored = localStorage.getItem('nham_theme');
                if (stored === 'dark' || stored === 'light') return stored;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-theme', theme);
                const isDark = theme === 'dark';
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                    btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                });
            }

            function toggleTheme() {
                const current = document.documentElement.getAttribute('data-bs-theme') || getPreferredTheme();
                const next = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('nham_theme', next);
                applyTheme(next);
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    btn.addEventListener('click', toggleTheme);
                });
                applyTheme(getPreferredTheme());
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>