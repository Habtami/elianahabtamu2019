<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Contract Management System')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/android-chrome-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Ethiopic:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        epss: { DEFAULT: '#1C75BC', dark: '#1664a0', light: '#E8F4FD' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Shared design system -->
    <link rel="stylesheet" href="{{ asset('css/epss-theme.css') }}?v={{ filemtime(public_path('css/epss-theme.css')) }}">

    <style>
        :root {
            --epss-blue: #1C75BC;
            --epss-blue-light: #E8F4FD;
            --epss-blue-dark: #1664a0;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 74px;
        }

        .app-shell { min-height: 100vh; }

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            border-right: 1px solid rgba(148, 163, 184, 0.08);
            box-shadow: 4px 0 24px rgba(2, 6, 23, 0.25);
            z-index: 1040;
            transition: width 0.3s ease, background-color 0.25s ease, color 0.25s ease;
        }

        .sidebar-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.1rem 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(255, 255, 255, 0.02);
        }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 12px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        .sidebar-title { flex: 1; min-width: 0; }

        .sidebar-title h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--sidebar-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-title p {
            margin: 0;
            font-size: 0.64rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--sidebar-text) 55%, transparent);
        }

        .sidebar-toggle-btn {
            position: absolute;
            right: -13px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid var(--sidebar-bg);
            background: var(--sidebar-active);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.62rem;
            cursor: pointer;
            z-index: 2;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            transition: background 0.2s ease, filter 0.2s ease;
        }

        .sidebar-toggle-btn:hover { filter: brightness(1.12); }

        .sidebar-scroll {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem 0.65rem 1rem;
        }

        .sidebar-section-label {
            padding: 0.9rem 0.85rem 0.35rem;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--sidebar-text) 50%, transparent);
            white-space: nowrap;
        }

        .sidebar-menu-item { margin: 0.1rem 0; }

        .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.58rem 0.85rem;
            border-radius: 10px;
            color: color-mix(in srgb, var(--sidebar-text) 74%, transparent);
            text-decoration: none;
            font-size: 0.865rem;
            font-weight: 500;
            transition: background 0.18s ease, color 0.18s ease;
            position: relative;
        }

        .sidebar-menu-item a:hover {
            background: color-mix(in srgb, var(--sidebar-text) 10%, transparent);
            color: var(--sidebar-text);
        }

        .sidebar-menu-item a.active {
            background: color-mix(in srgb, var(--sidebar-active) 26%, transparent);
            color: var(--sidebar-text);
            box-shadow: inset 3px 0 0 var(--sidebar-active);
        }

        .sidebar-menu-item a i {
            width: 20px;
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .sidebar-badge {
            margin-left: auto;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.64rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-footer {
            padding: 0.8rem;
            border-top: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(0, 0, 0, 0.08);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 0.5rem;
            border-radius: 10px;
            background: rgba(148, 163, 184, 0.08);
            margin-bottom: 0.65rem;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--sidebar-active), color-mix(in srgb, var(--sidebar-active) 72%, #000));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.78rem;
        }

        .sidebar-user .meta { min-width: 0; }

        .sidebar-user .name {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--sidebar-text);
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user .role {
            display: block;
            font-size: 0.66rem;
            color: color-mix(in srgb, var(--sidebar-text) 55%, transparent);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Collapsed sidebar */
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .sidebar-section-label,
        .sidebar.collapsed .sidebar-menu-item a span:not(.sidebar-badge),
        .sidebar.collapsed .sidebar-user .meta { display: none; }

        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 1rem 0.4rem;
        }

        .sidebar.collapsed .sidebar-menu-item a {
            justify-content: center;
            padding: 0.65rem 0;
        }

        .sidebar.collapsed .sidebar-menu-item a i {
            width: auto;
            font-size: 1rem;
        }

        .sidebar.collapsed .sidebar-badge {
            position: absolute;
            top: 2px;
            right: 6px;
            min-width: 16px;
            height: 16px;
            margin: 0;
            font-size: 0.55rem;
            padding: 0 3px;
        }

        .sidebar.collapsed .sidebar-user {
            justify-content: center;
            padding: 0.45rem 0;
        }

        .sidebar.collapsed .theme-selector { display: none; }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.55);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease;
            z-index: 1035;
        }

        .sidebar-overlay.show { opacity: 1; visibility: visible; }

        /* ---------- Main column ---------- */
        .app-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 70px;
            padding: 0.65rem 1.5rem;
            background: var(--header-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--epss-border);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
            color: var(--header-text);
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            min-width: 0;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 0;
        }

        .topbar-logo {
            height: 42px;
            width: auto;
            object-fit: contain;
        }

        .topbar-eyebrow {
            display: block;
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--header-text) 55%, transparent);
            line-height: 1.4;
        }

        .topbar-title {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--header-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topbar-clock {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.25;
            margin-right: 0.55rem;
        }

        .topbar-clock .date { font-size: 0.74rem; font-weight: 600; color: color-mix(in srgb, var(--header-text) 78%, transparent); }
        .topbar-clock .time { font-size: 0.68rem; color: color-mix(in srgb, var(--header-text) 55%, transparent); }

        .theme-selector {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            margin-top: 0.7rem;
        }

        .theme-selector .form-label {
            margin: 0;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: color-mix(in srgb, var(--sidebar-text) 62%, transparent);
        }

        .theme-selector .form-select {
            width: 100%;
            padding: 0.35rem 1.8rem 0.35rem 0.65rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--sidebar-text);
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid color-mix(in srgb, var(--sidebar-text) 22%, transparent);
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.65rem center;
            background-size: 12px;
        }

        .theme-selector .form-select:focus {
            border-color: var(--sidebar-active);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--sidebar-active) 25%, transparent);
        }

        .theme-selector .form-select option {
            color: #1e293b;
            background: #ffffff;
        }

        body.theme-light .theme-selector .form-select {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%231e293b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E");
        }

        .topbar-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 11px;
            border: 1px solid var(--epss-border);
            background: #f8fafc;
            color: color-mix(in srgb, var(--header-text) 78%, transparent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s ease;
        }

        .topbar-icon-btn:hover {
            background: color-mix(in srgb, var(--primary-color) 9%, #ffffff);
            border-color: color-mix(in srgb, var(--primary-color) 35%, #ffffff);
            color: var(--primary-color);
        }

        .topbar-icon-btn.logout:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .dot-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #ef4444;
            border: 2px solid #ffffff;
            color: #ffffff;
            font-size: 0.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.32rem 0.65rem 0.32rem 0.32rem;
            border-radius: 12px;
            border: 1px solid var(--epss-border);
            background: var(--header-bg);
            cursor: pointer;
            transition: background 0.18s ease;
        }

        .profile-btn:hover { background: #f8fafc; }

        .profile-avatar {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--epss-primary), var(--sidebar-active));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.76rem;
        }

        .profile-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.15;
            text-align: left;
        }

        .profile-meta .name { font-size: 0.8rem; font-weight: 700; color: var(--header-text); }
        .profile-meta .role { font-size: 0.66rem; color: color-mix(in srgb, var(--header-text) 55%, transparent); }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 11px;
            border: 1px solid var(--epss-border);
            background: #ffffff;
            color: #334155;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .main-content { flex: 1 1 auto; width: 100%; }

        .page-body {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 0.5rem;
        }

        .page-footer {
            width: 100%;
            padding: 0.9rem 1.5rem;
            border-top: 1px solid var(--epss-border);
            background: var(--header-bg);
            color: color-mix(in srgb, var(--header-text) 62%, transparent);
            font-size: 0.8rem;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .sidebar-floating-tooltip {
            position: fixed;
            pointer-events: none;
            background: #0f172a;
            color: #ffffff;
            padding: 7px 11px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(2, 6, 23, 0.3);
            white-space: nowrap;
            font-size: 0.75rem;
            font-weight: 600;
            transform: translateY(-50%) translateX(-6px);
            transition: opacity 0.16s ease, transform 0.16s ease;
            z-index: 1600;
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-floating-tooltip.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(0);
        }

        /* ---------- Body dark / light toggle (LawPro-style) ---------- */
        body.dark-theme {
            --epss-bg: #0b1220;
            --epss-surface: #111a2b;
            --epss-text: #cbd5e1;
            --epss-heading: #e2e8f0;
            --epss-muted: #94a3b8;
            --epss-border: #1e293b;
            background-color: #0b1220;
            color: #cbd5e1;
        }
        body.dark-theme h1, body.dark-theme h2, body.dark-theme h3,
        body.dark-theme h4, body.dark-theme h5, body.dark-theme h6 { color: #e2e8f0; }

        body.dark-theme .topbar,
        body.dark-theme .page-footer { background: #111a2b; border-color: #1e293b; color: #cbd5e1; }
        body.dark-theme .topbar-title { color: #e2e8f0; }
        body.dark-theme .topbar-eyebrow { color: #94a3b8; }
        body.dark-theme .topbar-clock .date { color: #cbd5e1; }
        body.dark-theme .topbar-clock .time { color: #94a3b8; }
        body.dark-theme .topbar-icon-btn,
        body.dark-theme .profile-btn,
        body.dark-theme .mobile-menu-btn { background: #16233a; border-color: #1e293b; color: #94a3b8; }
        body.dark-theme .profile-meta .name { color: #e2e8f0; }
        body.dark-theme .profile-meta .role { color: #94a3b8; }

        body.dark-theme .card { background: #111a2b; border-color: #1e293b; }
        body.dark-theme .kpi-card { background: #111a2b; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5); }
        body.dark-theme .kpi-card:hover { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6); }
        body.dark-theme .kpi-title { color: #94a3b8; }
        body.dark-theme .kpi-subtitle { color: #64748b; }
        body.dark-theme .card > .card-header { border-color: #1e293b; color: #e2e8f0; }

        body.dark-theme .table { color: #cbd5e1; }
        body.dark-theme .table > thead th { background: #16233a !important; color: #94a3b8; border-color: #1e293b !important; }
        body.dark-theme .table > tbody td { border-color: #1e293b; }
        body.dark-theme .table-hover > tbody > tr:hover > * { background-color: #16233a; }

        body.dark-theme .form-control,
        body.dark-theme .form-select { background-color: #0f1a2c; border-color: #1e293b; color: #e2e8f0; }
        body.dark-theme .form-control::placeholder { color: #64748b; }
        body.dark-theme .form-label { color: #cbd5e1; }

        body.dark-theme .dropdown-menu { background: #111a2b; border-color: #1e293b; }
        body.dark-theme .dropdown-item { color: #cbd5e1; }
        body.dark-theme .dropdown-item:hover { background: #16233a; color: #ffffff; }
        body.dark-theme .dropdown-divider { border-color: #1e293b; }

        body.dark-theme .modal-content { background: #111a2b; color: #cbd5e1; }
        body.dark-theme .modal-header,
        body.dark-theme .modal-footer { border-color: #1e293b; }
        body.dark-theme .btn-close { filter: invert(1) grayscale(100%); }

        body.dark-theme .pagination .page-link { background: #111a2b; border-color: #1e293b; color: #93c5fd; }
        body.dark-theme .pagination .page-item.active .page-link { background: var(--epss-primary); border-color: var(--epss-primary); color: #ffffff; }

        body.dark-theme .page-body .bg-white { background-color: #111a2b !important; }
        body.dark-theme .page-body .bg-gray-50,
        body.dark-theme .page-body .bg-slate-50,
        body.dark-theme .page-body .bg-gray-100,
        body.dark-theme .page-body .bg-slate-100 { background-color: #16233a !important; }
        body.dark-theme .page-body .text-gray-900,
        body.dark-theme .page-body .text-slate-900,
        body.dark-theme .page-body .text-gray-800,
        body.dark-theme .page-body .text-slate-800 { color: #e2e8f0 !important; }
        body.dark-theme .page-body .text-gray-700,
        body.dark-theme .page-body .text-slate-700,
        body.dark-theme .page-body .text-gray-600,
        body.dark-theme .page-body .text-slate-600 { color: #cbd5e1 !important; }
        body.dark-theme .page-body .text-gray-500,
        body.dark-theme .page-body .text-slate-500,
        body.dark-theme .page-body .text-gray-400 { color: #94a3b8 !important; }
        body.dark-theme .page-body .border-gray-100,
        body.dark-theme .page-body .border-gray-200,
        body.dark-theme .page-body .border-gray-300,
        body.dark-theme .page-body .border-slate-200 { border-color: #1e293b !important; }
        body.dark-theme .page-body .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        body.dark-theme .page-body .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #1e293b !important; }

        body.dark-theme .page-container { background: #0b1220; }
        body.dark-theme .content-card,
        body.dark-theme .table-container,
        body.dark-theme .stats-card { background: #111a2b; border-color: #1e293b; }
        body.dark-theme .table-header { background: #16233a; border-color: #1e293b; }
        body.dark-theme .table-row { border-color: #1e293b; }
        body.dark-theme .table-row:hover { background: #16233a; }
        body.dark-theme .search-input,
        body.dark-theme .filter-select { background: #0f1a2c; border-color: #334155; color: #e2e8f0; }
        body.dark-theme .empty-state { background: #111a2b; border-color: #334155; }
        body.dark-theme .filter-tag { background: #1e3a5f; border-color: #1d4ed8; color: #dbeafe; }
        body.dark-theme .contract-progress { background: #1e293b; }
        body.dark-theme .activity-timeline::before { background: #1e293b; }
        body.dark-theme .activity-dot { border-color: #111a2b; }

        /* ---------- Responsive ---------- */
        @media (max-width: 991.98px) {
            .sidebar {
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show { transform: translateX(0); }
            .sidebar-toggle-btn { display: none; }

            .app-main { margin-left: 0; }
            .mobile-menu-btn { display: inline-flex; }
            .topbar { padding: 0.6rem 1rem; min-height: 64px; }
            .topbar-clock { display: none; }
            .profile-meta { display: none; }
            .topbar-eyebrow { display: none; }
            .topbar-title { font-size: 1rem; }
            .topbar-logo { height: 34px; }
            .page-body { padding: 1rem; }
        }

        @media (max-width: 575.98px) {
            .topbar-brand { gap: 0.55rem; }
            .topbar-logo { height: 30px; }
            .topbar-title { font-size: 0.92rem; }
            .topbar-right { gap: 0.35rem; }
            .topbar-icon-btn { width: 36px; height: 36px; }
        }
    </style>
    @yield('styles')
</head>
<body class="app-body">
    @if(auth()->check())
    @php
        $navUser = auth()->user();
        $canViewContracts = $navUser->canViewAllContracts();
        $alertCount = 0;
        if ($canViewContracts) {
            $alertCount = \App\Models\Contract::forUser($navUser)
                ->where(function ($query) {
                    $query->whereRaw('DATEDIFF(end_date, CURDATE()) BETWEEN 1 AND alert_days')
                          ->orWhereRaw('performance_guarantee_end_date IS NOT NULL AND DATEDIFF(performance_guarantee_end_date, CURDATE()) BETWEEN 1 AND alert_days_before_guarantee_end');
                })
                ->count();
        }
        $nameParts = collect(preg_split('/\s+/', trim($navUser->name)))->filter()->take(2);
        $userInitials = $nameParts->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'U';
        $userRoleLabel = method_exists($navUser, 'getRoleDisplayName') ? $navUser->getRoleDisplayName() : ucfirst((string) $navUser->role);
    @endphp

    <div class="app-shell">
        <!-- ================= Sidebar ================= -->
        <aside class="sidebar" id="sidebar" data-expanded-width="260px" data-collapsed-width="74px">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/epss_logo.png') }}" alt="EPSS">
                </div>
                <div class="sidebar-title">
                    <h3>Contract Manager</h3>
                    <p>EPSS · CMS</p>
                </div>
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" onclick="toggleSidebarCollapse()" title="Toggle sidebar">
                    <i class="fas fa-chevron-left" id="toggleIcon"></i>
                </button>
            </div>

            <div class="sidebar-scroll">
                <nav class="sidebar-menu">
                    <div class="sidebar-section-label">Main</div>
                    <div class="sidebar-menu-item">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                            <i class="fas fa-gauge-high"></i><span>Dashboard</span>
                        </a>
                    </div>

                    @if($canViewContracts || $navUser->canCreateContracts())
                    <div class="sidebar-section-label">Contracts</div>
                    @endif

                    @if($canViewContracts)
                    <div class="sidebar-menu-item">
                        <a href="{{ route('contracts.index') }}" class="{{ request()->routeIs('contracts.*') && !request()->routeIs('contracts.reports') && !request()->routeIs('contracts.alerts') ? 'active' : '' }}" data-tooltip="Contracts">
                            <i class="fas fa-file-contract"></i><span>Contracts</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canCreateContracts())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('contracts.create') }}" class="{{ request()->routeIs('contracts.create') ? 'active' : '' }}" data-tooltip="New Contract">
                            <i class="fas fa-circle-plus"></i><span>New Contract</span>
                        </a>
                    </div>
                    @endif

                    @php
                        $showOperations = $navUser->canAccessTireTracking()
                            || $navUser->canViewBatteries()
                            || $navUser->canViewPurchaseOrders()
                            || $navUser->canViewPurchasingActivities()
                            || $navUser->canViewPaymentRequests()
                            || $navUser->canViewContractClosures()
                            || $canViewContracts;
                    @endphp
                    @if($showOperations)
                    <div class="sidebar-section-label">Operations</div>
                    @endif

                    @if($navUser->canAccessTireTracking())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('tires.index') }}" class="{{ request()->routeIs('tires.*') ? 'active' : '' }}" data-tooltip="Tire Tracking">
                            <i class="fas fa-truck"></i><span>Tire Tracking</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewBatteries())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('batteries.index') }}" class="{{ request()->routeIs('batteries.*') ? 'active' : '' }}" data-tooltip="Battery Given & Return">
                            <i class="fas fa-battery-full"></i><span>Battery Given &amp; Return</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewPurchaseOrders())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('purchase-orders.index') }}" class="{{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" data-tooltip="Purchase Order Processing">
                            <i class="fas fa-cart-shopping"></i><span>Purchase Order Processing</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewPurchasingActivities())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('purchasing-activities.index') }}" class="{{ request()->routeIs('purchasing-activities.*') ? 'active' : '' }}" data-tooltip="Purchasing Daily Activity">
                            <i class="fas fa-clipboard-list"></i><span>Purchasing Daily Activity</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewPaymentRequests())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('payment-requests.index') }}" class="{{ request()->routeIs('payment-requests.*') ? 'active' : '' }}" data-tooltip="Payment Request">
                            <i class="fas fa-money-bill-wave"></i><span>Payment Request</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewContractClosures())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('contract-closures.index') }}" class="{{ request()->routeIs('contract-closures.*') ? 'active' : '' }}" data-tooltip="Contract Closing">
                            <i class="fas fa-file-signature"></i><span>Contract Closing</span>
                        </a>
                    </div>
                    @endif

                    @if($canViewContracts)
                    <div class="sidebar-menu-item">
                        <a href="{{ route('contracts.alerts') }}" class="{{ request()->routeIs('contracts.alerts') ? 'active' : '' }}" data-tooltip="Alerts">
                            <i class="fas fa-bell"></i><span>Alerts</span>
                            @if($alertCount > 0)
                            <span class="sidebar-badge">{{ $alertCount }}</span>
                            @endif
                        </a>
                    </div>
                    @endif

                    @php
                        $showInsights = in_array($navUser->role, ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'], true)
                            || $navUser->canViewProcurementReport();
                    @endphp
                    @if($showInsights)
                    <div class="sidebar-section-label">Insights</div>
                    @endif

                    @if(in_array($navUser->role, ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'], true))
                    <div class="sidebar-menu-item">
                        <a href="{{ route('contracts.reports') }}" class="{{ request()->routeIs('contracts.reports') ? 'active' : '' }}" data-tooltip="Reports">
                            <i class="fas fa-chart-column"></i><span>Reports</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->canViewProcurementReport())
                    <div class="sidebar-menu-item">
                        <a href="{{ route('procurement-report.index') }}" class="{{ request()->routeIs('procurement-report.*') ? 'active' : '' }}" data-tooltip="Procurement Report">
                            <i class="fas fa-chart-pie"></i><span>Procurement Report</span>
                        </a>
                    </div>
                    @endif

                    @if(in_array($navUser->role, ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager', 'general_service', 'cmd'], true))
                    <div class="sidebar-menu-item">
                        <a href="{{ route('suppliers.performance-evaluation') }}" class="{{ request()->routeIs('suppliers.performance-evaluation') ? 'active' : '' }}" data-tooltip="Supplier Performance">
                            <i class="fas fa-people-arrows"></i><span>Supplier Performance</span>
                        </a>
                    </div>
                    @endif

                    @if($navUser->isAdmin())
                    <div class="sidebar-section-label">Administration</div>
                    <div class="sidebar-menu-item">
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-tooltip="User and Role Management">
                            <i class="fas fa-users-gear"></i><span>User and Role Management</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item">
                        <a href="{{ route('admin.activity-logs') }}" class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}" data-tooltip="Activity Logs">
                            <i class="fas fa-clock-rotate-left"></i><span>Activity Logs</span>
                        </a>
                    </div>
                    @endif

                    <div class="sidebar-section-label">Account</div>
                    <div class="sidebar-menu-item">
                        <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" data-tooltip="Profile">
                            <i class="fas fa-user"></i><span>Profile</span>
                        </a>
                    </div>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar">{{ $userInitials }}</div>
                    <div class="meta">
                        <span class="name">{{ $navUser->name }}</span>
                        <span class="role">{{ $userRoleLabel }}</span>
                    </div>
                </div>
                <div class="theme-selector">
                    <label for="theme-selector" class="form-label">ገጽታ (Theme)</label>
                    <select id="theme-selector" class="form-select form-select-sm">
                        <option value="theme-blue">ጥቁር ሰማያዊ</option>
                        <option value="theme-sky">ሰማያዊ</option>
                        <option value="theme-green">ጥቁር አረንጓዴ</option>
                        <option value="theme-purple">ጥቁር ወይንጠጅ</option>
                        <option value="theme-red">ጥቁር ቀይ</option>
                        <option value="theme-gray">ጥቁር ግራጫ</option>
                        <option value="theme-light">ነጭ</option>
                    </select>
                </div>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- ================= Main column ================= -->
        <div class="app-main" id="appMain">
            <header class="topbar" id="pageHeader">
                <div class="topbar-left">
                    <button type="button" class="mobile-menu-btn" onclick="toggleMobileSidebar()" aria-label="Open menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="topbar-brand">
                        <img src="{{ asset('images/epss_logo.png') }}" alt="EPSS" class="topbar-logo">
                        <div>
                            <span class="topbar-eyebrow">Contract Management System</span>
                            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
                        </div>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-clock">
                        <span class="date" id="topbarDate">{{ now()->format('D, M d Y') }}</span>
                        <span class="time" id="topbarTime">{{ now()->format('h:i A') }}</span>
                    </div>

                    <button type="button" class="topbar-icon-btn" id="darkModeToggle" title="Dark mode">
                        <i class="fas fa-moon"></i>
                    </button>

                    <a href="{{ $canViewContracts ? route('contracts.alerts') : route('dashboard') }}"
                       class="topbar-icon-btn" title="Alerts">
                        <i class="fas fa-bell"></i>
                        @if($alertCount > 0)
                        <span class="dot-badge">{{ $alertCount }}</span>
                        @endif
                    </a>

                    <div class="dropdown">
                        <button class="profile-btn dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="profile-avatar">{{ $userInitials }}</span>
                            <span class="profile-meta">
                                <span class="name">{{ $navUser->name }}</span>
                                <span class="role">{{ $userRoleLabel }}</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('password.change') }}">
                                    <i class="fas fa-key"></i> Change Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item text-danger" onclick="confirmLogout(event)">
                                    <i class="fas fa-right-from-bracket"></i> Logout
                                </button>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="topbar-icon-btn logout" onclick="confirmLogout(event)" title="Logout">
                        <i class="fas fa-right-from-bracket"></i>
                    </button>
                </div>
            </header>

            <main class="main-content" id="mainContent">
                <div class="page-body">
                    @if(session('success') || session('error') || session('warning'))
                    <div class="flash-stack">
                        @if(session('success'))
                        <div class="alert alert-success auto-dismiss mb-0 d-flex align-items-center">
                            <i class="fas fa-circle-check me-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        @endif
                        @if(session('error'))
                        <div class="alert alert-danger auto-dismiss mb-0 d-flex align-items-center">
                            <i class="fas fa-circle-exclamation me-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        @endif
                        @if(session('warning'))
                        <div class="alert alert-warning mb-0 d-flex align-items-center">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <footer class="page-footer" id="pageFooter">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <span>
                        <i class="fas fa-shield-halved text-epss me-1"></i>
                        Contract Management System v1.0 &middot; &copy; {{ date('Y') }} Ethiopian Pharmaceutical Supply Service
                    </span>
                    <span class="text-muted small">{{ $navUser->name }} &middot; {{ now()->format('M d, Y h:i A') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout confirmation modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-editor text-white" style="background:#0f172a;">
                    <h5 class="modal-title text-white" id="logoutModalLabel">
                        <i class="fas fa-right-from-bracket me-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <span class="stat-card-icon stat-icon-amber mx-auto" style="width:56px;height:56px;font-size:1.3rem;">
                            <i class="fas fa-question"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold mb-2">Are you sure you want to logout?</h6>
                    <p class="text-muted mb-0">You will be redirected to the login page and will need to sign in again.</p>
                    <div class="mt-3 p-3 rounded-3" style="background:#f8fafc;">
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-user me-1"></i>
                            <strong>{{ $navUser->name }}</strong> ({{ $userRoleLabel }})
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-xmark me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="performLogout()">
                        <i class="fas fa-right-from-bracket me-1"></i> Yes, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif

    <script>
        function confirmLogout(event) {
            if (event) event.preventDefault();
            const modalEl = document.getElementById('logoutModal');
            if (!modalEl || typeof bootstrap === 'undefined') {
                document.getElementById('logout-form').submit();
                return;
            }
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function performLogout() {
            document.getElementById('logout-form').submit();
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (!sidebar) return;
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show', sidebar.classList.contains('show'));
        }

        function toggleSidebarCollapse(animate = true) {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            if (!sidebar) return;

            const collapsed = sidebar.classList.contains('collapsed');
            const expandedWidth = sidebar.dataset.expandedWidth || '260px';
            const collapsedWidth = sidebar.dataset.collapsedWidth || '74px';

            if (animate) {
                sidebar.style.transition = 'width 0.3s ease';
                const main = document.getElementById('appMain');
                if (main) main.style.transition = 'margin-left 0.3s ease';
            }

            sidebar.classList.toggle('collapsed', !collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed ? 'false' : 'true');
            document.documentElement.style.setProperty('--sidebar-width', collapsed ? expandedWidth : collapsedWidth);

            if (toggleIcon) {
                toggleIcon.classList.toggle('fa-chevron-left', collapsed);
                toggleIcon.classList.toggle('fa-chevron-right', !collapsed);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Restore sidebar state
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (collapsed) {
                    sidebar.classList.add('collapsed');
                    const collapsedWidth = sidebar.dataset.collapsedWidth || '74px';
                    document.documentElement.style.setProperty('--sidebar-width', collapsedWidth);
                    const icon = document.getElementById('toggleIcon');
                    if (icon) {
                        icon.classList.remove('fa-chevron-left');
                        icon.classList.add('fa-chevron-right');
                    }
                }

                // Floating tooltips for collapsed sidebar
                const tooltip = document.createElement('div');
                tooltip.className = 'sidebar-floating-tooltip';
                document.body.appendChild(tooltip);
                let visibleFor = null;

                function showTooltip(el) {
                    const text = el.getAttribute('data-tooltip');
                    if (!text) return;
                    tooltip.textContent = text;
                    const rect = el.getBoundingClientRect();
                    tooltip.style.top = Math.round(rect.top + rect.height / 2) + 'px';
                    tooltip.style.left = Math.round(rect.right + 10) + 'px';
                    tooltip.classList.add('show');
                    visibleFor = el;
                }

                function hideTooltip() {
                    tooltip.classList.remove('show');
                    visibleFor = null;
                }

                document.querySelectorAll('.sidebar-menu-item a').forEach(function (link) {
                    link.addEventListener('mouseenter', function () {
                        if (sidebar.classList.contains('collapsed')) showTooltip(this);
                    });
                    link.addEventListener('mouseleave', function () {
                        if (visibleFor === this) hideTooltip();
                    });
                });

                const scroller = sidebar.querySelector('.sidebar-scroll');
                if (scroller) scroller.addEventListener('scroll', function () {
                    if (visibleFor) showTooltip(visibleFor);
                });
            }

            // Theme selector
            const themeSelect = document.getElementById('theme-selector');
            const savedTheme = localStorage.getItem('selectedTheme') || 'theme-blue';

            document.body.classList.add(savedTheme);
            if (themeSelect) themeSelect.value = savedTheme;

            if (themeSelect) {
                themeSelect.addEventListener('change', function () {
                    document.body.className = document.body.className.replace(/theme-\w+/g, '');
                    document.body.classList.add(this.value);
                    localStorage.setItem('selectedTheme', this.value);
                });
            }

            // Body dark / light toggle (LawPro-style)
            const darkToggle = document.getElementById('darkModeToggle');
            const bodyTheme = localStorage.getItem('bodyTheme') || 'light';

            function applyBodyTheme(mode) {
                const isDark = mode === 'dark';
                document.body.classList.toggle('dark-theme', isDark);
                if (darkToggle) {
                    darkToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                    darkToggle.title = isDark ? 'Light mode' : 'Dark mode';
                }
            }

            applyBodyTheme(bodyTheme);

            if (darkToggle) {
                darkToggle.addEventListener('click', function () {
                    const next = document.body.classList.contains('dark-theme') ? 'light' : 'dark';
                    localStorage.setItem('bodyTheme', next);
                    applyBodyTheme(next);
                });
            }

            // Mobile overlay + click outside
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) overlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });

            // Auto-dismiss flash messages
            document.querySelectorAll('.auto-dismiss').forEach(function (item) {
                setTimeout(function () {
                    item.style.transition = 'opacity .4s ease';
                    item.style.opacity = '0';
                    setTimeout(function () { item.remove(); }, 400);
                }, 6000);
            });

            // Topbar clock
            const dateEl = document.getElementById('topbarDate');
            const timeEl = document.getElementById('topbarTime');
            if (dateEl && timeEl) {
                setInterval(function () {
                    const now = new Date();
                    dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: '2-digit', year: 'numeric' });
                    timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }, 30000);
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
