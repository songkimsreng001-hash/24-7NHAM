<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>24/7 NHAM Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script>
        (function () {
            const saved = localStorage.getItem('nham_theme');
            const theme = (saved === 'dark' || saved === 'light') 
                ? saved 
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);

            // Restore sidebar collapsed/closed state immediately to prevent layout flash
            const sidebarPref = localStorage.getItem('nham_sidebar_collapsed');
            if (sidebarPref === 'true' || (sidebarPref === null && window.innerWidth < 768)) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Backend Styles (Cached) -->
    <link href="{{ asset('css/backend-custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div id="sidebar-overlay"></div>

    <aside id="sidebar" aria-label="Main navigation">
        <a href="{{ route('dashboard') }}" class="brand text-decoration-none" title="24/7 NHAM">
            <div class="brand-mark p-1" style="background: rgba(255,255,255,.95); overflow: hidden; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="brand-name">24/7 <span>NHAM</span></div>
        </a>

        <div class="sidebar-search" style="padding:14px 14px 2px;">
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9aa8a1;">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                </span>
                <input id="sidebarSearch" type="search" placeholder="{{ __('Search menu...') }}" style="width:100%;height:38px;border:1px solid #e6ece9;background:#f7faf8;border-radius:10px;padding:0 10px 0 34px;outline:none;color:#31433a;font-size:11px;">
            </div>
        </div>
        <div class="sidebar-scroll" id="sidebarScroll">
            <div class="sidebar-label">{{ __('Navigation') }}</div>
            <nav class="sidebar-nav">
                {{-- Group: Dashboard --}}
                @php
                    $isDashboardActive = request()->routeIs('dashboard') || request()->routeIs('admin.analytics*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isDashboardActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-dashboard"
                            aria-expanded="{{ $isDashboardActive ? 'true' : 'false' }}" aria-controls="group-dashboard" title="{{ __('Dashboard') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span>
                        <span class="nav-group-title">{{ __('Dashboard') }}</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isDashboardActive ? 'show' : '' }}" id="group-dashboard" data-initially-open="{{ $isDashboardActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('dashboard') }}" class="nav-sub-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="{{ __('Dashboard') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Dashboard') }}</span>
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="nav-sub-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}" title="{{ __('Analytics & KPIs') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Analytics & KPIs') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Group: Catalog & Menu --}}
                @php
                    $isCatalogActive = request()->routeIs('product.*') || request()->routeIs('category.*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isCatalogActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-catalog"
                            aria-expanded="{{ $isCatalogActive ? 'true' : 'false' }}" aria-controls="group-catalog" title="{{ __('Catalog & Menu') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="m5 7 7-3 7 3-7 3z"/><path d="M5 7v10l7 3 7-3V7M12 10v10"/></svg></span>
                        <span class="nav-group-title">{{ __('Catalog & Menu') }}</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isCatalogActive ? 'show' : '' }}" id="group-catalog" data-initially-open="{{ $isCatalogActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('product.index') }}" class="nav-sub-item {{ request()->routeIs('product.*') ? 'active' : '' }}" title="{{ __('Products') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Products') }}</span>
                            </a>
                            <a href="{{ route('category.list') }}" class="nav-sub-item {{ request()->routeIs('category.*') ? 'active' : '' }}" title="{{ __('Categories') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Categories') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Group: Orders & Deliveries --}}
                @php
                    $isOrdersActive = request()->routeIs('admin.order*') || request()->routeIs('admin.orders.active*');
                    $sidebarOrderCount = \App\Models\Order::whereIn('status',[1,2])->count();
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isOrdersActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-orders"
                            aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}" aria-controls="group-orders" title="{{ __('Orders & Delivery') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M6 3h12v18H6z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg></span>
                        <span class="nav-group-title">{{ __('Orders & Delivery') }}</span>
                        @if($sidebarOrderCount)
                            <span class="nav-badge me-1">{{ $sidebarOrderCount > 99 ? '99+' : $sidebarOrderCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isOrdersActive ? 'show' : '' }}" id="group-orders" data-initially-open="{{ $isOrdersActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.order') }}" class="nav-sub-item {{ request()->routeIs('admin.order*') ? 'active' : '' }}" title="{{ __('All Orders') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('All Orders') }}</span>
                                @if($sidebarOrderCount)
                                    <span class="nav-badge ms-auto">{{ $sidebarOrderCount > 99 ? '99+' : $sidebarOrderCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.orders.active') }}" class="nav-sub-item {{ request()->routeIs('admin.orders.active') ? 'active' : '' }}" title="{{ __('Active Deliveries') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Active Deliveries') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Group: Marketing & Promos --}}
                @php
                    $isMarketingActive = request()->routeIs('admin.promos*') || request()->routeIs('admin.broadcast*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isMarketingActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-marketing"
                            aria-expanded="{{ $isMarketingActive ? 'true' : 'false' }}" aria-controls="group-marketing" title="{{ __('Marketing & Promos') }}">
                        <span class="nav-icon"><i class="bi bi-megaphone"></i></span>
                        <span class="nav-group-title">{{ __('Marketing & Promos') }}</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isMarketingActive ? 'show' : '' }}" id="group-marketing" data-initially-open="{{ $isMarketingActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.promos') }}" class="nav-sub-item {{ request()->routeIs('admin.promos*') ? 'active' : '' }}" title="{{ __('Promo Codes') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Promo Codes') }}</span>
                            </a>
                            <a href="{{ route('admin.broadcast') }}" class="nav-sub-item {{ request()->routeIs('admin.broadcast*') ? 'active' : '' }}" title="{{ __('Broadcast Alerts') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Broadcast Alerts') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="sidebar-label">{{ __('System & Account') }}</div>
            <nav class="sidebar-nav">
                {{-- Group: Administration --}}
                @php
                    $isAdminActive = request()->routeIs('admin.users*') || request()->routeIs('admin.disputes*') || request()->routeIs('admin.audit*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isAdminActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-admin"
                            aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}" aria-controls="group-admin" title="{{ __('Administration') }}">
                        <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
                        <span class="nav-group-title">{{ __('Administration') }}</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isAdminActive ? 'show' : '' }}" id="group-admin" data-initially-open="{{ $isAdminActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.users') }}" class="nav-sub-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" title="{{ __('Users & Couriers') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Users & Couriers') }}</span>
                            </a>
                            <a href="{{ route('admin.disputes') }}" class="nav-sub-item {{ request()->routeIs('admin.disputes*') ? 'active' : '' }}" title="{{ __('Disputes Queue') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Disputes Queue') }}</span>
                            </a>
                            <a href="{{ route('admin.audit') }}" class="nav-sub-item {{ request()->routeIs('admin.audit*') ? 'active' : '' }}" title="{{ __('Audit Trail') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Audit Trail') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Group: Account & Settings --}}
                @php
                    $isAccountActive = request()->routeIs('profile.*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isAccountActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-account"
                            aria-expanded="{{ $isAccountActive ? 'true' : 'false' }}" aria-controls="group-account" title="{{ __('Account & Settings') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 20c.8-3.4 3.2-5 7-5s6.2 1.6 7 5"/></svg></span>
                        <span class="nav-group-title">{{ __('Account & Settings') }}</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isAccountActive ? 'show' : '' }}" id="group-account" data-initially-open="{{ $isAccountActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ auth()->id() ? route('profile.edit', auth()->id()) : route('login') }}" class="nav-sub-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="{{ __('Profile') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Profile') }}</span>
                            </a>
                            <a href="{{ url('/contact') }}" class="nav-sub-item" title="{{ __('Help & Support') }}">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">{{ __('Help & Support') }}</span>
                            </a>

                            <!-- Theme Mode Toggle at Sidebar -->
                            <button type="button" class="nav-sub-item logout-button admin-theme-toggle-btn w-100 border-0 bg-transparent text-start" id="sidebarThemeToggle" title="Toggle Light / Dark Mode">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text admin-theme-label">{{ __('Dark Mode') }}</span>
                                <span class="nav-badge admin-theme-badge ms-auto" style="font-size: 9px; padding: 2px 7px;">Light</span>
                            </button>

                            <!-- Log Out Form -->
                            <form action="{{ route('logout') }}" method="POST" class="logout-form m-0">
                                @csrf
                                <button type="submit" class="nav-sub-item logout-button w-100 border-0 bg-transparent text-start" title="{{ __('Log Out') }}">
                                    <span class="nav-sub-dot"></span>
                                    <span class="nav-text">{{ __('Log Out') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="sidebar-user" title="{{ auth()->user()->name ?? 'Administrator' }}">
            @if(auth()->user() && auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar rounded-circle object-fit-cover">
            @else
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</div>
            @endif
            <div class="sidebar-user-main">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role ?? 'admin' }}</div>
            </div>
        </div>
    </aside>

    <header id="topbar">
        <button class="sidebar-toggle-btn mobile-toggle" id="sidebar-toggle" type="button" aria-label="Toggle navigation" title="Toggle Sidebar">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <form class="global-search" id="globalSearchForm" action="{{ url('/search') }}" method="GET" autocomplete="off">
            <span class="search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg></span>
            <input id="globalSearchInput" name="keyword" type="search" placeholder="{{ __('Search products...') }}" autocomplete="off">
            <span class="search-key">Ctrl K</span>
            <div id="globalSearchResults" class="global-search-results"></div>
        </form>

        <div class="top-actions">
            @php
                $isKhmerBackend = in_array(session('locale'), ['kh', 'km', 'khmer', 'khmer_unicode']) || in_array(app()->getLocale(), ['kh', 'km', 'khmer', 'khmer_unicode']);
                $targetLocale = $isKhmerBackend ? 'english' : 'khmer_unicode';
                $tooltipTitle = $isKhmerBackend ? 'Switch to English (EN)' : 'ប្តូរជាភាសាខ្មែរ (KH)';
            @endphp
            <!-- Backend Language Switch Button: Rectangular EN / KH Box -->
            <a href="{{ route('lang.switch', $targetLocale) }}" 
               class="lang-switch-box text-decoration-none" 
               id="backendLangToggleBtn"
               title="{{ $tooltipTitle }}"
               aria-label="{{ $tooltipTitle }}">
                <span class="lang-code {{ !$isKhmerBackend ? 'active' : 'dim' }}">EN</span>
                <span class="lang-sep">/</span>
                <span class="lang-code {{ $isKhmerBackend ? 'active' : 'dim' }}">KH</span>
            </a>

            <!-- Topbar Theme Toggle Button -->
            <button class="top-icon admin-theme-toggle-btn" id="topbarThemeToggle" type="button" aria-label="Toggle theme" title="Toggle Light / Dark Mode">
                <i class="bi bi-moon-stars-fill text-warning theme-icon-dark" style="font-size: 16px;"></i>
                <i class="bi bi-sun-fill text-warning theme-icon-light" style="font-size: 16px;"></i>
            </button>
            <button class="top-icon" type="button" aria-label="Messages">
                <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="14" rx="2"/><path d="m5 7 7 5 7-5"/></svg>
            </button>
            <button class="top-icon" type="button" aria-label="Notifications">
                <span class="notification-dot"></span>
                <svg viewBox="0 0 24 24"><path d="M18 10a6 6 0 0 0-12 0c0 7-3 7-3 8h18c0-1-3-1-3-8M10 21h4"/></svg>
            </button>
            <div class="profile">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar rounded-circle object-fit-cover">
                @else
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</div>
                @endif
                <div class="profile-text">
                    <div class="profile-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="profile-email">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
        </div>
    </header>

    <main id="main-wrapper">
        <div class="content-container">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-3">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    <script>
        (function () {
            // =========================================================
            // SIDEBAR TOGGLE CONTROLLER (OPEN = ICONS+NAMES, CLOSED = ICONS ONLY)
            // =========================================================
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggle = document.getElementById('sidebar-toggle');

            function isSidebarCollapsed() {
                return document.documentElement.classList.contains('sidebar-collapsed');
            }

            function updateSidebarToggleUI(collapsed) {
                if (toggle) {
                    toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    toggle.setAttribute('title', collapsed ? 'Expand sidebar (show menu names)' : 'Collapse sidebar (show icons only)');
                }
            }

            function setSidebarCollapsed(collapsed, save = true) {
                if (collapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                    document.body.classList.add('sidebar-collapsed');
                    sidebar?.classList.remove('show');
                    overlay?.classList.remove('show');
                    document.body.style.overflow = '';
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                    document.body.classList.remove('sidebar-collapsed');
                    if (window.innerWidth < 768) {
                        sidebar?.classList.add('show');
                        overlay?.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    } else {
                        overlay?.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }
                updateSidebarToggleUI(collapsed);
                if (save) {
                    localStorage.setItem('nham_sidebar_collapsed', collapsed ? 'true' : 'false');
                }
            }

            // Sync initial state on load
            updateSidebarToggleUI(isSidebarCollapsed());

            toggle?.addEventListener('click', function (e) {
                e.preventDefault();
                setSidebarCollapsed(!isSidebarCollapsed(), true);
            });

            overlay?.addEventListener('click', function () {
                setSidebarCollapsed(true, true);
            });

            // =========================================================
            // SIDEBAR LISTDOWN / ACCORDION CONTROLLER (PURE VANILLA JS)
            // =========================================================
            document.querySelectorAll('#sidebarScroll .nav-group-toggle').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // If sidebar was in collapsed (icons-only) mode, expand it so the user can see sub-items
                    if (isSidebarCollapsed()) {
                        setSidebarCollapsed(false, true);
                    }

                    const targetSelector = this.getAttribute('data-bs-target') || this.getAttribute('data-target');
                    const collapseEl = targetSelector ? document.querySelector(targetSelector) : this.nextElementSibling;
                    if (!collapseEl) return;

                    const isOpen = collapseEl.classList.contains('show');

                    if (isOpen) {
                        collapseEl.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                    } else {
                        collapseEl.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            // On mobile devices, clicking an actual navigation link closes expanded sidebar back to icons
            document.querySelectorAll('#sidebarScroll .nav-sub-item, #sidebarScroll .nav-item-link').forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768 && !isSidebarCollapsed()) {
                        setSidebarCollapsed(true, true);
                    }
                });
            });

            function escapeHtml(val) {
                const div = document.createElement('div');
                div.textContent = val ?? '';
                return div.innerHTML;
            }

            function highlightMatch(text, query) {
                if (!query) return escapeHtml(text);
                const safe = escapeHtml(text);
                const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                return safe.replace(new RegExp(`(${escapedQuery})`, 'gi'), '<strong style="color:var(--nham-500);">$1</strong>');
            }

            // =========================================================
            // SIDEBAR SEARCH: Real-time filtering across groups and listdown items
            // =========================================================
            const sidebarSearch = document.getElementById('sidebarSearch');
            if (sidebarSearch) {
                sidebarSearch.addEventListener('input', function () {
                    const q = this.value.trim().toLowerCase();
                    let anyVisible = false;

                    document.querySelectorAll('#sidebarScroll .nav-group').forEach(group => {
                        const toggle = group.querySelector('.nav-group-toggle');
                        const collapseEl = group.querySelector('.nav-group-collapse');
                        const groupTitle = toggle?.querySelector('.nav-group-title')?.textContent.toLowerCase() || '';
                        const items = group.querySelectorAll('.nav-sub-item');
                        let groupMatches = false;

                        items.forEach(item => {
                            const text = item.textContent.toLowerCase();
                            const matches = !q || text.includes(q) || groupTitle.includes(q);
                            item.style.display = matches ? 'flex' : 'none';
                            if (matches) groupMatches = true;
                        });

                        if (!q) {
                            group.style.display = 'block';
                            if (collapseEl) {
                                const initiallyOpen = collapseEl.getAttribute('data-initially-open') === 'true';
                                if (initiallyOpen) {
                                    collapseEl.classList.add('show');
                                    toggle?.setAttribute('aria-expanded', 'true');
                                } else {
                                    collapseEl.classList.remove('show');
                                    toggle?.setAttribute('aria-expanded', 'false');
                                }
                            }
                        } else {
                            if (groupMatches) {
                                group.style.display = 'block';
                                collapseEl?.classList.add('show');
                                toggle?.setAttribute('aria-expanded', 'true');
                                anyVisible = true;
                            } else {
                                group.style.display = 'none';
                            }
                        }
                    });

                    // Hide headers and promo during active search
                    document.querySelectorAll('#sidebarScroll .sidebar-label').forEach(label => {
                        label.style.display = q ? 'none' : 'block';
                    });

                    const promo = document.querySelector('.sidebar-promo');
                    if (promo) {
                        promo.style.display = q ? 'none' : 'block';
                    }

                    let emptyMsg = document.getElementById('sidebarSearchEmpty');
                    if (!anyVisible && q) {
                        if (!emptyMsg) {
                            emptyMsg = document.createElement('div');
                            emptyMsg.id = 'sidebarSearchEmpty';
                            emptyMsg.style.cssText = 'padding:14px; text-align:center; color:rgba(255,255,255,.6); font-size:11px;';
                            emptyMsg.innerHTML = '<i class="bi bi-search me-1"></i> No menu matches';
                            document.getElementById('sidebarScroll').appendChild(emptyMsg);
                        }
                        emptyMsg.style.display = 'block';
                    } else if (emptyMsg) {
                        emptyMsg.style.display = 'none';
                    }
                });
            }

            // =========================================================
            // TOPBAR GLOBAL SEARCH: Instant automatic search on character input
            // =========================================================
            const globalSearchInput = document.getElementById('globalSearchInput');
            const globalSearchResults = document.getElementById('globalSearchResults');
            let globalSearchTimer = null;

            if (globalSearchInput && globalSearchResults) {
                globalSearchInput.addEventListener('input', function () {
                    const q = this.value.trim();
                    clearTimeout(globalSearchTimer);

                    if (!q) {
                        globalSearchResults.innerHTML = '';
                        globalSearchResults.classList.remove('show');
                        return;
                    }

                    globalSearchResults.innerHTML = `
                        <div style="padding:14px; text-align:center; color:#8d9e95; font-size:11px;">
                            <i class="bi bi-arrow-repeat me-1 spin"></i> Searching products...
                        </div>`;
                    globalSearchResults.classList.add('show');

                    globalSearchTimer = setTimeout(function () {
                        fetch(`{{ url('/navbar-search') }}?title=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(items => {
                            globalSearchResults.innerHTML = '';
                            if (!items.length) {
                                globalSearchResults.innerHTML = `
                                    <div style="padding:14px; text-align:center; color:#8d9e95; font-size:11px;">
                                        <i class="bi bi-search me-1"></i> No products found for "${escapeHtml(q)}"
                                    </div>`;
                                globalSearchResults.classList.add('show');
                                return;
                            }

                            items.forEach(p => {
                                const item = document.createElement('div');
                                item.className = 'global-search-item';
                                const img = p.image 
                                    ? `<img src="${escapeHtml(p.image)}" alt="" class="global-search-item-img" onerror="this.onerror=null;this.src='{{ asset('img/logo.png') }}';">`
                                    : `<div class="global-search-item-img d-grid place-items-center"><i class="bi bi-box-seam text-muted"></i></div>`;
                                
                                item.innerHTML = `
                                    ${img}
                                    <div class="global-search-item-info">
                                        <a href="${p.url}" class="global-search-item-title d-block text-decoration-none" target="_blank">${highlightMatch(p.title, q)}</a>
                                        <span class="global-search-item-price">$${escapeHtml(p.price)}</span>
                                    </div>
                                    <div class="global-search-item-action">
                                        <a href="${p.edit_url}" class="global-search-badge" title="Edit Product">Edit</a>
                                        <a href="${p.url}" class="global-search-badge" target="_blank" title="View in Store">View</a>
                                    </div>
                                `;
                                globalSearchResults.appendChild(item);
                            });
                            globalSearchResults.classList.add('show');
                        })
                        .catch(() => {
                            globalSearchResults.classList.remove('show');
                        });
                    }, 120);
                });

                globalSearchInput.addEventListener('focus', function () {
                    if (this.value.trim().length >= 1 && globalSearchResults.innerHTML.trim()) {
                        globalSearchResults.classList.add('show');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!document.getElementById('globalSearchForm')?.contains(e.target)) {
                        globalSearchResults.classList.remove('show');
                    }
                });
            }

            // Ctrl/Cmd + K focuses the product search.
            document.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    globalSearchInput?.focus();
                }
                if (e.key === 'Escape') {
                    globalSearchResults?.classList.remove('show');
                    globalSearchInput?.blur();
                }
            });

            // =========================================================
            // ADMIN LIGHT / DARK THEME CONTROLLER
            // =========================================================
            function getAdminTheme() {
                const stored = localStorage.getItem('nham_theme');
                if (stored === 'dark' || stored === 'light') return stored;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function applyAdminTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-theme', theme);
                const isDark = theme === 'dark';
                document.querySelectorAll('.admin-theme-label').forEach(el => {
                    el.textContent = isDark ? 'Light Mode' : 'Dark Mode';
                });
                document.querySelectorAll('.admin-theme-badge').forEach(el => {
                    el.textContent = isDark ? 'Dark' : 'Light';
                });
                document.querySelectorAll('.admin-theme-toggle-btn').forEach(btn => {
                    btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                    btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                });
            }

            function toggleAdminTheme(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                const current = document.documentElement.getAttribute('data-bs-theme') || getAdminTheme();
                const next = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('nham_theme', next);
                applyAdminTheme(next);
            }

            document.querySelectorAll('.admin-theme-toggle-btn').forEach(btn => {
                btn.addEventListener('click', toggleAdminTheme);
            });

            applyAdminTheme(getAdminTheme());

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('nham_theme')) {
                    applyAdminTheme(e.matches ? 'dark' : 'light');
                }
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
