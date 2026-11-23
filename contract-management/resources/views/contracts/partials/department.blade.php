@php
    $departmentSelected = old('department', $departmentValue ?? '');
    $canRegisterDepartment = in_array(auth()->user()->role, [
        \App\Models\User::ROLE_SUPER_ADMIN,
        \App\Models\User::ROLE_ADMIN,
        \App\Models\User::ROLE_GS_MANAGER,
        \App\Models\User::ROLE_CMD_MANAGER,
    ], true);
@endphp

<!-- Department -->
<div class="mb-6">
    <label for="department" class="form-label required">Department</label>
    <select id="department"
            name="department"
            class="form-input"
            required
            @if($canRegisterDepartment) onchange="toggleNewDepartment()" @endif>
        <option value="">Select Department</option>
        @foreach($departments as $key => $value)
            <option value="{{ $key }}" {{ (string) $departmentSelected === (string) $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
        @if($canRegisterDepartment)
        <option value="__new__">+ Register new department...</option>
        @endif
    </select>

    @if($canRegisterDepartment)
    <div id="newDepartmentWrap" class="mt-2 hidden">
        <div class="flex items-start gap-2">
            <input type="text"
                   id="newDepartmentName"
                   maxlength="191"
                   placeholder="Enter new department name"
                   class="form-input flex-1 min-w-0"
                   style="width:auto">
            <button type="button"
                    id="newDepartmentBtn"
                    onclick="registerDepartment()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-60">
                <i class="fas fa-plus mr-1"></i>Register
            </button>
        </div>
    </div>
    <p id="newDepartmentMsg" class="text-sm mt-1 hidden"></p>
    @endif

    @error('department')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if($canRegisterDepartment)
<script>
function toggleNewDepartment() {
    const select = document.getElementById('department');
    const wrap = document.getElementById('newDepartmentWrap');
    if (!select || !wrap) return;

    if (select.value === '__new__') {
        wrap.classList.remove('hidden');
        document.getElementById('newDepartmentName')?.focus();
        return;
    }

    wrap.classList.add('hidden');
    setNewDepartmentMsg('', false);
}

function setNewDepartmentMsg(text, isError) {
    const msg = document.getElementById('newDepartmentMsg');
    if (!msg) return;

    msg.textContent = text;
    msg.classList.toggle('hidden', !text);
    msg.classList.remove('text-red-600', 'text-green-600');

    if (text) {
        msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
    }
}

async function registerDepartment() {
    const select = document.getElementById('department');
    const input = document.getElementById('newDepartmentName');
    const button = document.getElementById('newDepartmentBtn');
    const wrap = document.getElementById('newDepartmentWrap');
    if (!select || !input || !button) return;

    const name = input.value.trim();
    if (!name) {
        setNewDepartmentMsg('Enter the department name.', true);
        input.focus();
        return;
    }

    button.disabled = true;
    setNewDepartmentMsg('', false);

    try {
        const response = await fetch('{{ route('contracts.departments.store') }}', {
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
            throw new Error(firstError || data.message || 'Unable to register the department.');
        }

        let option = Array.from(select.options).find((item) => item.value === data.value);

        if (!option) {
            option = new Option(data.label, data.value);

            const nextOption = Array.from(select.options).find((item) =>
                item.value !== '' &&
                item.value !== '__new__' &&
                item.textContent.trim().localeCompare(data.label, undefined, { sensitivity: 'base' }) > 0
            );

            select.insertBefore(
                option,
                nextOption || select.querySelector('option[value="__new__"]')
            );
        }

        select.value = data.value;
        input.value = '';
        wrap.classList.add('hidden');
        setNewDepartmentMsg(data.message + ' Selected.', false);
    } catch (error) {
        setNewDepartmentMsg(error.message, true);
    } finally {
        button.disabled = false;
    }
}

document.getElementById('newDepartmentName')?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        registerDepartment();
    }
});

const departmentForm = document.getElementById('department')?.form;
if (departmentForm) {
    departmentForm.addEventListener('submit', function (event) {
        const select = document.getElementById('department');
        if (select && select.value === '__new__') {
            event.preventDefault();
            event.stopImmediatePropagation();
            document.getElementById('newDepartmentName')?.focus();
            setNewDepartmentMsg('Register the new department or pick one from the list.', true);
        }
    });
}
</script>
@endif
