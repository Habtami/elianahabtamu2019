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
    .tsr-kpi-card.tsr-orange { border-left-color: #f59e0b; }

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

    /* ---------- form card ---------- */
    .tsr-form-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        overflow: hidden;
    }
    .tsr-form-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--tsr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .tsr-form-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tsr-form-header h3 svg {
        width: 20px; height: 20px;
        fill: none;
        stroke: var(--tsr-primary);
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-count-badge {
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
    }
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

    .tsr-form-body {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.25rem;
    }
    .tsr-field.tsr-span-2 { grid-column: 1 / -1; }
    .tsr-field label {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        color: var(--tsr-text-muted);
        margin-bottom: 0.4rem;
    }
    .tsr-required { color: var(--tsr-danger); }
    .tsr-input {
        width: 100%;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        padding: 0.65rem 0.85rem;
        font-size: 0.9rem;
        font-family: inherit;
        color: var(--tsr-text-main);
        background: #fff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .tsr-input:focus {
        border-color: var(--tsr-primary);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }
    .tsr-input[readonly] {
        background: #f8fafc;
        color: var(--tsr-text-muted);
        cursor: not-allowed;
    }
    .tsr-input.tsr-invalid { border-color: var(--tsr-danger); }
    textarea.tsr-input { resize: vertical; min-height: 90px; }
    .tsr-help {
        font-size: 0.78rem;
        color: var(--tsr-text-light);
        margin-top: 0.35rem;
    }
    .tsr-error {
        font-size: 0.78rem;
        color: var(--tsr-danger);
        margin-top: 0.35rem;
    }

    .tsr-upload {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        border: 1px dashed #cbd5e1;
        border-radius: var(--tsr-radius-sm);
        padding: 0.9rem 1rem;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .tsr-upload:hover { border-color: var(--tsr-primary); background: #f0f9ff; }
    .tsr-upload.tsr-invalid { border-color: var(--tsr-danger); background: var(--tsr-danger-bg); }
    .tsr-upload input[type="file"] { display: none; }
    .tsr-upload-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }
    .tsr-upload-icon svg {
        width: 18px; height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-upload-text { font-size: 0.85rem; font-weight: 600; color: var(--tsr-text-main); }
    .tsr-upload-text .tsr-upload-hint { display: block; font-weight: 400; font-size: 0.78rem; color: var(--tsr-text-light); margin-top: 0.15rem; }
    .tsr-upload-file {
        margin-left: auto;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--tsr-primary);
        background: var(--tsr-primary-light);
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        max-width: 45%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .tsr-form-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.85rem;
        color: var(--tsr-text-muted);
    }
    .tsr-form-footer .tsr-footer-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .tsr-btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
        background: #fff;
        color: var(--tsr-text-main);
        border: 1px solid var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .tsr-btn-cancel:hover { background: #fff; border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
        background: var(--tsr-success);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .tsr-btn-submit:hover { background: var(--tsr-success-hover); }
    .tsr-btn-submit svg,
    .tsr-btn-cancel svg {
        width: 15px; height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    @media (max-width: 700px) {
        .tsr-page { padding: 1rem; }
        .tsr-top-nav, .tsr-title-area { align-items: flex-start; }
        .tsr-user-info { text-align: left; }
        .tsr-form-body { grid-template-columns: 1fr; padding: 1.25rem; }
        .tsr-form-footer { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
@php
    $daysOut = $tireGive->give_date ? (int) $tireGive->give_date->diffInDays(now()) : 0;
    $initialKm = $tireGive->tire->initial_kilometer ?? 0;
    $rollNo = str_pad((string) $tireGive->id, 2, '0', STR_PAD_LEFT);
@endphp

<div class="tsr-page">
    <div class="tsr-dashboard">

        {{-- header --}}
        <div class="tsr-top-nav">
            <div class="tsr-title-area">
                <h1>Return Tire</h1>
                <p>Record the return of an issued tire and close the assignment.</p>
            </div>
            <div class="tsr-user-info">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                <br>
                <span class="tsr-user-badge">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- action bar --}}
        <div class="tsr-action-bar">
            <a href="{{ route('tires.return.list') }}" class="tsr-btn-action tsr-btn-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg> Outstanding Tires
            </a>
            <a href="{{ route('tires.show', $tireGive->tire) }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg> Tire Record
            </a>
        </div>

        {{-- KPI cards --}}
        <div class="tsr-kpi-grid">
            <div class="tsr-kpi-card tsr-blue">
                <div class="tsr-kpi-content">
                    <h4>Tire Serial</h4>
                    <div class="tsr-value tsr-value-text">{{ $tireGive->tire->serial_number }}</div>
                    <div class="tsr-sub">{{ $tireGive->tire->tire_type ?: 'Unknown type' }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#3b82f6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-purple">
                <div class="tsr-kpi-content">
                    <h4>Driver</h4>
                    <div class="tsr-value tsr-value-text">{{ $tireGive->driver->name ?? 'Unknown driver' }}</div>
                    <div class="tsr-sub">{{ $tireGive->driver?->phone ?: 'No phone recorded' }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#8b5cf6;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="tsr-kpi-card {{ $daysOut > 30 ? 'tsr-red' : 'tsr-orange' }}">
                <div class="tsr-kpi-content">
                    <h4>Days Out</h4>
                    <div class="tsr-value">{{ $daysOut }}</div>
                    <div class="tsr-sub">{{ $daysOut > 30 ? 'Out more than 30 days' : 'Within the 30-day window' }}</div>
                </div>
                <div class="tsr-kpi-icon" style="color:{{ $daysOut > 30 ? '#ef4444' : '#f59e0b' }};"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
            <div class="tsr-kpi-card tsr-orange">
                <div class="tsr-kpi-content">
                    <h4>KM at Issue</h4>
                    <div class="tsr-value">{{ number_format($tireGive->given_kilometer ?? 0) }}</div>
                    <div class="tsr-sub">Registered when issued</div>
                </div>
                <div class="tsr-kpi-icon" style="color:#f59e0b;"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            </div>
        </div>

        {{-- return form --}}
        <div class="tsr-form-card">
            <div class="tsr-form-header">
                <h3>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    Return Details
                    <span class="tsr-count-badge">Roll {{ $rollNo }}</span>
                </h3>
                <span class="tsr-days-pill {{ $daysOut > 30 ? 'tsr-overdue' : '' }}">
                    {{ $daysOut }} {{ $daysOut === 1 ? 'day' : 'days' }} out
                </span>
            </div>

            <form method="POST" action="{{ route('tires.return.store', $tireGive) }}" id="returnForm" enctype="multipart/form-data">
                @csrf
                <div class="tsr-form-body">
                    <div class="tsr-field">
                        <label for="return_date">Return Date <span class="tsr-required">*</span></label>
                        <input type="date" id="return_date" name="return_date" value="{{ old('return_date', now()->format('Y-m-d')) }}"
                               class="tsr-input @error('return_date') tsr-invalid @enderror" required>
                        @error('return_date')
                            <p class="tsr-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="tsr-field">
                        <label for="current_kilometer">Current Kilometer <span class="tsr-required">*</span></label>
                        <input type="number" id="current_kilometer" name="current_kilometer" value="{{ old('current_kilometer') }}"
                               class="tsr-input @error('current_kilometer') tsr-invalid @enderror"
                               placeholder="Enter current kilometer" min="{{ $initialKm }}" required>
                        <p class="tsr-help">Must be greater than {{ number_format($initialKm) }} km (initial KM)</p>
                        @error('current_kilometer')
                            <p class="tsr-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="tsr-field">
                        <label for="registered_kilometer">Registered Kilometer</label>
                        <input type="number" id="registered_kilometer" name="registered_kilometer" value="{{ $tireGive->given_kilometer }}" class="tsr-input" readonly>
                        <p class="tsr-help">From the tire give record</p>
                    </div>

                    <div class="tsr-field">
                        <label for="km_difference">KM Difference</label>
                        <input type="number" id="km_difference" name="km_difference" value="{{ old('km_difference', 0) }}" class="tsr-input" readonly>
                        <p class="tsr-help">Auto-calculated: current − registered</p>
                    </div>

                    <div class="tsr-field tsr-span-2">
                        <label for="photo">Returned Tire Photo <span class="tsr-required">*</span></label>
                        <label class="tsr-upload @error('photo') tsr-invalid @enderror" for="photo" id="photoDrop">
                            <input type="file" id="photo" name="photo" accept="image/*" required>
                            <span class="tsr-upload-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </span>
                            <span class="tsr-upload-text">
                                Click to upload a return photo
                                <span class="tsr-upload-hint">JPG or PNG up to 2 MB — required as evidence</span>
                            </span>
                            <span class="tsr-upload-file" id="photoName" hidden></span>
                        </label>
                        @error('photo')
                            <p class="tsr-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="tsr-field tsr-span-2">
                        <label for="notes">Notes <span style="text-transform:none;font-weight:400;">(optional)</span></label>
                        <textarea id="notes" name="notes" rows="3" class="tsr-input @error('notes') tsr-invalid @enderror" placeholder="Enter any notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="tsr-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="tsr-form-footer">
                    <div>Completing the return closes this issue and frees the tire for reissue.</div>
                    <div class="tsr-footer-actions">
                        <a href="{{ route('tires.show', $tireGive->tire) }}" class="tsr-btn-cancel">Cancel</a>
                        <button type="submit" class="tsr-btn-submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg> Complete Return
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const currentInput = document.getElementById('current_kilometer');
    const registeredInput = document.getElementById('registered_kilometer');
    const differenceInput = document.getElementById('km_difference');
    const initialKm = {{ (int) $initialKm }};
    const photoInput = document.getElementById('photo');
    const photoName = document.getElementById('photoName');

    function updateDifference() {
        const current = parseInt(currentInput.value, 10) || 0;
        const registered = parseInt(registeredInput.value, 10) || 0;
        differenceInput.value = current - registered;
    }

    currentInput.addEventListener('input', function () {
        updateDifference();
        if (currentInput.value !== '' && (parseInt(currentInput.value, 10) || 0) <= initialKm) {
            currentInput.setCustomValidity('Current kilometer must be greater than ' + initialKm.toLocaleString() + ' km.');
        } else {
            currentInput.setCustomValidity('');
        }
    });

    photoInput.addEventListener('change', function () {
        if (photoInput.files.length > 0) {
            photoName.textContent = photoInput.files[0].name;
            photoName.hidden = false;
        } else {
            photoName.hidden = true;
        }
    });

    updateDifference();
})();
</script>
@endsection
