@extends('layouts.app')

@section('title', 'Battery Given and Return')
@section('page-title', 'Battery Given and Return')

@section('styles')
<style>
    .batt-wrapper { max-width: 1200px; margin: 0 auto; padding: 1.5rem 1rem 2rem; }

    .batt-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.3rem;
    }
    .batt-toolbar h2 { font-size: 1.35rem; font-weight: 700; color: #0f2a44; margin: 0; }

    /* BUTTONS (reference palette) */
    .batt-btn {
        background: #1a6fc4;
        color: #fff;
        border: none;
        padding: 0.6rem 1.1rem;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: background 0.2s;
    }
    .batt-btn:hover { background: #155a9e; }
    .batt-btn.secondary { background: #e8eff7; color: #1a4a73; }
    .batt-btn.secondary:hover { background: #d5e2f0; }
    .batt-btn.danger { background: #e05252; }
    .batt-btn.danger:hover { background: #c53e3e; }
    .batt-btn.small { padding: 0.35rem 0.7rem; font-size: 0.72rem; }

    /* CARD / TABLE */
    .batt-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.3rem;
        box-shadow: 0 2px 8px rgba(0, 30, 60, 0.06);
        border: 1px solid #e3ebf3;
        margin-bottom: 1.3rem;
    }
    .batt-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-bottom: 1rem;
    }
    .batt-card-head h3 { font-size: 1rem; margin: 0; color: #0f2a44; }

    .batt-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .batt-search input, .batt-search select {
        padding: 0.5rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.82rem;
        background: #fff;
        color: #1e293b;
    }
    .batt-search input:focus, .batt-search select:focus {
        outline: none;
        border-color: #1a6fc4;
        box-shadow: 0 0 0 3px rgba(26, 111, 196, 0.12);
    }

    .batt-table-wrap { overflow-x: auto; }
    .batt-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .batt-table th {
        background: #f3f7fc;
        color: #345572;
        text-align: left;
        padding: 0.65rem 0.6rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 2px solid #d7e2ee;
        white-space: nowrap;
    }
    .batt-table td {
        padding: 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: middle;
    }
    .batt-table tr:hover td { background: #f8fbff; }
    .batt-empty { text-align: center; color: #8ca3b8; padding: 1.5rem; font-style: italic; }

    .batt-status {
        font-size: 0.68rem;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-weight: 600;
        white-space: nowrap;
    }
    .batt-status.in-service { background: #dcfce7; color: #166534; }
    .batt-status.returned { background: #fef9c3; color: #854d0e; }

    /* MODAL */
    .batt-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 100;
        padding: 1rem;
    }
    .batt-modal-bg.open { display: flex; }
    .batt-modal {
        background: #fff;
        border-radius: 16px;
        padding: 1.6rem;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 45px rgba(10, 25, 45, 0.25);
    }
    .batt-modal h3 { margin: 0 0 1.2rem; color: #0f2a44; font-size: 1.1rem; }

    .batt-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .batt-field label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        color: #46617d;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .batt-field input, .batt-field select {
        width: 100%;
        padding: 0.6rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.85rem;
        background: #fff;
        color: #1e293b;
    }
    .batt-field input:focus, .batt-field select:focus {
        outline: none;
        border-color: #1a6fc4;
        box-shadow: 0 0 0 3px rgba(26, 111, 196, 0.12);
    }
    .batt-field-error {
        color: #b91c1c;
        font-size: 0.72rem;
        font-weight: 600;
        margin-top: 0.3rem;
    }
    .batt-form-actions { margin-top: 1.2rem; display: flex; gap: 0.6rem; }

    .batt-delete-msg {
        font-size: 0.9rem;
        color: #46617d;
        line-height: 1.5;
        margin: 0;
    }
    .batt-delete-msg strong { color: #0f2a44; }

    .batt-history-scroll {
        max-height: 55vh;
        overflow-y: auto;
        border: 1px solid #e3ebf3;
        border-radius: 10px;
        background: #f8fbff;
    }
    .batt-history-list { display: flex; flex-direction: column; gap: 0.6rem; padding: 0.9rem; }
    .batt-history-item {
        background: #fff;
        border: 1px solid #e3ebf3;
        border-radius: 10px;
        padding: 0.75rem 0.85rem;
    }
    .batt-history-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.35rem;
    }
    .batt-history-action { font-weight: 700; color: #0f2a44; font-size: 0.85rem; }
    .batt-history-date { font-size: 0.72rem; color: #6b8299; white-space: nowrap; }
    .batt-history-meta { font-size: 0.78rem; color: #46617d; margin-bottom: 0.3rem; }
    .batt-history-notes {
        font-size: 0.78rem;
        color: #334155;
        background: #f8fbff;
        border-left: 3px solid #1a6fc4;
        border-radius: 6px;
        padding: 0.35rem 0.5rem;
    }
    .batt-history-user { font-size: 0.7rem; color: #93a6b9; margin-top: 0.35rem; }
    .batt-history-user-name { color: #46617d; font-weight: 600; }
    .batt-history-role { color: #6b8299; }
    .batt-history-loading {
        padding: 1.2rem;
        text-align: center;
        color: #6b8299;
        font-size: 0.85rem;
    }

    .batt-errors {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 10px;
        padding: 0.7rem 0.9rem;
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    .batt-errors ul { margin: 0; padding-left: 1.1rem; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer,
        .batt-toolbar, .batt-card-head, .batt-search, .batt-btn,
        .batt-modal-bg, .batt-actions, .no-print { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .batt-wrapper { max-width: none; margin: 0; padding: 0; }
        .batt-card { box-shadow: none; border: none; border-radius: 0; padding: 0; margin: 0; }
        .batt-table-wrap { overflow: visible; }
        .batt-table { width: 100%; font-size: 9pt; }
        .batt-table thead { display: table-header-group; }
        .batt-table th, .batt-table td { border: 1px solid #9aa9b8; padding: 0.3rem 0.35rem; }
        .batt-table tr:hover td { background: transparent; }
        .batt-status { padding: 0; border-radius: 0; background: transparent !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
<div class="batt-wrapper">

    <div class="batt-toolbar">
        <h2>🔋 Battery Given and Return</h2>
        <div class="no-print" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <button type="button" class="batt-btn secondary" onclick="window.print()">🖨️ Print</button>
            @if(auth()->user()->canExportBatteries())
            <a href="{{ route('batteries.export.excel', request()->only(['search', 'brand'])) }}" class="batt-btn secondary" style="text-decoration:none;">⬇️ Excel</a>
            <a href="{{ route('batteries.export.pdf', request()->only(['search', 'brand'])) }}" class="batt-btn secondary" style="text-decoration:none;">📄 PDF</a>
            @endif
            @if(auth()->user()->canViewBatteryReports())
                <a href="{{ route('batteries.report') }}" class="batt-btn secondary" style="text-decoration:none;">📋 Report</a>
            @endif
            @if(auth()->user()->canGiveBatteries())
            <button type="button" class="batt-btn" onclick="openBatteryModal()">+ Give Battery</button>
            @endif
        </div>
    </div>

    <div class="batt-card">
        <div class="batt-card-head">
            <h3>Given Batteries <span style="color:#6b8299; font-weight:400; font-size:0.8rem;">({{ $totalBatteries }} total &middot; {{ $inServiceBatteries }} in service &middot; {{ $returnedBatteries }} returned)</span></h3>

            <form method="GET" action="{{ route('batteries.index') }}" class="batt-search no-print">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search serial, type, plate, driver...">
                <select name="brand">
                    <option value="">All Brands</option>
                    @foreach($brands as $key => $label)
                        <option value="{{ $key }}" {{ request('brand') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="per_page">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }} per page</option>
                    @endforeach
                </select>
                <button type="submit" class="batt-btn small">Search</button>
                @if(request()->has('search') || request()->has('brand'))
                    <a href="{{ route('batteries.index') }}" class="batt-btn secondary small" style="text-decoration:none;">Clear</a>
                @endif
            </form>
        </div>

        <div class="batt-table-wrap">
            <table class="batt-table">
                <thead>
                    <tr>
                        <th>Date Mfg</th>
                        <th>Serial No</th>
                        <th>Material Code</th>
                        <th>Battery Type</th>
                        <th>Brand</th>
                        <th>Plate No</th>
                        <th>Driver</th>
                        <th>Initial KM</th>
                        <th>Return KM</th>
                        <th>Difference</th>
                        <th>Status</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batteries as $battery)
                    <tr>
                        <td>{{ $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '—' }}</td>
                        <td style="font-weight:600;">{{ $battery->serial_number }}</td>
                        <td>{{ $battery->material_code ?: '—' }}</td>
                        <td>{{ $battery->battery_type }}</td>
                        <td>{{ $battery->brand }}</td>
                        <td>{{ $battery->plate_number }}</td>
                        <td>{{ $battery->driver_name ?: '—' }}</td>
                        <td>{{ number_format($battery->initial_kilometer) }}</td>
                        <td>{{ number_format($battery->return_kilometer) }}</td>
                        <td>
                            @if($battery->status === 'returned' || $battery->return_kilometer > $battery->initial_kilometer)
                                @php
                                    $difference = $battery->return_kilometer - $battery->initial_kilometer;
                                @endphp
                                <span style="font-weight:600; color:{{ $difference >= 0 ? '#166534' : '#b91c1c' }};">
                                    {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference) }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="batt-status {{ $battery->status === 'returned' ? 'returned' : 'in-service' }}">
                                {{ $battery->getStatusDisplayName() }}
                            </span>
                        </td>
                        <td class="no-print batt-actions" style="white-space:nowrap;">
                            <button type="button"
                                    class="batt-btn secondary small"
                                    data-action="{{ route('batteries.history', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    onclick="openHistoryModal(this)">History</button>
                            @if(auth()->user()->canReturnBatteries() && !$battery->isReturned())
                            <button type="button"
                                    class="batt-btn small"
                                    data-action="{{ route('batteries.return', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    data-init="{{ $battery->initial_kilometer }}"
                                    onclick="openReturnModal(this)">Return</button>
                            @endif
                            @if(auth()->user()->canEditBatteries())
                            <button type="button"
                                    class="batt-btn secondary small"
                                    data-action="{{ route('batteries.update', $battery) }}"
                                    data-date="{{ $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '' }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    data-material="{{ $battery->material_code }}"
                                    data-type="{{ $battery->battery_type }}"
                                    data-brand="{{ $battery->brand }}"
                                    data-plate="{{ $battery->plate_number }}"
                                    data-driver="{{ $battery->driver_name }}"
                                    data-init="{{ $battery->initial_kilometer }}"
                                    data-ret="{{ $battery->return_kilometer }}"
                                    data-status="{{ $battery->status }}"
                                    onclick="editBattery(this)">Edit</button>
                            @endif
                            @if(auth()->user()->canDeleteBatteries())
                            <button type="button"
                                    class="batt-btn danger small"
                                    data-action="{{ route('batteries.destroy', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    onclick="openDeleteModal(this)">Del</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="batt-empty">No batteries given yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batteries->hasPages())
        <div class="no-print" style="margin-top:1rem;">
            {{ $batteries->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- MODAL: BATTERY -->
<div class="batt-modal-bg {{ $errors->any() && !$errors->has('return_serial') && !$errors->has('returned_kilometer') ? 'open' : '' }}" id="battModal">
    <div class="batt-modal">
        <h3 id="battModalTitle">🔋 Give Battery</h3>

        @if($errors->any())
        <div class="batt-errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @php $editingId = old('_battery_id'); @endphp
        <form method="POST"
              action="{{ $editingId ? route('batteries.update', $editingId) : route('batteries.store') }}"
              id="batteryForm"
              data-store-url="{{ route('batteries.store') }}">
            @csrf
            <input type="hidden" name="_battery_id" id="batteryId" value="{{ $editingId }}">
            <input type="hidden" name="_method" id="batteryMethod" value="PUT" {{ $editingId ? '' : 'disabled' }}>

            <div class="batt-form-grid">
                <div class="batt-field">
                    <label>Date Manufacturer</label>
                    <input type="date" name="date_of_manufacture" id="b_date" value="{{ old('date_of_manufacture') }}">
                </div>
                <div class="batt-field">
                    <label>Serial No</label>
                    <input name="serial_number" id="b_serial" value="{{ old('serial_number') }}" placeholder="BAT-0000" required>
                </div>
                <div class="batt-field">
                    <label>Material Code</label>
                    <select name="material_code" id="b_material" onchange="syncBatteryTypeFromMaterial()">
                        <option value="">Select material code</option>
                        @php $listedCodes = []; @endphp
                        @foreach($batteryTypes as $type)
                            @if($type['material_code'] && !in_array($type['material_code'], $listedCodes, true))
                                @php $listedCodes[] = $type['material_code']; @endphp
                                <option value="{{ $type['material_code'] }}"
                                        data-type="{{ $type['name'] }}"
                                        {{ old('material_code') === $type['material_code'] ? 'selected' : '' }}>
                                    {{ $type['material_code'] }} — {{ $type['name'] }}
                                </option>
                            @endif
                        @endforeach
                        <option value="__new__" {{ old('material_code') === '__new__' ? 'selected' : '' }}>+ Register new material code...</option>
                    </select>
                </div>
                <div class="batt-field">
                    <label>Battery Type</label>
                    <select name="battery_type" id="b_type" onchange="syncBatteryMaterial()" required>
                        <option value="">Select battery type</option>
                        @foreach($batteryTypes as $type)
                            <option value="{{ $type['name'] }}"
                                    data-code="{{ $type['material_code'] }}"
                                    {{ old('battery_type') === $type['name'] ? 'selected' : '' }}>
                                {{ $type['name'] }}{{ $type['material_code'] ? ' (' . $type['material_code'] . ')' : '' }}
                            </option>
                        @endforeach
                        <option value="__new__" {{ old('battery_type') === '__new__' ? 'selected' : '' }}>+ Register new type...</option>
                    </select>
                </div>
                <div class="batt-field" id="newTypeFields" style="display:none;">
                    <label>New Type Name</label>
                    <input name="new_battery_type" id="b_new_type" value="{{ old('new_battery_type') }}" placeholder="e.g. Car Battery 12V 85A">
                </div>
                <div class="batt-field" id="newTypeCodeField" style="display:none;">
                    <label>New Type Material Code</label>
                    <input name="new_battery_type_code" id="b_new_type_code" value="{{ old('new_battery_type_code') }}" placeholder="e.g. 1000000503">
                </div>
                <div class="batt-field">
                    <label>Battery Brand</label>
                    <select name="brand" id="b_brand" onchange="toggleBatteryBrandFields()">
                        @foreach($brands as $key => $label)
                            <option value="{{ $key }}" {{ old('brand') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                        <option value="__new__" {{ old('brand') === '__new__' ? 'selected' : '' }}>+ Register new brand...</option>
                    </select>
                </div>
                <div class="batt-field" id="newBrandField" style="display:none;">
                    <label>New Brand Name</label>
                    <input name="new_battery_brand" id="b_new_brand" value="{{ old('new_battery_brand') }}" placeholder="e.g. Yuasa">
                </div>
                <div class="batt-field">
                    <label>Plate No</label>
                    <input name="plate_number" id="b_plate" value="{{ old('plate_number') }}" placeholder="AA-123-BB" required>
                </div>
                <div class="batt-field">
                    <label>Driver Name</label>
                    <select name="driver_name" id="b_driver" onchange="toggleBatteryDriverFields()">
                        <option value="">Select driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->name }}" {{ old('driver_name') === $driver->name ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                        <option value="__new__" {{ old('driver_name') === '__new__' ? 'selected' : '' }}>+ Register new driver...</option>
                    </select>
                </div>
                <div class="batt-field" id="newDriverField" style="display:none;">
                    <label>New Driver Name</label>
                    <input name="new_driver_name" id="b_new_driver" value="{{ old('new_driver_name') }}" placeholder="e.g. Abebe Kebede">
                </div>
                <div class="batt-field">
                    <label>Initial KM</label>
                    <input type="number" name="initial_kilometer" id="b_initkm" value="{{ old('initial_kilometer', 0) }}" min="0">
                </div>
                <div class="batt-field">
                    <label>Return KM</label>
                    <input type="number" name="return_kilometer" id="b_retkm" value="{{ old('return_kilometer', 0) }}" min="0">
                </div>
                <div class="batt-field">
                    <label>Difference (KM)</label>
                    <input type="text" id="b_diff" value="0" readonly style="background:#f3f7fc; font-weight:700; color:#166534;">
                    <div class="batt-field-error" id="b_diff_warning" style="display:none;">Return KM cannot be less than Initial KM.</div>
                </div>
                <div class="batt-field">
                    <label>Battery Status</label>
                    <select name="status" id="b_status">
                        @foreach($batteryStatuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', 'in_service') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="batt-form-actions">
                <button type="submit" class="batt-btn">💾 Save Battery</button>
                <button type="button" class="batt-btn secondary" onclick="closeBatteryModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: DELETE CONFIRMATION -->
<div class="batt-modal-bg" id="deleteModal">
    <div class="batt-modal" style="max-width: 420px;">
        <h3>🗑️ Delete Battery</h3>
        <p class="batt-delete-msg">
            Are you sure you want to delete battery <strong id="deleteSerial">—</strong>?
            This action cannot be undone.
        </p>
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="batt-form-actions">
                <button type="submit" class="batt-btn danger">Yes, Delete</button>
                <button type="button" class="batt-btn secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: RETURN BATTERY -->
<div class="batt-modal-bg {{ ($errors->has('return_serial') || $errors->has('returned_kilometer')) ? 'open' : '' }}" id="returnModal">
    <div class="batt-modal" style="max-width: 460px;">
        <h3>↩️ Return Battery</h3>
        <p class="batt-delete-msg">
            Registered serial: <strong id="returnSerialDisplay">—</strong><br>
            The battery being returned must carry this exact serial number. If it does not match, the return is rejected.
        </p>
        <form method="POST" action="{{ old('return_action') }}" id="returnForm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="return_action" id="returnActionInput" value="{{ old('return_action') }}">
            <input type="hidden" name="return_serial_expected" id="returnSerialInput" value="{{ old('return_serial_expected') }}">
            <input type="hidden" name="return_initial" id="return_initial" value="{{ old('return_initial', 0) }}">
            <div class="batt-form-grid" style="grid-template-columns: 1fr;">
                <div class="batt-field">
                    <label>Confirm Serial Number</label>
                    <input name="return_serial" id="return_serial" value="{{ old('return_serial') }}" placeholder="Type the serial number exactly as registered" autocomplete="off">
                    <div class="batt-field-error" id="return_serial_error" style="display:none;">Serial number does not match the registered battery. Return rejected.</div>
                    @error('return_serial')
                    <div class="batt-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="batt-field">
                    <label>Return KM</label>
                    <input type="number" name="returned_kilometer" id="returned_kilometer" value="{{ old('returned_kilometer') }}" min="0" placeholder="e.g. 87500">
                    <div class="batt-field-error" id="return_km_error" style="display:none;">Return KM cannot be less than Initial KM.</div>
                    @error('returned_kilometer')
                    <div class="batt-field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="batt-form-actions">
                <button type="submit" class="batt-btn">Confirm Return</button>
                <button type="button" class="batt-btn secondary" onclick="closeReturnModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: HISTORY -->
<div class="batt-modal-bg" id="historyModal">
    <div class="batt-modal" style="max-width: 760px;">
        <h3>🕘 Battery History: <span id="historySerial">—</span></h3>
        <div class="batt-history-scroll" id="historyContent">
            <div class="batt-history-loading">Loading…</div>
        </div>
        <div class="batt-form-actions">
            <button type="button" class="batt-btn secondary" onclick="closeHistoryModal()">Close</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const battModal = document.getElementById('battModal');
    const batteryForm = document.getElementById('batteryForm');
    const batteryId = document.getElementById('batteryId');
    const batteryMethod = document.getElementById('batteryMethod');
    const battModalTitle = document.getElementById('battModalTitle');
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const returnModal = document.getElementById('returnModal');
    const returnForm = document.getElementById('returnForm');
    const historyModal = document.getElementById('historyModal');

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatKm(value) {
        return (value === null || value === undefined) ? '—' : Number(value).toLocaleString();
    }

    function openHistoryModal(button) {
        const d = button.dataset;
        const content = document.getElementById('historyContent');

        document.getElementById('historySerial').textContent = d.serial || '';
        content.innerHTML = '<div class="batt-history-loading">Loading history…</div>';
        historyModal.classList.add('open');

        fetch(d.action, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                const rows = Array.isArray(data.history) ? data.history : [];
                let html = '<div class="batt-history-list">';

                if (rows.length === 0) {
                    html += '<div class="batt-history-loading">No history found</div>';
                } else {
                    rows.forEach(function (item) {
                        html += '<div class="batt-history-item">' +
                            '<div class="batt-history-top">' +
                                '<span class="batt-history-action">' + escapeHtml(item.action) + '</span>' +
                                '<span class="batt-history-date">' + escapeHtml(item.date) + '</span>' +
                            '</div>' +
                            '<div class="batt-history-meta">' +
                                'Status: ' + escapeHtml(item.status) +
                                ' &middot; Driver: ' + escapeHtml(item.driver) +
                                ' &middot; KM: ' + escapeHtml(formatKm(item.initial_km)) +
                                ' &rarr; ' + escapeHtml(formatKm(item.return_km)) +
                            '</div>' +
                            (item.notes ? '<div class="batt-history-notes">' + escapeHtml(item.notes) + '</div>' : '') +
                            '<div class="batt-history-user">by ' +
                                '<span class="batt-history-user-name">' + escapeHtml(item.user) + '</span>' +
                                (item.email ? ' &middot; ' + escapeHtml(item.email) : '') +
                                (item.role ? ' <span class="batt-history-role">(' + escapeHtml(item.role) + ')</span>' : '') +
                            '</div>' +
                        '</div>';
                    });
                }

                html += '</div>';
                content.innerHTML = html;
            })
            .catch(function (err) {
                content.innerHTML = '<div class="batt-history-loading" style="color:#b91c1c;">' +
                    'Error loading history: ' + escapeHtml(err.message) + '</div>';
            });
    }

    function closeHistoryModal() {
        historyModal.classList.remove('open');
    }

    function openDeleteModal(button) {
        deleteForm.action = button.dataset.action;
        document.getElementById('deleteSerial').textContent = button.dataset.serial || '';
        deleteModal.classList.add('open');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('open');
    }

    function openReturnModal(button) {
        const d = button.dataset;
        returnForm.action = d.action;
        document.getElementById('returnActionInput').value = d.action;
        document.getElementById('returnSerialInput').value = d.serial || '';
        document.getElementById('returnSerialDisplay').textContent = d.serial || '—';
        document.getElementById('return_initial').value = d.init || 0;
        document.getElementById('return_serial').value = '';
        document.getElementById('returned_kilometer').value = '';
        document.getElementById('return_serial_error').style.display = 'none';
        document.getElementById('return_km_error').style.display = 'none';
        returnModal.classList.add('open');
        setTimeout(function () { document.getElementById('return_serial').focus(); }, 50);
    }

    function closeReturnModal() {
        returnModal.classList.remove('open');
    }

    function openBatteryModal() {
        battModal.classList.add('open');
    }

    function closeBatteryModal() {
        battModal.classList.remove('open');
        resetBatteryForm();
    }

    function resetBatteryForm() {
        batteryForm.action = batteryForm.dataset.storeUrl;
        batteryId.value = '';
        batteryMethod.disabled = true;
        battModalTitle.textContent = '🔋 Give Battery';
        batteryForm.reset();
        document.getElementById('b_type').value = '';
        document.getElementById('b_material').value = '';
        document.getElementById('b_new_type').value = '';
        document.getElementById('b_new_type_code').value = '';
        document.getElementById('b_new_brand').value = '';
        document.getElementById('b_new_driver').value = '';
        document.getElementById('b_initkm').value = 0;
        document.getElementById('b_retkm').value = 0;
        document.getElementById('b_status').value = 'in_service';
        syncBatteryDifference();
        toggleBatteryTypeFields();
        toggleBatteryBrandFields();
        toggleBatteryDriverFields();
    }

    function toggleBatteryTypeFields() {
        const typeIsNew = document.getElementById('b_type').value === '__new__';
        const materialIsNew = document.getElementById('b_material').value === '__new__';
        const isNew = typeIsNew || materialIsNew;
        document.getElementById('newTypeFields').style.display = isNew ? '' : 'none';
        document.getElementById('newTypeCodeField').style.display = isNew ? '' : 'none';
        document.getElementById('b_new_type_code').required = materialIsNew;
    }

    function toggleBatteryBrandFields() {
        const isNew = document.getElementById('b_brand').value === '__new__';
        document.getElementById('newBrandField').style.display = isNew ? '' : 'none';
    }

    function toggleBatteryDriverFields() {
        const isNew = document.getElementById('b_driver').value === '__new__';
        document.getElementById('newDriverField').style.display = isNew ? '' : 'none';
    }

    function syncBatteryMaterial() {
        const select = document.getElementById('b_type');
        const option = select.options[select.selectedIndex];
        const material = document.getElementById('b_material');

        if (select.value === '__new__') {
            material.value = '__new__';
        } else {
            const code = option && option.dataset.code ? option.dataset.code : '';

            if (code && !Array.from(material.options).some(function (listed) { return listed.value === code; })) {
                const added = new Option(code + ' — ' + option.value, code);
                added.dataset.type = option.value;
                material.add(added);
            }

            material.value = code;
        }

        toggleBatteryTypeFields();
    }

    function syncBatteryTypeFromMaterial() {
        const material = document.getElementById('b_material');
        const select = document.getElementById('b_type');

        if (material.value === '__new__') {
            select.value = '__new__';
        } else {
            const option = material.options[material.selectedIndex];
            const typeName = option && option.dataset.type ? option.dataset.type : '';

            if (typeName && Array.from(select.options).some(function (listed) { return listed.value === typeName; })) {
                select.value = typeName;
            }
        }

        toggleBatteryTypeFields();
    }

    function syncBatteryDifference() {
        const initial = Number(document.getElementById('b_initkm').value) || 0;
        const returned = Number(document.getElementById('b_retkm').value) || 0;
        const difference = returned - initial;
        const invalid = returned > 0 && returned < initial;
        const field = document.getElementById('b_diff');

        field.value = (difference > 0 ? '+' : '') + difference.toLocaleString();
        field.style.color = (invalid || difference < 0) ? '#b91c1c' : '#166534';
        document.getElementById('b_diff_warning').style.display = invalid ? 'block' : 'none';
    }

    function editBattery(button) {
        const d = button.dataset;
        batteryForm.action = d.action;
        batteryId.value = d.action.split('/').pop();
        batteryMethod.disabled = false;
        battModalTitle.textContent = '🔋 Edit Battery';

        const select = document.getElementById('b_type');
        if (d.type && !Array.from(select.options).some(function (option) { return option.value === d.type; })) {
            const option = new Option(d.type, d.type, true, true);
            option.dataset.code = d.material || '';
            select.add(option);
        }
        select.value = d.type || '';

        const materialSelect = document.getElementById('b_material');
        if (d.material && !Array.from(materialSelect.options).some(function (option) { return option.value === d.material; })) {
            const materialOption = new Option(d.material + (d.type ? ' — ' + d.type : ''), d.material);
            if (d.type) {
                materialOption.dataset.type = d.type;
            }
            materialSelect.add(materialOption);
        }
        materialSelect.value = d.material || '';

        const driverSelect = document.getElementById('b_driver');
        if (d.driver && !Array.from(driverSelect.options).some(function (option) { return option.value === d.driver; })) {
            driverSelect.add(new Option(d.driver, d.driver, true, true));
        }
        driverSelect.value = d.driver || '';

        document.getElementById('b_date').value = d.date || '';
        document.getElementById('b_serial').value = d.serial || '';
        document.getElementById('b_brand').value = d.brand || '';
        document.getElementById('b_plate').value = d.plate || '';
        document.getElementById('b_initkm').value = d.init || 0;
        document.getElementById('b_retkm').value = d.ret || 0;
        document.getElementById('b_status').value = d.status || 'in_service';

        syncBatteryDifference();
        toggleBatteryTypeFields();
        toggleBatteryBrandFields();
        toggleBatteryDriverFields();

        openBatteryModal();
    }

    battModal.addEventListener('click', function (event) {
        if (event.target === battModal) {
            closeBatteryModal();
        }
    });

    deleteModal.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });

    returnModal.addEventListener('click', function (event) {
        if (event.target === returnModal) {
            closeReturnModal();
        }
    });

    returnForm.addEventListener('submit', function (event) {
        const expected = document.getElementById('returnSerialInput').value;
        const entered = document.getElementById('return_serial').value.trim();
        const initial = Number(document.getElementById('return_initial').value) || 0;
        const returned = Number(document.getElementById('returned_kilometer').value) || 0;

        if (entered !== expected) {
            event.preventDefault();
            document.getElementById('return_serial_error').style.display = 'block';
            document.getElementById('return_serial').focus();
            return;
        }

        document.getElementById('return_serial_error').style.display = 'none';

        if (returned < initial) {
            event.preventDefault();
            document.getElementById('return_km_error').style.display = 'block';
            document.getElementById('returned_kilometer').focus();
        }
    });

    document.getElementById('return_serial').addEventListener('input', function () {
        document.getElementById('return_serial_error').style.display = 'none';
    });
    document.getElementById('returned_kilometer').addEventListener('input', function () {
        document.getElementById('return_km_error').style.display = 'none';
    });

    historyModal.addEventListener('click', function (event) {
        if (event.target === historyModal) {
            closeHistoryModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (battModal.classList.contains('open')) {
                closeBatteryModal();
            }
            if (deleteModal.classList.contains('open')) {
                closeDeleteModal();
            }
            if (returnModal.classList.contains('open')) {
                closeReturnModal();
            }
            if (historyModal.classList.contains('open')) {
                closeHistoryModal();
            }
        }
    });

    document.getElementById('b_date').value = document.getElementById('b_date').value || new Date().toISOString().split('T')[0];

    document.getElementById('b_initkm').addEventListener('input', syncBatteryDifference);
    document.getElementById('b_retkm').addEventListener('input', syncBatteryDifference);
    syncBatteryDifference();

    batteryForm.addEventListener('submit', function (event) {
        const initial = Number(document.getElementById('b_initkm').value) || 0;
        const returned = Number(document.getElementById('b_retkm').value) || 0;
        if (returned > 0 && returned < initial) {
            event.preventDefault();
            document.getElementById('b_retkm').focus();
        }
    });

    toggleBatteryTypeFields();
    toggleBatteryBrandFields();
    toggleBatteryDriverFields();

    if (returnModal.classList.contains('open')) {
        returnForm.action = document.getElementById('returnActionInput').value;
        document.getElementById('returnSerialDisplay').textContent = document.getElementById('returnSerialInput').value || '—';
    }
</script>
@endsection
