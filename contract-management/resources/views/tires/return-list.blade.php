@extends('layouts.app')

@section('title', 'Return Tire')
@section('page-title', 'Return Tire')
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
        --tsr-success-hover: #059669;
        --tsr-danger: #ef4444;
        --tsr-danger-bg: #fef2f2;
        --tsr-border: #e2e8f0;
        --tsr-radius-sm: 8px;
        --tsr-radius-md: 12px;
        --tsr-shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
        --tsr-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
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
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        transition: all 0.3s ease;
    }
    .tsr-kpi-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        border-left: 4px solid var(--tsr-border);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .tsr-kpi-card.tsr-blue { border-left-color: #3b82f6; }
    .tsr-kpi-card.tsr-purple { border-left-color: #8b5cf6; }
    .tsr-kpi-card.tsr-red { border-left-color: var(--tsr-danger); }
    .tsr-kpi-card.tsr-orange { border-left-color: #f59e0b; }

    .tsr-kpi-content h4 {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--tsr-text-muted);
        letter-spacing: 0.05em;
        font-weight: 600;
        transition: font-size 0.3s ease;
    }
    .tsr-kpi-content .tsr-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0.2rem 0;
        line-height: 1;
        transition: font-size 0.3s ease;
    }
    .tsr-kpi-content .tsr-sub {
        font-size: 0.8rem;
        color: var(--tsr-text-light);
        transition: opacity 0.25s ease, max-height 0.3s ease;
        overflow: hidden;
        max-height: 30px;
    }
    .tsr-kpi-icon { opacity: 0.2; width: 32px; height: 32px; transition: all 0.3s ease; }
    .tsr-kpi-icon svg { width: 100%; height: 100%; }

    .tsr-kpi-card.tsr-clickable { cursor: pointer; }
    .tsr-kpi-card.tsr-clickable:hover {
        transform: translateY(-2px);
        box-shadow: var(--tsr-shadow-md);
        border-color: #cbd5e1;
    }
    .tsr-kpi-card.tsr-clickable:focus-visible {
        outline: 2px solid var(--tsr-primary);
        outline-offset: 2px;
    }

    /* ---------- KPI detail modal ---------- */
    .tsr-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.55);
    }
    .tsr-modal-overlay.tsr-open { display: flex; }
    .tsr-modal-card {
        width: min(960px, 100%);
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.3);
        overflow: hidden;
    }
    .tsr-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--tsr-border);
    }
    .tsr-modal-head h3 { font-size: 1.05rem; font-weight: 700; }
    .tsr-modal-subtitle {
        margin-top: 0.2rem;
        font-size: 0.83rem;
        color: var(--tsr-text-muted);
    }
    .tsr-modal-close {
        border: none;
        background: transparent;
        color: var(--tsr-text-muted);
        font-size: 1.6rem;
        line-height: 1;
        cursor: pointer;
        padding: 0 0.25rem;
        border-radius: var(--tsr-radius-sm);
    }
    .tsr-modal-close:hover { color: var(--tsr-text-main); background: #f1f5f9; }
    .tsr-modal-body { overflow: auto; }
    .tsr-modal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }
    .tsr-modal-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
        text-align: left;
        padding: 0.7rem 1rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--tsr-text-muted);
        border-bottom: 1px solid var(--tsr-border);
        white-space: nowrap;
    }
    .tsr-modal-table td {
        padding: 0.7rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--tsr-text-main);
    }
    .tsr-modal-table tbody tr:hover { background: #f8fafc; }
    .tsr-modal-table td.tsr-modal-danger { color: var(--tsr-danger); font-weight: 700; }
    .tsr-modal-table th.tsr-modal-roll,
    .tsr-modal-table td.tsr-modal-roll {
        width: 3.5rem;
        color: var(--tsr-text-muted);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .tsr-modal-empty {
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: var(--tsr-text-muted);
        font-size: 0.9rem;
    }
    .tsr-modal-foot {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
        font-size: 0.85rem;
        color: var(--tsr-text-muted);
    }
    .tsr-modal-foot strong { color: var(--tsr-text-main); font-weight: 600; }
    @media (max-width: 720px) {
        .tsr-modal-overlay { padding: 0.75rem; }
        .tsr-modal-head { padding: 0.85rem 1rem; }
        .tsr-modal-table th, .tsr-modal-table td { padding: 0.55rem 0.65rem; }
        .tsr-modal-foot { padding: 0.65rem 0.9rem; }
    }

    .tsr-kpi-grid.tsr-compact { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.6rem; }
    .tsr-kpi-grid.tsr-compact .tsr-kpi-card { padding: 0.6rem 0.9rem; border-radius: var(--tsr-radius-sm); }
    .tsr-kpi-grid.tsr-compact .tsr-kpi-content h4 { font-size: 0.65rem; }
    .tsr-kpi-grid.tsr-compact .tsr-kpi-content .tsr-value { font-size: 1.15rem; margin: 0.05rem 0; }
    .tsr-kpi-grid.tsr-compact .tsr-kpi-content .tsr-sub { opacity: 0; max-height: 0; font-size: 0; }
    .tsr-kpi-grid.tsr-compact .tsr-kpi-icon { width: 20px; height: 20px; }

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
        min-width: 1100px;
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
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }
    .tsr-data-table th:hover { color: var(--tsr-primary); }
    .tsr-th-link {
        display: inline-flex;
        align-items: center;
        color: inherit;
        text-decoration: none;
    }
    .tsr-th-link:hover { color: inherit; }
    .tsr-sort-icon {
        display: inline-flex;
        vertical-align: middle;
        margin-left: 0.3rem;
        opacity: 0.4;
    }
    .tsr-sort-icon.tsr-active { opacity: 1; color: var(--tsr-primary); }
    .tsr-sort-icon svg {
        width: 12px; height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-data-table td {
        padding: 1rem;
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
    .tsr-serial-badge {
        display: inline-block;
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.8rem;
        margin-bottom: 0.3rem;
    }
    .tsr-spec-text {
        font-size: 0.75rem;
        color: var(--tsr-text-muted);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .tsr-spec-text::before { content: "•"; color: var(--tsr-primary); font-weight: bold; }
    .tsr-driver-name { font-weight: 600; color: var(--tsr-text-main); }
    .tsr-driver-phone { font-size: 0.75rem; color: var(--tsr-text-muted); margin-top: 0.2rem; }
    .tsr-plate-recorded {
        background: #f1f5f9;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .tsr-plate-missing {
        border: 1px dashed var(--tsr-border);
        color: var(--tsr-text-light);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    .tsr-date-main { font-weight: 500; }
    .tsr-date-day { font-size: 0.75rem; color: var(--tsr-text-muted); }
    .tsr-days-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--tsr-text-main);
    }
    .tsr-days-pill.tsr-overdue { background: var(--tsr-danger-bg); color: var(--tsr-danger); }
    .tsr-days-pill::before {
        content: "";
        display: inline-block;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

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
    .tsr-btn-icon svg {
        width: 15px; height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }
    .tsr-btn-return {
        background: var(--tsr-success);
        color: #fff;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        white-space: nowrap;
    }
    .tsr-btn-return:hover { background: var(--tsr-success-hover); color: #fff; }
    .tsr-btn-return svg {
        width: 14px; height: 14px;
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
    .tsr-footer-danger { color: var(--tsr-danger); font-weight: 600; }
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

    @media (max-width: 700px) {
        .tsr-page { padding: 1rem; }
        .tsr-top-nav, .tsr-title-area { align-items: flex-start; }
        .tsr-user-info { text-align: left; }
        .tsr-filter-card { align-items: stretch; }
        .tsr-search-box { min-width: 100%; }
        .tsr-data-table { min-width: 1000px; }
        .tsr-table-footer { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
@php
    $searching = request()->filled('search');
    $filtering = $searching || request()->filled('age');
    $queryState = array_filter([
        'search' => request('search'),
        'age' => request('age'),
        'sort' => $sort,
        'direction' => $direction,
    ], fn ($value) => $value !== null && $value !== '');
    $listLink = function (array $overrides = [], array $remove = []) use ($queryState) {
        $params = array_merge($queryState, $overrides);
        foreach ($remove as $key) {
            unset($params[$key]);
        }

        return route('tires.return.list', $params);
    };
    $indicatorClass = fn (string $column) => $sort === $column ? 'tsr-sort-icon tsr-active' : 'tsr-sort-icon';
    $indicatorPath = fn (string $column) => $sort === $column
        ? ($direction === 'asc' ? 'M7 9l5-5 5 5' : 'M7 15l5 5 5-5')
        : 'M7 15l5 5 5-5M7 9l5-5 5 5';
    $sortLink = fn (string $column) => $listLink([
        'sort' => $column,
        'direction' => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc',
    ]);
    $exportQuery = $queryState;
@endphp

<div class="tsr-page">
    <div class="tsr-dashboard">

        {{-- header --}}
        <div class="tsr-top-nav">
            <div class="tsr-title-area">
                <h1>Return Tire</h1>
                <p>Review every tire currently issued to drivers and record its return.</p>
            </div>
            <div class="tsr-user-info">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                <br>
                <span class="tsr-user-badge">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- action bar --}}
        <div class="tsr-action-bar">
            <a href="{{ route('tires.give') }}" class="tsr-btn-action tsr-btn-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> Give Tire
            </a>
            <a href="{{ route('tires.index') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg> Tire Inventory
            </a>
        </div>

        {{-- KPI cards --}}
        <div class="tsr-kpi-grid {{ $searching ? 'tsr-compact' : '' }}" id="kpiGrid">
            <div class="tsr-kpi-card tsr-blue tsr-clickable" role="button" tabindex="0" data-kpi="tires" aria-haspopup="dialog" title="View tires currently out">
                <div class="tsr-kpi-content">
                    <h4>Tires Out</h4>
                    <div class="tsr-value">{{ $tiresOut }}</div>
                    <div class="tsr-sub">Currently issued</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#3b82f6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-purple tsr-clickable" role="button" tabindex="0" data-kpi="drivers" aria-haspopup="dialog" title="View drivers holding a tire">
                <div class="tsr-kpi-content">
                    <h4>Drivers</h4>
                    <div class="tsr-value">{{ $driversOut }}</div>
                    <div class="tsr-sub">Holding a tire</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#8b5cf6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-red tsr-clickable" role="button" tabindex="0" data-kpi="overdue" aria-haspopup="dialog" title="View tires out more than 30 days">
                <div class="tsr-kpi-content">
                    <h4>Overdue</h4>
                    <div class="tsr-value">{{ $overdueCount }}</div>
                    <div class="tsr-sub">Out more than 30 days</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#ef4444;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-orange tsr-clickable" role="button" tabindex="0" data-kpi="average" aria-haspopup="dialog" title="View days out across open issues">
                <div class="tsr-kpi-content">
                    <h4>Avg Days Out</h4>
                    <div class="tsr-value">{{ $averageDays }}</div>
                    <div class="tsr-sub">Across open issues</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#f59e0b;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
        </div>

        {{-- filter & search --}}
        <div class="tsr-filter-card">
            <form method="GET" action="{{ route('tires.return.list') }}" class="tsr-search-box" role="search">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                @if(request()->filled('age'))
                    <input type="hidden" name="age" value="{{ request('age') }}">
                @endif
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" id="searchInput"
                       placeholder="Search serial number, driver, or plate...">
                <button type="button" class="tsr-search-clear {{ $searching ? 'tsr-visible' : '' }}" id="searchClear" title="Clear search">✕</button>
            </form>

            <div class="tsr-filter-pills">
                <a href="{{ $listLink([], ['age']) }}" class="tsr-pill {{ !request('age') ? 'tsr-active' : '' }}">
                    All <span class="tsr-pill-count">{{ $tiresOut }}</span>
                </a>
                <a href="{{ $listLink(['age' => 'recent']) }}" class="tsr-pill {{ request('age') === 'recent' ? 'tsr-active' : '' }}">
                    ≤ 30 days <span class="tsr-pill-count">{{ $recentCount }}</span>
                </a>
                <a href="{{ $listLink(['age' => 'overdue']) }}" class="tsr-pill tsr-danger {{ request('age') === 'overdue' ? 'tsr-active' : '' }}">
                    Overdue <span class="tsr-pill-count">{{ $overdueCount }}</span>
                </a>
                <a href="{{ route('tires.return.list') }}" class="tsr-pill tsr-reset" id="resetBtn">✕ Reset</a>
            </div>
        </div>

        {{-- table --}}
        <div class="tsr-table-card">
            <div class="tsr-table-header">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--tsr-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Outstanding Tires
                    <span class="tsr-count-badge" id="visibleCount">{{ $tireReturns->total() }}</span>
                </h3>
                <div class="tsr-header-actions">
                    <a href="{{ route('tires.return.excel', $exportQuery) }}" class="tsr-btn-export">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Excel
                    </a>
                    <a href="{{ route('tires.return.pdf', $exportQuery) }}" class="tsr-btn-export">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> PDF
                    </a>
                    <div class="tsr-quick-filter">
                        <input type="text" id="quickFilter" placeholder="Quick filter...">
                    </div>
                </div>
            </div>

            @if($tireReturns->isNotEmpty())
            <div class="tsr-table-wrapper">
                <table class="tsr-data-table" id="returnTable">
                    <thead>
                        <tr>
                            <th style="width:60px;" aria-sort="{{ $sort === 'roll' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('roll') }}">ROLL NO. <span class="{{ $indicatorClass('roll') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('roll') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'serial_number' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('serial_number') }}">TIRE <span class="{{ $indicatorClass('serial_number') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('serial_number') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'given_to' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('given_to') }}">DRIVER <span class="{{ $indicatorClass('given_to') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('given_to') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'plate' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('plate') }}">PLATE <span class="{{ $indicatorClass('plate') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('plate') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'given_by' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('given_by') }}">ISSUED BY <span class="{{ $indicatorClass('given_by') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('given_by') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'given_date' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('given_date') }}">GIVE DATE <span class="{{ $indicatorClass('given_date') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('given_date') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'km_at_issue' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('km_at_issue') }}">KM AT ISSUE <span class="{{ $indicatorClass('km_at_issue') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('km_at_issue') }}"/></svg></span></a>
                            </th>
                            <th aria-sort="{{ $sort === 'days_out' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <a class="tsr-th-link" href="{{ $sortLink('days_out') }}">DAYS OUT <span class="{{ $indicatorClass('days_out') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $indicatorPath('days_out') }}"/></svg></span></a>
                            </th>
                            <th style="text-align:right; cursor:default;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($tireReturns as $give)
                        @php
                            $daysOut = $give->give_date ? (int) $give->give_date->diffInDays(now()) : 0;
                            $plate = $give->plate_number ?: ($give->tire->plate_number ?? null);
                        @endphp
                        <tr>
                            <td><span class="tsr-roll-number">{{ str_pad((string) $give->id, 2, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="tsr-serial-badge">{{ $give->tire->serial_number ?? '—' }}</div>
                                <div class="tsr-spec-text">{{ $give->tire->tire_type ?? 'Unknown type' }}</div>
                            </td>
                            <td>
                                <div class="tsr-driver-name">{{ $give->driver->name ?? 'Unknown driver' }}</div>
                                @if($give->driver?->phone)
                                <div class="tsr-driver-phone">{{ $give->driver->phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if($plate)
                                    <span class="tsr-plate-recorded">{{ $plate }}</span>
                                @else
                                    <span class="tsr-plate-missing">Not recorded</span>
                                @endif
                            </td>
                            <td>{{ $give->givenBy->name ?? '—' }}</td>
                            <td>
                                <div class="tsr-date-main">{{ $give->give_date?->format('M d, Y') ?? '—' }}</div>
                                <div class="tsr-date-day">{{ $give->give_date?->format('l') }}</div>
                            </td>
                            <td>{{ $give->given_kilometer ? number_format($give->given_kilometer) . ' km' : '—' }}</td>
                            <td>
                                <span class="tsr-days-pill {{ $daysOut > 30 ? 'tsr-overdue' : '' }}">
                                    {{ $daysOut }} {{ $daysOut === 1 ? 'day' : 'days' }}
                                </span>
                            </td>
                            <td>
                                <div class="tsr-action-cell">
                                    @if($give->tire)
                                    <a href="{{ route('tires.show', $give->tire) }}" class="tsr-btn-icon" title="View tire" aria-label="View tire {{ $give->tire->serial_number }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @endif
                                    <a href="{{ route('tires.return', $give) }}" class="tsr-btn-return">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Return
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="tsr-no-results">
                No tires match your search. <strong>Try a different term.</strong>
            </div>
            @endif

            <div class="tsr-table-footer">
                <div>
                    Showing <strong>{{ $tireReturns->firstItem() ?? 0 }}</strong> to <strong>{{ $tireReturns->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $tireReturns->total() }}</strong> outstanding tires{{ $filtering ? ' (filtered)' : '' }}
                    @if($overdueCount > 0)
                        • <span class="tsr-footer-danger">{{ $overdueCount }} overdue</span>
                    @endif
                </div>
                @if($tireReturns->total() > 0)
                    <div class="tsr-pagination">
                        {{ $tireReturns->onEachSide(1)->links('tires.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- KPI detail modal --}}
    <div id="kpiModal" class="tsr-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="kpiModalTitle">
        <div class="tsr-modal-card">
            <div class="tsr-modal-head">
                <div>
                    <h3 id="kpiModalTitle">Details</h3>
                    <p id="kpiModalSubtitle" class="tsr-modal-subtitle"></p>
                </div>
                <button type="button" class="tsr-modal-close" id="kpiModalClose" aria-label="Close details">&times;</button>
            </div>
            <div class="tsr-modal-body" id="kpiModalBody"></div>
            <div class="tsr-modal-foot" id="kpiModalFoot">
                <span id="kpiModalRange"></span>
                <div class="tsr-pagination" id="kpiModalPages"></div>
            </div>
        </div>
    </div>

    <div class="tsr-toast" id="tsrToast" role="status" aria-live="polite">Exported successfully</div>
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

        // Live server-side search (debounced), matching the demo's instant filtering
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

        // Client-side quick filter within the current page
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

        // KPI cards open a modal listing the rows behind the count
        const kpiData = @json($kpiModal, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        const kpiModal = document.getElementById('kpiModal');
        const kpiModalTitle = document.getElementById('kpiModalTitle');
        const kpiModalSubtitle = document.getElementById('kpiModalSubtitle');
        const kpiModalBody = document.getElementById('kpiModalBody');
        const kpiModalClose = document.getElementById('kpiModalClose');
        const kpiModalFoot = document.getElementById('kpiModalFoot');
        const kpiModalRange = document.getElementById('kpiModalRange');
        const kpiModalPages = document.getElementById('kpiModalPages');
        const KPI_PAGE_SIZE = 10;
        let kpiLastFocus = null;
        let kpiRows = [];
        let kpiColumns = [];
        let kpiPage = 1;
        let kpiTableBody = null;

        function kpiTotalPages() {
            return Math.max(1, Math.ceil(kpiRows.length / KPI_PAGE_SIZE));
        }

        function kpiPageWindow(totalPages) {
            const pages = [];
            for (let page = 1; page <= totalPages; page++) {
                if (page === 1 || page === totalPages || Math.abs(page - kpiPage) <= 1) {
                    pages.push(page);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }
            return pages;
        }

        function paintKpiPage(refocus) {
            const total = kpiRows.length;
            const totalPages = kpiTotalPages();
            kpiPage = Math.min(Math.max(kpiPage, 1), totalPages);

            const start = (kpiPage - 1) * KPI_PAGE_SIZE;
            const pageRows = kpiRows.slice(start, start + KPI_PAGE_SIZE);
            const daysIndex = kpiColumns.indexOf('Days Out');

            kpiTableBody.innerHTML = '';
            pageRows.forEach(function (row, offset) {
                const tr = document.createElement('tr');

                const noCell = document.createElement('td');
                noCell.className = 'tsr-modal-roll';
                noCell.textContent = start + offset + 1;
                tr.appendChild(noCell);

                row.forEach(function (value, index) {
                    const td = document.createElement('td');
                    td.textContent = value;
                    if (index === daysIndex && Number(value) > 30) {
                        td.classList.add('tsr-modal-danger');
                    }
                    tr.appendChild(td);
                });

                kpiTableBody.appendChild(tr);
            });

            if (totalPages <= 1) {
                kpiModalFoot.style.display = 'none';
                return;
            }

            kpiModalFoot.style.display = 'flex';
            kpiModalRange.innerHTML = 'Showing <strong>' + (start + 1) + '–' + (start + pageRows.length) + '</strong> of <strong>' + total + '</strong>';

            kpiModalPages.innerHTML = '';

            const prev = document.createElement('button');
            prev.type = 'button';
            prev.className = 'tsr-page-btn';
            prev.textContent = '« Prev';
            prev.disabled = kpiPage <= 1;
            prev.addEventListener('click', function () { kpiGoToPage(kpiPage - 1, true); });
            kpiModalPages.appendChild(prev);

            kpiPageWindow(totalPages).forEach(function (item) {
                if (item === '...') {
                    const dots = document.createElement('span');
                    dots.className = 'tsr-page-dots';
                    dots.textContent = '...';
                    kpiModalPages.appendChild(dots);
                    return;
                }

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'tsr-page-btn' + (item === kpiPage ? ' tsr-active' : '');
                button.textContent = item;
                if (item === kpiPage) {
                    button.setAttribute('aria-current', 'page');
                }
                button.addEventListener('click', function () { kpiGoToPage(item, true); });
                kpiModalPages.appendChild(button);
            });

            const next = document.createElement('button');
            next.type = 'button';
            next.className = 'tsr-page-btn';
            next.textContent = 'Next »';
            next.disabled = kpiPage >= totalPages;
            next.addEventListener('click', function () { kpiGoToPage(kpiPage + 1, true); });
            kpiModalPages.appendChild(next);

            if (refocus) {
                const active = kpiModalPages.querySelector('.tsr-active');
                if (active) {
                    active.focus();
                }
            }
        }

        function kpiGoToPage(page, refocus) {
            kpiPage = page;
            paintKpiPage(refocus);
            kpiModalBody.scrollTop = 0;
        }

        function openKpiModal(key) {
            const data = kpiData[key];
            if (!data) return;

            kpiLastFocus = document.activeElement;
            kpiModalTitle.textContent = data.title;
            const count = (data.rows || []).length;
            kpiModalSubtitle.textContent = data.subtitle + ' · ' + count + (count === 1 ? ' record' : ' records');

            kpiRows = data.rows || [];
            kpiColumns = data.columns || [];
            kpiPage = 1;

            kpiModalBody.innerHTML = '';
            kpiModalBody.scrollTop = 0;
            kpiTableBody = null;
            kpiModalPages.innerHTML = '';
            kpiModalFoot.style.display = 'none';

            if (kpiRows.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'tsr-modal-empty';
                empty.textContent = 'No records to show.';
                kpiModalBody.appendChild(empty);
            } else {
                const table = document.createElement('table');
                table.className = 'tsr-modal-table';

                const thead = document.createElement('thead');
                const headRow = document.createElement('tr');

                const noHead = document.createElement('th');
                noHead.className = 'tsr-modal-roll';
                noHead.textContent = 'No';
                headRow.appendChild(noHead);

                kpiColumns.forEach(function (label) {
                    const th = document.createElement('th');
                    th.textContent = label;
                    headRow.appendChild(th);
                });

                thead.appendChild(headRow);
                table.appendChild(thead);

                kpiTableBody = document.createElement('tbody');
                table.appendChild(kpiTableBody);
                kpiModalBody.appendChild(table);

                paintKpiPage(false);
            }

            kpiModal.classList.add('tsr-open');
            document.body.style.overflow = 'hidden';
            kpiModalClose.focus();
        }

        function closeKpiModal() {
            kpiModal.classList.remove('tsr-open');
            document.body.style.overflow = '';
            if (kpiLastFocus && typeof kpiLastFocus.focus === 'function') {
                kpiLastFocus.focus();
            }
        }

        document.querySelectorAll('[data-kpi]').forEach(function (card) {
            card.addEventListener('click', function () {
                openKpiModal(card.dataset.kpi);
            });
            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openKpiModal(card.dataset.kpi);
                }
            });
        });

        if (kpiModal) {
            kpiModalClose.addEventListener('click', closeKpiModal);
            kpiModal.addEventListener('click', function (event) {
                if (event.target === kpiModal) closeKpiModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && kpiModal.classList.contains('tsr-open')) {
                    closeKpiModal();
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
