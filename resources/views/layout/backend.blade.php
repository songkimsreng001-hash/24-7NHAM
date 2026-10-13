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

    <style>
        :root {
            --nham-950:#06351f;
            --nham-900:#074d2d;
            --nham-800:#086b3c;
            --nham-700:#0b7a45;
            --nham-600:#129455;
            --nham-500:#18a85e;
            --nham-100:#e5f7ed;
            --nham-50:#f1fbf5;
            --ink:#14231d;
            --muted:#7b8b84;
            --line:#e8efeb;
            --surface:#ffffff;
            --page:#f7faf8;
            --sidebar-w:248px;
            --sidebar-collapsed-w:72px;
            --topbar-h:76px;
            --radius:18px;
        }

        * { box-sizing:border-box; }
        html,body { margin:0; min-height:100%; }
        body {
            font-family:'Inter',sans-serif;
            background:var(--page);
            color:var(--ink);
            overflow-x:hidden;
        }
        a { text-decoration:none; }
        button,input { font-family:inherit; }

        /* Sidebar */
        #sidebar {
            position:fixed;
            inset:0 auto 0 0;
            width:var(--sidebar-w);
            background:#217a42;
            border-right:1px solid #1a5e33;
            z-index:1050;
            display:flex;
            flex-direction:column;
            transition:width .25s cubic-bezier(.4, 0, .2, 1), transform .25s cubic-bezier(.4, 0, .2, 1);
            overflow-x:hidden;
        }
        .brand {
            height:var(--topbar-h);
            padding:0 22px;
            display:flex;
            align-items:center;
            gap:11px;
            border-bottom:1px solid rgba(255,255,255,.15);
        }
        .brand-mark {
            width:38px;height:38px;border-radius:12px;
            display:grid;place-items:center;
            color:#fff;background:rgba(255,255,255,.15);
            box-shadow:0 8px 18px rgba(0,0,0,.20);
        }
        .brand-name { font-size:18px;font-weight:800;letter-spacing:-.4px;color:#ffffff; }
        .brand-name span { color:#ffffff; }

        .sidebar-scroll { flex:1;overflow:auto;padding:18px 13px 12px; }
        .sidebar-scroll::-webkit-scrollbar { width:4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background:rgba(255,255,255,.25);border-radius:10px; }

        .sidebar-label {
            padding:13px 13px 8px;
            font-size:10px;font-weight:800;
            text-transform:uppercase;letter-spacing:.12em;
            color:rgba(255,255,255,.65);
        }
        .nav-item-link {
            position:relative;
            width:100%;
            display:flex;align-items:center;gap:12px;
            min-height:44px;padding:10px 13px;
            margin:2px 0;
            border-radius:12px;
            color:rgba(255,255,255,.7);
            font-size:13px;font-weight:600;
            transition:.18s ease;
            white-space:nowrap;
            overflow:hidden;
        }
        .nav-item-link:hover { background:rgba(255,255,255,.15);color:#ffffff; }
        .nav-item-link.active {
            color:#ffffff;
            background:rgba(255,255,255,.2);
        }
        .nav-item-link.active::before {
            content:"";position:absolute;left:-13px;top:8px;bottom:8px;
            width:4px;border-radius:0 5px 5px 0;background:#ffffff;
        }
        .nav-icon {
            width:20px;height:20px;display:grid;place-items:center;flex:none;
        }
        .nav-icon svg { width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8; }
        .nav-icon i { font-size:18px;line-height:1; }
        .nav-badge {
            margin-left:auto;min-width:22px;height:20px;padding:0 6px;
            display:grid;place-items:center;border-radius:8px;
            background:rgba(255,255,255,.2);color:#ffffff;font-size:10px;font-weight:800;
        }
        .logout-form { margin:0; }
        .logout-button { border:0;background:transparent;text-align:left;cursor:pointer;color:rgba(255,255,255,.7); }

        /* =========================================================
           SIDEBAR GROUP ACCORDION / LISTDOWN STYLING
        ========================================================= */
        .nav-group {
            margin-bottom: 4px;
        }
        .nav-group-toggle {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding: 10px 13px;
            margin: 2px 0;
            border-radius: 12px;
            border: 0;
            background: transparent;
            color: rgba(255, 255, 255, .78);
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        .nav-group-toggle:hover {
            background: rgba(255, 255, 255, .15);
            color: #ffffff;
        }
        .nav-group-toggle.has-active-child {
            color: #ffffff;
            background: rgba(255, 255, 255, .18);
            font-weight: 800;
        }
        .nav-group-toggle.has-active-child::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 4px;
            border-radius: 0 4px 4px 0;
            background: #1fd361;
        }
        .nav-group-title {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-group-chevron {
            width: 16px;
            height: 16px;
            display: grid;
            place-items: center;
            font-size: 11px;
            color: rgba(255, 255, 255, .65);
            transition: transform .22s ease, color .22s ease;
            flex: none;
            margin-left: auto;
        }
        .nav-group-toggle[aria-expanded="true"] {
            color: #ffffff;
            background: rgba(255, 255, 255, .12);
        }
        .nav-group-toggle[aria-expanded="true"].has-active-child {
            background: rgba(255, 255, 255, .22);
        }
        .nav-group-toggle[aria-expanded="true"] .nav-group-chevron {
            transform: rotate(180deg) !important;
            color: #ffffff !important;
        }
        .nav-group-collapse {
            display: none !important;
            visibility: visible !important;
            overflow: hidden;
        }
        .nav-group-collapse.show {
            display: block !important;
            visibility: visible !important;
            animation: navSlideDown .18s cubic-bezier(0, 0, 0.2, 1);
        }
        @keyframes navSlideDown {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .nav-group-items {
            padding: 3px 0 6px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            position: relative;
        }
        .nav-group-items::before {
            content: "";
            position: absolute;
            left: 21px;
            top: 4px;
            bottom: 8px;
            width: 1.5px;
            background: rgba(255, 255, 255, .18);
        }
        .nav-sub-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 38px;
            padding: 8px 12px 8px 22px;
            border-radius: 10px;
            color: rgba(255, 255, 255, .75);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all .16s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        .nav-sub-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .14);
            transform: translateX(3px);
        }
        .nav-sub-item.active {
            color: #ffffff;
            background: rgba(255, 255, 255, .24);
            font-weight: 700;
        }
        .nav-sub-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .4);
            flex: none;
            transition: all .16s ease;
        }
        .nav-sub-item:hover .nav-sub-dot,
        .nav-sub-item.active .nav-sub-dot {
            background: #1fd361;
            transform: scale(1.3);
            box-shadow: 0 0 6px rgba(31, 211, 97, .8);
        }
        .nav-sub-text,
        .nav-sub-item .nav-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-promo {
            margin:12px 10px 15px;
            padding:16px;
            border-radius:17px;
            background:linear-gradient(145deg,#155933,#1a6d3d);
            color:#fff;overflow:hidden;position:relative;
        }
        .sidebar-promo::after {
            content:"";position:absolute;width:100px;height:100px;right:-35px;bottom:-45px;
            border:20px solid rgba(255,255,255,.08);border-radius:50%;
        }
        .promo-title { font-size:12px;font-weight:700;line-height:1.45; }
        .promo-text { margin-top:5px;color:rgba(255,255,255,.58);font-size:10px; }
        .promo-btn {
            display:inline-flex;margin-top:12px;padding:7px 12px;border-radius:9px;
            background:#1fd361;color:#fff;font-size:10px;font-weight:700;
        }

        .sidebar-user {
            padding:13px 18px;
            border-top:1px solid rgba(255,255,255,.15);
            display:flex;align-items:center;gap:10px;
        }
        .avatar {
            width:34px;height:34px;border-radius:50%;display:grid;place-items:center;
            flex:none;background:rgba(255,255,255,.15);color:#ffffff;font-weight:800;font-size:12px;
        }
        .sidebar-user-main { min-width:0;flex:1; }
        .sidebar-user-name { font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#ffffff; }
        .sidebar-user-role { font-size:10px;color:rgba(255,255,255,.65);margin-top:2px;text-transform:capitalize; }

        /* Topbar */
        #topbar {
            position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--topbar-h);
            background:rgba(255,255,255,.94);backdrop-filter:blur(12px);
            border-bottom:1px solid var(--line);z-index:1040;
            display:flex;align-items:center;gap:18px;padding:0 28px;
            transition:left .25s cubic-bezier(.4, 0, .2, 1);
        }
        .sidebar-toggle-btn, .mobile-toggle {
            display:inline-flex;align-items:center;justify-content:center;border:0;background:transparent;color:#51635b;
            width:38px;height:38px;border-radius:10px;cursor:pointer;flex-shrink:0;transition:background .18s ease, color .18s ease;
        }
        .sidebar-toggle-btn:hover, .mobile-toggle:hover { background:#f2f7f4;color:var(--nham-800); }

        .global-search {
            flex:1;max-width:440px;position:relative;
        }
        .global-search input {
            width:100%;height:42px;border:1px solid #e6ece9;background:#f8faf9;
            border-radius:12px;padding:0 72px 0 41px;outline:none;color:#263830;font-size:12px;
        }
        .global-search input:focus { background:#fff;border-color:#b8d9c7;box-shadow:0 0 0 4px rgba(24,168,94,.07); }
        .search-icon { position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a29b; }
        .search-icon svg { width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:2; }
        .search-key {
            position:absolute;right:10px;top:50%;transform:translateY(-50%);
            border:1px solid #e3eae6;background:#fff;border-radius:6px;
            padding:2px 6px;color:#a2ada8;font-size:9px;font-weight:700;
        }
        .global-search-results {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,.12);
            max-height: 380px;
            overflow-y: auto;
            z-index: 1060;
        }
        .global-search-results.show {
            display: block;
        }
        .global-search-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-bottom: 1px solid #f2f5f3;
            color: var(--ink);
            text-decoration: none;
            transition: background .15s ease;
        }
        .global-search-item:last-child {
            border-bottom: none;
        }
        .global-search-item:hover {
            background: #f1fbf5;
        }
        .global-search-item-img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f0f4f2;
        }
        .global-search-item-info {
            flex: 1;
            min-width: 0;
        }
        .global-search-item-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .global-search-item-price {
            font-size: 10px;
            font-weight: 700;
            color: var(--nham-700);
        }
        .global-search-item-action {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
        }
        .global-search-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 3px 6px;
            border-radius: 6px;
            background: #eef8f2;
            color: var(--nham-700);
            text-decoration: none;
            display: inline-block;
        }
        .global-search-badge:hover {
            background: var(--nham-700);
            color: #ffffff;
        }
        .top-actions { margin-left:auto;display:flex;align-items:center;gap:8px; }
        .top-icon {
            width:38px;height:38px;border:0;background:transparent;border-radius:10px;
            display:grid;place-items:center;color:#64746d;position:relative;cursor:pointer;
        }
        .top-icon:hover { background:#f4f8f5;color:var(--nham-800); }
        .top-icon svg { width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8; }
        .notification-dot {
            position:absolute;top:8px;right:8px;width:6px;height:6px;border-radius:50%;
            background:#e45b45;border:2px solid #fff;box-sizing:content-box;
        }
        .profile {
            display:flex;align-items:center;gap:9px;margin-left:5px;padding-left:12px;
            border-left:1px solid var(--line);cursor:pointer;
        }
        .profile-text { line-height:1.2; }
        .profile-name { font-size:12px;font-weight:700;color:#263830; }
        .profile-email { font-size:9px;color:#9aa59f;margin-top:3px; }
        .profile .avatar { width:36px;height:36px; }

        /* Main */
        #main-wrapper {
            margin-left:var(--sidebar-w);
            padding:calc(var(--topbar-h) + 25px) 28px 34px;
            min-height:100vh;
            transition:margin-left .25s cubic-bezier(.4, 0, .2, 1);
        }
        .content-container { max-width:1600px;margin:0 auto; }

        .page-header {
            display:flex;align-items:flex-start;justify-content:space-between;gap:15px;
            margin-bottom:22px;
        }
        .page-header h1 { margin:0;font-size:27px;letter-spacing:-.8px;font-weight:800;color:#172a21; }
        .page-header p { margin:5px 0 0;font-size:11px;color:#8a9891; }
        .header-actions { display:flex;gap:9px; }
        .btn-primary-green,.btn-outline-green {
            display:inline-flex;align-items:center;justify-content:center;gap:7px;
            min-height:40px;padding:0 16px;border-radius:11px;font-size:11px;font-weight:700;
            transition:.18s ease;cursor:pointer;
        }
        .btn-primary-green { background:var(--nham-700);color:#fff;border:1px solid var(--nham-700); }
        .btn-primary-green:hover { background:var(--nham-800);color:#fff;transform:translateY(-1px); }
        .btn-outline-green { background:#fff;color:#53625b;border:1px solid #dbe5df; }
        .btn-outline-green:hover { border-color:#a8cdb8;color:var(--nham-800); }

        /* Shared dashboard components */
        .stat-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:15px; }
        .stat-card {
            min-height:150px;background:#fff;border:1px solid var(--line);border-radius:var(--radius);
            padding:20px;position:relative;overflow:hidden;box-shadow:0 2px 10px rgba(25,55,41,.025);
            transition:.2s ease;
        }
        .stat-card:hover { transform:translateY(-2px);box-shadow:0 10px 25px rgba(25,55,41,.07); }
        .stat-card.green { background:linear-gradient(145deg,#087340,#0c9654);border-color:#0b864b;box-shadow:0 10px 25px rgba(8,115,64,.15); }
        .stat-label { color:#85948c;font-size:11px;font-weight:700;margin-bottom:13px; }
        .stat-card.green .stat-label { color:rgba(255,255,255,.84); }
        .stat-value { font-size:31px;line-height:1;font-weight:800;letter-spacing:-1px;color:#15271f; }
        .stat-card.green .stat-value { color:#fff; }
        .stat-trend { margin-top:13px;font-size:10px;color:#96a29c; }
        .stat-card.green .stat-trend { color:rgba(255,255,255,.72); }
        .trend-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 8px;border-radius:7px;background:#f0f8f3;color:#4c7560;font-weight:700; }
        .stat-card.green .trend-badge { background:rgba(255,255,255,.12);color:#fff; }
        .stat-link,.stat-corner-link {
            position:absolute;right:17px;top:17px;width:31px;height:31px;border-radius:50%;
            display:grid;place-items:center;background:#f3f8f5;color:var(--nham-700);
        }
        .stat-card.green .stat-link { background:rgba(255,255,255,.18);color:#fff; }
        .stat-link svg,.stat-corner-link svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2; }

        .section-row { display:grid;gap:15px;margin-bottom:15px; }
        .section-row.three-col { grid-template-columns:1.25fr 1fr 1fr; }
        .section-row.two-col { grid-template-columns:1fr 1fr; }
        .section-row.left-heavy { grid-template-columns:1.5fr 1fr; }
        .panel {
            background:#fff;border:1px solid var(--line);border-radius:var(--radius);
            box-shadow:0 2px 10px rgba(25,55,41,.025);overflow:hidden;
        }
        .panel-header {
            display:flex;align-items:center;justify-content:space-between;gap:10px;
            padding:17px 19px 13px;border-bottom:1px solid #f0f4f2;
        }
        .panel-title { margin:0;color:#20332a;font-size:12px;font-weight:800; }
        .panel-badge { color:var(--nham-700);background:var(--nham-50);border:1px solid #d9eee2;padding:4px 8px;border-radius:7px;font-size:9px;font-weight:700; }
        .panel-body { padding:17px 19px; }
        .panel-action-btn {
            display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border:1px solid #e1e9e4;
            border-radius:8px;background:#fff;color:#68766f;font-size:9px;font-weight:700;
        }
        .panel-action-btn:hover { color:var(--nham-700);border-color:#b7d7c4; }

        .bar-chart-wrap { height:155px;display:flex;align-items:stretch;gap:12px;padding-top:8px; }
        .bar-col { flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:6px; }
        .bar-track { flex:1;width:100%;max-width:34px;display:flex;align-items:flex-end; }
        .bar {
            width:100%;min-height:8px;border-radius:9px 9px 5px 5px;
            background:#dbe9e1;position:relative;
        }
        .bar.filled { background:#a6d9bb; }
        .bar.active { background:linear-gradient(180deg,#0a8950,#0b6b3e);box-shadow:0 7px 16px rgba(9,123,69,.18); }
        .bar.striped { background:repeating-linear-gradient(135deg,#eef3f0 0,#eef3f0 3px,#dbe4df 3px,#dbe4df 5px); }
        .bar-day { font-size:9px;color:#9ba7a1;font-weight:700; }

        .mini-stats { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px; }
        .mini-stat { padding:10px;border-radius:11px;background:#f7faf8; }
        .mini-stat strong { display:block;font-size:13px;color:#24372e; }
        .mini-stat span { font-size:9px;color:#99a49e; }

        .reminder-card { background:linear-gradient(145deg,#f0faf4,#e6f7ed);border:1px solid #d4ecde;border-radius:14px;padding:16px; }
        .reminder-label { color:#278354;text-transform:uppercase;letter-spacing:.08em;font-size:8px;font-weight:800; }
        .reminder-title { margin-top:7px;font-size:14px;font-weight:800;color:#173126;line-height:1.35; }
        .reminder-time { margin:7px 0 13px;font-size:9px;color:#7f9188; }
        .btn-start { display:inline-flex;align-items:center;justify-content:center;padding:9px 13px;border-radius:9px;background:var(--nham-700);color:#fff;font-size:9px;font-weight:800; }
        .btn-start:hover { color:#fff;background:var(--nham-800); }

        .project-item { display:flex;align-items:center;gap:9px;padding:9px 0;border-bottom:1px solid #f2f5f3; }
        .project-item:last-child { border-bottom:0; }
        .project-dot { width:8px;height:8px;border-radius:50%;background:var(--nham-600);flex:none; }
        .project-name { flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:10px;font-weight:700;color:#32463d; }
        .project-due { font-size:9px;color:#9ba69f;white-space:nowrap; }

        .team-item { display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f1f4f2; }
        .team-item:last-child { border-bottom:0; }
        .team-avatar { width:35px;height:35px;border-radius:50%;display:grid;place-items:center;flex:none;background:#e1f2e8;color:#157044;font-size:11px;font-weight:800; }
        .team-info { flex:1;min-width:0; }
        .team-name { font-size:10px;font-weight:800;color:#263a31; }
        .team-task { margin-top:3px;font-size:9px;color:#8e9b95;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .team-status { font-size:8px;font-weight:800;padding:4px 7px;border-radius:7px;white-space:nowrap; }
        .status-done { color:#1c7c4c;background:#e9f7ef; }
        .status-prog { color:#9b7115;background:#fff7dc; }
        .status-pend { color:#bd4e42;background:#fff0ed; }
        .status-review { color:#3167a4;background:#edf4ff; }

        .donut-wrap { display:flex;align-items:center;gap:20px;padding:7px 0; }
        .donut {
            width:145px;height:145px;border-radius:50%;
            background:conic-gradient(#0a8950 var(--approved),#f1b94b 0 var(--paid),#e76c5d 0 var(--rejected),#edf2ef 0);
            position:relative;flex:none;display:grid;place-items:center;
        }
        .donut::after { content:"";width:98px;height:98px;border-radius:50%;background:#fff;position:absolute; }
        .donut-center { position:relative;z-index:1;text-align:center; }
        .donut-center strong { display:block;font-size:22px;color:#15271f;line-height:1; }
        .donut-center span { font-size:8px;color:#9aa69f; }
        .donut-legend { display:grid;gap:10px;font-size:9px;color:#6f7f77; }
        .legend-row { display:flex;align-items:center;gap:7px; }
        .legend-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }

        .time-tracker {
            margin-top:15px;padding:17px;border-radius:15px;color:#fff;
            background:radial-gradient(circle at 80% 10%,rgba(51,187,117,.22),transparent 35%),linear-gradient(145deg,#052b1a,#08713f);
            position:relative;overflow:hidden;text-align:left;
        }
        .time-tracker::after { content:"";position:absolute;inset:0;opacity:.12;background:repeating-radial-gradient(circle at 80% 20%,transparent 0 18px,#fff 19px 20px,transparent 21px 35px); }
        .tracker-label,.tracker-time,.tracker-controls { position:relative;z-index:1; }
        .tracker-label { font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.63);font-weight:700; }
        .tracker-time { margin:14px 0;font-size:25px;font-weight:800;letter-spacing:1px;font-variant-numeric:tabular-nums; }
        .tracker-controls { display:flex;gap:8px; }
        .tracker-btn { width:34px;height:34px;border:0;border-radius:50%;display:grid;place-items:center;cursor:pointer; }
        .tracker-btn.pause { background:rgba(255,255,255,.16);color:#fff; }
        .tracker-btn.stop { background:#e9584c;color:#fff; }
        .tracker-btn svg { width:13px;height:13px;fill:currentColor;stroke:currentColor; }

        .products-table { width:100%;border-collapse:collapse; }
        .products-table th { padding:0 12px 11px;text-align:left;border-bottom:1px solid #eef3f0;font-size:8px;color:#9aa59f;text-transform:uppercase;letter-spacing:.07em; }
        .products-table td { padding:12px;font-size:10px;color:#53635b;border-bottom:1px solid #f2f5f3;vertical-align:middle; }
        .products-table tr:last-child td { border-bottom:0; }
        .products-table tbody tr:hover td { background:#fbfdfc; }
        .product-name { font-size:10px;font-weight:800;color:#293d34; }
        .product-id { margin-top:3px;font-size:8px;color:#a1aaa5; }
        .cat-badge { display:inline-flex;padding:4px 7px;border-radius:6px;background:#eef8f2;color:#2c8155;font-size:8px;font-weight:800; }
        .price-cell { color:#21372b!important;font-weight:800!important; }
        .btn-view { display:inline-flex;padding:5px 8px;border:1px solid #dfe8e3;border-radius:7px;color:#66766d;font-size:8px;font-weight:700; }
        .btn-view:hover { border-color:#acd0ba;color:var(--nham-700); }

        .empty-state { padding:30px 20px;text-align:center;color:#94a19a;font-size:11px; }

        /* =========================================================
           SIDEBAR COLLAPSED / CLOSED STATE (DISPLAY ONLY ICONS)
        ========================================================= */
        html.sidebar-collapsed #sidebar,
        body.sidebar-collapsed #sidebar {
            width: var(--sidebar-collapsed-w);
        }

        html.sidebar-collapsed #topbar,
        body.sidebar-collapsed #topbar {
            left: var(--sidebar-collapsed-w);
        }

        html.sidebar-collapsed #main-wrapper,
        body.sidebar-collapsed #main-wrapper {
            margin-left: var(--sidebar-collapsed-w);
        }

        /* Hide text, badges, labels, promo in collapsed state */
        html.sidebar-collapsed .brand-name,
        body.sidebar-collapsed .brand-name,
        html.sidebar-collapsed .sidebar-search,
        body.sidebar-collapsed .sidebar-search,
        html.sidebar-collapsed .nav-text,
        body.sidebar-collapsed .nav-text,
        html.sidebar-collapsed .nav-badge,
        body.sidebar-collapsed .nav-badge,
        html.sidebar-collapsed .admin-theme-label,
        body.sidebar-collapsed .admin-theme-label,
        html.sidebar-collapsed .admin-theme-badge,
        body.sidebar-collapsed .admin-theme-badge,
        html.sidebar-collapsed .nav-group-title,
        body.sidebar-collapsed .nav-group-title,
        html.sidebar-collapsed .nav-group-chevron,
        body.sidebar-collapsed .nav-group-chevron,
        html.sidebar-collapsed .nav-group-collapse,
        body.sidebar-collapsed .nav-group-collapse,
        html.sidebar-collapsed .nav-sub-text,
        body.sidebar-collapsed .nav-sub-text,
        html.sidebar-collapsed .sidebar-promo,
        body.sidebar-collapsed .sidebar-promo,
        html.sidebar-collapsed .sidebar-user-main,
        body.sidebar-collapsed .sidebar-user-main {
            display: none !important;
        }

        /* Center brand logo in collapsed state */
        html.sidebar-collapsed .brand,
        body.sidebar-collapsed .brand {
            padding: 0;
            justify-content: center;
            gap: 0;
        }

        /* Subtle section divider when collapsed */
        html.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-label {
            height: 1px;
            padding: 0;
            margin: 10px 8px;
            background: rgba(255, 255, 255, .15);
            font-size: 0;
            overflow: hidden;
        }

        /* Center icons and compact links in collapsed state */
        html.sidebar-collapsed .sidebar-scroll,
        body.sidebar-collapsed .sidebar-scroll {
            padding: 14px 8px;
        }

        html.sidebar-collapsed .nav-item-link,
        body.sidebar-collapsed .nav-item-link,
        html.sidebar-collapsed .nav-group-toggle,
        body.sidebar-collapsed .nav-group-toggle {
            justify-content: center;
            padding: 10px 0;
            gap: 0;
            margin: 4px 0;
        }

        html.sidebar-collapsed .nav-icon,
        body.sidebar-collapsed .nav-icon,
        html.sidebar-collapsed .nav-group-toggle .nav-icon,
        body.sidebar-collapsed .nav-group-toggle .nav-icon {
            margin: 0 auto;
            width: 24px;
            height: 24px;
        }

        html.sidebar-collapsed .nav-icon svg,
        body.sidebar-collapsed .nav-icon svg,
        html.sidebar-collapsed .nav-icon i,
        body.sidebar-collapsed .nav-icon i {
            width: 19px;
            height: 19px;
            font-size: 19px;
        }

        html.sidebar-collapsed .nav-item-link.active::before,
        body.sidebar-collapsed .nav-item-link.active::before,
        html.sidebar-collapsed .nav-group-toggle.has-active-child::before,
        body.sidebar-collapsed .nav-group-toggle.has-active-child::before {
            left: 0;
            top: 6px;
            bottom: 6px;
            border-radius: 0 4px 4px 0;
        }

        /* Center user avatar in collapsed state */
        html.sidebar-collapsed .sidebar-user,
        body.sidebar-collapsed .sidebar-user {
            justify-content: center;
            padding: 12px 0;
            gap: 0;
        }

        @media (max-width:1200px) {
            .stat-grid { grid-template-columns:repeat(2,1fr); }
            .section-row.three-col { grid-template-columns:1fr 1fr; }
            .section-row.three-col > :first-child { grid-column:1 / -1; }
        }
        @media (max-width:991.98px) {
            .global-search { max-width:none; }
            .section-row.three-col,.section-row.two-col,.section-row.left-heavy { grid-template-columns:1fr; }
        }
        @media (max-width:767.98px) {
            :root {
                --sidebar-collapsed-w: 64px;
            }
            #topbar { padding: 0 14px; }
            #main-wrapper { padding-left: 14px; padding-right: 14px; }
            #sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(5, 28, 18, .45);
                z-index: 1045;
                backdrop-filter: blur(2px);
            }
            #sidebar-overlay.show {
                display: block;
            }
            html:not(.sidebar-collapsed) #sidebar,
            body:not(.sidebar-collapsed) #sidebar {
                box-shadow: 15px 0 40px rgba(0, 0, 0, .35);
                z-index: 1055;
            }
        }
        @media (max-width:680px) {
            .profile-text { display:none; }
            .profile { padding-left:5px;border-left:0; }
            .top-icon { display:none; }
            .global-search { max-width:none; }
            .global-search input { padding-right:14px; }
            .search-key { display:none; }
            .page-header { flex-direction:column; }
            .header-actions { width:100%; }
            .header-actions a { flex:1; }
            .stat-grid { grid-template-columns:1fr 1fr;gap:10px; }
            .stat-card { min-height:132px;padding:15px; }
            .stat-value { font-size:25px; }
            .stat-card:nth-child(4) { grid-column:1 / -1; }
            .donut-wrap { justify-content:center; }
            .donut-legend { display:none; }
        }
        @media (max-width:430px) {
            .stat-grid { grid-template-columns:1fr; }
            .stat-card:nth-child(4) { grid-column:auto; }
            .global-search { display:none; }
            .page-header h1 { font-size:24px; }
            .header-actions { flex-direction:column; }
            .header-actions a { width:100%; }
        }

        /* =========================================================
           ADMIN DARK MODE STYLING
        ========================================================= */
        .admin-theme-toggle-btn {
            cursor: pointer;
            transition: all .18s ease;
        }
        .admin-theme-toggle-btn:hover {
            transform: scale(1.04);
        }

        /* Auto display icons based on theme */
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
            --nham-950: #031c10;
            --nham-900: #052c1a;
            --nham-800: #074527;
            --nham-700: #0b6b3e;
            --nham-600: #108f51;
            --nham-500: #16a34a;
            --nham-100: #172a20;
            --nham-50: #122018;
            --ink: #e6f0eb;
            --muted: #8d9e95;
            --line: #22332a;
            --surface: #14201a;
            --page: #0c1410;
        }

        html[data-bs-theme="dark"] #sidebar,
        html[data-theme="dark"] #sidebar {
            background: #092614;
            border-right: 1px solid #13381e;
        }
        html[data-bs-theme="dark"] #topbar,
        html[data-theme="dark"] #topbar {
            background: rgba(18, 28, 23, 0.95);
            border-bottom: 1px solid #22332a;
        }
        html[data-bs-theme="dark"] .top-icon,
        html[data-theme="dark"] .top-icon,
        html[data-bs-theme="dark"] .sidebar-toggle-btn,
        html[data-theme="dark"] .sidebar-toggle-btn,
        html[data-bs-theme="dark"] .mobile-toggle,
        html[data-theme="dark"] .mobile-toggle {
            color: #9eb0a6;
        }
        html[data-bs-theme="dark"] .top-icon:hover,
        html[data-theme="dark"] .top-icon:hover,
        html[data-bs-theme="dark"] .sidebar-toggle-btn:hover,
        html[data-theme="dark"] .sidebar-toggle-btn:hover,
        html[data-bs-theme="dark"] .mobile-toggle:hover,
        html[data-theme="dark"] .mobile-toggle:hover {
            background: #182820;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .global-search input,
        html[data-theme="dark"] .global-search input {
            background: #101b15;
            border-color: #22332a;
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .global-search input:focus,
        html[data-theme="dark"] .global-search input:focus {
            background: #14221b;
            border-color: #18a85e;
        }
        html[data-bs-theme="dark"] .search-key,
        html[data-theme="dark"] .search-key {
            background: #14221b;
            border-color: #22332a;
            color: #8d9e95;
        }
        html[data-bs-theme="dark"] .profile,
        html[data-theme="dark"] .profile {
            border-left-color: #22332a;
        }
        html[data-bs-theme="dark"] .profile-name,
        html[data-theme="dark"] .profile-name {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .profile-email,
        html[data-theme="dark"] .profile-email {
            color: #8d9e95;
        }
        html[data-bs-theme="dark"] .panel,
        html[data-theme="dark"] .panel,
        html[data-bs-theme="dark"] .stat-card,
        html[data-theme="dark"] .stat-card {
            background: #14201a;
            border-color: #22332a;
            color: #e6f0eb;
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
        }
        html[data-bs-theme="dark"] .stat-card:not(.green) .stat-value,
        html[data-theme="dark"] .stat-card:not(.green) .stat-value {
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .stat-label,
        html[data-theme="dark"] .stat-label {
            color: #9eb0a6 !important;
        }
        html[data-bs-theme="dark"] .stat-trend,
        html[data-theme="dark"] .stat-trend {
            color: #7b8b84 !important;
        }
        html[data-bs-theme="dark"] .stat-corner-link,
        html[data-theme="dark"] .stat-corner-link {
            background: #1a2a21 !important;
            color: #4ade80 !important;
        }
        html[data-bs-theme="dark"] .panel-header,
        html[data-theme="dark"] .panel-header {
            border-bottom-color: #22332a;
        }
        html[data-bs-theme="dark"] .panel-title,
        html[data-theme="dark"] .panel-title {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .panel-action-btn,
        html[data-theme="dark"] .panel-action-btn {
            background: #101b15;
            border-color: #22332a;
            color: #9eb0a6;
        }
        html[data-bs-theme="dark"] .panel-action-btn:hover,
        html[data-theme="dark"] .panel-action-btn:hover {
            border-color: #18a85e;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .products-table th,
        html[data-theme="dark"] .products-table th {
            border-bottom-color: #22332a;
            color: #8d9e95;
        }
        html[data-bs-theme="dark"] .products-table td,
        html[data-theme="dark"] .products-table td {
            border-bottom-color: #1c2b23;
            color: #c4d4cc;
        }
        html[data-bs-theme="dark"] .products-table tbody tr:hover td,
        html[data-theme="dark"] .products-table tbody tr:hover td {
            background: #17261f;
        }
        html[data-bs-theme="dark"] .product-name,
        html[data-theme="dark"] .product-name {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .donut::after,
        html[data-theme="dark"] .donut::after {
            background: #14201a;
        }
        html[data-bs-theme="dark"] .donut-center strong,
        html[data-theme="dark"] .donut-center strong {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .mini-stat,
        html[data-theme="dark"] .mini-stat {
            background: #101b15;
        }
        html[data-bs-theme="dark"] .mini-stat strong,
        html[data-theme="dark"] .mini-stat strong {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .project-item,
        html[data-theme="dark"] .project-item,
        html[data-bs-theme="dark"] .team-item,
        html[data-theme="dark"] .team-item {
            border-bottom-color: #1c2b23;
        }
        html[data-bs-theme="dark"] .project-name,
        html[data-theme="dark"] .project-name,
        html[data-theme="dark"] .team-name,
        html[data-theme="dark"] .team-name {
            color: #e6f0eb;
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
        html[data-bs-theme="dark"] .page-header h1,
        html[data-theme="dark"] .page-header h1 {
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .page-header p,
        html[data-theme="dark"] .page-header p {
            color: #9eb0a6 !important;
        }
        html[data-bs-theme="dark"] .text-dark,
        html[data-theme="dark"] .text-dark {
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .text-secondary,
        html[data-theme="dark"] .text-secondary,
        html[data-bs-theme="dark"] .text-body-secondary,
        html[data-theme="dark"] .text-body-secondary {
            color: #9eb0a6 !important;
        }
        html[data-bs-theme="dark"] .text-muted,
        html[data-theme="dark"] .text-muted {
            color: #8d9e95 !important;
        }
        html[data-bs-theme="dark"] .text-body,
        html[data-theme="dark"] .text-body {
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] label,
        html[data-theme="dark"] label,
        html[data-bs-theme="dark"] .form-label,
        html[data-theme="dark"] .form-label {
            color: #c4d4cc !important;
        }
        html[data-bs-theme="dark"] .bg-light,
        html[data-theme="dark"] .bg-light,
        html[data-bs-theme="dark"] .bg-white,
        html[data-theme="dark"] .bg-white {
            background-color: #101b15 !important;
            color: #e6f0eb !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .badge.bg-light,
        html[data-theme="dark"] .badge.bg-light,
        html[data-bs-theme="dark"] .badge.bg-white,
        html[data-theme="dark"] .badge.bg-white {
            background-color: #192820 !important;
            color: #e6f0eb !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .badge.bg-secondary-subtle,
        html[data-theme="dark"] .badge.bg-secondary-subtle {
            background-color: #192820 !important;
            color: #9eb0a6 !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .btn-warning,
        html[data-theme="dark"] .btn-warning,
        html[data-bs-theme="dark"] .btn-warning.text-dark,
        html[data-theme="dark"] .btn-warning.text-dark,
        html[data-bs-theme="dark"] .badge.bg-warning,
        html[data-theme="dark"] .badge.bg-warning,
        html[data-bs-theme="dark"] .badge.bg-warning.text-dark,
        html[data-theme="dark"] .badge.bg-warning.text-dark {
            color: #0c1410 !important;
        }
        html[data-bs-theme="dark"] thead.table-light,
        html[data-theme="dark"] thead.table-light,
        html[data-bs-theme="dark"] .table-light,
        html[data-theme="dark"] .table-light,
        html[data-bs-theme="dark"] thead.table-light th,
        html[data-theme="dark"] thead.table-light th {
            background-color: #101b15 !important;
            color: #8d9e95 !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .table-hover tbody tr:hover td,
        html[data-theme="dark"] .table-hover tbody tr:hover td {
            background-color: #17261f !important;
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .input-group-text,
        html[data-theme="dark"] .input-group-text {
            background-color: #101b15 !important;
            border-color: #22332a !important;
            color: #8d9e95 !important;
        }
        html[data-bs-theme="dark"] .sales-total strong,
        html[data-theme="dark"] .sales-total strong,
        html[data-bs-theme="dark"] .status-summary-item strong,
        html[data-theme="dark"] .status-summary-item strong,
        html[data-bs-theme="dark"] .order-amount,
        html[data-theme="dark"] .order-amount {
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .quick-action-icon,
        html[data-theme="dark"] .quick-action-icon {
            background: #192820 !important;
            color: #4ade80 !important;
        }
        html[data-bs-theme="dark"] .border,
        html[data-theme="dark"] .border {
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .btn-outline-green,
        html[data-theme="dark"] .btn-outline-green {
            background: #121e17;
            border-color: #22332a;
            color: #c4d4cc;
        }
        html[data-bs-theme="dark"] .btn-outline-green:hover,
        html[data-theme="dark"] .btn-outline-green:hover {
            background: #182820;
            border-color: #18a85e;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] #sidebarSearch,
        html[data-theme="dark"] #sidebarSearch {
            background: rgba(255,255,255,.08) !important;
            border-color: rgba(255,255,255,.15) !important;
            color: #ffffff !important;
        }
        html[data-bs-theme="dark"] .nav-group-toggle:hover,
        html[data-theme="dark"] .nav-group-toggle:hover {
            background: rgba(255, 255, 255, .1);
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .nav-group-toggle[aria-expanded="true"],
        html[data-theme="dark"] .nav-group-toggle[aria-expanded="true"] {
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
        }
        html[data-bs-theme="dark"] .nav-group-toggle[aria-expanded="true"] .nav-group-chevron,
        html[data-theme="dark"] .nav-group-toggle[aria-expanded="true"] .nav-group-chevron {
            color: #4ade80 !important;
        }
        html[data-bs-theme="dark"] .nav-group-toggle.has-active-child,
        html[data-theme="dark"] .nav-group-toggle.has-active-child {
            background: rgba(22, 163, 74, .25);
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .nav-group-toggle.has-active-child::before,
        html[data-theme="dark"] .nav-group-toggle.has-active-child::before {
            background: #4ade80;
        }
        html[data-bs-theme="dark"] .nav-group-items::before,
        html[data-theme="dark"] .nav-group-items::before {
            background: rgba(74, 222, 128, .2);
        }
        html[data-bs-theme="dark"] .nav-sub-item:hover,
        html[data-theme="dark"] .nav-sub-item:hover {
            background: rgba(255, 255, 255, .08);
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .nav-sub-item.active,
        html[data-theme="dark"] .nav-sub-item.active {
            background: rgba(22, 163, 74, .3);
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .nav-sub-item.active .nav-sub-dot,
        html[data-theme="dark"] .nav-sub-item.active .nav-sub-dot {
            background: #4ade80;
            box-shadow: 0 0 6px rgba(74, 222, 128, .8);
        }
        html[data-bs-theme="dark"] .card,
        html[data-theme="dark"] .card {
            background-color: #14201a !important;
            border-color: #22332a !important;
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .card-header,
        html[data-theme="dark"] .card-header,
        html[data-bs-theme="dark"] .card-footer,
        html[data-theme="dark"] .card-footer {
            background-color: #101b15 !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .form-control,
        html[data-theme="dark"] .form-control,
        html[data-bs-theme="dark"] .form-select,
        html[data-theme="dark"] .form-select {
            background-color: #101b15 !important;
            border-color: #22332a !important;
            color: #e6f0eb !important;
        }
        html[data-bs-theme="dark"] .table,
        html[data-theme="dark"] .table {
            color: #e6f0eb !important;
            border-color: #22332a !important;
        }
        html[data-bs-theme="dark"] .table th,
        html[data-theme="dark"] .table th,
        html[data-bs-theme="dark"] .table td,
        html[data-theme="dark"] .table td {
            background-color: transparent !important;
            border-color: #22332a !important;
            color: #c4d4cc !important;
        }
        html[data-bs-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > *,
        html[data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        /* Global Search Dark Mode */
        html[data-bs-theme="dark"] .global-search-results,
        html[data-theme="dark"] .global-search-results {
            background: #14201a;
            border-color: #22332a;
            box-shadow: 0 10px 25px rgba(0,0,0,.45);
        }
        html[data-bs-theme="dark"] .global-search-item,
        html[data-theme="dark"] .global-search-item {
            border-bottom-color: #1c2b23;
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .global-search-item:hover,
        html[data-theme="dark"] .global-search-item:hover {
            background: #192a21;
        }
        html[data-bs-theme="dark"] .global-search-item-title,
        html[data-theme="dark"] .global-search-item-title {
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .global-search-item-price,
        html[data-theme="dark"] .global-search-item-price {
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .global-search-badge,
        html[data-theme="dark"] .global-search-badge {
            background: #1a3325;
            color: #4ade80;
        }
        html[data-bs-theme="dark"] .global-search input,
        html[data-theme="dark"] .global-search input {
            background: #101b15;
            border-color: #22332a;
            color: #e6f0eb;
        }
        html[data-bs-theme="dark"] .search-key,
        html[data-theme="dark"] .search-key {
            background: #192820;
            border-color: #22332a;
            color: #8d9e95;
        }

        /* =========================================================
           SWEETALERT2 LIGHT / DARK MODE FONT COLORS & THEME
        ========================================================= */
        .swal2-popup {
            font-family: inherit;
            border-radius: 16px !important;
        }

        /* SweetAlert Light Mode */
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-popup,
        html[data-bs-theme="light"] .swal2-popup,
        html[data-theme="light"] .swal2-popup {
            background: #ffffff !important;
            color: #1f2937 !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-title,
        html[data-bs-theme="light"] .swal2-title,
        html[data-theme="light"] .swal2-title {
            color: #111827 !important;
            font-weight: 700 !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-html-container,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-content,
        html[data-bs-theme="light"] .swal2-html-container,
        html[data-theme="light"] .swal2-html-container,
        html[data-bs-theme="light"] .swal2-content,
        html[data-theme="light"] .swal2-content {
            color: #4b5563 !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-footer,
        html[data-bs-theme="light"] .swal2-footer,
        html[data-theme="light"] .swal2-footer {
            color: #6b7280 !important;
            border-top: 1px solid #e5e7eb !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-footer a,
        html[data-bs-theme="light"] .swal2-footer a,
        html[data-theme="light"] .swal2-footer a {
            color: #16a34a !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-close,
        html[data-bs-theme="light"] .swal2-close,
        html[data-theme="light"] .swal2-close {
            color: #6b7280 !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-close:hover,
        html[data-bs-theme="light"] .swal2-close:hover,
        html[data-theme="light"] .swal2-close:hover {
            color: #111827 !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-input,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-textarea,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-select,
        html[data-bs-theme="light"] .swal2-input,
        html[data-theme="light"] .swal2-input,
        html[data-theme="light"] .swal2-textarea,
        html[data-theme="light"] .swal2-textarea,
        html[data-theme="light"] .swal2-select,
        html[data-theme="light"] .swal2-select {
            color: #111827 !important;
            background: #ffffff !important;
            border-color: #d1d5db !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-input::placeholder,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-textarea::placeholder,
        html[data-bs-theme="light"] .swal2-input::placeholder,
        html[data-theme="light"] .swal2-input::placeholder,
        html[data-theme="light"] .swal2-textarea::placeholder,
        html[data-theme="light"] .swal2-textarea::placeholder {
            color: #9ca3af !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-toast,
        html[data-bs-theme="light"] .swal2-toast,
        html[data-theme="light"] .swal2-toast {
            background: #ffffff !important;
            color: #1f2937 !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-toast .swal2-title,
        html[data-bs-theme="light"] .swal2-toast .swal2-title,
        html[data-theme="light"] .swal2-toast .swal2-title {
            color: #111827 !important;
        }
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .swal2-toast .swal2-html-container,
        html[data-bs-theme="light"] .swal2-toast .swal2-html-container,
        html[data-theme="light"] .swal2-toast .swal2-html-container {
            color: #4b5563 !important;
        }

        /* SweetAlert Dark Mode */
        html[data-bs-theme="dark"] .swal2-popup,
        html[data-theme="dark"] .swal2-popup {
            background: #14201a !important;
            color: #e6f0eb !important;
            border: 1px solid #22332a !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6) !important;
        }
        html[data-bs-theme="dark"] .swal2-title,
        html[data-theme="dark"] .swal2-title {
            color: #f8fafc !important;
            font-weight: 700 !important;
        }
        html[data-bs-theme="dark"] .swal2-html-container,
        html[data-theme="dark"] .swal2-html-container,
        html[data-bs-theme="dark"] .swal2-content,
        html[data-theme="dark"] .swal2-content {
            color: #cbd5e1 !important;
        }
        html[data-bs-theme="dark"] .swal2-footer,
        html[data-theme="dark"] .swal2-footer {
            color: #94a3b8 !important;
            border-top: 1px solid #22332a !important;
        }
        html[data-bs-theme="dark"] .swal2-footer a,
        html[data-theme="dark"] .swal2-footer a {
            color: #4ade80 !important;
        }
        html[data-bs-theme="dark"] .swal2-close,
        html[data-theme="dark"] .swal2-close {
            color: #94a3b8 !important;
        }
        html[data-bs-theme="dark"] .swal2-close:hover,
        html[data-theme="dark"] .swal2-close:hover {
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .swal2-input,
        html[data-theme="dark"] .swal2-input,
        html[data-theme="dark"] .swal2-textarea,
        html[data-theme="dark"] .swal2-textarea,
        html[data-theme="dark"] .swal2-select,
        html[data-theme="dark"] .swal2-select {
            color: #f8fafc !important;
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        html[data-bs-theme="dark"] .swal2-input::placeholder,
        html[data-theme="dark"] .swal2-input::placeholder,
        html[data-theme="dark"] .swal2-textarea::placeholder,
        html[data-theme="dark"] .swal2-textarea::placeholder {
            color: #64748b !important;
        }
        html[data-bs-theme="dark"] .swal2-validation-message,
        html[data-theme="dark"] .swal2-validation-message {
            background-color: #2b1317 !important;
            color: #fca5a5 !important;
            border: 1px solid #7f1d1d !important;
        }
        html[data-bs-theme="dark"] .swal2-cancel,
        html[data-theme="dark"] .swal2-cancel {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .swal2-icon.swal2-success [class^='swal2-success-circular-line'],
        html[data-theme="dark"] .swal2-icon.swal2-success [class^='swal2-success-circular-line'],
        html[data-bs-theme="dark"] .swal2-icon.swal2-success .swal2-success-fix,
        html[data-theme="dark"] .swal2-icon.swal2-success .swal2-success-fix {
            background-color: transparent !important;
        }
        html[data-bs-theme="dark"] .swal2-toast,
        html[data-theme="dark"] .swal2-toast {
            background: #14201a !important;
            color: #f8fafc !important;
            border: 1px solid #22332a !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }
        html[data-bs-theme="dark"] .swal2-toast .swal2-title,
        html[data-theme="dark"] .swal2-toast .swal2-title {
            color: #f8fafc !important;
        }
        html[data-bs-theme="dark"] .swal2-toast .swal2-html-container,
        html[data-theme="dark"] .swal2-toast .swal2-html-container {
            color: #cbd5e1 !important;
        }
        html[data-bs-theme="dark"] .swal2-toast .swal2-close,
        html[data-theme="dark"] .swal2-toast .swal2-close {
            color: #94a3b8 !important;
        }
    </style>

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
                <input id="sidebarSearch" type="search" placeholder="Search menu..." style="width:100%;height:38px;border:1px solid #e6ece9;background:#f7faf8;border-radius:10px;padding:0 10px 0 34px;outline:none;color:#31433a;font-size:11px;">
            </div>
        </div>
        <div class="sidebar-scroll" id="sidebarScroll">
            <div class="sidebar-label">Navigation</div>
            <nav class="sidebar-nav">
                {{-- Group: Dashboard --}}
                @php
                    $isDashboardActive = request()->routeIs('dashboard') || request()->routeIs('admin.analytics*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isDashboardActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-dashboard"
                            aria-expanded="{{ $isDashboardActive ? 'true' : 'false' }}" aria-controls="group-dashboard" title="Dashboard">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span>
                        <span class="nav-group-title">Dashboard</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isDashboardActive ? 'show' : '' }}" id="group-dashboard" data-initially-open="{{ $isDashboardActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('dashboard') }}" class="nav-sub-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="nav-sub-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}" title="Analytics & KPIs">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Analytics & KPIs</span>
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
                            aria-expanded="{{ $isCatalogActive ? 'true' : 'false' }}" aria-controls="group-catalog" title="Catalog & Menu">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="m5 7 7-3 7 3-7 3z"/><path d="M5 7v10l7 3 7-3V7M12 10v10"/></svg></span>
                        <span class="nav-group-title">Catalog & Menu</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isCatalogActive ? 'show' : '' }}" id="group-catalog" data-initially-open="{{ $isCatalogActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('product.index') }}" class="nav-sub-item {{ request()->routeIs('product.*') ? 'active' : '' }}" title="Products">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Products</span>
                            </a>
                            <a href="{{ route('category.list') }}" class="nav-sub-item {{ request()->routeIs('category.*') ? 'active' : '' }}" title="Categories">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Categories</span>
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
                            aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}" aria-controls="group-orders" title="Orders & Deliveries">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M6 3h12v18H6z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg></span>
                        <span class="nav-group-title">Orders & Delivery</span>
                        @if($sidebarOrderCount)
                            <span class="nav-badge me-1">{{ $sidebarOrderCount > 99 ? '99+' : $sidebarOrderCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isOrdersActive ? 'show' : '' }}" id="group-orders" data-initially-open="{{ $isOrdersActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.order') }}" class="nav-sub-item {{ request()->routeIs('admin.order*') ? 'active' : '' }}" title="All Orders">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">All Orders</span>
                                @if($sidebarOrderCount)
                                    <span class="nav-badge ms-auto">{{ $sidebarOrderCount > 99 ? '99+' : $sidebarOrderCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.orders.active') }}" class="nav-sub-item {{ request()->routeIs('admin.orders.active') ? 'active' : '' }}" title="Active Deliveries">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Active Deliveries</span>
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
                            aria-expanded="{{ $isMarketingActive ? 'true' : 'false' }}" aria-controls="group-marketing" title="Marketing & Promos">
                        <span class="nav-icon"><i class="bi bi-megaphone"></i></span>
                        <span class="nav-group-title">Marketing & Promos</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isMarketingActive ? 'show' : '' }}" id="group-marketing" data-initially-open="{{ $isMarketingActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.promos') }}" class="nav-sub-item {{ request()->routeIs('admin.promos*') ? 'active' : '' }}" title="Promo Codes">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Promo Codes</span>
                            </a>
                            <a href="{{ route('admin.broadcast') }}" class="nav-sub-item {{ request()->routeIs('admin.broadcast*') ? 'active' : '' }}" title="Broadcast Alerts">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Broadcast Alerts</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="sidebar-label">System & Account</div>
            <nav class="sidebar-nav">
                {{-- Group: Administration --}}
                @php
                    $isAdminActive = request()->routeIs('admin.users*') || request()->routeIs('admin.disputes*') || request()->routeIs('admin.audit*');
                @endphp
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle {{ $isAdminActive ? 'has-active-child' : '' }}"
                            data-bs-target="#group-admin"
                            aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}" aria-controls="group-admin" title="Administration">
                        <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
                        <span class="nav-group-title">Administration</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isAdminActive ? 'show' : '' }}" id="group-admin" data-initially-open="{{ $isAdminActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('admin.users') }}" class="nav-sub-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" title="Users & Couriers">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Users & Couriers</span>
                            </a>
                            <a href="{{ route('admin.disputes') }}" class="nav-sub-item {{ request()->routeIs('admin.disputes*') ? 'active' : '' }}" title="Disputes Queue">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Disputes Queue</span>
                            </a>
                            <a href="{{ route('admin.audit') }}" class="nav-sub-item {{ request()->routeIs('admin.audit*') ? 'active' : '' }}" title="Audit Trail">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Audit Trail</span>
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
                            aria-expanded="{{ $isAccountActive ? 'true' : 'false' }}" aria-controls="group-account" title="Account & Settings">
                        <span class="nav-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 20c.8-3.4 3.2-5 7-5s6.2 1.6 7 5"/></svg></span>
                        <span class="nav-group-title">Account & Settings</span>
                        <i class="bi bi-chevron-down nav-group-chevron"></i>
                    </button>
                    <div class="collapse nav-group-collapse {{ $isAccountActive ? 'show' : '' }}" id="group-account" data-initially-open="{{ $isAccountActive ? 'true' : 'false' }}">
                        <div class="nav-group-items">
                            <a href="{{ route('profile.edit', auth()->id()) }}" class="nav-sub-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profile">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Profile</span>
                            </a>
                            <a href="{{ url('/contact') }}" class="nav-sub-item" title="Help & Support">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text">Help & Support</span>
                            </a>

                            <!-- Theme Mode Toggle at Sidebar -->
                            <button type="button" class="nav-sub-item logout-button admin-theme-toggle-btn w-100 border-0 bg-transparent text-start" id="sidebarThemeToggle" title="Toggle Light / Dark Mode">
                                <span class="nav-sub-dot"></span>
                                <span class="nav-text admin-theme-label">Dark Mode</span>
                                <span class="nav-badge admin-theme-badge ms-auto" style="font-size: 9px; padding: 2px 7px;">Light</span>
                            </button>

                            <!-- Log Out Form -->
                            <form action="{{ route('logout') }}" method="POST" class="logout-form m-0">
                                @csrf
                                <button type="submit" class="nav-sub-item logout-button w-100 border-0 bg-transparent text-start" title="Log Out">
                                    <span class="nav-sub-dot"></span>
                                    <span class="nav-text">Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="sidebar-promo">
                <div class="promo-title">Manage your shop faster</div>
                <div class="promo-text">Add products and keep your orders organized from one place.</div>
                <a class="promo-btn" href="{{ route('product.create') }}">Add Product</a>
            </div>
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
            <input id="globalSearchInput" name="keyword" type="search" placeholder="Search products..." autocomplete="off">
            <span class="search-key">Ctrl K</span>
            <div id="globalSearchResults" class="global-search-results"></div>
        </form>

        <div class="top-actions">
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
