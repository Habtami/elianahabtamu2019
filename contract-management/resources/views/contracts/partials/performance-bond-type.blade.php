@php
    $performanceBondTypeSelected = old('performance_bond_type', $performanceBondTypeValue ?? '');
    $canRegisterPerformanceBondType = auth()->user()->canCreateContracts();
@endphp

<!-- Types of Performance Bonds -->
<div class="mb-6">
    <label for="performance_bond_type" class="form-label">Types of Performance Bonds</label>
    <select id="performance_bond_type"
            name="performance_bond_type"
            class="form-input"
            @if($canRegisterPerformanceBondType) onchange="toggleNewPerformanceBondType()" @endif>
        <option value="">Select Performance Bond Type</option>
        @foreach($performanceBondTypes as $type)
            <option value="{{ $type }}" {{ (string) $performanceBondTypeSelected === (string) $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
        @if($canRegisterPerformanceBondType)
        <option value="__new__">+ Register new performance bond type...</option>
        @endif
    </select>

    @if($canRegisterPerformanceBondType)
    <div id="newPerformanceBondTypeWrap" class="mt-2 hidden">
        <div class="flex items-start gap-2">
            <input type="text"
                   id="newPerformanceBondTypeName"
                   maxlength="191"
                   placeholder="Enter new performance bond type"
                   class="form-input flex-1 min-w-0"
                   style="width:auto">
            <button type="button"
                    id="newPerformanceBondTypeBtn"
                    onclick="registerPerformanceBondType()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60">
                <i class="fas fa-plus mr-1"></i>Register
            </button>
        </div>
    </div>
    <p id="newPerformanceBondTypeMsg" class="text-sm mt-1 hidden"></p>
    @endif

    @error('performance_bond_type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if($canRegisterPerformanceBondType)
<script>
function toggleNewPerformanceBondType() {
    const select = document.getElementById('performance_bond_type');
    const wrap = document.getElementById('newPerformanceBondTypeWrap');
    if (!select || !wrap) return;

    if (select.value === '__new__') {
        wrap.classList.remove('hidden');
        document.getElementById('newPerformanceBondTypeName')?.focus();
        return;
    }

    wrap.classList.add('hidden');
    setNewPerformanceBondTypeMsg('', false);
}

function setNewPerformanceBondTypeMsg(text, isError) {
    const msg = document.getElementById('newPerformanceBondTypeMsg');
    if (!msg) return;

    msg.textContent = text;
    msg.classList.toggle('hidden', !text);
    msg.classList.remove('text-red-600', 'text-green-600');

    if (text) {
        msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
    }
}

async function registerPerformanceBondType() {
    const select = document.getElementById('performance_bond_type');
    const input = document.getElementById('newPerformanceBondTypeName');
    const button = document.getElementById('newPerformanceBondTypeBtn');
    const wrap = document.getElementById('newPerformanceBondTypeWrap');
    if (!select || !input || !button) return;

    const name = input.value.trim();
    if (!name) {
        setNewPerformanceBondTypeMsg('Enter the performance bond type name.', true);
        input.focus();
        return;
    }

    button.disabled = true;
    setNewPerformanceBondTypeMsg('', false);

    try {
        const response = await fetch('{{ route('contracts.performance-bond-types.store') }}', {
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
            throw new Error(firstError || data.message || 'Unable to register the performance bond type.');
        }

        let option = Array.from(select.options).find((item) => item.value === data.name);

        if (!option) {
            option = new Option(data.name, data.name);

            const nextOption = Array.from(select.options).find((item) =>
                item.value !== '' &&
                item.value !== '__new__' &&
                item.textContent.trim().localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
            );

            select.insertBefore(
                option,
                nextOption || select.querySelector('option[value="__new__"]')
            );
        }

        select.value = data.name;
        input.value = '';
        wrap.classList.add('hidden');
        setNewPerformanceBondTypeMsg(data.message + ' Selected.', false);
    } catch (error) {
        setNewPerformanceBondTypeMsg(error.message, true);
    } finally {
        button.disabled = false;
    }
}

document.getElementById('newPerformanceBondTypeName')?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        registerPerformanceBondType();
    }
});

const performanceBondTypeForm = document.getElementById('performance_bond_type')?.form;
if (performanceBondTypeForm) {
    performanceBondTypeForm.addEventListener('submit', function (event) {
        const select = document.getElementById('performance_bond_type');
        if (select && select.value === '__new__') {
            event.preventDefault();
            event.stopImmediatePropagation();
            document.getElementById('newPerformanceBondTypeName')?.focus();
            setNewPerformanceBondTypeMsg('Register the new performance bond type or pick one from the list.', true);
        }
    });
}
</script>
@endif
