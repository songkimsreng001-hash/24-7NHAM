<!DOCTYPE html>
<html lang="en">

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           GLOBAL
        ========================================================= */
        :root {
            --green-dark: #166534;
            --green-base: #16a34a;
            --green-light: #22c55e;
            --green-pale: #f0fdf4;
            --green-border: #bbf7d0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: #1e293b;
        }

        /* =========================================================
           NAVBAR
        ========================================================= */
        .navbar-nham {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            padding: 0;
        }

        .navbar-nham .container {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 9px;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--green-dark) !important;
            text-decoration: none;
            white-space: nowrap;
        }

        .brand-logo-img {
            height: 42px;
            width: auto;
            max-width: 48px;
            object-fit: contain;
            display: inline-block;
            vertical-align: middle;
            border-radius: 8px;
            transition: transform .2s ease;
        }
        .navbar-brand-custom:hover .brand-logo-img {
            transform: scale(1.05);
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: var(--green-base);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .9rem;
            flex-shrink: 0;
        }

        .navbar-nham .nav-link {
            color: #475569 !important;
            font-size: .87rem;
            font-weight: 500;
            padding: 6px 12px !important;
            border-radius: 8px;
            transition: all .18s;
        }

        .navbar-nham .nav-link:hover,
        .navbar-nham .nav-link.active {
            color: var(--green-base) !important;
            background: var(--green-pale);
        }

        /* =========================================================
           DROPDOWN & MEGA MENU
        ========================================================= */
        .navbar-nham .dropdown-menu {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
            padding: 6px;
        }

        .navbar-nham .dropdown-item {
            border-radius: 8px;
            font-size: .86rem;
            padding: 7px 12px;
            color: #374151;
        }

        .navbar-nham .dropdown-item:hover {
            background: var(--green-pale);
            color: var(--green-base);
        }

        /* =========================================================
           MEGA DROPDOWN SPECIFIC
        ========================================================= */
        .navbar-nham .container {
            position: relative;
        }

        @media (min-width: 992px) {
            .nav-item.dropdown-mega {
                position: static !important;
            }

            .dropdown-mega .dropdown-mega-menu {
                position: absolute !important;
                top: 100% !important;
                left: 12px !important;
                right: 12px !important;
                width: calc(100% - 24px) !important;
                min-width: calc(100% - 24px) !important;
                max-width: 1320px;
                margin: 6px auto 0 !important;
                border-radius: 18px !important;
                border: 1px solid #e2e8f0 !important;
                background: #ffffff;
                box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(15, 23, 42, 0.05);
                padding: 24px 26px 18px !important;
                animation: megaDropdownFade .22s cubic-bezier(0.16, 1, 0.3, 1);
                z-index: 1060;
            }

            .nav-item.dropdown-mega:hover > .dropdown-mega-menu {
                display: block !important;
            }

            .nav-item.dropdown-mega:hover > .nav-link.dropdown-toggle {
                color: var(--green-base) !important;
                background: var(--green-pale);
            }
        }

        @media (max-width: 991.98px) {
            .dropdown-mega .dropdown-mega-menu {
                position: static !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 14px !important;
                padding: 16px !important;
                margin-top: 10px !important;
                background: #f8fafc;
                max-height: 75vh;
                overflow-y: auto;
            }

            .dropdown-mega .mega-menu-item {
                background: #ffffff;
                border: 1px solid #edf2f7;
                margin-bottom: 6px;
            }
        }

        @keyframes megaDropdownFade {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mega-column-title {
            font-size: .74rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #64748b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .mega-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border-radius: 10px;
            text-decoration: none;
            color: #1e293b;
            transition: all .16s ease;
        }

        .mega-menu-item:hover {
            background: var(--green-pale);
            transform: translateX(3px);
            color: #1e293b;
        }

        .mega-menu-item.active {
            background: var(--green-pale);
            border-left: 3px solid var(--green-base);
        }

        .mega-menu-item:hover .mega-item-name {
            color: var(--green-base);
        }

        .mega-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            transition: transform .18s;
        }

        .mega-menu-item:hover .mega-item-icon {
            transform: scale(1.08);
        }

        .mega-item-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .mega-item-name {
            font-size: .83rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.25;
        }

        .mega-item-desc {
            font-size: .71rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mega-promo-card {
            background: linear-gradient(145deg, #15803d 0%, #16a34a 100%);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(22, 163, 74, 0.22);
            position: relative;
            overflow: hidden;
        }

        .mega-promo-card::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 110px;
            height: 110px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .mega-menu-footer {
            border-top: 1px solid #f1f5f9;
        }

        /* =========================================================
           NAVBAR SEARCH
        ========================================================= */
        .navbar-search-wrapper {
            position: relative;
            width: 300px;
            z-index: 1050;
        }

        .navbar-search {
            width: 100%;
            height: 40px;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            transition: all .18s ease;
        }

        .navbar-search:focus-within {
            border-color: var(--green-base);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .10);
        }

        .navbar-search-icon {
            padding-left: 12px;
            color: #94a3b8;
            font-size: 15px;
            flex-shrink: 0;
        }

        .navbar-search-input {
            width: 100%;
            height: 100%;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 0 9px;
            font-size: .82rem;
            color: #1e293b;
            background: transparent;
        }

        .navbar-search-input::placeholder {
            color: #94a3b8;
        }

        .navbar-search-button {
            height: 100%;
            border: none;
            background: var(--green-base);
            color: #fff;
            padding: 0 13px;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s;
        }

        .navbar-search-button:hover {
            background: var(--green-dark);
        }

        /* =========================================================
           AUTO SEARCH DROPDOWN
        ========================================================= */
        .navbar-search-results {
            display: none;
            position: absolute;
            top: calc(100% + 7px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .12);
            overflow: hidden;
            max-height: 380px;
            overflow-y: auto;
            z-index: 9999;
        }

        .navbar-search-results.show {
            display: block;
        }

        .navbar-search-result {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            color: #1e293b;
            text-decoration: none;
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s ease;
            cursor: pointer;
        }

        .navbar-search-result:last-child {
            border-bottom: none;
        }

        .navbar-search-result:hover {
            background: var(--green-pale);
        }

        .navbar-search-result-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--green-pale);
            color: var(--green-base);
            border-radius: 8px;
        }

        .navbar-search-result-info {
            min-width: 0;
            flex: 1;
        }

        .navbar-search-result-title {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-search-result-price {
            display: block;
            margin-top: 2px;
            font-size: .74rem;
            font-weight: 600;
            color: var(--green-base);
        }

        .navbar-search-loading,
        .navbar-search-empty {
            padding: 18px;
            text-align: center;
            color: #64748b;
            font-size: .8rem;
        }

        .navbar-search-empty i {
            display: block;
            margin-bottom: 6px;
            font-size: 20px;
            color: #94a3b8;
        }

        /* =========================================================
           CART & AUTH
        ========================================================= */
        .cart-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 14px;
            background: #fff;
            color: #1e293b;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .18s;
            position: relative;
            white-space: nowrap;
        }

        .cart-btn:hover {
            border-color: var(--green-base);
            color: var(--green-base);
            background: var(--green-pale);
        }

        .cart-count {
            background: var(--green-base);
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-nav-login {
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 6px 14px;
            font-size: .84rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all .18s;
        }

        .btn-nav-login:hover {
            border-color: var(--green-base);
            color: var(--green-base);
        }

        .btn-nav-logout {
            border: 1.5px solid #ef4444;
            border-radius: 9px;
            padding: 6px 14px;
            font-size: .84rem;
            font-weight: 600;
            color: #ef4444;
            text-decoration: none;
            background: transparent;
            transition: all .18s;
        }

        .btn-nav-logout:hover {
            background: #ef4444;
            color: #fff;
            border-color: #dc2626;
        }

        .btn-nav-dash {
            background: var(--green-base);
            color: #fff !important;
            border-radius: 9px;
            padding: 6px 14px;
            font-size: .84rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .18s;
        }

        .btn-nav-dash:hover {
            background: var(--green-dark);
        }

        /* =========================================================
           HERO & CATEGORIES
        ========================================================= */
        .hero-strip {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-base) 100%);
            color: #fff;
            padding: 56px 0 48px;
            margin-bottom: 0;
        }

        /* .hero-strip h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero-strip p {
            font-size: 1rem;
            opacity: .85;
            margin-bottom: 22px;
        }

        .hero-search {
            display: flex;
            gap: 0;
            max-width: 480px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .15);
        }

        .hero-search input {
            flex: 1;
            border: none;
            outline: none;
            padding: 13px 18px;
            font-size: .9rem;
            color: #1e293b;
        }

        .hero-search button {
            background: var(--green-base);
            color: #fff;
            border: none;
            padding: 13px 20px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s;
        }

        .hero-search button:hover {
            background: var(--green-dark);
        } */

        .cat-pill-row {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 0;
        }

        .cat-pill-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .cat-pill-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-pill {
            white-space: nowrap;
            padding: 6px 16px;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all .18s;
            flex-shrink: 0;
        }

        .cat-pill:hover,
        .cat-pill.active {
            background: var(--green-base);
            color: #fff;
            border-color: var(--green-base);
        }

        /* =========================================================
           PRODUCT CARD
        ========================================================= */
        .product-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .09);
        }

        .product-card .card-img-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: #f8fafc;
        }

        .product-card .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s;
        }

        .product-card:hover .card-img-wrap img {
            transform: scale(1.04);
        }

        .product-card .card-body {
            padding: 16px 18px;
            flex: 1;
        }

        .product-card .product-name {
            font-size: .9rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .product-card .product-desc {
            font-size: .78rem;
            color: #64748b;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .product-card .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--green-base);
        }

        .product-card .card-footer-actions {
            padding: 12px 18px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 8px;
        }

        .btn-view-product {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 8px;
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            text-align: center;
            text-decoration: none;
            transition: all .18s;
        }

        .btn-view-product:hover {
            border-color: var(--green-base);
            color: var(--green-base);
        }

        .btn-add-cart {
            flex: 2;
            background: var(--green-base);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 8px;
            font-size: .8rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background .18s;
        }

        .btn-add-cart:hover {
            background: var(--green-dark);
            color: #fff;
        }

        /* =========================================================
           SECTION & EMPTY STATE
        ========================================================= */
        .section-header {
            padding: 32px 0 20px;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .section-header p {
            font-size: .85rem;
            color: #64748b;
            margin: 4px 0 0;
        }

        .empty-state {
            text-align: center;
            padding: 64px 24px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: .4;
        }

        .empty-state h5 {
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: .85rem;
        }

        /* =========================================================
           FOOTER & UTILS
        ========================================================= */
        .footer-nham {
            background: var(--green-dark);
            color: rgba(255, 255, 255, .75);
            padding: 40px 0 24px;
            margin-top: 64px;
        }

        .footer-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .footer-tagline {
            font-size: .8rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, .6);
            text-decoration: none;
            font-size: .82rem;
            transition: color .18s;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin-top: 28px;
            padding-top: 18px;
            font-size: .78rem;
            text-align: center;
        }

        .alert-success {
            background: var(--green-pale);
            border-color: var(--green-border);
            color: var(--green-dark);
        }

        .pagination .page-link {
            border-radius: 8px !important;
            border-color: #e2e8f0;
            color: #475569;
            font-size: .83rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--green-base);
            border-color: var(--green-base);
        }

        .pagination {
            gap: 4px;
        }

        /* =========================================================
           RESPONSIVE NAVBAR SEARCH
        ========================================================= */
        @media (max-width: 1199.98px) {
            .navbar-search-wrapper {
                width: 240px;
            }
        }

        @media (max-width: 991.98px) {
            .navbar-search-wrapper {
                width: 100%;
                margin-top: 12px;
                margin-bottom: 10px;
            }

            .navbar-search-results {
                width: 100%;
            }

            .navbar-search {
                height: 42px;
            }
        }

        @media (max-width: 575.98px) {
            .navbar-search-button {
                padding: 0 11px;
            }

            .navbar-search-input {
                font-size: .8rem;
            }

            .cart-btn {
                padding: 7px 11px;
            }
        }

        /* =========================================================
           DARK MODE STYLING (FRONTEND & NAVBAR)
        ========================================================= */
        .theme-toggle-btn {
            cursor: pointer;
            transition: transform .18s ease, background-color .18s ease;
            background: transparent;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.08);
        }

        /* Automatic icon display switching */
        html[data-bs-theme="dark"] .theme-icon-dark,
        html[data-theme="dark"] .theme-icon-dark {
            display: none !important;
        }
        html[data-bs-theme="dark"] .theme-icon-light,
        html[data-theme="dark"] .theme-icon-light {
            display: inline-block !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .theme-icon-light {
            display: none !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .theme-icon-dark {
            display: inline-block !important;
        }

        html[data-bs-theme="dark"],
        html[data-theme="dark"] {
            color-scheme: dark;
        }

        html[data-bs-theme="dark"] body,
        html[data-theme="dark"] body {
            background-color: #0b1320;
            color: #e2e8f0;
        }

        html[data-bs-theme="dark"] .navbar-nham,
        html[data-theme="dark"] .navbar-nham {
            background-color: #131c2e;
            border-bottom: 1px solid #223048;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }

        html[data-bs-theme="dark"] .navbar-brand-custom span,
        html[data-theme="dark"] .navbar-brand-custom span {
            color: #4ade80 !important;
        }

        html[data-bs-theme="dark"] .nav-link,
        html[data-theme="dark"] .nav-link {
            color: #cbd5e1 !important;
        }
        html[data-bs-theme="dark"] .nav-link:hover,
        html[data-theme="dark"] .nav-link:hover,
        html[data-bs-theme="dark"] .nav-link.active,
        html[data-theme="dark"] .nav-link.active {
            color: #4ade80 !important;
        }

        html[data-bs-theme="dark"] .navbar-search,
        html[data-theme="dark"] .navbar-search {
            background-color: #0b1320;
            border-color: #24344d;
        }
        html[data-bs-theme="dark"] .navbar-search-input,
        html[data-theme="dark"] .navbar-search-input {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .navbar-search-input::placeholder,
        html[data-theme="dark"] .navbar-search-input::placeholder {
            color: #64748b;
        }
        html[data-bs-theme="dark"] .navbar-search-results,
        html[data-theme="dark"] .navbar-search-results {
            background-color: #131c2e;
            border-color: #24344d;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        html[data-bs-theme="dark"] .navbar-search-item:hover,
        html[data-theme="dark"] .navbar-search-item:hover {
            background-color: #1e293b;
        }

        html[data-bs-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .dropdown-menu {
            background-color: #131c2e;
            border-color: #24344d;
            color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        html[data-bs-theme="dark"] .dropdown-item,
        html[data-theme="dark"] .dropdown-item {
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .dropdown-item:hover {
            background-color: #1e293b;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .dropdown-divider,
        html[data-theme="dark"] .dropdown-divider {
            border-color: #24344d;
        }

        html[data-bs-theme="dark"] .dropdown-mega-menu,
        html[data-theme="dark"] .dropdown-mega-menu {
            background-color: #131c2e;
            border-color: #24344d;
        }
        html[data-bs-theme="dark"] .mega-column-title,
        html[data-theme="dark"] .mega-column-title {
            color: #94a3b8;
            border-color: #24344d;
        }
        html[data-bs-theme="dark"] .mega-menu-item,
        html[data-theme="dark"] .mega-menu-item {
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .mega-menu-item:hover,
        html[data-theme="dark"] .mega-menu-item:hover {
            background-color: #1a2538;
        }
        html[data-bs-theme="dark"] .mega-item-name,
        html[data-theme="dark"] .mega-item-name {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .mega-item-desc,
        html[data-theme="dark"] .mega-item-desc {
            color: #94a3b8;
        }
        html[data-bs-theme="dark"] .mega-menu-footer,
        html[data-theme="dark"] .mega-menu-footer {
            background-color: #0b1320;
            border-color: #24344d !important;
        }

        html[data-bs-theme="dark"] .card,
        html[data-theme="dark"] .card {
            background-color: #131c2e;
            border-color: #24344d;
            color: #e2e8f0;
        }
        html[data-bs-theme="dark"] .card-header,
        html[data-theme="dark"] .card-header,
        html[data-bs-theme="dark"] .card-footer,
        html[data-theme="dark"] .card-footer {
            background-color: #0e1624;
            border-color: #24344d;
        }

        html[data-bs-theme="dark"] h1,
        html[data-theme="dark"] h1,
        html[data-bs-theme="dark"] h2,
        html[data-theme="dark"] h2,
        html[data-bs-theme="dark"] h3,
        html[data-theme="dark"] h3,
        html[data-bs-theme="dark"] h4,
        html[data-theme="dark"] h4,
        html[data-bs-theme="dark"] h5,
        html[data-theme="dark"] h5,
        html[data-bs-theme="dark"] h6,
        html[data-theme="dark"] h6,
        html[data-bs-theme="dark"] .h1,
        html[data-theme="dark"] .h1,
        html[data-bs-theme="dark"] .h2,
        html[data-theme="dark"] .h2,
        html[data-bs-theme="dark"] .h3,
        html[data-theme="dark"] .h3,
        html[data-bs-theme="dark"] .h4,
        html[data-theme="dark"] .h4,
        html[data-bs-theme="dark"] .h5,
        html[data-theme="dark"] .h5,
        html[data-bs-theme="dark"] .h6,
        html[data-theme="dark"] .h6 {
            color: #f8fafc;
        }

        html[data-bs-theme="dark"] .product-card,
        html[data-theme="dark"] .product-card {
            background-color: #131c2e;
            border-color: #24344d;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }
        html[data-bs-theme="dark"] .product-card:hover,
        html[data-theme="dark"] .product-card:hover {
            border-color: #16a34a;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        html[data-bs-theme="dark"] .product-card .product-name,
        html[data-theme="dark"] .product-card .product-name,
        html[data-bs-theme="dark"] .product-card-title,
        html[data-theme="dark"] .product-card-title,
        html[data-bs-theme="dark"] .product-title,
        html[data-theme="dark"] .product-title {
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .product-card .product-desc,
        html[data-theme="dark"] .product-card .product-desc,
        html[data-bs-theme="dark"] .product-desc,
        html[data-theme="dark"] .product-desc,
        html[data-bs-theme="dark"] .product-card-cat,
        html[data-theme="dark"] .product-card-cat {
            color: #94a3b8 !important;
        }
        html[data-bs-theme="dark"] .product-card .product-price,
        html[data-theme="dark"] .product-card .product-price,
        html[data-bs-theme="dark"] .product-card-price,
        html[data-theme="dark"] .product-card-price,
        html[data-bs-theme="dark"] .product-price,
        html[data-theme="dark"] .product-price {
            color: #4ade80 !important;
        }
        html[data-bs-theme="dark"] .product-card .card-footer-actions,
        html[data-theme="dark"] .product-card .card-footer-actions {
            border-top-color: #24344d;
        }
        html[data-bs-theme="dark"] .btn-view-product,
        html[data-theme="dark"] .btn-view-product {
            background-color: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .btn-view-product:hover,
        html[data-theme="dark"] .btn-view-product:hover {
            background-color: #24344d;
            border-color: #16a34a;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .card-img-wrap,
        html[data-theme="dark"] .card-img-wrap {
            background-color: #0e1624;
        }

        /* Category pills dark mode */
        html[data-bs-theme="dark"] .cat-pill-row,
        html[data-theme="dark"] .cat-pill-row {
            background-color: #0b1320;
            border-bottom-color: #24344d;
        }
        html[data-bs-theme="dark"] .cat-pill,
        html[data-theme="dark"] .cat-pill {
            background-color: #131c2e;
            border-color: #24344d;
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .cat-pill:hover,
        html[data-theme="dark"] .cat-pill:hover,
        html[data-bs-theme="dark"] .cat-pill.active,
        html[data-theme="dark"] .cat-pill.active {
            background-color: #16a34a;
            border-color: #16a34a;
            color: #ffffff;
        }

        html[data-bs-theme="dark"] .section-header h2,
        html[data-theme="dark"] .section-header h2 {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .section-header p,
        html[data-theme="dark"] .section-header p {
            color: #94a3b8;
        }

        html[data-bs-theme="dark"] .empty-state,
        html[data-theme="dark"] .empty-state {
            color: #94a3b8;
        }
        html[data-bs-theme="dark"] .empty-state h5,
        html[data-theme="dark"] .empty-state h5 {
            color: #f8fafc;
        }

        html[data-bs-theme="dark"] .footer-nham,
        html[data-theme="dark"] .footer-nham {
            background-color: #070d18;
            border-color: #1a2538;
            color: #94a3b8;
        }
        html[data-bs-theme="dark"] .footer-col h5,
        html[data-theme="dark"] .footer-col h5,
        html[data-bs-theme="dark"] .footer-col h6,
        html[data-theme="dark"] .footer-col h6 {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .footer-bottom,
        html[data-theme="dark"] .footer-bottom {
            border-color: #1a2538;
            color: #64748b;
        }

        html[data-bs-theme="dark"] .bg-light,
        html[data-theme="dark"] .bg-light,
        html[data-bs-theme="dark"] .bg-white,
        html[data-theme="dark"] .bg-white {
            background-color: #131c2e !important;
            color: #e2e8f0;
        }
        html[data-bs-theme="dark"] .text-dark,
        html[data-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .text-secondary,
        html[data-theme="dark"] .text-secondary,
        html[data-bs-theme="dark"] .text-body-secondary,
        html[data-theme="dark"] .text-body-secondary {
            color: #94a3b8 !important;
        }
        html[data-bs-theme="dark"] .text-muted,
        html[data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        html[data-bs-theme="dark"] .text-body,
        html[data-theme="dark"] .text-body {
            color: #e2e8f0 !important;
        }
        html[data-bs-theme="dark"] .text-body-emphasis,
        html[data-theme="dark"] .text-body-emphasis {
            color: #ffffff !important;
        }
        html[data-bs-theme="dark"] label,
        html[data-theme="dark"] label,
        html[data-bs-theme="dark"] .form-label,
        html[data-theme="dark"] .form-label {
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .input-group-text,
        html[data-theme="dark"] .input-group-text {
            background-color: #1a2538;
            border-color: #24344d;
            color: #cbd5e1;
        }

        /* Warning badges and buttons keep dark text for high contrast */
        html[data-bs-theme="dark"] .btn-warning,
        html[data-theme="dark"] .btn-warning,
        html[data-bs-theme="dark"] .btn-warning.text-dark,
        html[data-theme="dark"] .btn-warning.text-dark,
        html[data-bs-theme="dark"] .badge.bg-warning,
        html[data-theme="dark"] .badge.bg-warning,
        html[data-bs-theme="dark"] .badge.bg-warning.text-dark,
        html[data-theme="dark"] .badge.bg-warning.text-dark {
            color: #0f172a !important;
        }

        html[data-bs-theme="dark"] .badge.bg-light,
        html[data-theme="dark"] .badge.bg-light,
        html[data-bs-theme="dark"] .badge.bg-white,
        html[data-theme="dark"] .badge.bg-white {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        html[data-bs-theme="dark"] .badge.bg-secondary-subtle,
        html[data-theme="dark"] .badge.bg-secondary-subtle {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
        }

        /* Steppers, orders, timeline */
        html[data-bs-theme="dark"] .step-title,
        html[data-theme="dark"] .step-title {
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .step-desc,
        html[data-theme="dark"] .step-desc {
            color: #94a3b8 !important;
        }
        html[data-bs-theme="dark"] .stepper-step:not(.completed):not(.active) .step-title,
        html[data-theme="dark"] .stepper-step:not(.completed):not(.active) .step-title {
            color: #64748b !important;
        }
        html[data-bs-theme="dark"] .breadcrumb-item,
        html[data-theme="dark"] .breadcrumb-item {
            color: #94a3b8;
        }
        html[data-bs-theme="dark"] .breadcrumb-item.active,
        html[data-theme="dark"] .breadcrumb-item.active {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] code,
        html[data-theme="dark"] code {
            color: #4ade80;
        }

        html[data-bs-theme="dark"] .border,
        html[data-theme="dark"] .border {
            border-color: #24344d !important;
        }
        html[data-bs-theme="dark"] .btn-outline-secondary,
        html[data-theme="dark"] .btn-outline-secondary {
            border-color: #334155;
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .btn-outline-secondary:hover,
        html[data-theme="dark"] .btn-outline-secondary:hover {
            background-color: #1e293b;
            color: #fff;
        }
        html[data-bs-theme="dark"] .form-control,
        html[data-theme="dark"] .form-control,
        html[data-bs-theme="dark"] .form-select,
        html[data-theme="dark"] .form-select {
            background-color: #0b1320;
            border-color: #24344d;
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-control:focus,
        html[data-bs-theme="dark"] .form-select:focus,
        html[data-theme="dark"] .form-select:focus {
            background-color: #0e1726;
            border-color: #16a34a;
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .modal-content,
        html[data-theme="dark"] .modal-content {
            background-color: #131c2e;
            border-color: #24344d;
            color: #e2e8f0;
        }
        html[data-bs-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-header,
        html[data-bs-theme="dark"] .modal-footer,
        html[data-theme="dark"] .modal-footer {
            background-color: #0e1624 !important;
            border-color: #24344d;
        }
        html[data-bs-theme="dark"] .list-group-item,
        html[data-theme="dark"] .list-group-item {
            background-color: #131c2e;
            border-color: #24344d;
            color: #e2e8f0;
        }
        html[data-bs-theme="dark"] .table,
        html[data-theme="dark"] .table {
            color: #e2e8f0;
            border-color: #24344d;
        }
        html[data-bs-theme="dark"] .table td,
        html[data-theme="dark"] .table td,
        html[data-bs-theme="dark"] .table th,
        html[data-theme="dark"] .table th {
            border-color: #24344d;
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] thead.table-light,
        html[data-theme="dark"] thead.table-light,
        html[data-bs-theme="dark"] .table-light,
        html[data-theme="dark"] .table-light,
        html[data-bs-theme="dark"] thead.table-light th,
        html[data-theme="dark"] thead.table-light th {
            background-color: #0e1624 !important;
            color: #94a3b8 !important;
            border-color: #24344d !important;
        }
        html[data-bs-theme="dark"] .table-hover tbody tr:hover td,
        html[data-theme="dark"] .table-hover tbody tr:hover td {
            background-color: #1a2538 !important;
            color: #f8fafc;
        }

        html[data-bs-theme="dark"] .pagination .page-link,
        html[data-theme="dark"] .pagination .page-link {
            background-color: #131c2e;
            border-color: #24344d;
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .pagination .page-item.active .page-link,
        html[data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: #16a34a;
            border-color: #16a34a;
            color: #ffffff;
        }

        /* Navbar Search Dark Mode */
        html[data-bs-theme="dark"] .navbar-search-results,
        html[data-theme="dark"] .navbar-search-results {
            background-color: #131c2e;
            border-color: #24344d;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .45);
        }
        html[data-bs-theme="dark"] .navbar-search-result,
        html[data-theme="dark"] .navbar-search-result {
            color: #f8fafc;
            border-bottom-color: #1e293b;
        }
        html[data-bs-theme="dark"] .navbar-search-result:hover,
        html[data-theme="dark"] .navbar-search-result:hover {
            background-color: #1a273e;
        }
        html[data-bs-theme="dark"] .navbar-search-result-title,
        html[data-theme="dark"] .navbar-search-result-title {
            color: #f8fafc;
        }
        html[data-bs-theme="dark"] .navbar-search-empty,
        html[data-theme="dark"] .navbar-search-empty,
        html[data-bs-theme="dark"] .navbar-search-loading,
        html[data-theme="dark"] .navbar-search-loading {
            color: #94a3b8;
        }
    </style>

    @stack('styles')
</head>

<body>

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

            <!-- MOBILE ACTIONS (THEME TOGGLE & BURGER) -->
            <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-1">
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
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('list') ? 'active' : '' }}" href="{{ url('/list') }}">
                            Products
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('frontend*') ? 'active' : '' }}"
                            href="{{ url('/frontend') }}">
                            Categories
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
                            <span>Shop</span>
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
                                    value="{{ request('keyword', $keyword ?? '') }}" placeholder="Search product title..."
                                    autocomplete="off">
                                <button type="submit" class="navbar-search-button">
                                    Search
                                </button>
                            </div>
                        </form>

                        <!-- AUTO SEARCH RESULTS DROPDOWN -->
                        <div id="navbarSearchResults" class="navbar-search-results"></div>
                    </div>

                    <!-- CART -->
                    <a href="{{ route('cart') }}" class="cart-btn">
                        <i class="bi bi-cart-fill"></i>
                        Cart
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
                            Login
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 rounded-3 py-1 px-2 px-md-3" 
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: .84rem; font-weight: 600;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover border" style="width: 24px; height: 24px;">
                                @else
                                    <i class="bi bi-person-circle text-success fs-6"></i>
                                @endif
                                <span class="d-none d-md-inline text-dark">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-2" style="min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle border object-fit-cover" style="width: 36px; height: 36px;">
                                        <div style="min-width: 0;">
                                            <div class="fw-bold text-dark small text-truncate">{{ auth()->user()->name }}</div>
                                            <div class="text-muted text-truncate" style="font-size: .72rem;">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </li>
                                @if(auth()->user()->isDriver())
                                <li>
                                    <a class="dropdown-item py-2 rounded-2 fw-semibold text-success bg-success-subtle mb-1" href="{{ route('driver.dashboard') }}">
                                        <i class="bi bi-bicycle me-2 text-success"></i> Courier Portal
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('customer.orders.index') }}">
                                        <i class="bi bi-clock-history me-2 text-primary"></i> My Order History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('order.track') }}">
                                        <i class="bi bi-truck me-2 text-success"></i> Track Shipments
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('favorites.index') }}">
                                        <i class="bi bi-heart me-2 text-danger"></i> Saved Favorites
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('notifications.index') }}">
                                        <i class="bi bi-bell me-2 text-secondary"></i> Notifications
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('support.chat') }}">
                                        <i class="bi bi-headset me-2 text-info"></i> Support & Chat
                                    </a>
                                </li>
                                @if(auth()->user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2 text-warning"></i> Admin Dashboard
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2 rounded-2" href="{{ route('profile.edit', auth()->id()) }}">
                                        <i class="bi bi-gear me-2 text-secondary"></i> Account Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 rounded-2 theme-toggle-btn d-flex align-items-center justify-content-between border-0 bg-transparent w-100">
                                        <span>
                                            <i class="bi bi-moon-stars-fill text-warning me-2 theme-icon-dark"></i>
                                            <i class="bi bi-sun-fill text-warning me-2 theme-icon-light"></i>
                                            <span class="theme-dropdown-label">Dark Mode</span>
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill small theme-dropdown-badge" style="font-size: 0.68rem;">Theme</span>
                                    </button>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 rounded-2 text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
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
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
    @if(session('success'))
    <script>
        Swal.fire({toast:true, position:'top-end', icon:'success', title:@json(session('success')), showConfirmButton:false, timer:2600, timerProgressBar:true});
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({toast:true, position:'top-end', icon:'error', title:@json(session('error')), showConfirmButton:false, timer:3200, timerProgressBar:true});
    </script>
    @endif
    @if($errors->any())
    <script>
        Swal.fire({icon:'error', title:'Please check the form', html:@json(implode('<br>', $errors->all()))});
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
                fetch('{{ route("notifications.unread") }}')
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
            setInterval(checkUnreadNotifications, 8000);
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