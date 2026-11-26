@extends('layouts.app')

@section('title', 'Permissions & Roles - Admin')

@section('content')
<div class="container mx-auto px-2 py-8 max-w-full lg:px-4">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Admin</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Permissions</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                Roles &amp; Permissions
            </h1>
            <p class="text-gray-600 mt-2">Tick a box to grant a permission to a role, untick it to revoke it.</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('admin.roles') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition duration-200">
                <i class="fas fa-user-shield mr-2"></i>
                Manage Roles
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-users mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Permission Matrix -->
    <form method="POST" action="{{ route('admin.permissions.update') }}" id="permissionsForm">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 px-6 py-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Role Permission Matrix</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Changes take effect immediately for every user holding the affected role.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipPermissions"><i class="fas fa-info-circle"></i> TIP: info</button>
                    <span class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Super Administrator always keeps every permission.
                    </span>
                </div>
            </div>

            <div id="tipPermissions" class="table-tip-panel no-print">
                <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
                <ul>
                    <li>Rows are permissions grouped under module header rows; each role column holds one checkbox per permission.</li>
                    <li>"Toggle all" in a role heading ticks or clears every box for that role, and the count beside it updates live.</li>
                    <li>Super Administrator is always fully granted, so its column is disabled and its toggle is greyed out.</li>
                    <li>A small "modified" note marks any box that differs from that role's default setting.</li>
                    <li>"Save Permissions" stores every ticked box; "Reset to Defaults" asks for confirmation, then restores the shipped matrix.</li>
                    <li>Below the matrix, each role card summarises the permissions that role currently holds, grouped by module.</li>
                </ul>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" style="min-width: 320px;">
                                Permission
                            </th>
                            @foreach($roles as $roleKey => $roleLabel)
                                <th class="px-3 py-3 text-center align-bottom whitespace-nowrap" style="min-width: 140px;">
                                    <div class="font-semibold text-gray-700">{{ $roleLabel }}</div>
                                    <label class="inline-flex items-center mt-1 text-xs text-gray-500 font-normal {{ $roleKey === 'super_admin' ? 'opacity-50' : 'cursor-pointer' }}">
                                        <input type="checkbox" class="role-toggle mr-1" data-role="{{ $roleKey }}" {{ $roleKey === 'super_admin' ? 'disabled' : '' }}>
                                        Toggle all
                                    </label>
                                    <div class="text-xs text-gray-400 mt-1 role-count" data-role="{{ $roleKey }}"></div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $moduleKey => $module)
                            <tr class="bg-gray-100">
                                <td colspan="{{ count($roles) + 1 }}" class="px-4 py-2">
                                    <span class="font-semibold text-gray-700">
                                        <i class="fas {{ $module['icon'] ?? 'fa-circle' }} mr-2" style="color: {{ $module['color'] ?? '#6c757d' }};"></i>
                                        {{ $module['label'] }}
                                    </span>
                                </td>
                            </tr>

                            @foreach($catalog as $permissionKey => $permission)
                                @if(($permission['module'] ?? '') === $moduleKey)
                                    <tr class="border-b border-gray-100 hover:bg-blue-50/40">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $permission['label'] }}</div>
                                            @if($permission['description'])
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $permission['description'] }}</div>
                                            @endif
                                            <code class="text-[11px] text-gray-400">{{ $permissionKey }}</code>
                                        </td>
                                        @foreach($roles as $roleKey => $roleLabel)
                                            @php
                                                $granted = in_array($permissionKey, $matrix[$roleKey] ?? [], true);
                                                $isDefault = in_array($permissionKey, $defaults[$roleKey] ?? [], true);
                                            @endphp
                                            <td class="px-3 py-3 text-center">
                                                <input type="checkbox"
                                                       name="permissions[{{ $roleKey }}][]"
                                                       value="{{ $permissionKey }}"
                                                       class="permission-checkbox h-4 w-4 rounded border-gray-300 accent-blue-600 focus:ring-blue-500"
                                                       data-role="{{ $roleKey }}"
                                                       {{ $granted ? 'checked' : '' }}
                                                       {{ $roleKey === 'super_admin' ? 'disabled' : '' }}
                                                       title="{{ $roleLabel }} - {{ $permission['label'] }}">
                                                @if($roleKey !== 'super_admin' && $granted !== $isDefault)
                                                    <span class="block text-[10px] text-amber-600 mt-0.5">modified</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                <p class="text-xs text-gray-500">
                    Unchecked permissions are removed for the role and the role loses the corresponding feature access.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" id="resetPermissionsButton" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition duration-200">
                        <i class="fas fa-rotate-left mr-2"></i>
                        Reset to Defaults
                    </button>
                    <button type="submit" id="savePermissionsButton" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.permissions.reset') }}" id="resetPermissionsForm" class="hidden">
        @csrf
    </form>

    <!-- Role summary -->
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Role Summary</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
        @foreach($roles as $roleKey => $roleLabel)
            @php
                $roleColor = \App\Models\User::roleRecords()[$roleKey]['color'] ?? '#6c757d';
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 rounded-t-lg" style="background: {{ $roleColor }};">
                    <h3 class="text-white font-semibold">{{ $roleLabel }}</h3>
                    <span class="text-xs text-white/80">
                        {{ count($matrix[$roleKey] ?? []) }} of {{ count($catalog) }} permissions
                    </span>
                </div>
                <div class="px-4 py-3">
                    @if(empty($matrix[$roleKey]))
                        <p class="text-sm text-gray-500">No permissions granted.</p>
                    @else
                        @foreach($modules as $moduleKey => $module)
                            @php
                                $grantedInModule = array_filter($catalog, function ($permission) use ($moduleKey, $roleKey, $matrix) {
                                    return ($permission['module'] ?? '') === $moduleKey
                                        && in_array($permission['key'], $matrix[$roleKey] ?? [], true);
                                });
                            @endphp
                            @if(!empty($grantedInModule))
                                <p class="text-[11px] uppercase tracking-wide text-gray-400 mt-2 mb-1">{{ $module['label'] }}</p>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($grantedInModule as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">
                                            {{ $permission['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('permissionsForm');
    const saveButton = document.getElementById('savePermissionsButton');
    const resetButton = document.getElementById('resetPermissionsButton');
    const resetForm = document.getElementById('resetPermissionsForm');

    function checkboxesFor(role) {
        return document.querySelectorAll('.permission-checkbox[data-role="' + role + '"]:not([disabled])');
    }

    function updateCounts() {
        document.querySelectorAll('.role-count').forEach(function (element) {
            const role = element.getAttribute('data-role');
            const boxes = document.querySelectorAll('.permission-checkbox[data-role="' + role + '"]');
            const checked = document.querySelectorAll('.permission-checkbox[data-role="' + role + '"]:checked');
            element.textContent = checked.length + ' / ' + boxes.length;
        });

        document.querySelectorAll('.role-toggle').forEach(function (toggle) {
            const role = toggle.getAttribute('data-role');
            const boxes = checkboxesFor(role);
            const checked = document.querySelectorAll('.permission-checkbox[data-role="' + role + '"]:checked');
            toggle.checked = boxes.length > 0 && boxes.length === checked.length;
        });
    }

    document.querySelectorAll('.role-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const role = toggle.getAttribute('data-role');
            checkboxesFor(role).forEach(function (box) {
                box.checked = toggle.checked;
            });
            updateCounts();
        });
    });

    document.querySelectorAll('.permission-checkbox').forEach(function (box) {
        box.addEventListener('change', updateCounts);
    });

    if (form && saveButton) {
        form.addEventListener('submit', function () {
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        });
    }

    if (resetButton && resetForm) {
        resetButton.addEventListener('click', function () {
            if (confirm('Restore the default permission matrix? All custom changes will be lost.')) {
                resetForm.submit();
            }
        });
    }

    updateCounts();
});
</script>
@endsection
