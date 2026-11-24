@extends('layouts.app')

@section('title', 'Give Tire')
@section('page-title', 'Give Tire')
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
        white-space: nowrap;
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

    /* ---------- layout ---------- */
    .tsr-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        min-width: 0;
    }
    @media (min-width: 1024px) {
        .tsr-form-grid { grid-template-columns: minmax(0, 1fr) minmax(0, 340px); }
    }
    .tsr-form-grid > * { min-width: 0; }

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
    .tsr-card-header h3 svg {
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
    .tsr-card-body { padding: 1.5rem; flex: 1; }
    .tsr-card-footer {
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

    /* ---------- form ---------- */
    .tsr-form-body {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.25rem;
    }
    .tsr-field.tsr-span-2 { grid-column: 1 / -1; }
    .tsr-field label.tsr-label-row {
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
    .tsr-optional { text-transform: none; font-weight: 400; letter-spacing: 0; }
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
    .tsr-input.tsr-invalid { border-color: var(--tsr-danger); }
    textarea.tsr-input { resize: vertical; min-height: 90px; }
    .tsr-help { font-size: 0.78rem; color: var(--tsr-text-light); margin-top: 0.35rem; }
    .tsr-error { font-size: 0.78rem; color: var(--tsr-danger); margin-top: 0.35rem; }

    .tsr-autocomplete { position: relative; }
    .tsr-autocomplete-results {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        box-shadow: 0 10px 25px -6px rgba(15, 23, 42, 0.15);
        max-height: 220px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    .tsr-autocomplete-results.tsr-open { display: block; }
    .tsr-autocomplete-item {
        padding: 0.6rem 0.85rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background-color 0.15s;
    }
    .tsr-autocomplete-item:hover,
    .tsr-autocomplete-item:focus { background: #f8fafc; outline: none; }
    .tsr-autocomplete-item .tsr-ac-main { font-weight: 600; color: var(--tsr-text-main); }
    .tsr-autocomplete-item .tsr-ac-sub { font-size: 0.75rem; color: var(--tsr-text-muted); margin-top: 0.1rem; }
    .tsr-autocomplete-item.tsr-ac-new .tsr-ac-main { color: var(--tsr-success); }

    /* ---------- upload ---------- */
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
    .tsr-upload.tsr-invalid { border-color: var(--tsr-danger); background: #fef2f2; }
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
    .tsr-upload-hint { display: block; font-weight: 400; font-size: 0.78rem; color: var(--tsr-text-light); margin-top: 0.15rem; }
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
    .tsr-photo-preview {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }
    .tsr-photo-preview img {
        width: 72px; height: 72px;
        object-fit: cover;
        border-radius: var(--tsr-radius-sm);
        border: 1px solid var(--tsr-border);
        cursor: zoom-in;
    }
    .tsr-photo-preview span { font-size: 0.78rem; color: var(--tsr-text-muted); }

    /* ---------- footer buttons ---------- */
    .tsr-footer-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
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
    .tsr-btn-cancel:hover { border-color: #cbd5e1; color: var(--tsr-text-main); }
    .tsr-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
        background: var(--tsr-primary);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .tsr-btn-submit:hover { background: #0369a1; }
    .tsr-btn-submit svg,
    .tsr-btn-cancel svg {
        width: 15px; height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- guidelines ---------- */
    .tsr-guide-list { display: flex; flex-direction: column; gap: 1.1rem; }
    .tsr-guide-item { display: flex; gap: 0.75rem; align-items: flex-start; }
    .tsr-guide-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }
    .tsr-guide-icon svg {
        width: 17px; height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .tsr-guide-icon.tsr-blue { background: var(--tsr-primary-light); color: var(--tsr-primary); }
    .tsr-guide-icon.tsr-green { background: #ecfdf5; color: #059669; }
    .tsr-guide-icon.tsr-amber { background: #fffbeb; color: #b45309; }
    .tsr-guide-item h4 { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.15rem; }
    .tsr-guide-item p { font-size: 0.82rem; color: var(--tsr-text-muted); line-height: 1.5; }
    .tsr-guide-stats {
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--tsr-border);
        display: flex;
        gap: 1.5rem;
    }
    .tsr-guide-stat .tsr-stat-value { font-size: 1.2rem; font-weight: 700; }
    .tsr-guide-stat .tsr-stat-label { font-size: 0.75rem; color: var(--tsr-text-muted); text-transform: uppercase; letter-spacing: 0.04em; }

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
        .tsr-form-body { grid-template-columns: 1fr; padding: 1.25rem; }
        .tsr-card-header, .tsr-card-footer { padding-left: 1.25rem; padding-right: 1.25rem; }
        .tsr-card-footer { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
<div class="tsr-page">
    <div class="tsr-dashboard">

        {{-- header --}}
        <div class="tsr-top-nav">
            <div class="tsr-title-area">
                <h1>Give Tire</h1>
                <p>Register a tire give operation to a driver.</p>
            </div>
            <div class="tsr-user-info">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                <br>
                <span class="tsr-user-badge">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- action bar --}}
        <div class="tsr-action-bar">
            <a href="{{ route('tires.return.list') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg> Outstanding Tires
            </a>
            <a href="{{ route('tires.index') }}" class="tsr-btn-action tsr-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg> Tire Inventory
            </a>
        </div>

        <div class="tsr-form-grid">

            {{-- form --}}
            <div class="tsr-card">
                <div class="tsr-card-header">
                    <h3>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                        Give Details
                    </h3>
                    <span class="tsr-count-badge">New Issue</span>
                </div>

                <form method="POST" action="{{ route('tires.give.store') }}" id="giveForm" enctype="multipart/form-data">
                    @csrf
                    <div class="tsr-form-body">

                        <div class="tsr-field">
                            <label for="driver_name" class="tsr-label-row">Driver <span class="tsr-required">*</span></label>
                            <div class="tsr-autocomplete">
                                <input type="text" id="driver_name" name="driver_name" value="{{ old('driver_name') }}"
                                       class="tsr-input @error('driver_name') tsr-invalid @enderror"
                                       placeholder="Start typing a driver name..." autocomplete="off" required>
                                <input type="hidden" id="driver_id" name="driver_id" value="{{ old('driver_id') }}">
                                <div id="driver-autocomplete-results" class="tsr-autocomplete-results" role="listbox"></div>
                            </div>
                            <p class="tsr-help">Pick an existing driver or type a new name to register one.</p>
                            @error('driver_name')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                            @error('driver_id')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field">
                            <label for="plate_number" class="tsr-label-row">Plate Number <span class="tsr-required">*</span></label>
                            <div class="tsr-autocomplete">
                                <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number') }}"
                                       class="tsr-input @error('plate_number') tsr-invalid @enderror"
                                       placeholder="Type to search plate numbers..." autocomplete="off" required>
                                <div id="plate-autocomplete-results" class="tsr-autocomplete-results" role="listbox"></div>
                            </div>
                            <p class="tsr-help">Type to search existing plates or enter a new one.</p>
                            @error('plate_number')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field">
                            <label for="tire_serial" class="tsr-label-row">Tire Serial Number <span class="tsr-required">*</span></label>
                            <input type="text" id="tire_serial" name="tire_serial" value="{{ old('tire_serial') }}"
                                   class="tsr-input @error('tire_serial') tsr-invalid @enderror"
                                   placeholder="Enter new serial number..." autocomplete="off" required>
                            <p class="tsr-help">Each serial number is unique and can only be issued once.</p>
                            @error('tire_serial')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                            @error('tire_id')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field">
                            <label for="tire_type" class="tsr-label-row">Tire Type <span class="tsr-required">*</span></label>
                            <select id="tire_type" name="tire_type" class="tsr-input @error('tire_type') tsr-invalid @enderror" required>
                                <option value="">Select Tire Type</option>
                                @foreach($tireTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ $category ?: 'All Types' }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->name }}" {{ old('tire_type') == $type->name ? 'selected' : '' }}>
                                                {{ $type->name }} {{ $type->season ? ' ('.$type->season.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @php $hasOther = $tireTypes->contains('name', 'Other'); @endphp
                                @if(!$hasOther)
                                    <option value="Other" {{ old('tire_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                @endif
                            </select>
                            @error('tire_type')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field">
                            <label for="give_date" class="tsr-label-row">Give Date <span class="tsr-required">*</span></label>
                            <input type="date" id="give_date" name="give_date" value="{{ old('give_date', now()->format('Y-m-d')) }}"
                                   class="tsr-input @error('give_date') tsr-invalid @enderror" required>
                            @error('give_date')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field">
                            <label for="given_kilometer" class="tsr-label-row">Kilometer <span class="tsr-required">*</span></label>
                            <input type="number" id="given_kilometer" name="given_kilometer" value="{{ old('given_kilometer') }}"
                                   class="tsr-input @error('given_kilometer') tsr-invalid @enderror"
                                   placeholder="Enter current kilometer" min="0" required>
                            <p class="tsr-help">Current odometer reading at the moment of issue.</p>
                            @error('given_kilometer')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field tsr-span-2">
                            <label for="photo" class="tsr-label-row">Tire Photo <span class="tsr-required">*</span></label>
                            <label class="tsr-upload @error('photo') tsr-invalid @enderror" for="photo">
                                <input type="file" id="photo" name="photo" accept="image/*" required>
                                <span class="tsr-upload-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </span>
                                <span class="tsr-upload-text">
                                    Click to upload a tire photo
                                    <span class="tsr-upload-hint">JPG or PNG up to 2 MB — required as evidence</span>
                                </span>
                                <span class="tsr-upload-file" id="photoName" hidden></span>
                            </label>
                            <div class="tsr-photo-preview" id="photoPreviewWrap" hidden>
                                <img id="photoPreview" src="" alt="Selected tire photo preview" data-lightbox="">
                                <span>Click the thumbnail to preview full size.</span>
                            </div>
                            @error('photo')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="tsr-field tsr-span-2">
                            <label for="notes" class="tsr-label-row">Notes <span class="tsr-optional">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="tsr-input @error('notes') tsr-invalid @enderror" placeholder="Enter any notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="tsr-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="tsr-card-footer">
                        <div>The tire is recorded as outstanding until it is returned.</div>
                        <div class="tsr-footer-actions">
                            <a href="{{ route('tires.index') }}" class="tsr-btn-cancel">Cancel</a>
                            <button type="submit" class="tsr-btn-submit">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/></svg> Give Tire
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- guidelines --}}
            <div class="tsr-card">
                <div class="tsr-card-header">
                    <h3>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        Guidelines
                    </h3>
                </div>
                <div class="tsr-card-body">
                    <div class="tsr-guide-list">
                        <div class="tsr-guide-item">
                            <span class="tsr-guide-icon tsr-blue">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </span>
                            <div>
                                <h4>Driver Selection</h4>
                                <p>Select a driver from the list, or type a new name and it will be registered automatically.</p>
                            </div>
                        </div>
                        <div class="tsr-guide-item">
                            <span class="tsr-guide-icon tsr-green">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                            </span>
                            <div>
                                <h4>Serial Number</h4>
                                <p>Serial numbers are unique — a serial that was issued before cannot be issued again.</p>
                            </div>
                        </div>
                        <div class="tsr-guide-item">
                            <span class="tsr-guide-icon tsr-amber">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </span>
                            <div>
                                <h4>Photo</h4>
                                <p>Upload a photo of the tire as evidence of its condition at the time of issue.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tsr-guide-stats">
                        <div class="tsr-guide-stat">
                            <div class="tsr-stat-value">{{ $drivers->count() }}</div>
                            <div class="tsr-stat-label">Drivers on file</div>
                        </div>
                        <div class="tsr-guide-stat">
                            <div class="tsr-stat-value">{{ $tireTypes->count() }}</div>
                            <div class="tsr-stat-label">Tire types</div>
                        </div>
                    </div>
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
    const driverInput = document.getElementById('driver_name');
    const driverIdInput = document.getElementById('driver_id');
    const driverResults = document.getElementById('driver-autocomplete-results');
    const plateInput = document.getElementById('plate_number');
    const plateResults = document.getElementById('plate-autocomplete-results');
    const photoInput = document.getElementById('photo');
    const photoName = document.getElementById('photoName');
    const previewWrap = document.getElementById('photoPreviewWrap');
    const previewImage = document.getElementById('photoPreview');
    const lightbox = document.getElementById('tireLightbox');
    const lightboxImage = document.getElementById('tireLightboxImage');

    let driverTimeout;
    let plateTimeout;

    function buildItem(main, sub, onPick, isNew) {
        const item = document.createElement('div');
        item.className = 'tsr-autocomplete-item' + (isNew ? ' tsr-ac-new' : '');
        item.setAttribute('role', 'option');
        item.tabIndex = 0;

        const mainEl = document.createElement('div');
        mainEl.className = 'tsr-ac-main';
        mainEl.textContent = main;
        item.appendChild(mainEl);

        if (sub) {
            const subEl = document.createElement('div');
            subEl.className = 'tsr-ac-sub';
            subEl.textContent = sub;
            item.appendChild(subEl);
        }

        item.addEventListener('mousedown', function (event) {
            event.preventDefault();
            onPick();
        });
        item.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                onPick();
            }
        });

        return item;
    }

    driverInput.addEventListener('input', function () {
        driverIdInput.value = '';
        const q = driverInput.value.trim();
        clearTimeout(driverTimeout);
        driverResults.classList.remove('tsr-open');
        if (q.length < 2) return;
        driverTimeout = setTimeout(function () {
            fetch('/api/drivers/search?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(function (response) { return response.json(); })
                .then(renderDrivers)
                .catch(function () { driverResults.classList.remove('tsr-open'); });
        }, 300);
    });

    function renderDrivers(drivers) {
        driverResults.innerHTML = '';
        drivers.forEach(function (driver) {
            driverResults.appendChild(buildItem(driver.name, driver.license_number || '', function () {
                driverInput.value = driver.name;
                driverIdInput.value = driver.id;
                driverResults.classList.remove('tsr-open');
            }));
        });
        driverResults.appendChild(buildItem('Add new: "' + driverInput.value.trim() + '"', 'Creates a new driver record', function () {
            driverIdInput.value = '';
            driverResults.classList.remove('tsr-open');
        }, true));
        driverResults.classList.add('tsr-open');
    }

    plateInput.addEventListener('input', function () {
        const q = plateInput.value.trim();
        clearTimeout(plateTimeout);
        plateResults.classList.remove('tsr-open');
        if (q.length < 1) return;
        plateTimeout = setTimeout(function () {
            fetch('/api/tires/search?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(function (response) { return response.json(); })
                .then(renderPlates)
                .catch(function () { plateResults.classList.remove('tsr-open'); });
        }, 300);
    });

    function renderPlates(tires) {
        plateResults.innerHTML = '';
        const plates = Array.from(new Set(tires.map(function (t) { return t.plate_number; }).filter(Boolean)));
        plates.forEach(function (plate) {
            plateResults.appendChild(buildItem(plate, '', function () {
                plateInput.value = plate;
                plateResults.classList.remove('tsr-open');
            }));
        });
        plateResults.appendChild(buildItem('New plate: "' + plateInput.value.trim() + '"', 'Saved with this issue', function () {
            plateResults.classList.remove('tsr-open');
        }, true));
        plateResults.classList.add('tsr-open');
    }

    document.addEventListener('click', function (event) {
        if (!driverResults.contains(event.target) && event.target !== driverInput) {
            driverResults.classList.remove('tsr-open');
        }
        if (!plateResults.contains(event.target) && event.target !== plateInput) {
            plateResults.classList.remove('tsr-open');
        }
    });

    photoInput.addEventListener('change', function () {
        const file = photoInput.files[0];
        if (!file) {
            photoName.hidden = true;
            previewWrap.hidden = true;
            previewImage.removeAttribute('data-lightbox');
            return;
        }
        photoName.textContent = file.name;
        photoName.hidden = false;

        const reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = event.target.result;
            previewImage.setAttribute('data-lightbox', event.target.result);
            previewWrap.hidden = false;
        };
        reader.readAsDataURL(file);
    });

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
        el.addEventListener('click', function () {
            const src = el.getAttribute('data-lightbox');
            if (src) openLightbox(src);
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
