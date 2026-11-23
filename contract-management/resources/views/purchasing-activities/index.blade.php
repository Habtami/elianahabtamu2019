@extends('layouts.app')

@section('title', 'Purchasing Daily Activity')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-clipboard-list text-[#b45309] mr-3"></i>
                Purchasing Daily Activity
            </h1>
            <p class="text-gray-600 mt-2">Register proforma and request-for-quotation activity by material group, unit and procurement type.</p>
        </div>
        @if(auth()->user()->canCreatePurchasingActivities())
        <button type="button" onclick="openActivityModal()"
                class="inline-flex items-center px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
            <i class="fas fa-plus mr-2"></i>
            New Activity
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
        <button type="button" onclick="openStatModal('total', 'Total Activities')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#b45309] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#1C75BC]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Activities</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</div>
        </button>
        <button type="button" onclick="openStatModal('pending', 'Pending')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#b45309] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pending }}</div>
        </button>
        <button type="button" onclick="openStatModal('complete', 'Complete')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#b45309] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-green-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Complete</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $complete }}</div>
        </button>
        <button type="button" onclick="openStatModal('today', &quot;Today's Requests&quot;)"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#b45309] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#b45309]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Today's Requests</div>
            <div class="text-2xl font-bold text-[#b45309] mt-1">{{ $today }}</div>
        </button>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('purchasing-activities.index') }}"
          class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">Request From</label>
                <input type="date" id="from" name="from" value="{{ request('from') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">Request To</label>
                <input type="date" id="to" name="to" value="{{ request('to') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
            </div>
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document</label>
                <select id="document_type" name="document_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All documents</option>
                    @foreach($documentTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('document_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="procurement_type" class="block text-sm font-medium text-gray-700 mb-1">Procurement Type</label>
                <select id="procurement_type" name="procurement_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All types</option>
                    @foreach($procurementTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('procurement_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="material_group" class="block text-sm font-medium text-gray-700 mb-1">Material Group</label>
                <select id="material_group" name="material_group"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All groups</option>
                    @foreach($materialGroups as $group)
                        <option value="{{ $group }}" @selected(request('material_group') === $group)>{{ $group }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('purchasing-activities.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-rotate-left mr-2"></i>Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daily Activity Register</h2>
            <span class="text-sm text-gray-500">
                {{ $activities->total() }} record(s) &middot; page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-medium">Request Date</th>
                        <th class="px-6 py-3 font-medium">Document</th>
                        <th class="px-6 py-3 font-medium">Material Group</th>
                        <th class="px-6 py-3 font-medium">Unit</th>
                        <th class="px-6 py-3 font-medium">Procurement Type</th>
                        <th class="px-6 py-3 font-medium">Remark</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-6 py-4 text-gray-700">{{ $activity->request_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->document_type === \App\Models\PurchasingActivity::DOCUMENT_PROFORMA) bg-indigo-100 text-indigo-800
                                @else bg-sky-100 text-sky-800 @endif">
                                {{ $activity->getDocumentTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $activity->material_group }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $activity->unit }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->procurement_type === \App\Models\PurchasingActivity::PROCUREMENT_TENDER) bg-teal-100 text-teal-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $activity->getProcurementTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $activity->remarks ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->isComplete()) bg-green-100 text-green-800
                                @else bg-amber-100 text-amber-800 @endif">
                                @if($activity->isComplete())
                                    <i class="fas fa-check mr-1"></i>
                                @endif
                                {{ $activity->getStatusLabel() }}
                            </span>
                            @if($activity->isComplete())
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $activity->completer->name ?? 'Unknown' }}
                                    &middot; {{ $activity->completed_at?->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$activity->isComplete() && auth()->user()->canCompletePurchasingActivities())
                            <form method="POST" action="{{ route('purchasing-activities.complete', $activity) }}"
                                  onsubmit="return confirm('Mark this purchasing activity as complete?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700">
                                    <i class="fas fa-check mr-1"></i>Complete
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300"></i>
                            <p class="mt-4 text-gray-700 font-medium">No purchasing activities found</p>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['from', 'to', 'status', 'document_type', 'procurement_type', 'material_group']))
                                    Try adjusting or resetting the filters.
                                @else
                                    Record a proforma or quotation request to start the daily register.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>

@if(auth()->user()->canCreatePurchasingActivities())
<!-- New Purchasing Activity modal -->
<div id="activityModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeActivityModal()"></div>
    <div class="relative mx-auto mt-16 w-full max-w-lg px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-[#b45309] mr-2"></i>New Purchasing Activity
                </h3>
                <button type="button" onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('purchasing-activities.store') }}" onsubmit="return validateActivityForm();">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="pa_document_type" class="block text-sm font-medium text-gray-700 mb-1">Document <span class="text-red-500">*</span></label>
                        <select id="pa_document_type" name="document_type" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select document type</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('document_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('document_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">Request Date <span class="text-red-500">*</span></label>
                            <input type="date" id="request_date" name="request_date"
                                   value="{{ old('request_date', now()->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]"
                                   required>
                            @error('request_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pa_unit" class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
                            <select id="pa_unit" name="unit" required onchange="toggleNewUnit()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <option value="">Select unit</option>
                                @foreach($units as $unitName)
                                    <option value="{{ $unitName }}" @selected(old('unit') === $unitName)>{{ $unitName }}</option>
                                @endforeach
                                <option value="__new__" @selected(old('unit') === '__new__')>+ Register new unit...</option>
                            </select>
                            <div id="newUnitWrap" class="mt-2 hidden">
                                <div class="flex gap-2">
                                    <input type="text" id="newUnitName" maxlength="50"
                                           value="{{ old('new_unit') }}"
                                           placeholder="e.g. Pcs, Kg, Liter"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                    <button type="button" id="newUnitBtn" onclick="registerUnit()"
                                            class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                        <i class="fas fa-plus mr-1"></i>Register
                                    </button>
                                </div>
                            </div>
                            <p id="newUnitMsg" class="text-sm mt-1 hidden"></p>
                            @error('unit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="pa_material_group" class="block text-sm font-medium text-gray-700 mb-1">Material Group <span class="text-red-500">*</span></label>
                        <select id="pa_material_group" name="material_group" required onchange="toggleNewMaterialGroup()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select material group</option>
                            @foreach($materialGroups as $group)
                                <option value="{{ $group }}" @selected(old('material_group') === $group)>{{ $group }}</option>
                            @endforeach
                            <option value="__new__" @selected(old('material_group') === '__new__')>+ Register new material group...</option>
                        </select>
                        <div id="newMaterialGroupWrap" class="mt-2 hidden">
                            <div class="flex gap-2">
                                <input type="text" id="newMaterialGroupName" maxlength="191"
                                       value="{{ old('new_material_group') }}"
                                       placeholder="Enter new material group name"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <button type="button" id="newMaterialGroupBtn" onclick="registerMaterialGroup()"
                                        class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                    <i class="fas fa-plus mr-1"></i>Register
                                </button>
                            </div>
                        </div>
                        <p id="newMaterialGroupMsg" class="text-sm mt-1 hidden"></p>
                        @error('material_group')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pa_procurement_type" class="block text-sm font-medium text-gray-700 mb-1">Procurement Type <span class="text-red-500">*</span></label>
                        <select id="pa_procurement_type" name="procurement_type" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select procurement type</option>
                            @foreach($procurementTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('procurement_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('procurement_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pa_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                            Remark <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea id="pa_remarks" name="remarks" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]"
                                  placeholder="Add any note about this request...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        New activities start as Pending and can be marked Complete by Finance or an administrator.
                    </p>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closeActivityModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
                        Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Stat cards detail modal -->
<div id="statModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatModal()"></div>
    <div class="relative mx-auto mt-12 w-full max-w-4xl px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="statModalTitle" class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-[#b45309] mr-2"></i>Activities
                </h3>
                <button type="button" onclick="closeStatModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <p id="statModalMeta" class="text-sm text-gray-600 mb-3"></p>

                <div id="statModalLoading" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading activities...
                </div>

                <div id="statModalEmpty" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i>
                    No activities in this group.
                </div>

                <div id="statModalTableWrap" class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-lg border border-gray-200 hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 font-medium">Request Date</th>
                                <th class="px-4 py-2.5 font-medium">Document</th>
                                <th class="px-4 py-2.5 font-medium">Material Group</th>
                                <th class="px-4 py-2.5 font-medium">Unit</th>
                                <th class="px-4 py-2.5 font-medium">Procurement Type</th>
                                <th class="px-4 py-2.5 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody id="statModalBody" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end px-6 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closeStatModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openActivityModal() {
        const modal = document.getElementById('activityModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const select = document.getElementById('pa_document_type');
        if (select) select.focus();
    }

    function closeActivityModal() {
        const modal = document.getElementById('activityModal');
        if (modal) modal.classList.add('hidden');
    }

    let statRequestSeq = 0;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));
    }

    function openStatModal(scope, title) {
        const modal = document.getElementById('statModal');
        if (!modal) return;

        document.getElementById('statModalTitle').innerHTML =
            '<i class="fas fa-clipboard-list text-[#b45309] mr-2"></i>' + escapeHtml(title);
        document.getElementById('statModalMeta').textContent = '';
        document.getElementById('statModalBody').innerHTML = '';
        document.getElementById('statModalEmpty').classList.add('hidden');
        document.getElementById('statModalTableWrap').classList.add('hidden');
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
            const response = await fetch('{{ url('/purchasing-activities/summary') }}/' + encodeURIComponent(scope), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Unable to load activities.');
            }

            if (seq !== statRequestSeq) return;

            renderStatSummary(data);
        } catch (error) {
            if (seq !== statRequestSeq) return;

            document.getElementById('statModalLoading').classList.add('hidden');
            document.getElementById('statModalMeta').textContent = error.message;
        }
    }

    function renderStatSummary(data) {
        const loading = document.getElementById('statModalLoading');
        const meta = document.getElementById('statModalMeta');
        const body = document.getElementById('statModalBody');
        const tableWrap = document.getElementById('statModalTableWrap');
        const empty = document.getElementById('statModalEmpty');

        loading.classList.add('hidden');
        meta.textContent = data.total === 0
            ? 'No records'
            : `Showing ${data.items.length} of ${data.total} record(s)` + (data.limited ? ' — latest 50 shown' : '');

        if (!data.items.length) {
            body.innerHTML = '';
            tableWrap.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');
        tableWrap.classList.remove('hidden');
        tableWrap.scrollTop = 0;

        body.innerHTML = data.items.map((item) => {
            const badge = item.is_complete
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' + escapeHtml(item.status) + '</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">' + escapeHtml(item.status) + '</span>';

            const completed = item.completed_at
                ? '<div class="text-xs text-gray-500 mt-1">' + escapeHtml(item.completed_by || 'Unknown') + ' &middot; ' + escapeHtml(item.completed_at) + '</div>'
                : '';

            return '<tr class="hover:bg-gray-50 align-top">' +
                '<td class="px-4 py-3 text-gray-700 whitespace-nowrap">' + escapeHtml(item.date) + '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + escapeHtml(item.document) + '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + escapeHtml(item.material_group) +
                    (item.remarks ? '<div class="text-xs text-gray-500 mt-1">' + escapeHtml(item.remarks) + '</div>' : '') +
                '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + escapeHtml(item.unit) + '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + escapeHtml(item.procurement_type) + '</td>' +
                '<td class="px-4 py-3">' + badge + completed + '</td>' +
                '</tr>';
        }).join('');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeActivityModal();
            closeStatModal();
        }
    });

    function toggleNewMaterialGroup() {
        const select = document.getElementById('pa_material_group');
        const wrap = document.getElementById('newMaterialGroupWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newMaterialGroupName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewMaterialGroupMsg('', false);
    }

    function setNewMaterialGroupMsg(text, isError) {
        const msg = document.getElementById('newMaterialGroupMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerMaterialGroup() {
        const select = document.getElementById('pa_material_group');
        const input = document.getElementById('newMaterialGroupName');
        const button = document.getElementById('newMaterialGroupBtn');
        const wrap = document.getElementById('newMaterialGroupWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewMaterialGroupMsg('Enter the material group name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewMaterialGroupMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.material-groups.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the material group.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewMaterialGroupMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewMaterialGroupMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function toggleNewUnit() {
        const select = document.getElementById('pa_unit');
        const wrap = document.getElementById('newUnitWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newUnitName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewUnitMsg('', false);
    }

    function setNewUnitMsg(text, isError) {
        const msg = document.getElementById('newUnitMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerUnit() {
        const select = document.getElementById('pa_unit');
        const input = document.getElementById('newUnitName');
        const button = document.getElementById('newUnitBtn');
        const wrap = document.getElementById('newUnitWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewUnitMsg('Enter the unit name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewUnitMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.units.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the unit.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewUnitMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewUnitMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function validateActivityForm() {
        const groupSelect = document.getElementById('pa_material_group');

        if (groupSelect && groupSelect.value === '__new__') {
            document.getElementById('newMaterialGroupWrap')?.classList.remove('hidden');
            setNewMaterialGroupMsg('Register the new material group before saving.', true);
            document.getElementById('newMaterialGroupName')?.focus();
            return false;
        }

        const unitSelect = document.getElementById('pa_unit');

        if (unitSelect && unitSelect.value === '__new__') {
            document.getElementById('newUnitWrap')?.classList.remove('hidden');
            setNewUnitMsg('Register the new unit before saving.', true);
            document.getElementById('newUnitName')?.focus();
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const groupSelect = document.getElementById('pa_material_group');

        if (groupSelect && groupSelect.value === '__new__') {
            toggleNewMaterialGroup();
        }

        const unitSelect = document.getElementById('pa_unit');

        if (unitSelect && unitSelect.value === '__new__') {
            toggleNewUnit();
        }
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openActivityModal);
    @endif
</script>
@endsection
