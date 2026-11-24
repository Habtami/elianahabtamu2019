@extends('layouts.app')

@section('title', 'Driver Management')
@section('page-title', 'Driver Management')
@section('hide-global-search', true)

@section('styles')
<style>
    .tsr-page {
        --tsr-bg: #f1f5f9;
        --tsr-surface: #ffffff;
        --tsr-text-main: #0f172a;
        --tsr-text-muted: #64748b;
        --tsr-text-light: #94a3b8;
        --tsr-primary: #0284c7;
        --tsr-primary-light: #e0f2fe;
        --tsr-success: #10b981;
        --tsr-danger: #ef4444;
        --tsr-danger-bg: #fef2f2;
        --tsr-border: #e2e8f0;
        --tsr-radius-sm: 8px;
        --tsr-radius-md: 12px;
        --tsr-shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
        --tsr-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

        background-color: var(--tsr-bg);
        font-family: var(--tsr-font-sans);
        color: var(--tsr-text-main);
        padding: 2rem 1.5rem;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    .tsr-page *,
    .tsr-page *::before,
    .tsr-page *::after { box-sizing: border-box; }
    .tsr-page h1, .tsr-page h3, .tsr-page h4, .tsr-page p { margin: 0; }

    .tsr-dashboard {
        width: 100%;
        max-width: 1350px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* ---------- header ---------- */
    .tsr-top-nav {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .tsr-title-area h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem; }
    .tsr-title-area p { color: var(--tsr-text-muted); font-size: 0.95rem; }
    .tsr-user-info {
        text-align: right;
        font-size: 0.9rem;
        color: var(--tsr-text-muted);
        line-height: 1.6;
    }
    .tsr-user-info strong { color: var(--tsr-text-main); }
    .tsr-user-badge {
        display: inline-block;
        margin-top: 0.25rem;
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .tsr-action-bar {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    .tsr-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .tsr-btn-blue { background: var(--tsr-primary); color: #fff; }
    .tsr-btn-blue:hover { background: #0369a1; color: #fff; }
    .tsr-btn-white {
        background: #fff;
        color: var(--tsr-text-main);
        border-color: var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
    }
    .tsr-btn-white:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-action svg {
        width: 16px; height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- KPI cards ---------- */
    .tsr-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .tsr-kpi-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 1.15rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        border-left: 4px solid var(--tsr-border);
        overflow: hidden;
    }
    .tsr-kpi-card.tsr-blue { border-left-color: #3b82f6; }
    .tsr-kpi-card.tsr-green { border-left-color: var(--tsr-success); }
    .tsr-kpi-card.tsr-slate { border-left-color: #94a3b8; }
    .tsr-kpi-card.tsr-orange { border-left-color: #f59e0b; }
    .tsr-kpi-card.tsr-red { border-left-color: var(--tsr-danger); }

    .tsr-kpi-content h4 {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--tsr-text-muted);
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .tsr-kpi-content .tsr-value {
        font-size: 1.7rem;
        font-weight: 700;
        margin: 0.2rem 0;
        line-height: 1;
    }
    .tsr-kpi-content .tsr-sub { font-size: 0.78rem; color: var(--tsr-text-light); }
    .tsr-kpi-icon { opacity: 0.2; width: 30px; height: 30px; flex: none; }
    .tsr-kpi-icon svg { width: 100%; height: 100%; }
    .tsr-kpi-card.tsr-clickable {
        cursor: pointer;
        text-align: left;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .tsr-kpi-card.tsr-clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgb(15 23 42 / 0.08);
        border-color: #cbd5e1;
    }
    .tsr-kpi-card.tsr-clickable:focus-visible {
        outline: 2px solid var(--tsr-primary);
        outline-offset: 2px;
    }
    .tsr-kpi-hint {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.35rem;
        font-size: 0.7rem;
        color: var(--tsr-text-light);
    }
    .tsr-kpi-card.tsr-clickable:hover .tsr-kpi-hint { color: var(--tsr-primary); }

    /* ---------- filter card ---------- */
    .tsr-filter-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        flex-wrap: wrap;
    }
    .tsr-search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
        display: flex;
        align-items: center;
    }
    .tsr-search-box > svg {
        position: absolute;
        left: 0.8rem;
        width: 18px;
        height: 18px;
        color: var(--tsr-text-muted);
        pointer-events: none;
    }
    .tsr-search-box input {
        width: 100%;
        padding: 0.6rem 2.2rem 0.6rem 2.5rem;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
        font-family: inherit;
        color: var(--tsr-text-main);
        background: #fff;
    }
    .tsr-search-box input:focus { border-color: var(--tsr-primary); }
    .tsr-search-clear {
        position: absolute;
        right: 0.6rem;
        width: 20px;
        height: 20px;
        border: none;
        background: transparent;
        color: var(--tsr-text-muted);
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .tsr-search-clear:hover { background: #f1f5f9; color: var(--tsr-text-main); }
    .tsr-search-clear.tsr-visible { display: flex; }

    .tsr-filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .tsr-pill {
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #fff;
        color: var(--tsr-text-muted);
        border: 1px solid var(--tsr-border);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s;
        text-decoration: none;
    }
    .tsr-pill:hover { background: #f8fafc; color: var(--tsr-text-muted); }
    .tsr-pill.tsr-active {
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        border-color: #bae6fd;
    }
    .tsr-pill.tsr-danger { background: var(--tsr-danger-bg); color: var(--tsr-danger); border-color: #fecaca; }
    .tsr-pill.tsr-danger.tsr-active { background: #fee2e2; }
    .tsr-pill-count {
        background: rgba(0, 0, 0, 0.05);
        padding: 0.1rem 0.4rem;
        border-radius: 999px;
        font-size: 0.7rem;
    }
    .tsr-pill.tsr-danger .tsr-pill-count { background: rgba(239, 68, 68, 0.1); }
    .tsr-pill.tsr-reset { border: none; background: transparent; }

    /* ---------- table card ---------- */
    .tsr-table-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        overflow: hidden;
    }
    .tsr-table-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--tsr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .tsr-table-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tsr-count-badge {
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
    }
    .tsr-header-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }
    .tsr-btn-export {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.8rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        background: #fff;
        color: var(--tsr-text-main);
        border: 1px solid var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        text-decoration: none;
    }
    .tsr-btn-export:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-export svg {
        width: 14px; height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-quick-filter input {
        padding: 0.4rem 0.8rem;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        outline: none;
        font-family: inherit;
        color: var(--tsr-text-main);
    }
    .tsr-quick-filter input:focus { border-color: var(--tsr-primary); }

    .tsr-table-wrapper { overflow-x: auto; }
    .tsr-data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 1050px;
    }
    .tsr-data-table th {
        padding: 0.9rem 1rem;
        background: #f8fafc;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--tsr-text-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--tsr-border);
        white-space: nowrap;
    }
    .tsr-data-table td {
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--tsr-border);
        vertical-align: middle;
        font-size: 0.9rem;
        color: var(--tsr-text-main);
    }
    .tsr-data-table tbody tr:last-child td { border-bottom: none; }
    .tsr-data-table tbody tr:hover { background: #f8fafc; }
    .tsr-data-table tbody tr.tsr-hidden { display: none; }

    /* ---------- cells ---------- */
    .tsr-roll-number { font-size: 0.9rem; font-weight: 600; color: var(--tsr-text-muted); }
    .tsr-driver-cell { display: flex; align-items: center; gap: 0.7rem; }
    .tsr-avatar {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex: none;
    }
    .tsr-driver-name { font-weight: 600; color: var(--tsr-text-main); }
    .tsr-driver-sub { font-size: 0.75rem; color: var(--tsr-text-muted); margin-top: 0.1rem; }
    .tsr-code-badge {
        display: inline-block;
        background: #f1f5f9;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .tsr-missing {
        border: 1px dashed var(--tsr-border);
        color: var(--tsr-text-light);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    .tsr-date-main { font-weight: 500; white-space: nowrap; }
    .tsr-date-day { font-size: 0.75rem; color: var(--tsr-text-muted); }
    .tsr-days-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--tsr-text-main);
        white-space: nowrap;
    }
    .tsr-days-pill.tsr-overdue { background: var(--tsr-danger-bg); color: var(--tsr-danger); }
    .tsr-days-pill.tsr-warning { background: #fffbeb; color: #b45309; }
    .tsr-days-pill::before {
        content: "";
        display: inline-block;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .tsr-status-btn {
        border: 1px solid transparent;
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: filter 0.2s;
    }
    .tsr-status-btn:hover { filter: brightness(0.95); }
    .tsr-status-on { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .tsr-status-off { background: #f1f5f9; color: var(--tsr-text-muted); border-color: var(--tsr-border); }
    .tsr-tire-count { font-weight: 600; }
    .tsr-tire-none { color: var(--tsr-text-light); font-size: 0.85rem; }

    /* ---------- actions ---------- */
    .tsr-action-cell { display: flex; justify-content: flex-end; gap: 0.5rem; }
    .tsr-btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 6px;
        border: 1px solid var(--tsr-border);
        background: #fff;
        color: var(--tsr-text-muted);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .tsr-btn-icon:hover { background: #f1f5f9; color: var(--tsr-primary); border-color: #cbd5e1; }
    .tsr-btn-icon.tsr-danger:hover { background: var(--tsr-danger-bg); color: var(--tsr-danger); border-color: #fecaca; }
    .tsr-btn-icon svg {
        width: 15px; height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- no results ---------- */
    .tsr-no-results {
        padding: 2.5rem 1rem;
        text-align: center;
        color: var(--tsr-text-muted);
        font-size: 0.95rem;
    }
    .tsr-no-results strong { color: var(--tsr-text-main); }
    .tsr-empty { padding: 3rem 1rem; text-align: center; }
    .tsr-empty h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.4rem; }
    .tsr-empty p { color: var(--tsr-text-muted); margin-bottom: 1rem; }

    /* ---------- footer ---------- */
    .tsr-table-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--tsr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: var(--tsr-text-muted);
        background: #f8fafc;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .tsr-table-footer strong { color: var(--tsr-text-main); font-weight: 600; }
    .tsr-pagination { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }
    .tsr-page-btn {
        padding: 0.4rem 0.8rem;
        border: 1px solid var(--tsr-border);
        background: #fff;
        border-radius: 6px;
        color: var(--tsr-text-muted);
        cursor: pointer;
        font-weight: 500;
        font-size: 0.85rem;
        text-decoration: none;
        font-family: inherit;
    }
    .tsr-page-btn:hover:not(:disabled) { background: #f1f5f9; color: var(--tsr-text-muted); }
    .tsr-page-btn.tsr-active { background: var(--tsr-primary); color: #fff; border-color: var(--tsr-primary); }
    .tsr-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .tsr-page-dots { padding: 0.4rem 0.3rem; color: var(--tsr-text-muted); }

    /* ---------- toast ---------- */
    .tsr-toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: #0f172a;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease, transform 0.25s ease;
        z-index: 1000;
        white-space: nowrap;
    }
    .tsr-toast.tsr-show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* ---------- KPI modal ---------- */
    .tsr-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        z-index: 1200;
    }
    .tsr-modal-overlay.tsr-open { display: flex; }
    .tsr-modal {
        background: #fff;
        border-radius: var(--tsr-radius-md);
        width: 100%;
        max-width: 840px;
        max-height: 86vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.35);
        overflow: hidden;
    }
    .tsr-modal-header {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--tsr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .tsr-modal-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tsr-modal-close {
        width: 34px; height: 34px;
        border-radius: 8px;
        border: 1px solid var(--tsr-border);
        background: #fff;
        color: var(--tsr-text-muted);
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .tsr-modal-close:hover { background: #f1f5f9; color: var(--tsr-text-main); }
    .tsr-modal-body { overflow: auto; }
    .tsr-modal-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .tsr-modal-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
        padding: 0.7rem 1.2rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--tsr-text-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--tsr-border);
        white-space: nowrap;
    }
    .tsr-modal-table td {
        padding: 0.7rem 1.2rem;
        border-bottom: 1px solid var(--tsr-border);
        font-size: 0.85rem;
        color: var(--tsr-text-main);
        vertical-align: middle;
    }
    .tsr-modal-table tbody tr:last-child td { border-bottom: none; }
    .tsr-modal-table tbody tr:hover { background: #f8fafc; }
    .tsr-modal-table tbody tr.tsr-hidden { display: none; }
    .tsr-modal-empty {
        display: none;
        padding: 2.5rem 1rem;
        text-align: center;
        color: var(--tsr-text-muted);
        font-size: 0.92rem;
    }
    .tsr-modal-empty.tsr-show { display: block; }
    .tsr-modal-footer {
        padding: 0.9rem 1.4rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 700px) {
        .tsr-page { padding: 1rem; }
        .tsr-top-nav, .tsr-title-area { align-items: flex-start; }
        .tsr-user-info { text-align: left; }
        .tsr-filter-card { align-items: stretch; }
        .tsr-search-box { min-width: 100%; }
        .tsr-data-table { min-width: 980px; }
        .tsr-table-footer { flex-direction: column; align-items: flex-start; }
        .tsr-modal-overlay { padding: 0.75rem; }
        .tsr-modal { max-height: 92vh; }
        .tsr-modal-table th:nth-child(2), .tsr-modal-table td:nth-child(2),
        .tsr-modal-table th:nth-child(3), .tsr-modal-table td:nth-child(3) { display: none; }
        .tsr-modal-table th, .tsr-modal-table td { padding: 0.65rem 0.9rem; }
    }
</style>
@endsection

@section('content')
@php
    $status = request('status');
    $filtering = request()->filled('search') || $status !== null && $status !== '';
    $queryState = array_filter([
        'search' => request('search'),
        'status' => $status,
    ], fn ($value) => $value !== null && $value !== '');
    $listLink = function (array $overrides = [], array $remove = []) use ($queryState) {
        $params = array_merge($queryState, $overrides);
        foreach ($remove as $key) {
            unset($params[$key]);
        }

        return route('drivers.index', $params);
    };
@endphp

<div class="tsr-page">
    <div class="tsr-dashboard">

        {{-- header --}}
        <div class="tsr-top-nav">
            <div class="tsr-title-area">
                <h1>Driver Management</h1>
                <p>Every driver on file, with license standing and current tire load.</p>
            </div>
            <div class="tsr-user-info">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                <br>
                <span class="tsr-user-badge">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- action bar --}}
        <div class="tsr-action-bar">
            @if(auth()->user()->canEditDrivers())
            <a href="{{ route('drivers.create') }}" class="tsr-btn-action tsr-btn-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg> Add New Driver
            </a>
            @endif
            <a href="{{ route('tires.give') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"/><path d="M21 2l-9.6 9.6"/><path d="M15.5 7.5l3 3L22 7l-3-3"/></svg> Give Tire
            </a>
            <a href="{{ route('tires.return.list') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Outstanding Tires
            </a>
        </div>

        {{-- KPI cards --}}
        <div class="tsr-kpi-grid">
            <div class="tsr-kpi-card tsr-blue tsr-clickable" data-scope="all" data-label="Total Drivers"
                 data-count="{{ $totalDrivers }}" role="button" tabindex="0" aria-haspopup="dialog"
                 aria-label="View all {{ $totalDrivers }} drivers">
                <div class="tsr-kpi-content">
                    <h4>Total Drivers</h4>
                    <div class="tsr-value">{{ $totalDrivers }}</div>
                    <div class="tsr-sub">On record</div>
                    <div class="tsr-kpi-hint">View list →</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#3b82f6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-green tsr-clickable" data-scope="active" data-label="Active Drivers"
                 data-count="{{ $activeDrivers }}" role="button" tabindex="0" aria-haspopup="dialog"
                 aria-label="View {{ $activeDrivers }} active drivers">
                <div class="tsr-kpi-content">
                    <h4>Active</h4>
                    <div class="tsr-value">{{ $activeDrivers }}</div>
                    <div class="tsr-sub">Available for issue</div>
                    <div class="tsr-kpi-hint">View list →</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#10b981;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-slate tsr-clickable" data-scope="inactive" data-label="Inactive Drivers"
                 data-count="{{ $inactiveDrivers }}" role="button" tabindex="0" aria-haspopup="dialog"
                 aria-label="View {{ $inactiveDrivers }} inactive drivers">
                <div class="tsr-kpi-content">
                    <h4>Inactive</h4>
                    <div class="tsr-value">{{ $inactiveDrivers }}</div>
                    <div class="tsr-sub">Not in service</div>
                    <div class="tsr-kpi-hint">View list →</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#94a3b8;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-orange tsr-clickable" data-scope="expiring" data-label="License Expiring"
                 data-count="{{ $licenseExpiring }}" role="button" tabindex="0" aria-haspopup="dialog"
                 aria-label="View {{ $licenseExpiring }} drivers with licenses expiring within 30 days">
                <div class="tsr-kpi-content">
                    <h4>License Expiring</h4>
                    <div class="tsr-value">{{ $licenseExpiring }}</div>
                    <div class="tsr-sub">Within 30 days</div>
                    <div class="tsr-kpi-hint">View list →</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#f59e0b;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-red tsr-clickable" data-scope="expired" data-label="License Expired"
                 data-count="{{ $licenseExpired }}" role="button" tabindex="0" aria-haspopup="dialog"
                 aria-label="View {{ $licenseExpired }} drivers with expired licenses">
                <div class="tsr-kpi-content">
                    <h4>License Expired</h4>
                    <div class="tsr-value">{{ $licenseExpired }}</div>
                    <div class="tsr-sub">Needs renewal</div>
                    <div class="tsr-kpi-hint">View list →</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#ef4444;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></div>
            </div>
        </div>

        {{-- filter & search --}}
        <div class="tsr-filter-card">
            <form method="GET" action="{{ route('drivers.index') }}" class="tsr-search-box" role="search">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" id="searchInput"
                       placeholder="Search by name, license, phone or email...">
                <button type="button" class="tsr-search-clear {{ request()->filled('search') ? 'tsr-visible' : '' }}" id="searchClear" title="Clear search">✕</button>
            </form>

            <div class="tsr-filter-pills">
                <a href="{{ $listLink([], ['status']) }}" class="tsr-pill {{ !$status ? 'tsr-active' : '' }}">
                    All <span class="tsr-pill-count">{{ $totalDrivers }}</span>
                </a>
                <a href="{{ $listLink(['status' => 'active']) }}" class="tsr-pill {{ $status === 'active' ? 'tsr-active' : '' }}">
                    Active <span class="tsr-pill-count">{{ $activeDrivers }}</span>
                </a>
                <a href="{{ $listLink(['status' => 'inactive']) }}" class="tsr-pill {{ $status === 'inactive' ? 'tsr-active' : '' }}">
                    Inactive <span class="tsr-pill-count">{{ $inactiveDrivers }}</span>
                </a>
                <a href="{{ $listLink(['status' => 'license_expiring']) }}" class="tsr-pill {{ $status === 'license_expiring' ? 'tsr-active' : '' }}">
                    Expiring <span class="tsr-pill-count">{{ $licenseExpiring }}</span>
                </a>
                <a href="{{ $listLink(['status' => 'license_expired']) }}" class="tsr-pill tsr-danger {{ $status === 'license_expired' ? 'tsr-active' : '' }}">
                    Expired <span class="tsr-pill-count">{{ $licenseExpired }}</span>
                </a>
                <a href="{{ route('drivers.index') }}" class="tsr-pill tsr-reset" id="resetBtn">✕ Reset</a>
            </div>
        </div>

        {{-- table --}}
        <div class="tsr-table-card">
            <div class="tsr-table-header">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--tsr-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Drivers
                    <span class="tsr-count-badge" id="visibleCount">{{ $drivers->total() }}</span>
                </h3>
                <div class="tsr-header-actions">
                    <a href="{{ route('exports.drivers', $queryState) }}" class="tsr-btn-export">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Excel
                    </a>
                    <div class="tsr-quick-filter">
                        <input type="text" id="quickFilter" placeholder="Quick filter...">
                    </div>
                </div>
            </div>

            @if($drivers->isNotEmpty())
            <div class="tsr-table-wrapper">
                <table class="tsr-data-table" id="driversTable">
                    <thead>
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>DRIVER</th>
                            <th>LICENSE</th>
                            <th>PHONE</th>
                            <th>LICENSE EXPIRY</th>
                            <th>STATUS</th>
                            <th>TIRES</th>
                            <th style="text-align:right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($drivers as $driver)
                        @php
                            $initials = collect(preg_split('/\s+/', trim($driver->name)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <tr>
                            <td><span class="tsr-roll-number">{{ str_pad((string) $driver->id, 2, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="tsr-driver-cell">
                                    <span class="tsr-avatar">{{ $initials }}</span>
                                    <div>
                                        <div class="tsr-driver-name">{{ $driver->name }}</div>
                                        @if($driver->email)
                                            <div class="tsr-driver-sub">{{ $driver->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($driver->license_number)
                                    <span class="tsr-code-badge">{{ $driver->license_number }}</span>
                                @else
                                    <span class="tsr-missing">Not recorded</span>
                                @endif
                            </td>
                            <td>{{ $driver->phone ?? '—' }}</td>
                            <td>
                                @if($driver->license_expiry)
                                    <div class="tsr-date-main">{{ $driver->license_expiry->format('M d, Y') }}</div>
                                    @if($driver->isLicenseExpired())
                                        <span class="tsr-days-pill tsr-overdue">Expired</span>
                                    @elseif($driver->isLicenseExpiringSoon())
                                        <span class="tsr-days-pill tsr-warning">Expiring</span>
                                    @endif
                                @else
                                    <span class="tsr-missing">Not recorded</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('drivers.toggleStatus', $driver) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="tsr-status-btn {{ $driver->status ? 'tsr-status-on' : 'tsr-status-off' }}"
                                            title="Toggle driver status">
                                        {{ $driver->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                @if($driver->active_tires_count > 0)
                                    <span class="tsr-days-pill">{{ $driver->active_tires_count }} active</span>
                                @else
                                    <span class="tsr-tire-none">None</span>
                                @endif
                            </td>
                            <td>
                                <div class="tsr-action-cell">
                                    <a href="{{ route('drivers.show', $driver) }}" class="tsr-btn-icon" title="View driver" aria-label="View {{ $driver->name }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('drivers.edit', $driver) }}" class="tsr-btn-icon" title="Edit driver" aria-label="Edit {{ $driver->name }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                    </a>
                                    @if(auth()->user()->canManageTires())
                                    <form method="POST" action="{{ route('drivers.destroy', $driver) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this driver?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tsr-btn-icon tsr-danger" title="Delete driver" aria-label="Delete {{ $driver->name }}">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @elseif($filtering)
            <div class="tsr-no-results">
                No drivers match your filters. <strong>Try a different term or reset.</strong>
            </div>
            @else
            <div class="tsr-empty">
                <h3>No drivers found</h3>
                <p>Get started by adding your first driver.</p>
                @if(auth()->user()->canEditDrivers())
                <a href="{{ route('drivers.create') }}" class="tsr-btn-action tsr-btn-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> Add New Driver
                </a>
                @endif
            </div>
            @endif

            <div class="tsr-table-footer">
                <div>
                    Showing <strong>{{ $drivers->firstItem() ?? 0 }}</strong> to <strong>{{ $drivers->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $drivers->total() }}</strong> drivers{{ $filtering ? ' (filtered)' : '' }}
                </div>
                @if($drivers->total() > 0)
                    <div class="tsr-pagination">
                        {{ $drivers->appends(request()->query())->onEachSide(1)->links('tires.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- KPI detail modal --}}
    <div class="tsr-modal-overlay" id="kpiModal" role="dialog" aria-modal="true" aria-labelledby="kpiModalTitle">
        <div class="tsr-modal">
            <div class="tsr-modal-header">
                <h3 id="kpiModalTitle">
                    <span id="kpiModalLabel">Drivers</span>
                    <span class="tsr-count-badge" id="kpiModalCount">{{ $totalDrivers }}</span>
                </h3>
                <button type="button" class="tsr-modal-close" id="kpiModalClose" aria-label="Close">✕</button>
            </div>
            <div class="tsr-modal-body">
                <table class="tsr-modal-table">
                    <thead>
                        <tr>
                            <th>DRIVER</th>
                            <th>LICENSE</th>
                            <th>PHONE</th>
                            <th>LICENSE EXPIRY</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="kpiModalBody">
                        @foreach($modalDrivers as $driver)
                        @php
                            $expiryDate = $driver->license_expiry;
                            $today = now()->startOfDay();
                            $scopes = ['all', $driver->status ? 'active' : 'inactive'];
                            if ($expiryDate) {
                                if ($expiryDate->lt($today)) {
                                    $scopes[] = 'expired';
                                } elseif ($expiryDate->lte($today->copy()->addDays(30))) {
                                    $scopes[] = 'expiring';
                                }
                            }
                            $modalInitials = collect(preg_split('/\s+/', trim($driver->name)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <tr data-scopes="{{ implode(' ', $scopes) }}">
                            <td>
                                <div class="tsr-driver-cell">
                                    <span class="tsr-avatar">{{ $modalInitials }}</span>
                                    <div>
                                        <div class="tsr-driver-name">{{ $driver->name }}</div>
                                        @if($driver->email)
                                            <div class="tsr-driver-sub">{{ $driver->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($driver->license_number)
                                    <span class="tsr-code-badge">{{ $driver->license_number }}</span>
                                @else
                                    <span class="tsr-missing">Not recorded</span>
                                @endif
                            </td>
                            <td>{{ $driver->phone ?? '—' }}</td>
                            <td>
                                @if($expiryDate)
                                    <div class="tsr-date-main">{{ $expiryDate->format('M d, Y') }}</div>
                                    @if(in_array('expired', $scopes, true))
                                        <span class="tsr-days-pill tsr-overdue">Expired</span>
                                    @elseif(in_array('expiring', $scopes, true))
                                        <span class="tsr-days-pill tsr-warning">Expiring</span>
                                    @endif
                                @else
                                    <span class="tsr-missing">Not recorded</span>
                                @endif
                            </td>
                            <td>
                                <span class="tsr-status-btn {{ $driver->status ? 'tsr-status-on' : 'tsr-status-off' }}">{{ $driver->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="tsr-modal-empty" id="kpiModalEmpty">No drivers in this group.</div>
            </div>
            <div class="tsr-modal-footer">
                <button type="button" class="tsr-btn-action tsr-btn-white" id="kpiModalDone">Close</button>
            </div>
        </div>
    </div>

    <div class="tsr-toast" id="tsrToast" role="status" aria-live="polite">Saved</div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        const quickFilter = document.getElementById('quickFilter');
        const tableBody = document.getElementById('tableBody');
        const visibleCount = document.getElementById('visibleCount');
        const toast = document.getElementById('tsrToast');

        if (searchInput) {
            let timer = null;
            searchInput.addEventListener('input', function () {
                searchClear.classList.toggle('tsr-visible', this.value.length > 0);
                clearTimeout(timer);
                timer = setTimeout(function () {
                    searchInput.form.submit();
                }, 500);
            });
        }

        if (searchClear && searchInput) {
            searchClear.addEventListener('click', function () {
                searchInput.value = '';
                searchInput.form.submit();
            });
        }

        if (quickFilter && tableBody) {
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            quickFilter.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();
                let visible = 0;
                rows.forEach(function (row) {
                    const match = !term || row.textContent.toLowerCase().includes(term);
                    row.classList.toggle('tsr-hidden', !match);
                    if (match) visible++;
                });
                if (visibleCount) {
                    visibleCount.textContent = visible;
                }
            });
        }

        const kpiModal = document.getElementById('kpiModal');
        if (kpiModal) {
            const modalLabel = document.getElementById('kpiModalLabel');
            const modalCount = document.getElementById('kpiModalCount');
            const modalBody = document.getElementById('kpiModalBody');
            const modalEmpty = document.getElementById('kpiModalEmpty');
            const modalClose = document.getElementById('kpiModalClose');
            const modalDone = document.getElementById('kpiModalDone');
            const modalRows = modalBody ? Array.from(modalBody.querySelectorAll('tr')) : [];
            let lastCard = null;

            const openModal = function (card) {
                const scope = card.dataset.scope;
                let visible = 0;
                modalRows.forEach(function (row) {
                    const show = (row.dataset.scopes || '').split(' ').indexOf(scope) !== -1;
                    row.classList.toggle('tsr-hidden', !show);
                    if (show) visible++;
                });
                if (modalLabel) modalLabel.textContent = card.dataset.label || 'Drivers';
                if (modalCount) modalCount.textContent = visible;
                if (modalEmpty) modalEmpty.classList.toggle('tsr-show', visible === 0);
                lastCard = card;
                kpiModal.classList.add('tsr-open');
                document.body.style.overflow = 'hidden';
                if (modalClose) modalClose.focus();
            };

            const closeModal = function () {
                kpiModal.classList.remove('tsr-open');
                document.body.style.overflow = '';
                if (lastCard) lastCard.focus();
            };

            document.querySelectorAll('.tsr-kpi-card[data-scope]').forEach(function (card) {
                card.addEventListener('click', function () { openModal(card); });
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openModal(card);
                    }
                });
            });

            if (modalClose) modalClose.addEventListener('click', closeModal);
            if (modalDone) modalDone.addEventListener('click', closeModal);
            kpiModal.addEventListener('click', function (e) {
                if (e.target === kpiModal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && kpiModal.classList.contains('tsr-open')) {
                    closeModal();
                }
            });
        }

        @if(session('success'))
        if (toast) {
            toast.textContent = @json(session('success'));
            toast.classList.add('tsr-show');
            setTimeout(function () { toast.classList.remove('tsr-show'); }, 2600);
        }
        @endif
    });
</script>
@endsection
