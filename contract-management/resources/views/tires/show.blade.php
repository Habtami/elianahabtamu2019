@extends('layouts.app')

@section('title', 'Tire Details — ' . $tire->serial_number)
@section('page-title', 'Tire Details')
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
        --tsr-warning: #f59e0b;
        --tsr-warning-bg: #fffbeb;
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
    .tsr-title-area p .tsr-serial { color: var(--tsr-primary); font-weight: 700; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
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
        white-space: nowrap;
    }
    .tsr-btn-blue { background: var(--tsr-primary); color: #fff; }
    .tsr-btn-blue:hover { background: #0369a1; color: #fff; }
    .tsr-btn-green { background: var(--tsr-success); color: #fff; }
    .tsr-btn-green:hover { background: var(--tsr-success-hover); color: #fff; }
    .tsr-btn-white {
        background: #fff;
        color: var(--tsr-text-main);
        border-color: var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
    }
    .tsr-btn-white:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-disabled { opacity: 0.55; cursor: not-allowed; }
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
        gap: 0.75rem;
    }
    .tsr-kpi-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 0.85rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        border-left: 4px solid var(--tsr-border);
        overflow: hidden;
    }
    .tsr-kpi-card.tsr-blue { border-left-color: #3b82f6; }
    .tsr-kpi-card.tsr-purple { border-left-color: #8b5cf6; }
    .tsr-kpi-card.tsr-red { border-left-color: var(--tsr-danger); }
    .tsr-kpi-card.tsr-orange { border-left-color: var(--tsr-warning); }
    .tsr-kpi-card.tsr-green { border-left-color: var(--tsr-success); }

    .tsr-kpi-content { min-width: 0; }
    .tsr-kpi-content h4 {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: var(--tsr-text-muted);
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .tsr-kpi-content .tsr-value {
        font-size: 1.45rem;
        font-weight: 700;
        margin: 0.15rem 0;
        line-height: 1.1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tsr-kpi-content .tsr-value.tsr-value-text { font-size: 1.15rem; }
    .tsr-kpi-content .tsr-sub {
        font-size: 0.72rem;
        color: var(--tsr-text-light);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tsr-kpi-icon { opacity: 0.2; width: 26px; height: 26px; flex: none; }
    .tsr-kpi-icon svg { width: 100%; height: 100%; }

    /* ---------- layout grid ---------- */
    .tsr-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        min-width: 0;
    }
    @media (min-width: 1024px) {
        .tsr-detail-grid { grid-template-columns: minmax(0, 340px) minmax(0, 1fr); }
    }
    .tsr-detail-grid > * { min-width: 0; }

    /* ---------- cards ---------- */
    .tsr-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .tsr-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--tsr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .tsr-card-header h3 {
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
    .tsr-card-body { padding: 1.5rem; flex: 1; }
    .tsr-card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    /* ---------- status pills ---------- */
    .tsr-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .tsr-status-pill::before {
        content: "";
        display: inline-block;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .tsr-status-pill.tsr-in-use { background: var(--tsr-warning-bg); color: #b45309; }
    .tsr-status-pill.tsr-available { background: #ecfdf5; color: #047857; }
    .tsr-status-pill.tsr-inactive { background: #f1f5f9; color: var(--tsr-text-muted); }
    .tsr-status-pill.tsr-returned { background: #ecfdf5; color: #047857; }
    .tsr-status-pill.tsr-pending { background: var(--tsr-warning-bg); color: #b45309; }

    /* ---------- photo ---------- */
    .tsr-photo {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        border-radius: var(--tsr-radius-sm);
        border: 1px solid var(--tsr-border);
        background: #f8fafc;
        cursor: zoom-in;
        transition: filter 0.2s;
    }
    .tsr-photo:hover { filter: brightness(0.96); }
    .tsr-photo-empty {
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: var(--tsr-radius-sm);
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--tsr-text-light);
        font-size: 0.8rem;
    }
    .tsr-photo-empty svg {
        width: 36px; height: 36px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.6;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- spec rows ---------- */
    .tsr-spec-list { margin-top: 1.1rem; display: flex; flex-direction: column; }
    .tsr-spec-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 0.75rem;
        padding: 0.55rem 0;
        border-bottom: 1px dashed var(--tsr-border);
        font-size: 0.875rem;
    }
    .tsr-spec-row:last-child { border-bottom: 0; }
    .tsr-spec-label { color: var(--tsr-text-muted); flex-shrink: 0; }
    .tsr-spec-value {
        color: var(--tsr-text-main);
        font-weight: 600;
        text-align: right;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    /* ---------- timeline ---------- */
    .tsr-timeline { position: relative; padding-left: 2rem; }
    .tsr-timeline::before {
        content: "";
        position: absolute;
        left: 0.6rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 2px;
        background: linear-gradient(#bae6fd, #a7f3d0);
        border-radius: 2px;
    }
    .tsr-timeline-entry { position: relative; margin-bottom: 1rem; }
    .tsr-timeline-entry:last-child { margin-bottom: 0; }
    .tsr-timeline-entry::before {
        content: "";
        position: absolute;
        left: -1.68rem;
        top: 1.2rem;
        width: 12px; height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: var(--tsr-primary);
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
    }
    .tsr-timeline-entry.tsr-is-return::before {
        background: var(--tsr-success);
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .tsr-timeline-entry.tsr-is-pending::before {
        background: var(--tsr-warning);
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.22);
    }

    .tsr-history-card {
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        padding: 1rem;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .tsr-history-card:hover { border-color: #cbd5e1; box-shadow: var(--tsr-shadow-sm); }
    .tsr-history-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }
    .tsr-history-title { display: flex; align-items: center; gap: 0.7rem; min-width: 0; }
    .tsr-history-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }
    .tsr-history-icon svg {
        width: 17px; height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-history-icon.tsr-give { background: var(--tsr-primary-light); color: var(--tsr-primary); }
    .tsr-history-icon.tsr-return { background: #ecfdf5; color: #059669; }
    .tsr-history-title h4 { font-size: 0.95rem; font-weight: 700; }
    .tsr-history-title p { font-size: 0.82rem; color: var(--tsr-text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .tsr-history-title p strong { color: var(--tsr-text-main); font-weight: 600; }
    .tsr-history-date { font-size: 0.78rem; color: var(--tsr-text-light); white-space: nowrap; flex: none; }

    .tsr-history-meta {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .tsr-meta-grid { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .tsr-meta-label { display: block; font-size: 0.72rem; color: var(--tsr-text-muted); margin-bottom: 0.15rem; }
    .tsr-meta-value { font-size: 0.9rem; font-weight: 600; }
    .tsr-meta-value.tsr-positive { color: #047857; }
    .tsr-meta-value.tsr-negative { color: var(--tsr-danger); }

    .tsr-photo-thumb {
        width: 40px; height: 40px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--tsr-border);
        cursor: zoom-in;
        transition: opacity 0.2s;
    }
    .tsr-photo-thumb:hover { opacity: 0.85; }
    .tsr-photo-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--tsr-primary);
        text-decoration: none;
        cursor: zoom-in;
    }

    .tsr-btn-return {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        background: var(--tsr-success);
        color: #fff;
        border: none;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.2s;
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

    .tsr-btn-block {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        padding: 0.6rem 1rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .tsr-btn-block svg {
        width: 15px; height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-btn-block.tsr-primary { background: var(--tsr-primary); color: #fff; }
    .tsr-btn-block.tsr-primary:hover { background: #0369a1; color: #fff; }
    .tsr-btn-block.tsr-success { background: var(--tsr-success); color: #fff; }
    .tsr-btn-block.tsr-success:hover { background: var(--tsr-success-hover); color: #fff; }
    .tsr-btn-block.tsr-ghost { background: #fff; color: var(--tsr-text-main); border-color: var(--tsr-border); box-shadow: var(--tsr-shadow-sm); }
    .tsr-btn-block.tsr-ghost:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-block[disabled], .tsr-btn-block.tsr-disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    /* ---------- empty ---------- */
    .tsr-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--tsr-text-muted);
        font-size: 0.95rem;
    }
    .tsr-empty-icon {
        width: 56px; height: 56px;
        margin: 0 auto 0.75rem;
        border-radius: 50%;
        background: #f1f5f9;
        color: var(--tsr-text-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tsr-empty-icon svg {
        width: 28px; height: 28px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.6;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- lightbox ---------- */
    .tsr-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.82);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 2rem;
    }
    .tsr-lightbox.tsr-open { display: flex; }
    .tsr-lightbox img {
        max-width: 88vw;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        background: #fff;
    }
    .tsr-lightbox-close {
        position: absolute;
        top: 1.25rem;
        right: 1.5rem;
        width: 38px; height: 38px;
        border-radius: 50%;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tsr-lightbox-close:hover { background: rgba(255, 255, 255, 0.28); }

    @media (max-width: 700px) {
        .tsr-page { padding: 1rem; }
        .tsr-top-nav, .tsr-title-area { align-items: flex-start; }
        .tsr-user-info { text-align: left; }
        .tsr-card-body { padding: 1.25rem; }
        .tsr-card-header, .tsr-card-footer { padding-left: 1.25rem; padding-right: 1.25rem; }
    }
</style>
@endsection

@section('content')
@php
    $openGive = $tireGives->firstWhere('is_returned', false);
    $daysOut = $openGive && $openGive->give_date ? (int) $openGive->give_date->diffInDays(now()) : null;
    $historyCount = $tireGives->count() + $tireReturns->count();
    $statusLabel = $tire->isGivenOut() ? 'In Use' : ($tire->is_active ? 'Available' : 'Inactive');
    $statusClass = $tire->isGivenOut() ? 'tsr-in-use' : ($tire->is_active ? 'tsr-available' : 'tsr-inactive');
@endphp

<div class="tsr-page">
    <div class="tsr-dashboard">

        {{-- header --}}
        <div class="tsr-top-nav">
            <div class="tsr-title-area">
                <h1>Tire Details</h1>
                <p>Serial <span class="tsr-serial">{{ $tire->serial_number }}</span> · {{ $tire->tire_type ?: 'Unknown type' }}</p>
            </div>
            <div class="tsr-user-info">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                <br>
                <span class="tsr-user-badge">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- action bar --}}
        <div class="tsr-action-bar">
            @if($openGive && auth()->user()->canPerformTireOperations())
                <a href="{{ route('tires.return', $openGive) }}" class="tsr-btn-action tsr-btn-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Return Tire
                </a>
            @elseif(auth()->user()->canPerformTireOperations() && !$tire->isGivenOut())
                <a href="{{ route('tires.give', ['tire_id' => $tire->id]) }}" class="tsr-btn-action tsr-btn-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg> Give to Driver
                </a>
            @endif
            <a href="{{ route('tires.return.list') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg> Outstanding Tires
            </a>
            <a href="{{ route('tires.index') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg> Tire Inventory
            </a>
        </div>

        {{-- KPI cards --}}
        <div class="tsr-kpi-grid">
            <div class="tsr-kpi-card {{ $tire->isGivenOut() ? 'tsr-orange' : ($tire->is_active ? 'tsr-green' : 'tsr-blue') }}">
                <div class="tsr-kpi-content">
                    <h4>Status</h4>
                    <div class="tsr-value tsr-value-text">{{ $statusLabel }}</div>
                    <div class="tsr-sub">{{ $tire->isGivenOut() ? 'Currently issued to a driver' : ($tire->is_active ? 'Ready for issue' : 'Not in service') }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:{{ $tire->isGivenOut() ? '#f59e0b' : ($tire->is_active ? '#10b981' : '#3b82f6') }};"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-purple">
                <div class="tsr-kpi-content">
                    <h4>Current Driver</h4>
                    <div class="tsr-value tsr-value-text">{{ $openGive?->driver?->name ?? '—' }}</div>
                    <div class="tsr-sub">{{ $openGive ? ($openGive->driver?->phone ?: 'No phone recorded') : 'No tire issued' }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#8b5cf6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="tsr-kpi-card {{ $daysOut !== null && $daysOut > 30 ? 'tsr-red' : 'tsr-orange' }}">
                <div class="tsr-kpi-content">
                    <h4>Days Out</h4>
                    <div class="tsr-value">{{ $daysOut ?? '—' }}</div>
                    <div class="tsr-sub">{{ $daysOut === null ? 'Not issued right now' : ($daysOut > 30 ? 'Out more than 30 days' : 'Within the 30-day window') }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:{{ $daysOut !== null && $daysOut > 30 ? '#ef4444' : '#f59e0b' }};"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-orange">
                <div class="tsr-kpi-content">
                    <h4>Initial KM</h4>
                    <div class="tsr-value">{{ number_format($tire->initial_kilometer ?? 0) }}</div>
                    <div class="tsr-sub">Registered {{ $tire->created_at?->format('M d, Y') ?? '—' }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#f59e0b;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
        </div>

        {{-- detail grid --}}
        <div class="tsr-detail-grid">

            {{-- summary --}}
            <div class="tsr-card">
                <div class="tsr-card-header">
                    <h3>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>
                        Tire Summary
                    </h3>
                    <span class="tsr-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <div class="tsr-card-body">
                    @if($tire->photo)
                        <img src="{{ asset('storage/' . $tire->photo) }}" alt="Tire {{ $tire->serial_number }}" class="tsr-photo" data-lightbox="{{ asset('storage/' . $tire->photo) }}">
                    @else
                        <div class="tsr-photo-empty">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                            No photo on file
                        </div>
                    @endif

                    <div class="tsr-spec-list">
                        <div class="tsr-spec-row">
                            <span class="tsr-spec-label">Serial Number</span>
                            <span class="tsr-spec-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">{{ $tire->serial_number }}</span>
                        </div>
                        <div class="tsr-spec-row">
                            <span class="tsr-spec-label">Tire Type</span>
                            <span class="tsr-spec-value">{{ $tire->tire_type ?: 'N/A' }}</span>
                        </div>
                        <div class="tsr-spec-row">
                            <span class="tsr-spec-label">Plate Number</span>
                            <span class="tsr-spec-value">{{ $tire->plate_number ?: 'Not recorded' }}</span>
                        </div>
                        <div class="tsr-spec-row">
                            <span class="tsr-spec-label">Initial KM</span>
                            <span class="tsr-spec-value">{{ number_format($tire->initial_kilometer ?? 0) }} km</span>
                        </div>
                        <div class="tsr-spec-row">
                            <span class="tsr-spec-label">Registered</span>
                            <span class="tsr-spec-value">{{ $tire->created_at?->format('M d, Y') ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="tsr-card-footer">
                    @if($openGive && auth()->user()->canPerformTireOperations())
                        <a href="{{ route('tires.return', $openGive) }}" class="tsr-btn-block tsr-success">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Return Tire
                        </a>
                    @elseif(auth()->user()->canPerformTireOperations() && !$tire->isGivenOut())
                        <a href="{{ route('tires.give', ['tire_id' => $tire->id]) }}" class="tsr-btn-block tsr-primary">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg> Give to Driver
                        </a>
                    @endif
                    @if(auth()->user()->canManageTires() && !$tire->hasBeenReturned())
                        <a href="{{ route('tires.edit', $tire) }}" class="tsr-btn-block tsr-ghost">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg> Edit Tire
                        </a>
                    @elseif(auth()->user()->canManageTires() && $tire->hasBeenReturned())
                        <button type="button" class="tsr-btn-block tsr-ghost tsr-disabled" disabled
                                title="This tire has been returned and cannot be edited">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg> Edit Tire (Returned)
                        </button>
                    @endif
                </div>
            </div>

            {{-- history --}}
            <div class="tsr-card">
                <div class="tsr-card-header">
                    <h3>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Give &amp; Return History
                        <span class="tsr-count-badge">{{ $historyCount }}</span>
                    </h3>
                </div>
                <div class="tsr-card-body">
                    @if($historyCount === 0)
                        <div class="tsr-empty">
                            <div class="tsr-empty-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                            </div>
                            No give or return operations recorded for this tire.
                        </div>
                    @else
                        <div class="tsr-timeline">

                            {{-- gives --}}
                            @foreach($tireGives as $give)
                                <div class="tsr-timeline-entry {{ $give->is_returned ? '' : 'tsr-is-pending' }}">
                                    <div class="tsr-history-card">
                                        <div class="tsr-history-top">
                                            <div class="tsr-history-title">
                                                <span class="tsr-history-icon tsr-give">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                                                </span>
                                                <div style="min-width:0;">
                                                    <h4>Tire Given</h4>
                                                    <p>To: <strong>{{ $give->driver->name ?? 'N/A' }}</strong></p>
                                                </div>
                                            </div>
                                            <span class="tsr-history-date">{{ $give->give_date?->format('M d, Y') ?? '—' }}</span>
                                        </div>

                                        <div class="tsr-history-meta">
                                            <div class="tsr-meta-grid">
                                                <div>
                                                    <span class="tsr-meta-label">KM at Give</span>
                                                    <span class="tsr-meta-value">{{ number_format($give->given_kilometer ?? 0) }}</span>
                                                </div>
                                                @if($give->give_date)
                                                    <div>
                                                        <span class="tsr-meta-label">Day</span>
                                                        <span class="tsr-meta-value">{{ $give->give_date->format('l') }}</span>
                                                    </div>
                                                @endif
                                                @if($give->plate_number)
                                                    <div>
                                                        <span class="tsr-meta-label">Plate</span>
                                                        <span class="tsr-meta-value">{{ $give->plate_number }}</span>
                                                    </div>
                                                @endif
                                                @if($give->photo)
                                                    <div>
                                                        <span class="tsr-meta-label">Photo</span>
                                                        <span class="tsr-photo-link" data-lightbox="{{ asset('storage/' . $give->photo) }}" role="button" tabindex="0">
                                                            <img src="{{ asset('storage/' . $give->photo) }}" alt="Give photo" class="tsr-photo-thumb">
                                                            View
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                @if(!$give->is_returned)
                                                    @if(auth()->user()->canPerformTireOperations())
                                                        <a href="{{ route('tires.return', $give) }}" class="tsr-btn-return">
                                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Return Now
                                                        </a>
                                                    @else
                                                        <span class="tsr-status-pill tsr-pending">Pending Return</span>
                                                    @endif
                                                @else
                                                    <span class="tsr-status-pill tsr-returned">Returned</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- returns --}}
                            @foreach($tireReturns as $ret)
                                <div class="tsr-timeline-entry tsr-is-return">
                                    <div class="tsr-history-card">
                                        <div class="tsr-history-top">
                                            <div class="tsr-history-title">
                                                <span class="tsr-history-icon tsr-return">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                                </span>
                                                <div style="min-width:0;">
                                                    <h4>Tire Returned</h4>
                                                    <p>By: <strong>{{ $ret->returnedBy->name ?? 'N/A' }}</strong></p>
                                                </div>
                                            </div>
                                            <span class="tsr-history-date">{{ $ret->return_date?->format('M d, Y') ?? '—' }}</span>
                                        </div>

                                        <div class="tsr-history-meta">
                                            <div class="tsr-meta-grid">
                                                <div>
                                                    <span class="tsr-meta-label">Current KM</span>
                                                    <span class="tsr-meta-value tsr-positive">{{ number_format($ret->current_kilometer ?? 0) }}</span>
                                                </div>
                                                <div>
                                                    <span class="tsr-meta-label">Registered KM</span>
                                                    <span class="tsr-meta-value">{{ number_format($ret->registered_kilometer ?? 0) }}</span>
                                                </div>
                                                <div>
                                                    <span class="tsr-meta-label">KM Difference</span>
                                                    <span class="tsr-meta-value {{ ($ret->km_difference ?? 0) >= 0 ? 'tsr-positive' : 'tsr-negative' }}">
                                                        {{ ($ret->km_difference ?? 0) > 0 ? '+' : '' }}{{ number_format($ret->km_difference ?? 0) }}
                                                    </span>
                                                </div>
                                                @if($ret->photo)
                                                    <div>
                                                        <span class="tsr-meta-label">Photo</span>
                                                        <span class="tsr-photo-link" data-lightbox="{{ asset('storage/' . $ret->photo) }}" role="button" tabindex="0">
                                                            <img src="{{ asset('storage/' . $ret->photo) }}" alt="Return photo" class="tsr-photo-thumb">
                                                            View
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

{{-- lightbox --}}
<div class="tsr-lightbox" id="tireLightbox" aria-hidden="true">
    <button type="button" class="tsr-lightbox-close" id="tireLightboxClose" aria-label="Close photo">&times;</button>
    <img src="" alt="Photo preview" id="tireLightboxImage">
</div>
@endsection

@section('scripts')
<script>
(function () {
    const lightbox = document.getElementById('tireLightbox');
    const lightboxImage = document.getElementById('tireLightboxImage');

    function openLightbox(src) {
        lightboxImage.src = src;
        lightbox.classList.add('tsr-open');
        lightbox.setAttribute('aria-hidden', 'false');
    }

    function closeLightbox() {
        lightbox.classList.remove('tsr-open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImage.src = '';
    }

    document.querySelectorAll('[data-lightbox]').forEach(function (el) {
        el.addEventListener('click', function () { openLightbox(el.dataset.lightbox); });
        el.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openLightbox(el.dataset.lightbox);
            }
        });
    });

    document.getElementById('tireLightboxClose').addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && lightbox.classList.contains('tsr-open')) closeLightbox();
    });
})();
</script>
@endsection
