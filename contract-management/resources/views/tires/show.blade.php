@extends('layouts.app')

@section('title', 'Tire Details — ' . $tire->serial_number)
@section('page-title', 'Tire Details')

@push('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-dark: #1664a0;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }

    /* ---------- Cards ---------- */
    .info-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: .75rem;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.04);
        transition: box-shadow .25s ease;
    }
    .info-card:hover { box-shadow: 0 4px 14px -4px rgba(0,0,0,.08); }

    .status-badge {
        font-size: .72rem;
        padding: .28rem .8rem;
        border-radius: 9999px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        white-space: nowrap;
    }

    /* ---------- Spec list ---------- */
    .spec-row {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        padding: .55rem 0;
        border-bottom: 1px dashed #F1F5F9;
        font-size: .875rem;
    }
    .spec-row:last-child { border-bottom: 0; }
    .spec-label { color: #6B7280; flex-shrink: 0; }
    .spec-value {
        color: #1F2937;
        font-weight: 600;
        text-align: right;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    /* ---------- Timeline ---------- */
    .timeline { position: relative; padding-left: 2.25rem; }
    .timeline::before {
        content: '';
        position: absolute;
        left: .65rem;
        top: .4rem;
        bottom: .4rem;
        width: 2px;
        background: linear-gradient(#DBEAFE, #D1FAE5);
        border-radius: 2px;
    }
    .timeline-entry { position: relative; margin-bottom: 1rem; }
    .timeline-entry::before {
        content: '';
        position: absolute;
        left: -1.72rem;
        top: 1.15rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: var(--epss-blue);
        box-shadow: 0 0 0 2px rgba(28,117,188,.25);
    }
    .timeline-entry.is-return::before {
        background: var(--success);
        box-shadow: 0 0 0 2px rgba(16,185,129,.25);
    }
    .timeline-entry.is-pending::before {
        background: var(--warning);
        box-shadow: 0 0 0 2px rgba(245,158,11,.25);
    }

    .history-card {
        border: 1px solid #E5E7EB;
        border-radius: .75rem;
        padding: 1rem;
        background: #fff;
        transition: border-color .2s ease, transform .2s ease;
    }
    .history-card:hover { border-color: #CBD5E1; transform: translateY(-1px); }

    /* ---------- Action buttons ---------- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1rem;
        border-radius: .65rem;
        font-weight: 600;
        font-size: .875rem;
        transition: all .2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,.05);
        white-space: nowrap;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 14px -4px rgba(0,0,0,.15); }
    .btn-primary { background: var(--epss-blue); color: #fff; }
    .btn-primary:hover { background: var(--epss-blue-dark); color: #fff; }
    .btn-ghost { background: #F3F4F6; color: #1F2937; }
    .btn-ghost:hover { background: #E5E7EB; }

    /* ---------- Layout safety when sidebar opens ---------- */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        min-width: 0;
    }
    @media (min-width: 1024px) {
        .detail-grid {
            grid-template-columns: minmax(0, 320px) minmax(0, 1fr);
        }
    }
    .detail-grid > * { min-width: 0; }   /* prevents overflow pushing layout wide */

    /* Prevent the whole page from getting a horizontal scrollbar
       when the sidebar expands on medium screens */
    .page-container { overflow-x: clip; }

    /* Photo blocks keep aspect ratio and never overflow */
    .media-thumb {
        width: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        border-radius: .65rem;
        background: #F3F4F6;
    }
    .media-thumb-placeholder {
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: .65rem;
        background: #F9FAFB;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9CA3AF;
    }
</style>
@endpush

@section('content')
<div class="page-container py-6 lg:py-8">
    <div class="container mx-auto px-4">

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 truncate">Tire Details</h1>
                <p class="text-gray-600 mt-1 font-mono text-sm truncate">{{ $tire->serial_number }}</p>
            </div>
            <a href="{{ route('tires.index') }}"
               class="btn btn-ghost self-start sm:self-auto">
                <i class="fas fa-arrow-left"></i> Back to Tires
            </a>
        </div>

        {{-- ============ Body grid ============ --}}
        <div class="detail-grid">

                 {{-- ---------- Left: Tire summary ---------- --}}
             <div class="info-card p-5">
                 @if($tire->photo)
                     <div class="relative group" data-tire-photo>
                         <img src="{{ asset('storage/' . $tire->photo) }}"
                              alt="Tire Photo"
                              class="media-thumb mb-4 group-hover:brightness-105 transition-all cursor-pointer">
                         <div class="photo-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                              data-photo-overlay>
                             <img src="{{ asset('storage/' . $tire->photo) }}"
                                  alt="Tire Photo Full"
                                  class="max-w-[80vw] max-h-[80vh] object-contain rounded-lg">
                             <button type="button" data-photo-close
                                     class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">&times;</button>
                         </div>
                     </div>
                 @else
                     <div class="media-thumb-placeholder mb-4">
                         <i class="fas fa-tire text-4xl"></i>
                     </div>
                 @endif

                 <div class="flex items-start justify-between gap-2 mb-3">
                     <h3 class="text-lg font-bold text-gray-800 break-all leading-snug">
                         {{ $tire->serial_number }}
                    </h3>
                    @if($tire->isGivenOut())
                        <span class="status-badge bg-yellow-100 text-yellow-800">
                            <i class="fas fa-circle text-[6px]"></i> In Use
                        </span>
                    @elseif($tire->is_active)
                        <span class="status-badge bg-green-100 text-green-800">
                            <i class="fas fa-circle text-[6px]"></i> Available
                        </span>
                    @else
                        <span class="status-badge bg-gray-100 text-gray-800">
                            <i class="fas fa-circle text-[6px]"></i> Inactive
                        </span>
                    @endif
                </div>

                <div class="mt-2">
                    <div class="spec-row">
                        <span class="spec-label">Tire Type</span>
                        <span class="spec-value">{{ $tire->tire_type }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Plate Number</span>
                        <span class="spec-value">{{ $tire->plate_number ?? 'N/A' }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Initial KM</span>
                        <span class="spec-value">{{ number_format($tire->initial_kilometer) }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Registered</span>
                        <span class="spec-value">{{ $tire->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="mt-5 space-y-2">
                    @if(auth()->user()->canPerformTireOperations() && !$tire->isGivenOut())
                        <a href="{{ route('tires.give', ['tire_id' => $tire->id]) }}"
                           class="btn btn-primary w-full">
                            <i class="fas fa-hand-holding"></i> Give to Driver
                        </a>
                    @endif
                    @if(auth()->user()->canManageTires() && !$tire->hasBeenReturned())
                        <a href="{{ route('tires.edit', $tire) }}"
                           class="btn btn-ghost w-full">
                            <i class="fas fa-edit"></i> Edit Tire
                        </a>
                    @elseif(auth()->user()->canManageTires() && $tire->hasBeenReturned())
                        <button type="button"
                           class="btn btn-ghost w-full"
                           title="This tire has been returned and cannot be edited"
                           aria-label="Tire already returned - cannot edit"
                           style="cursor: not-allowed; opacity: 0.5;">
                            <i class="fas fa-edit"></i> Edit Tire (Returned)
                        </button>
                    @endif
                </div>
            </div>

            {{-- ---------- Right: History ---------- --}}
            <div class="info-card p-5 lg:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Give &amp; Return History</h3>
                    <span class="text-xs text-gray-500">
                        {{ $tireGives->count() + $tireReturns->count() }} records
                    </span>
                </div>

                @if($tireGives->count() === 0 && $tireReturns->count() === 0)
                    {{-- Empty state --}}
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-3">
                            <i class="fas fa-tire text-3xl"></i>
                        </div>
                        <p class="text-gray-500">No give/return operations recorded for this tire.</p>
                    </div>
                @else
                    <div class="timeline">

                        {{-- ----- Gives ----- --}}
                        @foreach($tireGives as $give)
                            <div class="timeline-entry {{ $give->is_returned ? '' : 'is-pending' }}">
                                <div class="history-card">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="bg-blue-100 p-2 rounded-lg shrink-0">
                                                <i class="fas fa-hand-holding text-blue-600"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800">Tire Given</h4>
                                                <p class="text-sm text-gray-500 truncate">
                                                    To: <span class="font-medium text-gray-700">{{ $give->driver->name ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 shrink-0">
                                            {{ $give->give_date->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-500 block text-xs">KM at Give</span>
                                            <span class="font-semibold">{{ number_format($give->given_kilometer) }}</span>
                                        </div>
                                         @if($give->photo)
                                             <div class="col-span-2 sm:col-span-1 flex items-center">
                                                 <div class="relative group cursor-pointer">
                                                     <img src="{{ asset('storage/' . $give->photo) }}"
                                                          alt="Give Photo"
                                                          class="w-10 h-10 object-cover rounded border-2 border-gray-200 group-hover:opacity-90">
                                                     <div class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                         <img src="{{ asset('storage/' . $give->photo) }}"
                                                              alt="Give Photo Full"
                                                              class="max-w-[80vw] max-h-[80vh] object-contain rounded-lg">
                                                     </div>
                                                 </div>
                                                 <span class="ml-2 text-[#1C75BC] text-sm">View Photo</span>
                                             </div>
                                         @endif
                                        <div class="flex items-center justify-end col-span-2 sm:col-span-1">
                                            @if(!$give->is_returned)
                                                @if(auth()->user()->canPerformTireOperations())
                                                    <a href="{{ route('tires.return', $give) }}"
                                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                        <i class="fas fa-undo-alt mr-1"></i> Return Now
                                                    </a>
                                                @else
                                                    <span class="status-badge bg-yellow-100 text-yellow-800">Pending Return</span>
                                                @endif
                                            @else
                                                <span class="status-badge bg-green-100 text-green-800">Returned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- ----- Returns ----- --}}
                        @foreach($tireReturns as $ret)
                            <div class="timeline-entry is-return">
                                <div class="history-card">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="bg-green-100 p-2 rounded-lg shrink-0">
                                                <i class="fas fa-undo-alt text-green-600"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800">Tire Returned</h4>
                                                <p class="text-sm text-gray-500 truncate">
                                                    By: <span class="font-medium text-gray-700">{{ $ret->returnedBy->name ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 shrink-0">
                                            {{ $ret->return_date->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-500 block text-xs">Current KM</span>
                                            <span class="font-semibold text-green-600">{{ number_format($ret->current_kilometer) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Registered KM</span>
                                            <span class="font-semibold">{{ number_format($ret->registered_kilometer) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">KM Difference</span>
                                            <span class="font-semibold {{ $ret->km_difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $ret->km_difference >= 0 ? '+' : '' }}{{ number_format($ret->km_difference) }}
                                            </span>
                                        </div>
                                    </div>

                                     @if($ret->photo)
                                         <div class="mt-3">
                                             <div class="relative group cursor-pointer inline-block">
                                                 <img src="{{ asset('storage/' . $ret->photo) }}"
                                                      alt="Return Photo"
                                                      class="w-10 h-10 object-cover rounded border-2 border-gray-200 group-hover:opacity-90">
                                                 <div class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                     <img src="{{ asset('storage/' . $ret->photo) }}"
                                                          alt="Return Photo Full"
                                                          class="max-w-[80vw] max-h-[80vh] object-contain rounded-lg">
                                                 </div>
                                             </div>
                                             <span class="ml-2 text-[#1C75BC] text-sm">View Return Photo</span>
                                         </div>
                                     @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>
        </div>

        {{-- ============ Status Summary Bar ============ --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="info-card p-4 text-center">
                <div class="text-2xl font-bold text-[#1C75BC]">{{ number_format($tire->initial_kilometer) }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Initial KM</div>
            </div>
            @php
                $currentGive = $tire->tireGives()->where('is_returned', false)->first();
            @endphp
            <div class="info-card p-4 text-center">
                <div class="text-2xl font-bold {{ $tire->isGivenOut() ? 'text-yellow-600' : ($tire->is_active ? 'text-green-600' : 'text-gray-500') }}">
                    {{ $tire->isGivenOut() ? 'In Use' : ($tire->is_active ? 'Available' : 'Inactive') }}
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Current Status</div>
            </div>
            <div class="info-card p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $tire->isGivenOut() && $currentGive ? ($currentGive->driver->name ?? 'N/A') : 'N/A' }}
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Current Driver</div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
 <script>
 document.addEventListener('DOMContentLoaded', function() {
     // Close photo overlay
     const photoClose = document.querySelector('[data-photo-close]');
     if (photoClose) {
         photoClose.addEventListener('click', function() {
             const overlay = document.querySelector('[data-photo-overlay]');
             if (overlay) overlay.classList.add('hidden');
         });
     }
 });
 </script>
 @endpush
@endsection