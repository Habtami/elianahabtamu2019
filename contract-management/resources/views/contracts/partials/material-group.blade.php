@php
    $materialGroupSelected = old('material_group', $materialGroupValue ?? '');
    $canRegisterMaterialGroup = auth()->user()->canCreateContracts();
@endphp

<!-- Material Group -->
<div class="mb-6">
    <label for="material_group" class="form-label required">Material Group</label>
    <select id="material_group"
            name="material_group"
            class="form-input"
            required
            @if($canRegisterMaterialGroup) onchange="toggleNewMaterialGroup()" @endif>
        <option value="">Select Material Group</option>
        @foreach($materialGroups as $group)
            <option value="{{ $group }}" {{ $materialGroupSelected === $group ? 'selected' : '' }}>
                {{ $group }}
            </option>
        @endforeach
        @if($canRegisterMaterialGroup)
        <option value="__new__">+ Register new material group...</option>
        @endif
    </select>

    @if($canRegisterMaterialGroup)
    <div id="newMaterialGroupWrap" class="mt-2 hidden">
        <div class="flex items-start gap-2">
            <input type="text"
                   id="newMaterialGroupName"
                   maxlength="191"
                   placeholder="Enter new material group name"
                   class="form-input flex-1 min-w-0"
                   style="width:auto">
            <button type="button"
                    id="newMaterialGroupBtn"
                    onclick="registerMaterialGroup()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60">
                <i class="fas fa-plus mr-1"></i>Register
            </button>
        </div>
    </div>
    <p id="newMaterialGroupMsg" class="text-sm mt-1 hidden"></p>
    @endif

    @error('material_group')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if($canRegisterMaterialGroup)
<script>
function toggleNewMaterialGroup() {
    const select = document.getElementById('material_group');
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
    const select = document.getElementById('material_group');
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
        const response = await fetch('{{ route('contracts.material-groups.store') }}', {
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
        setNewMaterialGroupMsg(data.message + ' Selected.', false);
    } catch (error) {
        setNewMaterialGroupMsg(error.message, true);
    } finally {
        button.disabled = false;
    }
}

document.getElementById('newMaterialGroupName')?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        registerMaterialGroup();
    }
});

const materialGroupForm = document.getElementById('material_group')?.form;
if (materialGroupForm) {
    materialGroupForm.addEventListener('submit', function (event) {
        const select = document.getElementById('material_group');
        if (select && select.value === '__new__') {
            event.preventDefault();
            document.getElementById('newMaterialGroupName')?.focus();
            setNewMaterialGroupMsg('Register the new material group or pick one from the list.', true);
        }
    });
}
</script>
@endif
