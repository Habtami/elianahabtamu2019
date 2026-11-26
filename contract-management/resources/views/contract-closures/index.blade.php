@extends('layouts.app')

@section('title', 'Contract Closing')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-file-signature text-[#0d9488] mr-3"></i>
                Contract Closing
            </h1>
            <p class="text-gray-600 mt-2">Track daily contract closings from the closing request through completion.</p>
        </div>
        @if(auth()->user()->canCreateContractClosures())
        <button type="button" onclick="openClosureModal()"
                class="inline-flex items-center px-4 py-2 bg-[#0d9488] text-white font-medium rounded-lg hover:bg-[#0f766e] transition">
            <i class="fas fa-plus mr-2"></i>
            New Closing Request
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Daily status cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-5 py-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Closings</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-5 py-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pending }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-5 py-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Complete</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $complete }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-5 py-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Closing Today</div>
            <div class="text-2xl font-bold text-[#0d9488] mt-1">{{ $today }}</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('contract-closures.index') }}"
          class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]"
                       placeholder="Contract or supplier name">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                    <option value="">All statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">Closing From</label>
                <input type="date" id="from" name="from" value="{{ request('from') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">Closing To</label>
                <input type="date" id="to" name="to" value="{{ request('to') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#0d9488] text-white font-medium rounded-lg hover:bg-[#0f766e] transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('contract-closures.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-rotate-left mr-2"></i>Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Contract Closings</h2>
            <div class="flex items-center gap-3">
                <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipContractClosures"><i class="fas fa-info-circle"></i> TIP: info</button>
                <span class="text-sm text-gray-500">
                    {{ $closures->total() }} record(s) &middot; page {{ $closures->currentPage() }} of {{ $closures->lastPage() }}
                </span>
            </div>
        </div>

        <div id="tipContractClosures" class="table-tip-panel no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li>Search matches contract or supplier name; Status, Closing From and Closing To narrow the list, then press "Filter"; "Reset" clears everything.</li>
                <li>Each row lists Contract (with supplier), Department, Contract End Date, Closing Date, Status and Actions.</li>
                <li>Green "Complete" badges also show who completed the closing and when; amber badges are still pending.</li>
                <li>"Complete" turns a pending closing into a completed one after a confirmation prompt, and only appears for users allowed to complete closures.</li>
                <li>"View" opens the closing details; the header shows the record count and current page, with page links below the table.</li>
                <li>"New Closing Request" records a contract and closing date; a new closing stays pending until Finance or an administrator completes it.</li>
            </ul>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-medium">Contract</th>
                        <th class="px-6 py-3 font-medium">Department</th>
                        <th class="px-6 py-3 font-medium">Contract End Date</th>
                        <th class="px-6 py-3 font-medium">Closing Date</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($closures as $closure)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $closure->contract->name ?? 'Deleted contract' }}</div>
                            <div class="text-xs text-gray-500">{{ $closure->contract->supplier_name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $closure->contract ? $closure->contract->getDepartmentDisplayName() : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $closure->contract?->end_date?->format('M d, Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $closure->closing_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($closure->isComplete()) bg-green-100 text-green-800
                                @else bg-amber-100 text-amber-800 @endif">
                                @if($closure->isComplete())
                                    <i class="fas fa-check mr-1"></i>
                                @endif
                                {{ $closure->getStatusLabel() }}
                            </span>
                            @if($closure->isComplete())
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $closure->completer->name ?? 'Unknown' }}
                                    &middot; {{ $closure->completed_at?->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!$closure->isComplete() && auth()->user()->canCompleteContractClosures())
                                <form method="POST" action="{{ route('contract-closures.complete', $closure) }}"
                                      onsubmit="return confirm('Mark this contract closing as complete?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i>Complete
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('contract-closures.show', $closure) }}"
                                   class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition"
                                   title="View closing details">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-file-signature text-4xl text-gray-300"></i>
                            <p class="mt-4 text-gray-700 font-medium">No contract closings found</p>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'status', 'from', 'to']))
                                    Try adjusting or resetting the filters.
                                @else
                                    Create a closing request to start tracking daily closings.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($closures->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $closures->links() }}
        </div>
        @endif
    </div>
</div>

@if(auth()->user()->canCreateContractClosures())
<!-- New Closing Request modal -->
<div id="closureModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeClosureModal()"></div>
    <div class="relative mx-auto mt-20 w-full max-w-lg px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-file-signature text-[#0d9488] mr-2"></i>New Closing Request
                </h3>
                <button type="button" onclick="closeClosureModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('contract-closures.store') }}">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="contract_id" class="block text-sm font-medium text-gray-700 mb-1">Contract <span class="text-red-500">*</span></label>
                        <select id="contract_id" name="contract_id" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                            <option value="">Select a contract</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" @selected((string) old('contract_id') === (string) $contract->id)>
                                    {{ $contract->name }} &mdash; {{ $contract->getDepartmentDisplayName() }}@if($contract->end_date) &mdash; ends {{ $contract->end_date->format('M d, Y') }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('contract_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @if($contracts->isEmpty())
                            <p class="text-xs text-gray-500 mt-1">Every contract available to you already has a closing record.</p>
                        @endif
                    </div>
                    <div>
                        <label for="closing_date" class="block text-sm font-medium text-gray-700 mb-1">Closing Date <span class="text-red-500">*</span></label>
                        <input type="date" id="closing_date" name="closing_date"
                               value="{{ old('closing_date', now()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]"
                               required>
                        @error('closing_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" rows="3"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]"
                                  placeholder="Reason or notes for closing this contract">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        The closing stays Pending until Finance or an administrator marks it Complete.
                    </p>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closeClosureModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#0d9488] text-white font-medium rounded-lg hover:bg-[#0f766e] transition">
                        Save Closing Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function openClosureModal() {
        const modal = document.getElementById('closureModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const select = document.getElementById('contract_id');
        if (select) select.focus();
    }

    function closeClosureModal() {
        const modal = document.getElementById('closureModal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeClosureModal();
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openClosureModal);
    @endif
</script>
@endsection
