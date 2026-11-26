{{-- Shared "Type of Report" selector: jumps to any report the user can open. --}}
@php
    $reportTypeOptions = \App\Helpers\ReportTypes::forUser();
    $reportTypeSelected = $reportTypeCurrent ?? \App\Helpers\ReportTypes::currentKey();
@endphp
@if(count($reportTypeOptions) > 1)
<div class="type-of-report no-print">
    <label for="typeOfReport">Type of Report</label>
    <select id="typeOfReport" title="Switch to another report" onchange="if (this.value) { window.location.href = this.value; }">
        @foreach($reportTypeOptions as $key => $option)
            <option value="{{ $option['url'] }}" @selected($key === $reportTypeSelected)>{{ $option['label'] }}</option>
        @endforeach
    </select>
</div>
<style>
    .type-of-report {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(15, 42, 68, 0.15);
        box-shadow: 0 1px 2px rgba(15, 42, 68, 0.08);
        font-family: inherit;
    }
    .type-of-report label {
        margin: 0;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #1C75BC;
        white-space: nowrap;
    }
    .type-of-report select {
        border: 0;
        background: transparent;
        font-size: 13px;
        font-weight: 700;
        color: #0f2a44;
        padding: 2px 4px;
        outline: none;
        cursor: pointer;
        max-width: 230px;
    }
    .type-of-report select:focus-visible {
        outline: 2px solid #1C75BC;
        outline-offset: 2px;
        border-radius: 6px;
    }
    @media print {
        .type-of-report { display: none !important; }
    }
</style>
@endif
