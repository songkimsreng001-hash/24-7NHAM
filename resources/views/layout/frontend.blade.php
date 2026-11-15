<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="24/7 NHAM — Food, Drinks & More" />
    <meta name="color-scheme" content="light dark" />

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

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom App Styles (Cached) -->
    <link href="{{ asset('css/frontend-custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    @php
        $isKhmerFrontend = in_array(session('locale'), ['kh', 'km', 'khmer', 'khmer_unicode']) || in_array(app()->getLocale(), ['kh', 'km', 'khmer', 'khmer_unicode']);
        $frontendTargetLocale = $isKhmerFrontend ? 'english' : 'khmer_unicode';
        $frontendTooltip = $isKhmerFrontend ? 'Switch to English (EN)' : 'ប្តូរជាភាសាខ្មែរ (KH)';
    @endphp

    <!-- =========================================================
         NAVBAR
    ========================================================= -->
    <nav class="navbar navbar-expand-lg navbar-nham sticky-top">
        <div class="container">

            <!-- BRAND -->
            <a class="navbar-brand-custom" href="{{ url('/') }}">
                <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" class="brand-logo-img">
                <span style="font-weight: 800; font-size: 1.18rem; letter-spacing: -0.4px; color: var(--green-dark);">24/7 NHAM</span>
            </a>

            <!-- MOBILE ACTIONS (LANGUAGE, THEME TOGGLE & BURGER) -->
            <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-1">
                <a href="{{ route('lang.switch', $frontendTargetLocale) }}" 
                   class="lang-switch-box text-decoration-none d-inline-flex align-items-center justify-content-center" 
                   style="height: 34px; padding: 0 9px; font-size: 13px;"
                   title="{{ $frontendTooltip }}"
                   aria-label="{{ $frontendTooltip }}">
                    <span class="lang-code {{ !$isKhmerFrontend ? 'active' : 'dim' }}">EN</span>
                    <span class="lang-sep">/</span>
                    <span class="lang-code {{ $isKhmerFrontend ? 'active' : 'dim' }}">KH</span>
                </a>
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm rounded-3 p-1 theme-toggle-btn d-flex align-items-center justify-content-center" 
                        id="mobileThemeToggleBtn"
                        style="width: 36px; height: 36px;"
                        title="Toggle Light / Dark Mode" 
                        aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill theme-icon-dark text-warning" style="font-size: 1.05rem;"></i>
                    <i class="bi bi-sun-fill theme-icon-light text-warning" style="font-size: 1.05rem;"></i>
                </button>
                <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="mainNav">

                <!-- NAVIGATION -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">
                            {{ __('Home') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('list') ? 'active' : '' }}" href="{{ url('/list') }}">
                            {{ __('Products') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('frontend*') ? 'active' : '' }}"
                            href="{{ url('/frontend') }}">
                            {{ __('Categories') }}
                        </a>
                    </li>

                    <li class="nav-item dropdown dropdown-mega position-static">
                        <a class="nav-link dropdown-toggle d-inline-flex align-items-center gap-1 {{ request()->routeIs('order.track*') || request()->routeIs('customer.orders*') || request()->is('list*') ? 'active' : '' }}" 
                           href="#" 
                           id="shopMegaDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           data-bs-display="static" 
                           aria-expanded="false">
                            <span>{{ __('Shop') }}</span>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-1 py-0 ms-1 fw-bold" style="font-size: 0.65rem;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-mega-menu shadow-lg border-0" aria-labelledby="shopMegaDropdown">
                            <div class="row g-3 g-lg-4">
                                <!-- Column 1: Quick Services & Orders -->
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="mega-column-title">
                                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Services
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        <a class="mega-menu-item {{ request()->is('list') && !request('category') ? 'active' : '' }}" href="{{ url('/list') }}">
                                            <div class="mega-item-icon bg-success-subtle text-success">
                                                <i class="fas fa-th-large"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <div class="mega-item-name">All Products</div>
                                                <div class="mega-item-desc">Browse full food & drink catalog</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request()->routeIs('order.track*') ? 'active' : '' }}" href="{{ route('order.track') }}">
                                            <div class="mega-item-icon bg-primary-subtle text-primary">
                                                <i class="bi bi-truck"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="mega-item-name">Track Order</span>
                                                    <span class="badge bg-primary text-white rounded-pill" style="font-size: 0.6rem;">LIVE GPS</span>
                                                </div>
                                                <div class="mega-item-desc">5-stage real-time delivery map</div>
                                            </div>
                                        </a>

                                        @auth
                                        <a class="mega-menu-item {{ request()->routeIs('customer.orders*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                                            <div class="mega-item-icon bg-warning-subtle text-warning">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="mega-item-name">My Orders</span>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border rounded-pill" style="font-size: 0.6rem;">History</span>
                                                </div>
                                                <div class="mega-item-desc">Past receipts, invoices & reorder</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request()->routeIs('favorites*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                                            <div class="mega-item-icon bg-danger-subtle text-danger">
                                                <i class="bi bi-heart-fill"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Favorite Dishes</span>
                                                <div class="mega-item-desc">Your saved meals & snacks</div>
                                            </div>
                                        </a>
                                        @endauth

                                        <a class="mega-menu-item" href="{{ route('support.chat') }}">
                                            <div class="mega-item-icon bg-info-subtle text-info">
                                                <i class="bi bi-headset"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">24/7 Live Support</span>
                                                <div class="mega-item-desc">Instant delivery help & chat</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Column 2: Meals & Mains -->
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="mega-column-title">
                                        <i class="fas fa-utensils text-success me-1"></i> Meals & Mains
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        <a class="mega-menu-item {{ request('category') == 'food' ? 'active' : '' }}" href="{{ url('/list?category=food') }}">
                                            <div class="mega-item-icon bg-warning-subtle text-warning">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Food & Hot Meals</span>
                                                <div class="mega-item-desc">Cooked rice, stir fries & soups</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'fast-food' ? 'active' : '' }}" href="{{ url('/list?category=fast-food') }}">
                                            <div class="mega-item-icon bg-danger-subtle text-danger">
                                                <i class="fas fa-hamburger"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="mega-item-name">Fast Food</span>
                                                    <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.58rem;">HOT</span>
                                                </div>
                                                <div class="mega-item-desc">Burgers, crispy chicken & fries</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'sea-food' ? 'active' : '' }}" href="{{ url('/list?category=sea-food') }}">
                                            <div class="mega-item-icon bg-primary-subtle text-primary">
                                                <i class="fas fa-fish"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Sea Food</span>
                                                <div class="mega-item-desc">Fresh prawns, squid & fish</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'snacks' ? 'active' : '' }}" href="{{ url('/list?category=snacks') }}">
                                            <div class="mega-item-icon bg-secondary-subtle text-secondary">
                                                <i class="fas fa-cookie"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Snacks & Bites</span>
                                                <div class="mega-item-desc">Chips, biscuits & nibbles</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Column 3: Beverages & Sweet Treats -->
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="mega-column-title">
                                        <i class="fas fa-glass-cheers text-info me-1"></i> Drinks & Treats
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        <a class="mega-menu-item {{ request('category') == 'drink' ? 'active' : '' }}" href="{{ url('/list?category=drink') }}">
                                            <div class="mega-item-icon bg-info-subtle text-info">
                                                <i class="fas fa-mug-hot"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Coffee & Tea</span>
                                                <div class="mega-item-desc">Iced latte, milk tea & matcha</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'beverage' ? 'active' : '' }}" href="{{ url('/list?category=beverage') }}">
                                            <div class="mega-item-icon bg-primary-subtle text-primary">
                                                <i class="fas fa-glass-water"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Beverages</span>
                                                <div class="mega-item-desc">Sodas, fruit juices & waters</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'beer' ? 'active' : '' }}" href="{{ url('/list?category=beer') }}">
                                            <div class="mega-item-icon bg-warning-subtle text-warning">
                                                <i class="fas fa-beer-mug-empty"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="mega-item-name">Beer & Cider</span>
                                                    <span class="badge bg-secondary text-white rounded-pill" style="font-size: 0.58rem;">ICE COLD</span>
                                                </div>
                                                <div class="mega-item-desc">Lagers, drafts & craft cans</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'milk' ? 'active' : '' }}" href="{{ url('/list?category=milk') }}">
                                            <div class="mega-item-icon bg-success-subtle text-success">
                                                <i class="fas fa-glass-whiskey"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Milk & Shakes</span>
                                                <div class="mega-item-desc">Pure dairy, yogurts & shakes</div>
                                            </div>
                                        </a>

                                        <a class="mega-menu-item {{ request('category') == 'ice-cream' ? 'active' : '' }}" href="{{ url('/list?category=ice-cream') }}">
                                            <div class="mega-item-icon bg-danger-subtle text-danger">
                                                <i class="fas fa-ice-cream"></i>
                                            </div>
                                            <div class="mega-item-text">
                                                <span class="mega-item-name">Ice Cream</span>
                                                <div class="mega-item-desc">Scoops, gelato & ice pops</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Column 4: Promo & Fast Delivery Card -->
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="mega-promo-card h-100 d-flex flex-column justify-content-between p-3 p-xl-4 rounded-4">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 32px; width: auto; object-fit: contain;">
                                                    <span class="fw-bold text-white small" style="letter-spacing: -0.2px;">24/7 NHAM</span>
                                                </div>
                                                <span class="badge bg-white text-success fw-bold px-2 py-1" style="font-size: 0.65rem;">OPEN 24/7</span>
                                            </div>
                                            <h6 class="fw-bolder text-white mb-1" style="font-size: 1.02rem;">Hungry? We Deliver!</h6>
                                            <p class="text-white-50 small mb-3" style="font-size: 0.77rem; line-height: 1.45;">
                                                Fast 20-35 mins delivery directly to your door across Phnom Penh.
                                            </p>
                                            <div class="p-2 rounded-3 mb-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-white-50" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">PROMO CODE</span>
                                                    <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.62rem;">10% OFF</span>
                                                </div>
                                                <div class="fw-bolder text-warning font-monospace mt-1" style="font-size: 0.95rem; letter-spacing: 1px;">WELCOME10</div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ url('/list') }}" class="btn btn-light btn-sm w-100 fw-bold text-success rounded-pill py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                                <span>Order Now</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mega Menu Bottom Bar -->
                            <div class="mega-menu-footer mt-3 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div class="d-flex flex-wrap align-items-center gap-3 text-muted" style="font-size: 0.76rem;">
                                    <span><i class="bi bi-shield-check text-success me-1"></i> Contactless Delivery</span>
                                    <span><i class="bi bi-stopwatch text-primary me-1"></i> Avg 25 Mins</span>
                                    <span><i class="bi bi-credit-card-2-front text-warning me-1"></i> Cash on Delivery & Stripe</span>
                                </div>
                                <a href="{{ url('/frontend') }}" class="text-success fw-bold text-decoration-none small d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                    <span>Browse All Categories</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- RIGHT SIDE ACTIONS -->
                <div class="d-flex align-items-center gap-2">

                    <!-- NAVBAR SEARCH -->
                    <div class="navbar-search-wrapper">
                        <form action="{{ url('/search') }}" method="GET" id="navbarSearchForm" autocomplete="off">
                            <div class="navbar-search">
                                <i class="bi bi-search navbar-search-icon"></i>
                                <input type="text" name="title" id="navbarSearchInput" class="navbar-search-input"
                                    value="{{ request('keyword', $keyword ?? '') }}" placeholder="{{ __('Search product title...') }}"
                                    autocomplete="off">
                                <button type="submit" class="navbar-search-button">
                                    {{ __('Search') }}
                                </button>
                            </div>
                        </form>

                        <!-- AUTO SEARCH RESULTS DROPDOWN -->
                        <div id="navbarSearchResults" class="navbar-search-results"></div>
                    </div>

                    <!-- CART -->
                    <a href="{{ route('cart') }}" class="cart-btn">
                        <i class="bi bi-cart-fill"></i>
                        {{ __('Cart') }}
                        @php $cartCount = count((array) session('cart')); @endphp
                        @if($cartCount > 0)
                            <span class="cart-count">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @auth
                        <!-- FAVORITES -->
                        <a href="{{ route('favorites.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center rounded-3 p-2" style="width: 38px; height: 38px;" title="My Favorites">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </a>

                        <!-- NOTIFICATIONS BELL -->
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary position-relative d-flex align-items-center justify-content-center rounded-3 p-2" style="width: 38px; height: 38px;" title="Notifications">
                            <i class="bi bi-bell-fill text-secondary"></i>
                            <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.65rem;">
                                0
                            </span>
                        </a>
                    @endauth

                    <!-- LANGUAGE SWITCHER (EN / KH) DESKTOP FOR GUESTS -->
                    @guest
                    <a href="{{ route('lang.switch', $frontendTargetLocale) }}" 
                       class="lang-switch-box text-decoration-none d-none d-lg-inline-flex" 
                       id="desktopLangToggleBtn"
                       title="{{ $frontendTooltip }}"
                       aria-label="{{ $frontendTooltip }}">
                        <span class="lang-code {{ !$isKhmerFrontend ? 'active' : 'dim' }}">EN</span>
                        <span class="lang-sep">/</span>
                        <span class="lang-code {{ $isKhmerFrontend ? 'active' : 'dim' }}">KH</span>
                    </a>
                    @endguest

                    <!-- THEME TOGGLE (LIGHT / DARK) DESKTOP -->
                    <button type="button" 
                            class="btn btn-outline-secondary d-none d-lg-flex align-items-center justify-content-center rounded-3 p-2 theme-toggle-btn" 
                            id="desktopThemeToggleBtn"
                            style="width: 38px; height: 38px;" 
                            title="Toggle Light / Dark Mode" 
                            aria-label="Toggle theme">
                        <i class="bi bi-moon-stars-fill theme-icon-dark text-warning"></i>
                        <i class="bi bi-sun-fill theme-icon-light text-warning"></i>
                    </button>

                    <!-- LOGIN / LOGOUT -->
                    @guest
                        <a href="{{ route('login') }}" class="btn-nav-login">
                            {{ __('Login') }}
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1 rounded-3 py-1 ps-1 pe-2" 
                                    type="button" 
                                    id="navProfileDropdown"
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false" 
                                    style="height: 38px;"
                                    title="{{ auth()->user()->name }}"
                                    aria-label="User profile menu">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover border" style="width: 26px; height: 26px;">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-2" aria-labelledby="navProfileDropdown" style="min-width: 230px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle border object-fit-cover" style="width: 36px; height: 36px;">
                                        <div style="min-width: 0;">
                                            <div class="fw-bold small text-truncate">{{ auth()->user()->name }}</div>
                                            <div class="text-muted text-truncate" style="font-size: .72rem;">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </li>
                                @if(auth()->user()->isDriver())
                                <li>
                                    <a class="dropdown-item py-2 rounded-2 fw-semibold text-success bg-success-subtle mb-1" href="{{ route('driver.dashboard') }}">
                                        <i class="bi bi-bicycle me-2 text-success"></i> {{ __('Courier Portal') }}
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('customer.orders.index') }}">
                                        <i class="bi bi-clock-history me-2 text-primary"></i> {{ __('My Order History') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('order.track') }}">
                                        <i class="bi bi-truck me-2 text-success"></i> {{ __('Track Shipments') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('favorites.index') }}">
                                        <i class="bi bi-heart me-2 text-danger"></i> {{ __('Saved Favorites') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('notifications.index') }}">
                                        <i class="bi bi-bell me-2 text-secondary"></i> {{ __('Notifications') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('support.chat') }}">
                                        <i class="bi bi-headset me-2 text-info"></i> {{ __('Support & Chat') }}
                                    </a>
                                </li>
                                @if((auth()->user()->role ?? '') === 'admin')
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2 text-warning"></i> {{ __('Admin Dashboard') }}
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ auth()->id() ? route('profile.edit', auth()->id()) : route('login') }}">
                                        <i class="bi bi-gear me-2 text-secondary"></i> {{ __('Account Profile') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <!-- LANGUAGE SWITCHER (EN / KH) IN PROFILE DROPDOWN -->
                                <li class="px-2 py-1">
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border lang-dropdown-item-wrap">
                                        <span class="small fw-semibold text-secondary d-flex align-items-center">
                                            <i class="bi bi-translate text-primary me-2 fs-6"></i>
                                            <span>{{ __('Language') }}</span>
                                        </span>
                                        <a href="{{ route('lang.switch', $frontendTargetLocale) }}" 
                                           class="lang-switch-box text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                           style="height: 30px; padding: 0 10px; font-size: 13px;"
                                           title="{{ $frontendTooltip }}"
                                           aria-label="{{ $frontendTooltip }}">
                                            <span class="lang-code {{ !$isKhmerFrontend ? 'active' : 'dim' }}">EN</span>
                                            <span class="lang-sep">/</span>
                                            <span class="lang-code {{ $isKhmerFrontend ? 'active' : 'dim' }}">KH</span>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 rounded-2 theme-toggle-btn d-flex align-items-center justify-content-between border-0 bg-transparent w-100">
                                        <span>
                                            <i class="bi bi-moon-stars-fill text-warning me-2 theme-icon-dark"></i>
                                            <i class="bi bi-sun-fill text-warning me-2 theme-icon-light"></i>
                                            <span class="theme-dropdown-label">{{ __('Dark Mode') }}</span>
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill small theme-dropdown-badge" style="font-size: 0.68rem;">Theme</span>
                                    </button>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 rounded-2 text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest

                </div>
            </div>
        </div>
    </nav>

    <!-- =========================================================
         FLASH MESSAGE
    ========================================================= -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>
                {{ is_array(session('success')) ? implode(', ', session('success')) : session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ is_array(session('error')) ? implode(', ', session('error')) : session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- =========================================================
         PAGE CONTENT
    ========================================================= -->
    @yield('content')

    <!-- =========================================================
         FOOTER
    ========================================================= -->
    <footer class="footer-nham">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="footer-brand d-flex align-items-center gap-2">
                        <img src="{{ asset('img/logo.png') }}" alt="24/7 NHAM" style="height: 38px; width: auto; object-fit: contain; background: #fff; padding: 2px; border-radius: 8px;">
                        <span style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.3px; color: #fff;">24/7 NHAM</span>
                    </div>
                    <p class="footer-tagline mt-2">Your go-to store for food, drinks & more.</p>
                </div>

                <div class="col-md-4">
                    <div class="text-white mb-2" style="font-size:.85rem; font-weight:600">Shop</div>
                    <div class="footer-links d-flex flex-column gap-1">
                        <a href="{{ url('/list') }}">All Products</a>
                        <a href="{{ url('/frontend') }}">Browse Categories</a>
                        <a href="{{ url('/search') }}">Search</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-white mb-2" style="font-size:.85rem; font-weight:600">Account</div>
                    <div class="footer-links d-flex flex-column gap-1">
                        @guest
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('register') }}">Register</a>
                        @else
                            <a href="{{ route('customer.orders.index') }}">My Order History</a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-white text-decoration-none" style="font-size: .82rem;">Logout</button>
                            </form>
                        @endguest
                        <a href="{{ route('order.track') }}">Track Shipments</a>
                        <a href="{{ route('cart') }}">My Cart</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} 24/7 NHAM. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- =========================================================
         JAVASCRIPT
    ========================================================= -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @php
        $flashSuccess = session('success');
        if (is_array($flashSuccess)) {
            $flashSuccess = implode(', ', $flashSuccess);
        }
        $flashError = session('error');
        if (is_array($flashError)) {
            $flashError = implode(', ', $flashError);
        }
        $flashErrors = (isset($errors) && $errors->any()) ? implode('<br>', $errors->all()) : null;
    @endphp

    @if($flashSuccess || $flashError || $flashErrors)
    <div id="appFlashNotice" 
         class="d-none"
         data-success="{{ $flashSuccess ? e($flashSuccess) : '' }}"
         data-error="{{ $flashError ? e($flashError) : '' }}"
         data-errors="{{ $flashErrors ? e($flashErrors) : '' }}"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') return;
            const noticeEl = document.getElementById('appFlashNotice');
            if (!noticeEl) return;
            const successText = noticeEl.getAttribute('data-success');
            const errorText = noticeEl.getAttribute('data-error');
            const validationErrors = noticeEl.getAttribute('data-errors');

            if (successText) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: successText,
                    showConfirmButton: false,
                    timer: 2600,
                    timerProgressBar: true
                });
            } else if (errorText) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: errorText,
                    showConfirmButton: false,
                    timer: 3200,
                    timerProgressBar: true
                });
            }

            if (validationErrors) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please check the form',
                    html: validationErrors
                });
            }
        });
    </script>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('navbarSearchInput');
            const searchResults = document.getElementById('navbarSearchResults');
            const searchForm = document.getElementById('navbarSearchForm');

            if (!searchInput || !searchResults) return;

            let searchTimer = null;

            function escapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            }

            function closeSearchResults() {
                searchResults.classList.remove('show');
            }

            function showSearchResults() {
                searchResults.classList.add('show');
            }

            function highlightMatch(text, query) {
                if (!query) return escapeHtml(text);
                const safe = escapeHtml(text);
                const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                return safe.replace(new RegExp(`(${escapedQuery})`, 'gi'), '<strong class="text-success">$1</strong>');
            }

            /* =====================================================
               INPUT EVENT: Begins search as soon as characters are typed
            ===================================================== */
            searchInput.addEventListener('input', function () {
                const keyword = this.value.trim();
                clearTimeout(searchTimer);

                if (keyword.length === 0) {
                    searchResults.innerHTML = '';
                    closeSearchResults();
                    return;
                }

                searchResults.innerHTML = `
                    <div class="navbar-search-loading">
                        <i class="bi bi-arrow-repeat me-1 spin"></i>
                        Searching products...
                    </div>`;
                showSearchResults();

                searchTimer = setTimeout(function () {
                    const url = `{{ url('/navbar-search') }}?title=${encodeURIComponent(keyword)}`;

                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Search request failed');
                        return response.json();
                    })
                    .then(products => {
                        searchResults.innerHTML = '';

                        if (!products.length) {
                            searchResults.innerHTML = `
                                <div class="navbar-search-empty">
                                    <i class="bi bi-search"></i>
                                    No product found for <strong>"${escapeHtml(keyword)}"</strong>
                                </div>`;
                            showSearchResults();
                            return;
                        }

                        products.forEach(product => {
                            const result = document.createElement('a');
                            result.href = product.url;
                            result.className = 'navbar-search-result';
                            result.setAttribute('data-title', product.title);

                            let priceHtml = '';
                            if (product.price !== null && product.price !== undefined && product.price !== '') {
                                priceHtml = `<span class="navbar-search-result-price">$${escapeHtml(product.price)}</span>`;
                            }

                            const imgHtml = product.image
                                ? `<img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.title)}" class="rounded object-fit-cover" style="width:36px;height:36px;" onerror="this.onerror=null;this.src='{{ asset('img/logo.png') }}';">`
                                : `<div class="navbar-search-result-icon"><i class="bi bi-box-seam"></i></div>`;

                            result.innerHTML = `
                                ${imgHtml}
                                <div class="navbar-search-result-info">
                                    <span class="navbar-search-result-title">${highlightMatch(product.title, keyword)}</span>
                                    ${priceHtml}
                                </div>
                                <i class="bi bi-chevron-right text-muted ms-auto small"></i>
                            `;

                            searchResults.appendChild(result);
                        });

                        showSearchResults();
                    })
                    .catch(error => {
                        console.error(error);
                        searchResults.innerHTML = `
                            <div class="navbar-search-empty text-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                Unable to search products.
                            </div>`;
                        showSearchResults();
                    });
                }, 120);
            });

            /* =====================================================
               FOCUS & BLUR & KEYBOARD
            ===================================================== */
            searchInput.addEventListener('focus', function () {
                if (this.value.trim().length >= 1 && searchResults.innerHTML.trim() !== '') {
                    showSearchResults();
                }
            });

            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeSearchResults();
                }
            });

            document.addEventListener('click', function (event) {
                const wrapper = document.querySelector('.navbar-search-wrapper');
                if (wrapper && !wrapper.contains(event.target)) {
                    closeSearchResults();
                }
            });

            /* =====================================================
               FORM SUBMIT
            ===================================================== */
            searchForm.addEventListener('submit', function (event) {
                const keyword = searchInput.value.trim();
                if (keyword.length === 0) {
                    event.preventDefault();
                    searchInput.focus();
                    return;
                }
                closeSearchResults();
            });
        });
    </script>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('notifBadge');
            function checkUnreadNotifications() {
                fetch('{{ route("notifications.unread", [], false) }}')
                    .then(r => r.json())
                    .then(data => {
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    })
                    .catch(err => {});
            }
            checkUnreadNotifications();
            setInterval(checkUnreadNotifications, 45000);
        });
    </script>
    @endauth

    <!-- =========================================================
         LIGHT / DARK MODE CONTROLLER
    ========================================================= -->
    <script>
        (function () {
            function getPreferredTheme() {
                const stored = localStorage.getItem('nham_theme');
                if (stored === 'dark' || stored === 'light') {
                    return stored;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-theme', theme);
                const metaTheme = document.querySelector('meta[name="color-scheme"]');
                if (metaTheme) {
                    metaTheme.content = theme;
                }
                const isDark = theme === 'dark';
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                    btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
                });
                document.querySelectorAll('.theme-dropdown-label').forEach(el => {
                    el.textContent = isDark ? 'Light Mode' : 'Dark Mode';
                });
                document.querySelectorAll('.theme-dropdown-badge').forEach(el => {
                    el.textContent = isDark ? 'Dark' : 'Light';
                });
            }

            function toggleTheme(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                const current = document.documentElement.getAttribute('data-bs-theme') || getPreferredTheme();
                const newTheme = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('nham_theme', newTheme);
                applyTheme(newTheme);
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    btn.addEventListener('click', toggleTheme);
                });

                // Set initial label/badge text
                applyTheme(getPreferredTheme());

                // React to OS changes if not explicitly overridden by user
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    if (!localStorage.getItem('nham_theme')) {
                        applyTheme(e.matches ? 'dark' : 'light');
                    }
                });
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>