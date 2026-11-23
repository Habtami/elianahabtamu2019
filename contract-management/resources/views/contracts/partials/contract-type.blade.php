@php
    $contractTypeSelected = old('contract_type', $contractTypeValue ?? '');
    $canRegisterContractType = auth()->user()->canCreateContracts();
@endphp

<!-- Contract Type -->
<div class="mb-6">
    <label for="contract_type" class="form-label required">Contract Type</label>
    <select id="contract_type"
            name="contract_type"
            class="form-input"
            required
            @if($canRegisterContractType) onchange="toggleNewContractType()" @endif>
        <option value="">Select Contract Type</option>
        @foreach($contractTypes as $type)
            <option value="{{ $type }}" {{ (string) $contractTypeSelected === (string) $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
        @if($canRegisterContractType)
        <option value="__new__">+ Register new contract type...</option>
        @endif
    </select>

    @if($canRegisterContractType)
    <div id="newContractTypeWrap" class="mt-2 hidden">
        <div class="flex items-start gap-2">
            <input type="text"
                   id="newContractTypeName"
                   maxlength="191"
                   placeholder="Enter new contract type"
                   class="form-input flex-1 min-w-0"
                   style="width:auto">
            <button type="button"
                    id="newContractTypeBtn"
                    onclick="registerContractType()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60">
                <i class="fas fa-plus mr-1"></i>Register
            </button>
        </div>
    </div>
    <p id="newContractTypeMsg" class="text-sm mt-1 hidden"></p>
    @endif

    @error('contract_type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if($canRegisterContractType)
<script>
function toggleNewContractType() {
    const select = document.getElementById('contract_type');
    const wrap = document.getElementById('newContractTypeWrap');
    if (!select || !wrap) return;

    if (select.value === '__new__') {
        wrap.classList.remove('hidden');
        document.getElementById('newContractTypeName')?.focus();
        return;
    }

    wrap.classList.add('hidden');
    setNewContractTypeMsg('', false);
}

function setNewContractTypeMsg(text, isError) {
    const msg = document.getElementById('newContractTypeMsg');
    if (!msg) return;

    msg.textContent = text;
    msg.classList.toggle('hidden', !text);
    msg.classList.remove('text-red-600', 'text-green-600');

    if (text) {
        msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
    }
}

async function registerContractType() {
    const select = document.getElementById('contract_type');
    const input = document.getElementById('newContractTypeName');
    const button = document.getElementById('newContractTypeBtn');
    const wrap = document.getElementById('newContractTypeWrap');
    if (!select || !input || !button) return;

    const name = input.value.trim();
    if (!name) {
        setNewContractTypeMsg('Enter the contract type name.', true);
        input.focus();
        return;
    }

    button.disabled = true;
    setNewContractTypeMsg('', false);

    try {
        const response = await fetch('{{ route('contracts.contract-types.store') }}', {
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
            throw new Error(firstError || data.message || 'Unable to register the contract type.');
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
        setNewContractTypeMsg(data.message + ' Selected.', false);
    } catch (error) {
        setNewContractTypeMsg(error.message, true);
    } finally {
        button.disabled = false;
    }
}

document.getElementById('newContractTypeName')?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        registerContractType();
    }
});

const contractTypeForm = document.getElementById('contract_type')?.form;
if (contractTypeForm) {
    contractTypeForm.addEventListener('submit', function (event) {
        const select = document.getElementById('contract_type');
        if (select && select.value === '__new__') {
            event.preventDefault();
            event.stopImmediatePropagation();
            document.getElementById('newContractTypeName')?.focus();
            setNewContractTypeMsg('Register the new contract type or pick one from the list.', true);
        }
    });
}
</script>
@endif
