@extends('layouts.app')

@section('title', 'Tire Tracking System')
@section('page-title', 'Tire Tracking Dashboard')

{{-- Tell layout NOT to render its global search (we have our own) --}}
@section('hide-global-search', true)

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-dark: #1664a0;
        --epss-blue-light: #E8F4FD;
    }

    /* ---------- HEADER BAR ---------- */
    .header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .header-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1E293B;
        margin: 0;
        letter-spacing: -0.02em;
    }
    .header-user {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.1rem;
        text-align: right;
    }
    .header-user .user-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }
    .header-user .user-role {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--epss-blue);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* ---------- ACTION BUTTONS ---------- */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.15rem;
        border-radius: 0.7rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1.5px solid transparent;
        cursor: pointer;
    }
    .action-btn:hover { transform: translateY(-2px); }
    .action-btn.primary {
        background: linear-gradient(135deg, #1C75BC, #1664a0);
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(28, 117, 188, 0.45);
    }
    .action-btn.primary:hover { color: #fff; box-shadow: 0 8px 18px -4px rgba(28, 117, 188, 0.55); }
    .action-btn.success {
        background: linear-gradient(135deg, #10B981, #059669);
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.4);
    }
    .action-btn.success:hover { color: #fff; }
    .action-btn.ghost {
        background: #fff;
        color: #475569;
        border-color: #E2E8F0;
    }
    .action-btn.ghost:hover { border-color: #CBD5E1; color: #1E293B; }

    /* ---------- TOOLBAR ---------- */
    .toolbar {
        background: #fff;
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        border: 1px solid #EDF2F7;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .toolbar-search {
        position: relative;
        flex: 1 1 260px;
        min-width: 200px;
    }
    .toolbar-search input {
        width: 100%;
        padding: 0.68rem 1rem 0.68rem 2.5rem;
        border: 1.5px solid #E2E8F0;
        border-radius: 0.7rem;
        font-size: 0.875rem;
        background: #F8FAFC;
        color: #334155;
        transition: all 0.2s;
    }
    .toolbar-search input:focus {
        outline: none;
        border-color: var(--epss-blue);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.12);
    }
    .toolbar-search .search-icon {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 0.85rem;
        pointer-events: none;
    }
    .toolbar-select {
        padding: 0.68rem 2.1rem 0.68rem 0.9rem;
        border: 1.5px solid #E2E8F0;
        border-radius: 0.7rem;
        font-size: 0.875rem;
        background-color: #F8FAFC;
        color: #334155;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748B' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
    }
    .toolbar-select:focus {
        outline: none;
        border-color: var(--epss-blue);
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.12);
    }
    .toolbar-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.68rem 0.95rem;
        border-radius: 0.7rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748B;
        background: #F1F5F9;
        text-decoration: none !important;
        transition: all 0.2s;
    }
    .toolbar-reset:hover { background: #E2E8F0; color: #334155; }

    /* ---------- TABLE CARD ---------- */
    .table-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #EDF2F7;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .table-card-header {
        padding: 1rem 1.4rem;
        border-bottom: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        background: linear-gradient(180deg, #FCFDFE 0%, #F8FAFC 100%);
    }
    .table-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1E293B;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .badge-count {
        background: #E8F4FD;
        color: var(--epss-blue);
        font-size: 0.72rem;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
        font-weight: 700;
    }
    .table-controls {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .table-controls input {
        padding: 0.55rem 0.85rem;
        border: 1.5px solid #E2E8F0;
        border-radius: 0.6rem;
        font-size: 0.82rem;
        width: 200px;
        transition: all 0.2s;
    }
    .table-controls input:focus {
        outline: none;
        border-color: var(--epss-blue);
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
    }
    .table-controls select {
        padding: 0.55rem 1.7rem 0.55rem 0.7rem;
        border: 1.5px solid #E2E8F0;
        border-radius: 0.6rem;
        font-size: 0.82rem;
        background-color: #fff;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748B' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.55rem center;
    }

    /* ---------- TABLE ---------- */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        padding: 0.85rem 1.2rem;
        text-align: left;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #F8FAFC;
        border-bottom: 1px solid #EDF2F7;
        white-space: nowrap;
        user-select: none;
        transition: background 0.15s;
    }
    .modern-table thead th.sortable {
        cursor: pointer;
    }
    .modern-table thead th.sortable:hover { background: #F1F5F9; color: #334155; }
    .modern-table thead th.sortable:focus-visible {
        outline: 2px solid var(--epss-blue);
        outline-offset: -2px;
    }
    .modern-table thead th .sort-icon {
        margin-left: 0.35rem;
        opacity: 0.35;
        font-size: 0.7rem;
    }
    .modern-table tbody td {
        padding: 0.95rem 1.2rem;
        font-size: 0.875rem;
        color: #475569;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .modern-table tbody tr { transition: background 0.15s; }
    .modern-table tbody tr:hover { background: #F8FAFC; }
    .modern-table tbody tr:last-child td { border-bottom: none; }

    .tire-id {
        font-weight: 700;
        color: #94A3B8;
        font-size: 0.78rem;
        font-family: 'SF Mono', Monaco, monospace;
    }
    .serial-number {
        font-family: 'SF Mono', Monaco, 'Courier New', monospace;
        font-size: 0.8rem;
        color: #475569;
        background: #F1F5F9;
        padding: 0.25rem 0.6rem;
        border-radius: 0.4rem;
        display: inline-block;
        font-weight: 500;
    }
    .tire-type-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        color: #1E293B;
        font-size: 0.85rem;
    }
    .plate-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.6rem;
        background: #F1F5F9;
        border-radius: 0.45rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        font-family: 'SF Mono', Monaco, monospace;
        border: 1px solid #E2E8F0;
    }
    .plate-badge.empty {
        background: transparent;
        border: 1px dashed #CBD5E1;
        color: #94A3B8;
        font-weight: 400;
        font-family: inherit;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }
    .status-pill .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-pill.in-use    { background: #FEF3C7; color: #92400E; }
    .status-pill.in-use .dot { background: #F59E0B; }
    .status-pill.returned  { background: #D1FAE5; color: #065F46; }
    .status-pill.returned .dot { background: #10B981; }

    .row-actions {
        display: flex;
        gap: 0.35rem;
        align-items: center;
        justify-content: flex-end;
    }
    .row-actions a,
    .row-actions button {
        width: 32px; height: 32px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: all 0.18s;
        text-decoration: none !important;
        border: none;
        cursor: pointer;
    }
    .row-actions a:focus-visible,
    .row-actions button:focus-visible {
        outline: 2px solid var(--epss-blue);
        outline-offset: 2px;
    }
    .row-actions .act-view { color: #1C75BC; background: #E8F4FD; }
    .row-actions .act-view:hover { background: #1C75BC; color: #fff; transform: translateY(-1px); }
    .row-actions .act-give { color: #059669; background: #D1FAE5; }
    .row-actions .act-give:hover { background: #059669; color: #fff; transform: translateY(-1px); }
    .row-actions .act-edit { color: #7C3AED; background: #F3E8FF; }
    .row-actions .act-edit:hover { background: #7C3AED; color: #fff; transform: translateY(-1px); }
    .row-actions .act-history { color: #374151; background: #E5E7EB; }
    .row-actions .act-history:hover { background: #374151; color: #fff; transform: translateY(-1px); }

    /* ---------- PAGINATION ---------- */
    .pagination-wrapper {
        padding: 1rem 1.4rem;
        border-top: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        background: #FCFDFE;
    }
    .pagination-info { font-size: 0.8rem; color: #94A3B8; }
    .pagination-info strong { color: #475569; font-weight: 600; }
    .pagination-controls { display: flex; gap: 0.3rem; align-items: center; }
    .page-btn {
        min-width: 34px; height: 34px;
        padding: 0 0.6rem;
        border-radius: 0.5rem;
        border: 1.5px solid #E2E8F0;
        background: #fff;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .page-btn:hover:not(:disabled) {
        border-color: var(--epss-blue);
        color: var(--epss-blue);
        background: #E8F4FD;
    }
    .page-btn.active {
        background: var(--epss-blue);
        border-color: var(--epss-blue);
        color: #fff;
        box-shadow: 0 3px 8px -2px rgba(28, 117, 188, 0.4);
    }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .page-dots { color: #94A3B8; padding: 0 0.35rem; font-size: 0.8rem; }

    /* ---------- HISTORY MODAL ---------- */
    .history-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }
    .history-modal-content {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
    }
    .history-header {
        background: var(--epss-blue);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .history-title { margin: 0; font-size: 1rem; font-weight: 700; }
    .history-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }
    .history-close:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
    .history-body {
        padding: 1rem 1.5rem;
        max-height: 400px;
        overflow-y: auto;
    }
    .history-footer {
        padding: 0.75rem 1.5rem;
        background: #F8FAFC;
        border-top: 1px solid #E5E7EB;
        display: flex;
        justify-content: flex-end;
    }
    .history-close-btn {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        background: var(--epss-blue);
        color: white;
        border: none;
        font-weight: 600;
        cursor: pointer;
    }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th {
        background: #F8FAFC;
        padding: 0.5rem 0.75rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .history-table td {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.85rem;
        color: #334155;
    }
    .history-table tr:hover { background: #F8FAFC; }
    .history-loading {
        padding: 2rem;
        text-align: center;
        color: #64748B;
    }

    /* ---------- EMPTY STATE ---------- */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #E8F4FD, #F1F5F9);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--epss-blue);
        margin-bottom: 1.25rem;
    }

    /* ---------- TIRE DETAIL MODAL ---------- */
    .tire-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        padding: 1rem;
    }
    .tire-modal-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 1100px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }
    .tire-modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #1C75BC, #1664a0);
        color: #fff;
    }
    .tire-modal-header h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tire-modal-close {
        background: rgba(255,255,255,0.15);
        border: none;
        color: #fff;
        font-size: 1.4rem;
        cursor: pointer;
        line-height: 1;
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .tire-modal-close:hover { background: rgba(255,255,255,0.25); }
    .tire-modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
        background: #F8FAFC;
    }
    .tire-modal-footer {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid #E5E7EB;
        background: #fff;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    .tire-modal-footer .btn {
        padding: 0.55rem 1.1rem;
        border-radius: 0.55rem;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
    }
    .tire-modal-footer .btn-secondary {
        background: #E2E8F0;
        color: #334155;
    }
    .tire-modal-footer .btn-secondary:hover { background: #CBD5E1; }

    /* ---------- TIRE DETAIL LAYOUT ---------- */
    .tire-detail-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    @media (min-width: 1024px) {
        .tire-detail-layout { grid-template-columns: 1fr 1fr; }
    }
    .tire-detail-left,
    .tire-detail-right {
        min-width: 0;
    }

    .tire-detail-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1E293B;
        margin: 0 0 0.85rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #EDF2F7;
    }
    .tire-detail-section-title i {
        color: var(--epss-blue);
        font-size: 0.85rem;
    }

    /* ---------- PHOTO GALLERY (BOTH PHOTOS) ---------- */
    .tire-photo-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    @media (max-width: 480px) {
        .tire-photo-grid { grid-template-columns: 1fr; }
    }
    .tire-photo-tile {
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #F1F5F9;
        aspect-ratio: 16 / 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .tire-photo-tile .tire-detail-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        margin: 0;
        border-radius: 0;
        transition: transform 0.25s ease;
    }
    .tire-photo-tile a:hover .tire-detail-photo {
        transform: scale(1.04);
    }
    .tire-photo-tile-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #CBD5E1;
        gap: 0.4rem;
        border: 1.5px dashed #E2E8F0;
        background: #F8FAFC;
    }
    .tire-photo-tile-empty i {
        font-size: 1.5rem;
    }
    .tire-photo-tile-empty .empty-label {
        font-size: 0.72rem;
        color: #94A3B8;
        text-align: center;
        line-height: 1.3;
        font-weight: 500;
    }
    .tire-photo-tile-empty .empty-label small {
        font-size: 0.62rem;
        color: #CBD5E1;
        font-weight: 400;
    }

    .tire-hero-photo-badge {
        position: absolute;
        bottom: 0.55rem;
        left: 0.55rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        backdrop-filter: blur(4px);
    }
    .tire-hero-photo-badge.given    { color: #B45309; border: 1px solid #FCD34D; }
    .tire-hero-photo-badge.returned { color: #15803D; border: 1px solid #86EFAC; }

    .tire-photo-date {
        position: absolute;
        top: 0.55rem;
        right: 0.55rem;
        font-size: 0.65rem;
        font-weight: 600;
        color: #334155;
        background: rgba(255, 255, 255, 0.94);
        padding: 0.18rem 0.45rem;
        border-radius: 0.4rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    /* ---------- INFO CARDS ---------- */
    .tire-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
    }
    @media (max-width: 480px) {
        .tire-info-grid { grid-template-columns: 1fr; }
    }
    .tire-info-card {
        background: #fff;
        border: 1px solid #EDF2F7;
        border-radius: 0.6rem;
        padding: 0.6rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-width: 0;
    }
    .tire-info-card .info-label {
        font-size: 0.68rem;
        color: #94A3B8;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .tire-info-card .info-label i {
        color: #CBD5E1;
        font-size: 0.68rem;
    }
    .tire-info-card .info-value {
        font-size: 0.875rem;
        color: #1E293B;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tire-info-card .info-value.mono {
        font-family: 'SF Mono', Monaco, 'Courier New', monospace;
        font-size: 0.78rem;
    }

    /* ---------- KM CARDS ---------- */
    .tire-km-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.6rem;
    }
    @media (max-width: 480px) {
        .tire-km-grid { grid-template-columns: 1fr; }
    }
    .tire-km-card {
        background: linear-gradient(135deg, #F8FAFC, #F1F5F9);
        border: 1px solid #EDF2F7;
        border-radius: 0.6rem;
        padding: 0.7rem 0.85rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    .tire-km-card.positive {
        background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
        border-color: #A7F3D0;
    }
    .tire-km-card.negative {
        background: linear-gradient(135deg, #FEF2F2, #FEE2E2);
        border-color: #FECACA;
    }
    .tire-km-card .km-label {
        font-size: 0.62rem;
        color: #64748B;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .tire-km-card .km-value {
        font-size: 1rem;
        font-weight: 800;
        color: #1E293B;
        letter-spacing: -0.02em;
    }
    .tire-km-card.positive .km-value { color: #065F46; }
    .tire-km-card.negative .km-value { color: #991B1B; }

    /* ---------- TIMELINE HISTORY ---------- */
    .tire-timeline {
        position: relative;
        padding-left: 1.25rem;
    }
    .tire-timeline::before {
        content: '';
        position: absolute;
        left: 0.4rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 2px;
        background: linear-gradient(180deg, #FDE047, #86EFAC);
        border-radius: 1px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1rem;
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -1.05rem;
        top: 0.35rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #E2E8F0;
    }
    .timeline-dot.out      { background: #F59E0B; box-shadow: 0 0 0 2px #FEF3C7; }
    .timeline-dot.returned { background: #10B981; box-shadow: 0 0 0 2px #D1FAE5; }

    .timeline-content {
        background: #fff;
        border: 1px solid #EDF2F7;
        border-radius: 0.65rem;
        padding: 0.7rem 0.85rem;
    }
    .timeline-item.returned .timeline-content { background: #F0FDF4; border-color: #D1FAE5; }
    .timeline-item.out      .timeline-content { background: #FFFBEB; border-color: #FEF3C7; }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
    }
    .timeline-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
    }
    .timeline-badge.out      { background: #FEF3C7; color: #92400E; }
    .timeline-badge.returned { background: #D1FAE5; color: #065F46; }
    .timeline-date {
        font-size: 0.7rem;
        color: #64748B;
        font-weight: 500;
    }
    .timeline-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.85rem;
        font-size: 0.75rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .timeline-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .timeline-meta i {
        color: #94A3B8;
        font-size: 0.68rem;
    }
    .timeline-photo {
        display: inline-block;
        position: relative;
        width: 72px;
        height: 54px;
        border-radius: 0.45rem;
        overflow: hidden;
        border: 1px solid #E2E8F0;
        text-decoration: none;
        background: #F1F5F9;
    }
    .timeline-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.2s;
    }
    .timeline-photo:hover img { transform: scale(1.05); }
    .timeline-photo-tag {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.65);
        color: #fff;
        font-size: 0.52rem;
        font-weight: 600;
        text-align: center;
        padding: 0.15rem 0.2rem;
        line-height: 1.1;
    }
    .timeline-photo-empty {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #CBD5E1;
        font-size: 0.85rem;
        border-style: dashed;
    }

    /* ---------- EMPTY GIVE STATE ---------- */
    .give-empty {
        text-align: center;
        padding: 1.5rem 1rem;
        color: #64748B;
        background: #fff;
        border: 1px dashed #E2E8F0;
        border-radius: 0.75rem;
    }
    .give-empty i {
        font-size: 1.25rem;
        color: #94A3B8;
        margin-bottom: 0.5rem;
        display: block;
    }
    .give-empty p {
        margin: 0.25rem 0;
        font-size: 0.85rem;
    }
    .give-empty .hint {
        font-size: 0.75rem;
        color: #94A3B8;
    }

    /* ---------- MODAL EMPTY / ERROR ---------- */
    .modal-empty {
        padding: 2rem;
        text-align: center;
        color: #64748B;
        font-size: 0.875rem;
    }

    .tire-modal-error {
        text-align: center;
        padding: 2.5rem 1rem;
    }
    .tire-modal-error i {
        font-size: 2.25rem;
        color: #F87171;
    }
    .tire-modal-error p {
        margin: 0.5rem 0;
        color: #475569;
        font-size: 0.9rem;
    }
    .tire-modal-error .btn-retry {
        margin-top: 0.75rem;
        padding: 0.55rem 1.1rem;
        border-radius: 0.55rem;
        border: none;
        background: #1C75BC;
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
    }

    /* ---------- GIVE MODAL FORM ---------- */
    .give-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .give-form-grid .mb-4 {
        margin-bottom: 0;
    }
    @media (max-width: 640px) {
        .give-modal-content { max-width: 95vw; }
        .give-form-grid { grid-template-columns: 1fr; }
    }
    .serial-warning {
        margin-top: 0.25rem;
    }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 640px) {
        .header-user { align-items: flex-start; text-align: left; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-search { min-width: 100%; }
        .table-controls { width: 100%; }
        .table-controls input { width: 100%; }
    }
</style>
@endsection

@section('content')
@php
    // Cache counts once to avoid repeated queries in the view
    $tireCount = $tires->count();
@endphp

<div class="py-6" style="background:#F8FAFC;min-height:100vh;">
    <div class="container mx-auto px-4">

        {{-- ========== HEADER BAR ========== --}}
        <div class="header-bar">
            <div class="header-title">
               
            </div>
        </div>

        {{-- ========== ACTION BUTTONS ========== --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            @if(auth()->user()->canPerformTireOperations())
                 <button type="button" id="empty-give-btn" class="action-btn primary" onclick="openGiveModal()">
                     <i class="fas fa-hand-holding" aria-hidden="true"></i> Give Tire
                 </button>
            @endif
            @if(auth()->user()->canManageTires())
            <a href="{{ route('tires.return.list') }}" class="action-btn success">
                <i class="fas fa-undo" aria-hidden="true"></i> Return Tire
            </a>
            @endif
            @if(auth()->user()->canEditDrivers())
            <a href="{{ route('drivers.index') }}" class="action-btn ghost">
                <i class="fas fa-user-friends" aria-hidden="true"></i> Drivers
            </a>
            @endif
            @if(auth()->user()->canViewTireReports() && Route::has('tires.reports'))
            <a href="{{ route('tires.reports') }}" class="action-btn ghost">
                <i class="fas fa-chart-bar" aria-hidden="true"></i> Reports
            </a>
            @endif
        </div>

        {{-- ========== STAT CARDS ========== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

            <div class="kpi-card blue" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('total-tires', 'Total Tires')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Total Tires</p>
                    <p class="kpi-value">{{ $totalTires }}</p>
                    <p class="kpi-subtitle">{{ $activeTires }} active</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-circle-notch" aria-hidden="true"></i></div>
            </div>

            <div class="kpi-card yellow" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('in-use', 'In Use')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">In Use</p>
                    <p class="kpi-value">{{ $givenOutTires }}</p>
                    <p class="kpi-subtitle">Currently given out</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-hand-holding" aria-hidden="true"></i></div>
            </div>

            <div class="kpi-card green" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('returned', 'Returned')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Returned</p>
                    <p class="kpi-value">{{ $returnedTires }}</p>
                    <p class="kpi-subtitle">Previously returned</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-undo-alt" aria-hidden="true"></i></div>
            </div>

            <div class="kpi-card purple" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('active-drivers', 'Active Drivers')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Active Drivers</p>
                    <p class="kpi-value">{{ $activeDriversCount }}</p>
                    <p class="kpi-subtitle">{{ $tiresOutCount }} tires out</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-user-friends" aria-hidden="true"></i></div>
            </div>
        </div>

        {{-- ========== UNIFIED FILTER TOOLBAR ========== --}}
        <form method="GET" action="{{ route('tires.index') }}" class="toolbar mb-6" id="filter-form">
            <div class="toolbar-search">
                <i class="fas fa-search search-icon" aria-hidden="true"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by serial number, type, or plate..."
                       aria-label="Search tires">
            </div>

            <select name="status" class="toolbar-select" aria-label="Filter by status"
                    data-auto-submit>
                <option value="">All Status</option>
                <option value="in_use"   {{ request('status') == 'in_use'   ? 'selected' : '' }}>In Use</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>

            <select name="tire_type" class="toolbar-select" aria-label="Filter by tire type"
                    data-auto-submit>
                <option value="">All Types</option>
                @foreach(\App\Models\TireType::where('is_active', true)->orderBy('sort_order')->get() as $tType)
                    <option value="{{ $tType->name }}" {{ request('tire_type') == $tType->name ? 'selected' : '' }}>
                        {{ $tType->name }}
                    </option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status', 'tire_type']))
            <a href="{{ route('tires.index') }}" class="toolbar-reset">
                <i class="fas fa-times" aria-hidden="true"></i> Reset
            </a>
            @endif
        </form>

        {{-- ========== TABLE CARD ========== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-card-title">
                    <i class="fas fa-list-ul" style="color:var(--epss-blue);" aria-hidden="true"></i>
                    Tire Inventory
                    <span class="badge-count">{{ $tireCount }}</span>
                </h3>
                @if($tireCount > 0)
                <div class="table-controls">
                    <input type="text" id="quick-filter" placeholder="Quick filter..."
                           aria-label="Quick filter current page">
                    <select id="page-size" aria-label="Rows per page">
                        <option value="10" selected>10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
                @endif
            </div>

            @if($tireCount > 0)
            <div class="overflow-x-auto">
                <table id="tires-table" class="modern-table">
                    <thead>
                        <tr>
                            <th data-sort="0" class="sortable" tabindex="0" role="button" aria-sort="none">ID <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                            <th data-sort="1" class="sortable" tabindex="0" role="button" aria-sort="none">Tire Type <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                            <th data-sort="2" class="sortable" tabindex="0" role="button" aria-sort="none">Serial Number <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                             <th data-sort="3" class="sortable" tabindex="0" role="button" aria-sort="none">Plate Number <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                             <th data-sort="4" class="sortable" tabindex="0" role="button" aria-sort="none">Given By <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                             <th data-sort="5" class="sortable" tabindex="0" role="button" aria-sort="none">Driver <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                              <th data-sort="6" class="sortable" tabindex="0" role="button" aria-sort="none">Initial KM <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                              <th data-sort="7" class="sortable" tabindex="0" role="button" aria-sort="none">Current KM <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                              <th data-sort="8" class="sortable" tabindex="0" role="button" aria-sort="none">KM Difference <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                              <th data-sort="9" class="sortable" tabindex="0" role="button" aria-sort="none">Status <i class="fas fa-sort sort-icon" aria-hidden="true"></i></th>
                              <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tires as $tire)
                         @php
                             // Latest give/return record (sort by id desc)
                             // — persists even after return.
                             $latestGive = $tire->tireGives->sortByDesc('id')->first();

                              // Current KM: use current give's km, or last return's km, or initial km
                              if ($latestGive && !$latestGive->is_returned) {
                                  $currentKm = $latestGive->given_kilometer;
                              } else {
                                  $latestReturn = $tire->tireReturns->sortByDesc('id')->first();
                                  $currentKm = $latestReturn ? $latestReturn->current_kilometer : $tire->initial_kilometer;
                              }

                              // KM Difference: current KM - initial KM
                              $kmDifference = $currentKm - ($tire->initial_kilometer ?? 0);

                             // Status: ONLY two values — In Use or Returned
                             if ($tire->isGivenOut()) {
                                $statusKey   = 'in-use';
                                $statusLabel = 'In Use';
                                $statusSort  = '1-in-use';
                            } else {
                                $statusKey   = 'returned';
                                $statusLabel = 'Returned';
                                $statusSort  = '2-returned';
                            }
                        @endphp
                        <tr>
                            <td><span class="tire-id">#{{ $tire->id }}</span></td>
                            <td>
                                <span class="tire-type-tag">
                                    <i class="fas fa-circle" style="font-size:0.4rem;color:var(--epss-blue);" aria-hidden="true"></i>
                                    {{ $tire->tire_type }}
                                </span>
                            </td>
                            <td><span class="serial-number">{{ $tire->serial_number }}</span></td>
                            <td>
                                @if($tire->plate_number)
                                    <span class="plate-badge">
                                        <i class="fas fa-car" style="margin-right:0.25rem;font-size:0.7rem;" aria-hidden="true"></i>
                                        {{ $tire->plate_number }}
                                    </span>
                                @else
                                    <span class="plate-badge empty">&mdash;</span>
                                @endif
                             </td>
                             <td>
                                 @if($latestGive && $latestGive->givenBy)
                                     <span class="text-gray-700">{{ $latestGive->givenBy->name }}</span>
                                 @else
                                     <span class="text-gray-400">&mdash;</span>
                                 @endif
                             </td>
                             <td>
                                 @if($latestGive && $latestGive->driver)
                                     <span class="text-gray-700">{{ $latestGive->driver->name }}</span>
                                 @else
                                     <span class="text-gray-400">&mdash;</span>
                                 @endif
                             </td>
                              <td>{{ number_format($tire->initial_kilometer) }}</td>
                              <td data-sort-value="{{ $currentKm ?? 0 }}">
                                  <span class="text-gray-700 font-medium">{{ number_format($currentKm ?? 0) }}</span>
                              </td>
                               <td data-sort-value="{{ $kmDifference }}">
                                   <span class="{{ $kmDifference >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                       {{ $kmDifference >= 0 ? '+' : '' }}{{ number_format($kmDifference) }}
                                   </span>
                               </td>
                              <td data-sort-value="{{ $statusSort }}">
                                  <span class="status-pill {{ $statusKey }}">
                                      <span class="dot"></span> {{ $statusLabel }}
                                  </span>
                              </td>
                            <td>
                                <div class="row-actions">
                                     <button type="button" class="act-view"
                                            title="View" aria-label="View tire {{ $tire->serial_number }}"
                                            data-tire-id="{{ $tire->id }}">
                                         <i class="fas fa-eye" aria-hidden="true"></i>
                                     </button>
                                     @if(auth()->user()->canPerformTireOperations() && !$tire->isGivenOut() && !$tire->hasBeenReturned())
                                     <button type="button" class="act-give"
                                        title="Give Tire" aria-label="Give tire {{ $tire->serial_number }}"
                                        onclick="openGiveModal()">
                                         <i class="fas fa-hand-holding" aria-hidden="true"></i>
                                     </button>
                                     @endif
                                    <button type="button" class="act-history" title="History"
                                            aria-label="View history for {{ $tire->serial_number }}"
                                            data-tire-id="{{ $tire->id }}"
                                            data-tire-serial="{{ $tire->serial_number }}">
                                        <i class="fas fa-history" aria-hidden="true"></i>
                                    </button>
                                    @if(auth()->user()->canManageTires() && !$tire->hasBeenReturned())
                                    <a href="{{ route('tires.edit', $tire) }}" class="act-edit"
                                       title="Edit" aria-label="Edit tire {{ $tire->serial_number }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                <div class="pagination-info" id="pagination-info"></div>
                <div class="pagination-controls" id="pagination-controls"></div>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-circle-notch" aria-hidden="true"></i></div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No tires registered</h3>
                <p class="text-gray-500 mb-4">Get started by registering a tire or returning an existing one.</p>
                @if(auth()->user()->canPerformTireOperations())
                <a href="{{ route('tires.give') }}" class="action-btn primary">
                    <i class="fas fa-hand-holding" aria-hidden="true"></i> Give Tire
                </a>
                @endif
                @if(auth()->user()->canManageTires())
                <a href="{{ route('tires.create') }}" class="action-btn success">
                    <i class="fas fa-plus" aria-hidden="true"></i> Register Tire
                </a>
                @endif
            </div>
            @endif
        </div>

    </div>
</div>

{{-- History Modal --}}
<div id="history-modal" class="history-modal-overlay" style="display: none;"
     role="dialog" aria-modal="true" aria-labelledby="history-modal-title">
    <div class="history-modal-content" style="max-width: 700px;">
        <div class="history-header">
            <h3 class="history-title" id="history-modal-title">Tire History: <span id="history-tire-serial"></span></h3>
            <button type="button" class="history-close" aria-label="Close history">&times;</button>
        </div>
        <div class="history-body">
            <div id="history-content">
                <div class="history-loading">Loading...</div>
            </div>
        </div>
        <div class="history-footer">
            <button type="button" class="history-close-btn">Close</button>
        </div>
    </div>
</div>

{{-- Tire Detail Modal --}}
<div id="tire-detail-modal" class="tire-modal-overlay" style="display: none;"
     role="dialog" aria-modal="true" aria-labelledby="tire-modal-title">
    <div class="tire-modal-card">
        <div class="tire-modal-header">
            <h2 id="tire-modal-title"><i class="fas fa-info-circle"></i> Tire Details</h2>
            <button type="button" class="tire-modal-close" aria-label="Close tire details">&times;</button>
        </div>
        <div id="tire-modal-body" class="tire-modal-body">
            <div class="modal-empty">Loading tire details...</div>
        </div>
        <div class="tire-modal-footer">
            <button type="button" class="btn btn-secondary" data-tire-modal-close>Close</button>
        </div>
    </div>
</div>

{{-- Give Tire Modal --}}
<div id="give-modal" class="tire-modal-overlay" style="display: none;"
     role="dialog" aria-modal="true" aria-labelledby="give-modal-title">
    <div class="tire-modal-card" style="max-width: 720px;">
        <div class="tire-modal-header">
            <h2 id="give-modal-title"><i class="fas fa-hand-holding"></i> Give Tire to Driver</h2>
            <button type="button" class="tire-modal-close" aria-label="Close give form">&times;</button>
        </div>
        <div id="give-modal-body" class="tire-modal-body">
            <form method="POST" action="{{ route('tires.give.store') }}" id="giveFormModal" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="give-form-grid">
                    <!-- Serial Number -->
                    <div class="mb-4">
                        <label for="give_serial" class="form-label required">Tire Serial Number</label>
                        <input type="text" id="give_serial" name="tire_serial" value="{{ old('tire_serial') }}" class="form-input" placeholder="Enter serial number..." required>
                        @error('tire_serial')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tire Type -->
                    <div class="mb-4">
                        <label for="give_tire_type" class="form-label required">Tire Type</label>
                        <select id="give_tire_type" name="tire_type" class="form-input" required>
                            <option value="">Select Tire Type</option>
                            @foreach($tireTypes as $key => $name)
                                @php($value = is_object($name) ? $name->name : (is_array($name) ? ($name['name'] ?? $key) : $name))
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('tire_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Driver Name -->
                    <div class="mb-4">
                        <label for="give_driver" class="form-label required">Driver Name</label>
                        <select id="give_driver" name="driver_id" class="form-input" required>
                            <option value="">Select Driver</option>
                            @foreach(($driversList ?? collect()) as $driver)
                                <option value="{{ $driver->id }}"
                                        data-name="{{ $driver->name }}"
                                        @selected((string) old('driver_id') === (string) $driver->id)>
                                    {{ $driver->name }}{{ $driver->phone ? ' — ' . $driver->phone : '' }}
                                </option>
                            @endforeach
                            @if(auth()->user()->canPerformTireOperations() || auth()->user()->canEditDrivers())
                                <option value="__new__">➕ Register new driver…</option>
                            @endif
                        </select>
                        <input type="hidden" id="give_driver_name" name="driver_name" value="{{ old('driver_name') }}">
                        @error('driver_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('driver_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <div id="give-new-driver" class="mt-3 hidden">
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-3">
                                <p class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-plus mr-1"></i> Register New Driver
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input type="text" id="new_driver_name" class="form-input" placeholder="Full name *" autocomplete="off">
                                    <input type="text" id="new_driver_phone" class="form-input" placeholder="Phone" autocomplete="off">
                                    <input type="text" id="new_driver_license" class="form-input" placeholder="License number" autocomplete="off">
                                </div>
                                <div id="new-driver-error" class="text-sm text-red-500 mt-2" style="display: none;"></div>
                                <button type="button" id="new-driver-save" class="btn btn-primary mt-2">
                                    <i class="fas fa-user-plus mr-1"></i> Register &amp; Select
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Plate Number -->
                    <div class="mb-4">
                         <label for="give_plate" class="form-label required">Plate Number</label>
                         <div class="autocomplete-container">
                             <input type="text" id="give_plate" name="plate_number" value="{{ old('plate_number') }}" class="form-input" placeholder="Enter plate number..." autocomplete="off" required>
                             <div id="give-plate-results" class="autocomplete-results"></div>
                         </div>
                    </div>

                    <!-- Give Date -->
                    <div class="mb-4">
                        <label for="give_date" class="form-label required">Give Date</label>
                        <input type="date" id="give_date" name="give_date" value="{{ old('give_date', now()->format('Y-m-d')) }}" class="form-input" required>
                    </div>

                    <!-- Kilometer -->
                    <div class="mb-4">
                        <label for="give_km" class="form-label required">Kilometer</label>
                        <input type="number" id="give_km" name="given_kilometer" value="{{ old('given_kilometer') }}" class="form-input" placeholder="Enter current kilometer" min="0" required>
                        @error('given_kilometer')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo -->
                    <div class="mb-4">
                        <label for="give_photo" class="form-label required">Tire Photo</label>
                        <input type="file" id="give_photo" name="photo" class="form-input" accept="image/*" required>
                        <div id="give-photo-preview-container" class="mt-3 hidden">
                            <img id="give-photo-preview" src="" alt="Photo Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="give_notes" class="form-label">Notes</label>
                        <textarea id="give_notes" name="notes" rows="2" class="form-input" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="tire-modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeGiveModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="give-modal-submit">
                        <i class="fas fa-hand-holding mr-1"></i> Give Tire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Photo Lightbox --}}
<div id="photo-lightbox" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-[60] hidden"
     role="dialog" aria-modal="true" aria-label="Photo preview">
    <img id="photo-lightbox-img" src="" alt="Full Photo" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg">
    <button type="button" id="photo-lightbox-close" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300"
            aria-label="Close photo">&times;</button>
</div>

<!-- Stat detail modal -->
<div id="statModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="z-index: 1200" role="dialog" aria-modal="true" aria-labelledby="statModalTitle">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatModal()"></div>
    <div class="relative mx-auto mt-10 mb-10 w-full max-w-2xl px-4 max-h-[calc(100vh-5rem)] overflow-y-auto">
        <div class="bg-white rounded-xl shadow-xl">
            <div class="flex items-start justify-between px-5 py-4 border-b border-gray-200">
                <div>
                    <h3 id="statModalTitle" class="text-lg font-semibold text-gray-800"></h3>
                    <p id="statModalMeta" class="text-sm text-gray-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeStatModal()" aria-label="Close"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none p-2 -m-2">&times;</button>
            </div>
            <div class="p-5">
                <div id="statModalLoading" class="text-center py-10 text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                </div>
                <div id="statModalEmpty" class="hidden text-center py-10">
                    <i class="fas fa-inbox text-3xl text-gray-300"></i>
                    <p class="mt-3 text-gray-600">No records found.</p>
                </div>
                <div id="statModalList" class="hidden divide-y divide-gray-100"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    /* ============================================================
       SHARED HELPERS
       ============================================================ */
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function absoluteUrl(path) {
        if (!path) return '';
        if (/^https?:\/\//i.test(path) || path.startsWith('data:')) return path;
        return '/' + String(path).replace(/^\/+/, '');
    }

    function formatDate(value) {
        if (!value) return '\u2014';
        const d = new Date(value);
        if (isNaN(d.getTime())) return String(value);
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: '2-digit' });
    }

    function formatKm(value) {
        if (value === null || value === undefined || value === '') return '\u2014';
        const str = String(value).replace(/,/g, '');
        const n = Number(str);
        return isFinite(n) ? n.toLocaleString() : '\u2014';
    }

    /* ============================================================
       TOOLBAR: auto-submit on select change
       ============================================================ */
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            filterForm.querySelectorAll('[data-auto-submit]').forEach(function (sel) {
                sel.addEventListener('change', function () { filterForm.submit(); });
            });
        }
    });

    /* ============================================================
       TABLE: SORT / FILTER / PAGINATE
       ============================================================ */
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('tires-table');
        if (!table) return;

        const tbody       = table.querySelector('tbody');
        const allRows     = Array.from(tbody.querySelectorAll('tr'));
        const quickFilter = document.getElementById('quick-filter');
        const pageSizeSel = document.getElementById('page-size');
        const infoEl      = document.getElementById('pagination-info');
        const controlsEl  = document.getElementById('pagination-controls');

        let currentPage   = 1;
        let pageSize      = 10;
        let filteredRows  = [...allRows];
        let sortColumn    = -1;
        let sortDirection = 'asc';

        /* ---- Sorting ---- */
        const sortableHeaders = table.querySelectorAll('thead th[data-sort]');

        function handleSort(th) {
            const col = parseInt(th.dataset.sort, 10);
            if (sortColumn === col) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = col;
                sortDirection = 'asc';
            }
            updateSortIcons();
            currentPage = 1;
            render();
        }

        sortableHeaders.forEach(function (th) {
            th.addEventListener('click', function () { handleSort(th); });
            th.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleSort(th);
                }
            });
        });

        function updateSortIcons() {
            sortableHeaders.forEach(function (th) {
                const icon = th.querySelector('.sort-icon');
                const col  = parseInt(th.dataset.sort, 10);
                if (col === sortColumn) {
                    icon.className = 'fas fa-sort-' + (sortDirection === 'asc' ? 'up' : 'down') + ' sort-icon';
                    icon.style.opacity = '1';
                    icon.style.color = 'var(--epss-blue)';
                    th.setAttribute('aria-sort', sortDirection === 'asc' ? 'ascending' : 'descending');
                } else {
                    icon.className = 'fas fa-sort sort-icon';
                    icon.style.opacity = '0.35';
                    icon.style.color = '';
                    th.setAttribute('aria-sort', 'none');
                }
            });
        }

        function cellSortValue(row, col) {
            const cell = row.cells[col];
            if (!cell) return '';
            if (cell.dataset && cell.dataset.sortValue !== undefined) {
                return cell.dataset.sortValue;
            }
            return cell.textContent.trim();
        }

        function applySort(rows) {
            if (sortColumn < 0) return rows;
            return rows.slice().sort(function (a, b) {
                const aText = cellSortValue(a, sortColumn);
                const bText = cellSortValue(b, sortColumn);
                const aNum  = parseFloat(aText.replace(/[^0-9.-]/g, ''));
                const bNum  = parseFloat(bText.replace(/[^0-9.-]/g, ''));
                if (!isNaN(aNum) && !isNaN(bNum) && /^[\d.,\s-]+$/.test(aText) && /^[\d.,\s-]+$/.test(bText)) {
                    return sortDirection === 'asc' ? aNum - bNum : bNum - aNum;
                }
                return sortDirection === 'asc'
                    ? aText.localeCompare(bText)
                    : bText.localeCompare(aText);
            });
        }

        /* ---- Quick filter ---- */
        if (quickFilter) {
            quickFilter.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                filteredRows = allRows.filter(function (row) {
                    return Array.from(row.cells).some(function (c) {
                        return c.textContent.toLowerCase().includes(term);
                    });
                });
                currentPage = 1;
                render();
            });
        }

        /* ---- Page size ---- */
        if (pageSizeSel) {
            pageSizeSel.addEventListener('change', function () {
                pageSize = parseInt(this.value, 10) || 10;
                currentPage = 1;
                render();
            });
        }

        /* ---- Render ---- */
        function render() {
            const sorted     = applySort(filteredRows);
            const total      = sorted.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;

            const start    = (currentPage - 1) * pageSize;
            const end      = start + pageSize;
            const pageRows = sorted.slice(start, end);

            tbody.innerHTML = '';

            if (pageRows.length === 0) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 11;
                td.style.textAlign = 'center';
                td.style.padding = '2rem';
                td.style.color = '#94A3B8';
                td.textContent = 'No matching tires';
                tr.appendChild(td);
                tbody.appendChild(tr);

                infoEl.textContent = 'No results found';
                controlsEl.innerHTML = '';
                return;
            }

            pageRows.forEach(function (r) { tbody.appendChild(r); });

            infoEl.innerHTML = 'Showing <strong>' + (start + 1) + '&ndash;' +
                Math.min(end, total) + '</strong> of <strong>' + total + '</strong> results';

            controlsEl.innerHTML = '';
            if (totalPages > 1) {
                controlsEl.appendChild(makeBtn(
                    '<i class="fas fa-chevron-left" style="font-size:0.7rem;" aria-hidden="true"></i>',
                    currentPage === 1,
                    'Previous page',
                    function () { currentPage--; render(); }
                ));

                buildPageRange(currentPage, totalPages).forEach(function (p) {
                    if (p === '...') {
                        const dots = document.createElement('span');
                        dots.className = 'page-dots';
                        dots.textContent = '\u2026';
                        controlsEl.appendChild(dots);
                    } else {
                        const btn = makeBtn(String(p), false, 'Page ' + p, function () {
                            currentPage = p; render();
                        });
                        if (p === currentPage) {
                            btn.classList.add('active');
                            btn.setAttribute('aria-current', 'page');
                        }
                        controlsEl.appendChild(btn);
                    }
                });

                controlsEl.appendChild(makeBtn(
                    '<i class="fas fa-chevron-right" style="font-size:0.7rem;" aria-hidden="true"></i>',
                    currentPage === totalPages,
                    'Next page',
                    function () { currentPage++; render(); }
                ));
            }
        }

        function makeBtn(html, disabled, label, onClick) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'page-btn';
            btn.innerHTML = html;
            btn.disabled = disabled;
            if (label) btn.setAttribute('aria-label', label);
            btn.addEventListener('click', onClick);
            return btn;
        }

        function buildPageRange(current, total) {
            const range = [];
            for (let i = 1; i <= total; i++) {
                if (i === 1 || i === total || (i >= current - 1 && i <= current + 1)) {
                    range.push(i);
                } else if (i === current - 2 || i === current + 2) {
                    range.push('...');
                }
            }
            return Array.from(new Set(range));
        }

        render();

        /* ---- Delegated actions (survive re-render) ---- */
        table.addEventListener('click', function (e) {
            const historyBtn = e.target.closest('.act-history');
            if (historyBtn) {
                openHistoryModal(historyBtn.dataset.tireId, historyBtn.dataset.tireSerial);
                return;
            }
            const viewBtn = e.target.closest('.act-view');
            if (viewBtn) {
                e.preventDefault();
                openTireModal(viewBtn.dataset.tireId);
            }
        });
    });

    /* ============================================================
       HISTORY MODAL
       ============================================================ */
    let lastFocusedEl = null;

    function openHistoryModal(tireId, serialNumber) {
        const modal     = document.getElementById('history-modal');
        const serialEl  = document.getElementById('history-tire-serial');
        const contentEl = document.getElementById('history-content');
        if (!modal) return;

        lastFocusedEl = document.activeElement;
        serialEl.textContent = serialNumber;
        contentEl.innerHTML  = '<div class="history-loading">Loading history...</div>';
        modal.style.display  = 'flex';

        const closeBtn = modal.querySelector('.history-close');
        if (closeBtn) closeBtn.focus();

        const url = '{{ url('/tires') }}/' + encodeURIComponent(tireId) + '/history';

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                const rows = Array.isArray(data.history) ? data.history : [];
                let html = '<table class="history-table"><thead><tr>' +
                    '<th>Date</th><th>Type</th><th>Driver</th><th>KM</th><th>User</th>' +
                    '</tr></thead><tbody>';

                if (rows.length === 0) {
                    html += '<tr><td colspan="5" style="text-align:center;padding:2rem;">No history found</td></tr>';
                } else {
                    rows.forEach(function (item) {
                        const isGiven = item.type === 'given';
                        const color   = isGiven ? '#F59E0B' : '#10B981';
                        const label   = isGiven ? 'Given' : 'Returned';
                        const km      = (item.km !== null && item.km !== undefined)
                                          ? Number(item.km).toLocaleString()
                                          : '-';
                        html += '<tr>' +
                            '<td>' + escapeHtml(item.date) + '</td>' +
                            '<td><span style="color:' + color + ';font-weight:600;">' + label + '</span></td>' +
                            '<td>' + escapeHtml(item.driver) + '</td>' +
                            '<td>' + km + '</td>' +
                            '<td>' + escapeHtml(item.user) + '</td>' +
                        '</tr>';
                    });
                }
                html += '</tbody></table>';
                contentEl.innerHTML = html;
            })
            .catch(function (err) {
                contentEl.innerHTML = '<div style="padding:1rem;color:#EF4444;">' +
                    'Error loading history: ' + escapeHtml(err.message) + '</div>';
            });
    }

    function closeHistoryModal() {
        const modal = document.getElementById('history-modal');
        if (!modal) return;
        modal.style.display = 'none';
        if (lastFocusedEl && typeof lastFocusedEl.focus === 'function') {
            lastFocusedEl.focus();
        }
    }

    /* ============================================================
       GIVE MODAL
       ============================================================ */
    function openGiveModal() {
        const modal = document.getElementById('give-modal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeGiveModal() {
        const modal = document.getElementById('give-modal');
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Expose for inline onclick handlers
    window.openGiveModal = openGiveModal;
    window.closeGiveModal = closeGiveModal;

    document.addEventListener('DOMContentLoaded', function () {
        // History modal
        const histModal = document.getElementById('history-modal');
        if (histModal) {
            histModal.addEventListener('click', function (e) {
                if (e.target === histModal) closeHistoryModal();
            });
            const closeTop    = histModal.querySelector('.history-close');
            const closeFooter = histModal.querySelector('.history-close-btn');
            if (closeTop)    closeTop.addEventListener('click', closeHistoryModal);
            if (closeFooter) closeFooter.addEventListener('click', closeHistoryModal);
        }

        // Give modal
        const giveModal = document.getElementById('give-modal');
        if (giveModal) {
            giveModal.addEventListener('click', function (e) {
                if (e.target === giveModal) closeGiveModal();
            });
            const closeGiveBtn = giveModal.querySelector('.tire-modal-close');
            if (closeGiveBtn) closeGiveBtn.addEventListener('click', closeGiveModal);

            // Driver dropdown + inline registration
            const driverSelect = giveModal.querySelector('#give_driver');
            const driverNameHidden = giveModal.querySelector('#give_driver_name');
            const newDriverPanel = giveModal.querySelector('#give-new-driver');
            const newDriverName = giveModal.querySelector('#new_driver_name');
            const newDriverPhone = giveModal.querySelector('#new_driver_phone');
            const newDriverLicense = giveModal.querySelector('#new_driver_license');
            const newDriverError = giveModal.querySelector('#new-driver-error');
            const newDriverSave = giveModal.querySelector('#new-driver-save');

            function selectedDriverName() {
                if (!driverSelect || !driverSelect.value || driverSelect.value === '__new__') return '';
                const opt = driverSelect.options[driverSelect.selectedIndex];
                return opt ? (opt.dataset.name || opt.textContent.trim()) : '';
            }

            function syncDriverSelection() {
                const isNew = driverSelect && driverSelect.value === '__new__';
                if (driverNameHidden) driverNameHidden.value = isNew ? '' : selectedDriverName();
                if (newDriverPanel) newDriverPanel.classList.toggle('hidden', !isNew);
                if (isNew && newDriverName) newDriverName.focus();
            }

            if (driverSelect) {
                driverSelect.addEventListener('change', syncDriverSelection);
                syncDriverSelection();
            }

            function showNewDriverError(message) {
                if (!newDriverError) return;
                newDriverError.textContent = message || '';
                newDriverError.style.display = message ? 'block' : 'none';
            }

            function addDriverOption(driver) {
                if (!driverSelect) return;
                const value = String(driver.id);
                let option = Array.from(driverSelect.options).find(o => o.value === value);
                if (!option) {
                    option = document.createElement('option');
                    option.value = value;
                    const newOption = driverSelect.querySelector('option[value="__new__"]');
                    driverSelect.insertBefore(option, newOption || null);
                }
                option.dataset.name = driver.name;
                option.textContent = driver.name + (driver.phone ? ' — ' + driver.phone : '');
                driverSelect.value = value;
                syncDriverSelection();
            }

            if (newDriverSave) {
                newDriverSave.addEventListener('click', function () {
                    const name = ((newDriverName && newDriverName.value) || '').trim();
                    showNewDriverError('');
                    if (!name) {
                        showNewDriverError('Driver name is required.');
                        if (newDriverName) newDriverName.focus();
                        return;
                    }

                    newDriverSave.disabled = true;
                    const originalHtml = newDriverSave.innerHTML;
                    newDriverSave.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i> Registering...';

                    fetch('{{ route('drivers.quick-store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            name: name,
                            phone: ((newDriverPhone && newDriverPhone.value) || '').trim() || null,
                            license_number: ((newDriverLicense && newDriverLicense.value) || '').trim() || null
                        })
                    })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            const first = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Could not register the driver.');
                            showNewDriverError(first);
                            return;
                        }
                        addDriverOption(data);
                        if (newDriverName) newDriverName.value = '';
                        if (newDriverPhone) newDriverPhone.value = '';
                        if (newDriverLicense) newDriverLicense.value = '';
                    })
                    .catch(() => showNewDriverError('Could not register the driver. Please try again.'))
                    .finally(() => {
                        newDriverSave.disabled = false;
                        newDriverSave.innerHTML = originalHtml;
                    });
                });
            }

            // Photo preview in give modal
            const photoInput = giveModal.querySelector('#give_photo');
            const photoPreviewContainer = giveModal.querySelector('#give-photo-preview-container');
            const photoPreview = giveModal.querySelector('#give-photo-preview');

            if (photoInput && photoPreview && photoPreviewContainer) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            photoPreview.src = ev.target.result;
                            photoPreviewContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        photoPreviewContainer.classList.add('hidden');
                    }
                });
            }

            // Serial number validation - check if serial already exists
            const serialInput = giveModal.querySelector('#give_serial');
            let serialWarningEl = null;
            let serialTimeout;

            if (serialInput) {
                serialInput.addEventListener('input', function() {
                    const serial = this.value.trim();
                    clearTimeout(serialTimeout);
                    if (!serialWarningEl) {
                        serialWarningEl = document.createElement('div');
                        serialWarningEl.className = 'serial-warning';
                        serialInput.parentNode.insertBefore(serialWarningEl, serialInput.nextSibling);
                    }
                    if (serial.length < 3) {
                        serialWarningEl.style.display = 'none';
                        return;
                    }
                    serialTimeout = setTimeout(() => {
                        fetch('/api/tires/check-serial?serial=' + encodeURIComponent(serial), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.exists) {
                                serialWarningEl.innerHTML = '<span class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-triangle"></i> This serial number already exists in the system.</span>';
                                serialWarningEl.style.display = 'block';
                                serialInput.classList.add('border-red-500');
                            } else {
                                serialWarningEl.style.display = 'none';
                                serialInput.classList.remove('border-red-500');
                            }
                        });
                    }, 500);
                });
            }

             // Form submit - AJAX with inline validation errors
             const giveForm = giveModal.querySelector('#giveFormModal');
             const submitBtn = giveModal.querySelector('#give-modal-submit');
             if (giveForm && submitBtn) {
                 giveForm.addEventListener('submit', function(e) {
                     e.preventDefault();

                     // Clear previous errors
                     giveModal.querySelectorAll('.field-error').forEach(el => el.remove());
                     giveModal.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

                     // Client-side validation
                     let hasError = false;
                     const requiredFields = ['tire_serial', 'tire_type', 'plate_number', 'give_date', 'given_kilometer', 'photo'];
                     requiredFields.forEach(field => {
                         const input = giveForm.querySelector('[name="' + field + '"]');
                         if (input) {
                             const isEmpty = input.type === 'file' ? !input.files || input.files.length === 0 : !input.value.trim();
                             if (isEmpty) {
                                 showFieldError(input, 'This field is required.');
                                 hasError = true;
                             }
                         }
                     });

                     if (driverSelect) {
                         if (!driverSelect.value) {
                             showFieldError(driverSelect, 'Please select a driver.');
                             hasError = true;
                         } else if (driverSelect.value === '__new__') {
                             showFieldError(driverSelect, 'Register the new driver first, then select them.');
                             hasError = true;
                         }
                     }

                     // Check serial uniqueness
                     if (serialWarningEl && serialWarningEl.style.display === 'block') {
                         showFieldError(serialInput, 'This serial number already exists in the system.');
                         hasError = true;
                     }

                     if (hasError) {
                         return false;
                     }

                     submitBtn.disabled = true;
                     submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i> Saving...';

                     const formData = new FormData(giveForm);

                     fetch(giveForm.action, {
                         method: 'POST',
                         body: formData,
                         headers: {
                             'X-Requested-With': 'XMLHttpRequest',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                         },
                     })
                     .then(async (res) => {
                         const data = await res.json();

                         if (res.status === 422 && data.errors) {
                             // Display validation errors inline
                             Object.keys(data.errors).forEach(field => {
                                 const input = giveForm.querySelector('[name="' + field + '"]');
                                 if (input) {
                                     showFieldError(input, data.errors[field][0]);
                                 }
                             });
                         } else if (res.ok || res.status === 200) {
                             // Success - close modal and reload
                             closeGiveModal();
                             window.location.reload();
                         } else if (data.message) {
                             alert('Error: ' + data.message);
                         }
                     })
                     .catch(err => {
                         alert('An error occurred. Please try again.');
                     })
                     .finally(() => {
                         submitBtn.disabled = false;
                         submitBtn.innerHTML = '<i class="fas fa-hand-holding mr-1"></i> Give Tire';
                     });
                 });
             }

             function showFieldError(input, message) {
                 input.classList.add('border-red-500');
                 const errorEl = document.createElement('div');
                 errorEl.className = 'field-error text-red-500 text-sm mt-1';
                 errorEl.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>' + escapeHtml(message);
                 input.parentNode.insertBefore(errorEl, input.nextSibling);
             }
        }
    });

    /* ============================================================
       TIRE DETAIL MODAL (PROFESSIONAL VIEW)
       ============================================================ */
    let detailLastFocused = null;

    function openTireModal(tireId) {
        const modal = document.getElementById('tire-detail-modal');
        const body  = document.getElementById('tire-modal-body');
        if (!modal || !body) return;

        detailLastFocused = document.activeElement;

        body.innerHTML = '<div class="modal-empty">' +
            '<i class="fas fa-circle-notch fa-spin" style="font-size:1.5rem;color:#94A3B8;"></i>' +
            '<p style="margin-top:.75rem;">Loading tire details...</p></div>';

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            const closeBtn = modal.querySelector('.tire-modal-close');
            if (closeBtn) closeBtn.focus();
        }, 0);

        fetch('/api/tires/' + encodeURIComponent(tireId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (payload) {
            const tire = (payload && payload.data && typeof payload.data === 'object')
                ? payload.data
                : payload;
            renderTireModal(tire);
        })
        .catch(function (err) {
            body.innerHTML =
                '<div class="tire-modal-error">' +
                    '<i class="fas fa-exclamation-triangle"></i>' +
                    '<p><strong>Could not load tire details</strong></p>' +
                    '<p>' + escapeHtml(err.message) + '</p>' +
                    '<button type="button" class="btn-retry" data-tire-retry>' +
                        '<i class="fas fa-redo"></i> Retry' +
                    '</button>' +
                '</div>';
            const retry = body.querySelector('[data-tire-retry]');
            if (retry) retry.addEventListener('click', function () { openTireModal(tireId); });
        });
    }

    function renderTireModal(tire) {
        const body = document.getElementById('tire-modal-body');
        if (!body) return;

        tire = tire || {};

        // ---------------------------------------------------------------
        // BUSINESS RULES:
        //   • A tire is "registered" only when given to a driver.
        //     Photo captured → tire_gives.photo, label "የተሰጠው ጎማ".
        //   • On return, ANOTHER photo captured → label "የመለሰው ጎማ".
        //   • No tire.photo — only give/return photos.
        //   • Status: ONLY "In Use" or "Returned".
        // ---------------------------------------------------------------

        const currentGive = tire.current_give || tire.currentGive || null;
        const gives       = Array.isArray(tire.gives) ? tire.gives : [];

        const isGiven     = !!(tire.is_given_out || tire.isGivenOut);
        const hasReturn   = gives.some(function (g) { return g.is_returned; });

        let statusText, statusClass;
        if (isGiven) {
            statusText  = 'In Use';
            statusClass = 'in-use';
        } else if (hasReturn) {
            statusText  = 'Returned';
            statusClass = 'returned';
        } else {
            statusText  = 'In Use';
            statusClass = 'in-use';
        }

        // ---------------------------------------------------------------
        // Latest give + latest return records
        // ---------------------------------------------------------------
        const sortedGives = gives.slice().sort(function (a, b) {
            return (Number(b.id) || 0) - (Number(a.id) || 0);
        });

        const latestGiveRecord   = sortedGives.find(function (g) { return !g.is_returned; }) || null;
        const latestReturnRecord = sortedGives.find(function (g) { return  g.is_returned; }) || null;
        const latestAnyRecord    = sortedGives[0] || null;

        const givePhotoUrl   = latestGiveRecord   ? absoluteUrl(latestGiveRecord.photo)   : '';
        const returnPhotoUrl = latestReturnRecord ? absoluteUrl(latestReturnRecord.return_photo || latestReturnRecord.photo) : '';

        // ---------------------------------------------------------------
        // PHOTO GALLERY — both photos side by side
        // ---------------------------------------------------------------
        function photoTile(url, label, type, dateValue) {
            if (!url) {
                return '<div class="tire-photo-tile ' + type + ' tire-photo-tile-empty">' +
                    '<i class="fas fa-camera"></i>' +
                    '<span class="empty-label">' + escapeHtml(label) + '<br>' +
                        '<small>No photo</small></span>' +
                '</div>';
            }
            return '<div class="tire-photo-tile ' + type + '">' +
                '<a href="#" class="photo-open-link" data-photo="' + escapeHtml(url) + '" ' +
                   'aria-label="View ' + escapeHtml(label) + '">' +
                    '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(label) + '" class="tire-detail-photo">' +
                '</a>' +
                '<span class="tire-hero-photo-badge ' + type + '">' +
                    '<i class="fas fa-camera"></i> ' + escapeHtml(label) +
                '</span>' +
                (dateValue ? '<span class="tire-photo-date">' + escapeHtml(formatDate(dateValue)) + '</span>' : '') +
            '</div>';
        }

        const galleryHtml =
            '<div class="tire-photo-grid">' +
                photoTile(
                    givePhotoUrl,
                    'የተሰጠው ጎማ',
                    'given',
                    latestGiveRecord ? (latestGiveRecord.give_date || latestGiveRecord.created_at) : null
                ) +
                photoTile(
                    returnPhotoUrl,
                    'የመለሰው ጎማ',
                    'returned',
                     latestReturnRecord ? (latestReturnRecord.return_date || latestReturnRecord.give_date || latestReturnRecord.created_at) : null
                ) +
            '</div>';

        // ---------------------------------------------------------------
        // KM INFO
        // ---------------------------------------------------------------
        const initialKm  = Number(tire.initial_kilometer) || 0;
        const currentKm = latestReturnRecord
            ? Number(latestReturnRecord.current_kilometer || latestReturnRecord.given_kilometer || 0)
            : (latestAnyRecord ? Number(latestAnyRecord.given_kilometer || 0) : initialKm);
        const kmDiff     = currentKm - initialKm;
        const kmDiffSign = kmDiff >= 0 ? '+' : '';

        // ---------------------------------------------------------------
        // DRIVER / GIVER — persistent (from latest record)
        // ---------------------------------------------------------------
        const metaRecord = latestAnyRecord;

        const driverName = (metaRecord && metaRecord.driver && (metaRecord.driver.name || metaRecord.driver))
            || (metaRecord && metaRecord.driver_name)
            || null;
        const giverName  = (metaRecord && (metaRecord.givenBy || metaRecord.given_by))
            || (metaRecord && metaRecord.given_by_name)
            || null;
        const giverLabel = (giverName && typeof giverName === 'object')
            ? (giverName.name || '')
            : (giverName || '');
        const driverLabel = (driverName && typeof driverName === 'object')
            ? (driverName.name || '')
            : (driverName || '');

        // ---------------------------------------------------------------
        // INFO CARDS
        // ---------------------------------------------------------------
        const infoCardsHtml =
            '<div class="tire-info-grid">' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-barcode"></i> Serial</span>' +
                    '<span class="info-value mono">' + escapeHtml(tire.serial_number || '—') + '</span>' +
                '</div>' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-circle-notch"></i> Type</span>' +
                    '<span class="info-value">' + escapeHtml(tire.tire_type || '—') + '</span>' +
                '</div>' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-car"></i> Plate</span>' +
                    '<span class="info-value mono">' + escapeHtml(tire.plate_number || '—') + '</span>' +
                '</div>' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-user"></i> Driver</span>' +
                    '<span class="info-value">' + escapeHtml(driverLabel || '—') + '</span>' +
                '</div>' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-user-check"></i> Given By</span>' +
                    '<span class="info-value">' + escapeHtml(giverLabel || '—') + '</span>' +
                '</div>' +
                '<div class="tire-info-card">' +
                    '<span class="info-label"><i class="fas fa-info-circle"></i> Status</span>' +
                    '<span class="info-value"><span class="status-pill ' + statusClass + '">' +
                        '<span class="dot"></span> ' + statusText +
                    '</span></span>' +
                '</div>' +
            '</div>';

        // ---------------------------------------------------------------
        // KM CARDS
        // ---------------------------------------------------------------
        const kmCardsHtml =
            '<div class="tire-km-grid">' +
                '<div class="tire-km-card">' +
                    '<span class="km-label">Initial KM</span>' +
                    '<span class="km-value">' + formatKm(initialKm) + '</span>' +
                '</div>' +
                '<div class="tire-km-card">' +
                    '<span class="km-label">Current KM</span>' +
                    '<span class="km-value">' + formatKm(currentKm) + '</span>' +
                '</div>' +
                '<div class="tire-km-card ' + (kmDiff >= 0 ? 'positive' : 'negative') + '">' +
                    '<span class="km-label">Difference</span>' +
                    '<span class="km-value">' + kmDiffSign + formatKm(kmDiff) + '</span>' +
                '</div>' +
            '</div>';

        // ---------------------------------------------------------------
        // HISTORY TIMELINE
        // ---------------------------------------------------------------
        let historyHtml = '';
        if (gives.length === 0) {
            historyHtml =
                '<div class="give-empty">' +
                    '<i class="fas fa-info-circle"></i>' +
                    '<p>Tire has not been given out yet.</p>' +
                    '<p class="hint">A photo will be captured automatically when this tire is given to a driver.</p>' +
                '</div>';
        } else {
            historyHtml = '<div class="tire-timeline">' + sortedGives.map(function (g) {
                const returned   = !!g.is_returned;
                const itemClass  = returned ? 'returned' : 'out';
                const statusLbl  = returned ? 'Returned' : 'Given Out';
                const photoLabel = returned ? 'የመለሰው ጎማ' : 'የተሰጠው ጎማ';
                const givePhoto  = absoluteUrl(g.photo);
                const returnPhoto = absoluteUrl(g.return_photo || g.photo);
                const driver     = g.driver_name || (g.driver && g.driver.name) || '—';
                const giver      = g.given_by || (g.givenBy && g.givenBy.name) || '—';
                const km         = formatKm(g.given_kilometer);
                const date = returned ? (g.return_date || formatDate(g.give_date || g.created_at)) : formatDate(g.give_date || g.created_at);

                const timelinePhoto = returned ? returnPhoto : givePhoto;

                const photoHtml = timelinePhoto
                    ? '<a href="#" class="timeline-photo photo-open-link" data-photo="' + escapeHtml(timelinePhoto) + '">' +
                          '<img src="' + escapeHtml(timelinePhoto) + '" alt="' + escapeHtml(photoLabel) + '">' +
                          '<span class="timeline-photo-tag">' + escapeHtml(photoLabel) + '</span>' +
                      '</a>'
                    : '<div class="timeline-photo timeline-photo-empty">' +
                          '<i class="fas fa-camera-slash"></i>' +
                      '</div>';

                return '<div class="timeline-item ' + itemClass + '">' +
                    '<div class="timeline-dot ' + itemClass + '"></div>' +
                    '<div class="timeline-content">' +
                        '<div class="timeline-header">' +
                            '<span class="timeline-badge ' + itemClass + '">' + statusLbl + '</span>' +
                            '<span class="timeline-date">' + escapeHtml(date) + '</span>' +
                        '</div>' +
                        '<div class="timeline-meta">' +
                            '<span><i class="fas fa-user"></i> ' + escapeHtml(driver) + '</span>' +
                            '<span><i class="fas fa-user-check"></i> ' + escapeHtml(giver) + '</span>' +
                            '<span><i class="fas fa-tachometer-alt"></i> ' + escapeHtml(km) + ' km</span>' +
                        '</div>' +
                        photoHtml +
                    '</div>' +
                '</div>';
            }).join('') + '</div>';
        }

        // ---------------------------------------------------------------
        // FULL LAYOUT
        // ---------------------------------------------------------------
        body.innerHTML =
            '<div class="tire-detail-layout">' +

                // LEFT — Photos
                '<div class="tire-detail-left">' +
                    '<h3 class="tire-detail-section-title">' +
                        '<i class="fas fa-images"></i> Photos' +
                    '</h3>' +
                    galleryHtml +
                '</div>' +

                // RIGHT — Info + KM + History
                '<div class="tire-detail-right">' +
                    '<h3 class="tire-detail-section-title">' +
                        '<i class="fas fa-info-circle"></i> Tire Information' +
                    '</h3>' +
                    infoCardsHtml +

                    '<h3 class="tire-detail-section-title" style="margin-top:1.25rem;">' +
                        '<i class="fas fa-tachometer-alt"></i> Kilometer' +
                    '</h3>' +
                    kmCardsHtml +

                    '<h3 class="tire-detail-section-title" style="margin-top:1.25rem;">' +
                        '<i class="fas fa-history"></i> Give &amp; Return History' +
                    '</h3>' +
                    historyHtml +
                '</div>' +

            '</div>';

        // Wire photo links
        body.querySelectorAll('.photo-open-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = this.dataset.photo;
                if (url) openLightbox(url);
            });
        });
    }

    function closeTireModal() {
        const modal = document.getElementById('tire-detail-modal');
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
        if (detailLastFocused && typeof detailLastFocused.focus === 'function') {
            detailLastFocused.focus();
        }
    }

    /* ============================================================
       PHOTO LIGHTBOX
       ============================================================ */
    function openLightbox(photoUrl) {
        const lightbox = document.getElementById('photo-lightbox');
        const img      = document.getElementById('photo-lightbox-img');
        if (!lightbox || !img) return;
        img.src = absoluteUrl(photoUrl);
        lightbox.classList.remove('hidden');
        lightbox.setAttribute('tabindex', '-1');
        document.body.style.overflow = 'hidden';
        lightbox.focus();
    }

    function closeLightbox() {
        const lightbox = document.getElementById('photo-lightbox');
        const img      = document.getElementById('photo-lightbox-img');
        if (!lightbox) return;
        lightbox.classList.add('hidden');
        if (img) img.src = '';
        const detail = document.getElementById('tire-detail-modal');
        if (detail && detail.style.display !== 'none') {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    /* ============================================================
       MODAL WIRING
       ============================================================ */
    document.addEventListener('DOMContentLoaded', function () {
        const detailModal = document.getElementById('tire-detail-modal');
        if (detailModal) {
            detailModal.addEventListener('click', function (e) {
                if (e.target === detailModal) closeTireModal();
            });
            detailModal.querySelectorAll('.tire-modal-close, [data-tire-modal-close]').forEach(function (b) {
                b.addEventListener('click', closeTireModal);
            });
        }

        const lightbox = document.getElementById('photo-lightbox');
        if (lightbox) {
            lightbox.addEventListener('click', function (e) {
                if (e.target === lightbox) closeLightbox();
            });
        }
        const lightboxClose = document.getElementById('photo-lightbox-close');
        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    });

    /* ============================================================
       STAT CARD DETAIL MODAL
       ============================================================ */
    let statRequestSeq = 0;

    const statTones = {
        green: 'bg-green-100 text-green-800',
        amber: 'bg-amber-100 text-amber-800',
        red: 'bg-red-100 text-red-800',
        blue: 'bg-blue-100 text-blue-800',
        gray: 'bg-gray-100 text-gray-700',
    };

    function openStatModal(scope, title) {
        const modal = document.getElementById('statModal');
        if (!modal) return;

        document.getElementById('statModalTitle').textContent = title;
        document.getElementById('statModalMeta').textContent = '';
        document.getElementById('statModalList').innerHTML = '';
        document.getElementById('statModalList').classList.add('hidden');
        document.getElementById('statModalEmpty').classList.add('hidden');
        document.getElementById('statModalLoading').classList.remove('hidden');

        modal.classList.remove('hidden');
        loadStatSummary(scope);
    }

    function closeStatModal() {
        const modal = document.getElementById('statModal');
        if (modal) modal.classList.add('hidden');
    }

    async function loadStatSummary(scope) {
        const seq = ++statRequestSeq;

        try {
            const response = await fetch('{{ url('/tires/summary') }}/' + encodeURIComponent(scope), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) throw new Error(data.message || 'Unable to load details.');
            if (seq !== statRequestSeq) return;

            renderStatSummary(data);
        } catch (error) {
            if (seq !== statRequestSeq) return;
            document.getElementById('statModalLoading').classList.add('hidden');
            const empty = document.getElementById('statModalEmpty');
            empty.querySelector('p').textContent = error.message;
            empty.classList.remove('hidden');
        }
    }

    function renderStatSummary(data) {
        document.getElementById('statModalLoading').classList.add('hidden');
        document.getElementById('statModalMeta').textContent = (data.count ?? 0) + ' record(s)';

        const empty = document.getElementById('statModalEmpty');
        const list = document.getElementById('statModalList');
        const items = Array.isArray(data.items) ? data.items : [];

        if (!items.length) {
            empty.classList.remove('hidden');
            return;
        }

        list.innerHTML = items.map(function (item, index) {
            const title = item.url
                ? '<a href="' + escapeHtml(item.url) + '" class="font-medium text-[#1C75BC] hover:underline">' + escapeHtml(item.title) + '</a>'
                : '<span class="font-medium text-gray-800">' + escapeHtml(item.title) + '</span>';

            const badge = item.badge
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium ' +
                    (statTones[item.tone] || statTones.gray) + '">' + escapeHtml(item.badge) + '</span>'
                : '';

            return '<div class="flex items-start gap-3 py-2.5">' +
                '<div class="w-6 shrink-0 pt-0.5 text-right text-xs font-semibold text-gray-400">' + (index + 1) + '</div>' +
                '<div class="min-w-0 flex-1">' + title +
                (item.subtitle ? '<div class="text-sm text-gray-600 truncate">' + escapeHtml(item.subtitle) + '</div>' : '') +
                (item.meta ? '<div class="text-xs text-gray-500 mt-0.5">' + escapeHtml(item.meta) + '</div>' : '') +
                '</div>' +
                (badge ? '<div class="shrink-0">' + badge + '</div>' : '') +
                '</div>';
        }).join('');

        list.classList.remove('hidden');
    }

    window.openStatModal = openStatModal;
    window.closeStatModal = closeStatModal;

    /* Global Escape handler */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;

        const statModal = document.getElementById('statModal');
        if (statModal && !statModal.classList.contains('hidden')) {
            closeStatModal();
            return;
        }
        const lightbox = document.getElementById('photo-lightbox');
        if (lightbox && !lightbox.classList.contains('hidden')) {
            closeLightbox();
            return;
        }
        const detail = document.getElementById('tire-detail-modal');
        if (detail && detail.style.display !== 'none') {
            closeTireModal();
            return;
        }
        const history = document.getElementById('history-modal');
        if (history && history.style.display !== 'none') {
            closeHistoryModal();
            return;
        }
        const giveModal = document.getElementById('give-modal');
        if (giveModal && giveModal.style.display !== 'none') {
            closeGiveModal();
        }
    });

})();
</script>
@endsection