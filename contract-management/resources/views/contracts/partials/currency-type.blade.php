@php
    $currencyTypeSelected = old('currency_type', $currencyTypeValue ?? '');
    $canRegisterCurrencyType = auth()->user()->canCreateContracts();
@endphp

<!-- Currency Type -->
<div class="mb-6">
    <label for="currency_type" class="form-label">Currency Type</label>
    <select id="currency_type"
            name="currency_type"
            class="form-input"
            @if($canRegisterCurrencyType) onchange="toggleNewCurrencyType()" @endif>
        <option value="">Select Currency</option>
        @foreach($currencyTypes as $type)
            <option value="{{ $type }}" {{ (string) $currencyTypeSelected === (string) $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
        @if($canRegisterCurrencyType)
        <option value="__new__">+ Register new currency type...</option>
        @endif
    </select>

    @if($canRegisterCurrencyType)
    <div id="newCurrencyTypeWrap" class="mt-2 hidden">
        <div class="flex items-start gap-2">
            <input type="text"
                   id="newCurrencyTypeName"
                   maxlength="191"
                   placeholder="Enter new currency type"
                   class="form-input flex-1 min-w-0"
                   style="width:auto">
            <button type="button"
                    id="newCurrencyTypeBtn"
                    onclick="registerCurrencyType()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60">
                <i class="fas fa-plus mr-1"></i>Register
            </button>
        </div>
    </div>
    <p id="newCurrencyTypeMsg" class="text-sm mt-1 hidden"></p>
    @endif

    @error('currency_type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if($canRegisterCurrencyType)
<script>
function toggleNewCurrencyType() {
    const select = document.getElementById('currency_type');
    const wrap = document.getElementById('newCurrencyTypeWrap');
    if (!select || !wrap) return;

    if (select.value === '__new__') {
        wrap.classList.remove('hidden');
        document.getElementById('newCurrencyTypeName')?.focus();
        return;
    }

    wrap.classList.add('hidden');
    setNewCurrencyTypeMsg('', false);
}

function setNewCurrencyTypeMsg(text, isError) {
    const msg = document.getElementById('newCurrencyTypeMsg');
    if (!msg) return;

    msg.textContent = text;
    msg.classList.toggle('hidden', !text);
    msg.classList.remove('text-red-600', 'text-green-600');

    if (text) {
        msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
    }
}

async function registerCurrencyType() {
    const select = document.getElementById('currency_type');
    const input = document.getElementById('newCurrencyTypeName');
    const button = document.getElementById('newCurrencyTypeBtn');
    const wrap = document.getElementById('newCurrencyTypeWrap');
    if (!select || !input || !button) return;

    const name = input.value.trim();
    if (!name) {
        setNewCurrencyTypeMsg('Enter the currency type name.', true);
        input.focus();
        return;
    }

    button.disabled = true;
    setNewCurrencyTypeMsg('', false);

    try {
        const response = await fetch('{{ route('contracts.currency-types.store') }}', {
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
            throw new Error(firstError || data.message || 'Unable to register the currency type.');
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
        setNewCurrencyTypeMsg(data.message + ' Selected.', false);
    } catch (error) {
        setNewCurrencyTypeMsg(error.message, true);
    } finally {
        button.disabled = false;
    }
}

document.getElementById('newCurrencyTypeName')?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        registerCurrencyType();
    }
});

const currencyTypeForm = document.getElementById('currency_type')?.form;
if (currencyTypeForm) {
    currencyTypeForm.addEventListener('submit', function (event) {
        const select = document.getElementById('currency_type');
        if (select && select.value === '__new__') {
            event.preventDefault();
            event.stopImmediatePropagation();
            document.getElementById('newCurrencyTypeName')?.focus();
            setNewCurrencyTypeMsg('Register the new currency type or pick one from the list.', true);
        }
    });
}
</script>
@endif
