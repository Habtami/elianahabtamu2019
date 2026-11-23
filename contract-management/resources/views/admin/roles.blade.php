@extends('layouts.app')

@section('title', 'Manage Roles - Admin')

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
                    <span class="text-sm font-medium text-gray-500">Roles</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-shield text-blue-600 mr-3"></i>
                Role Management
            </h1>
            <p class="text-gray-600 mt-2">Create roles and control what each of them is allowed to do.</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('admin.permissions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-check-square mr-2"></i>
                Permission Matrix
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-users mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Role list -->
        <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Roles</h2>
                <p class="text-sm text-gray-600 mt-1">{{ count($roles) }} role(s) defined in the system.</p>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($roles as $role)
                    @php
                        $isProtected = in_array($role['key'], \App\Models\Role::protectedKeys(), true);
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div class="flex items-start">
                                <span class="w-3 h-10 rounded mr-3" style="background: {{ $role['color'] }};"></span>
                                <div>
                                    <div class="flex items-center flex-wrap gap-2">
                                        <h3 class="font-semibold text-gray-800">{{ $role['name'] }}</h3>
                                        @if(! $role['is_active'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Inactive</span>
                                        @endif
                                        @if($role['is_admin'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">Admin</span>
                                        @endif
                                        @if($isProtected)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800">System</span>
                                        @endif
                                    </div>
                                    <code class="text-xs text-gray-400">{{ $role['key'] }}</code>
                                    @if($role['description'])
                                        <p class="text-sm text-gray-600 mt-1">{{ $role['description'] }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $role['users_count'] }} user(s) &middot; {{ $role['permissions_count'] }} permission(s) granted
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition duration-200 edit-role-button"
                                        data-role="{{ $role['key'] }}"
                                        data-name="{{ $role['name'] }}"
                                        data-description="{{ $role['description'] }}"
                                        data-color="{{ $role['color'] }}"
                                        data-is-admin="{{ $role['is_admin'] ? 1 : 0 }}"
                                        data-is-active="{{ $role['is_active'] ? 1 : 0 }}">
                                    <i class="fas fa-pen mr-1"></i>
                                    Edit
                                </button>

                                @unless($isProtected)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role['key']) }}"
                                          onsubmit="return confirm('Delete the role {{ $role['name'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition duration-200">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Create role -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-fit">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">New Role</h2>
                <p class="text-sm text-gray-600 mt-1">The role appears as a new column in the permission matrix.</p>
            </div>

            <form method="POST" action="{{ route('admin.roles.store') }}" class="px-6 py-5 space-y-4">
                @csrf

                <div>
                    <label for="role_key" class="block text-sm font-medium text-gray-700 mb-1">Role Key</label>
                    <input type="text" name="key" id="role_key" required pattern="[a-z][a-z0-9_]*" placeholder="e.g. auditor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Lowercase letters, numbers and underscores. Cannot be changed later.</p>
                </div>

                <div>
                    <label for="role_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="name" id="role_name" required placeholder="e.g. Auditor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="role_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="role_description" rows="2" placeholder="What is this role responsible for?"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label for="role_color" class="block text-sm font-medium text-gray-700 mb-1">Colour</label>
                    <input type="color" name="color" id="role_color" value="#6c757d"
                           class="w-20 h-9 p-1 border border-gray-300 rounded-md cursor-pointer">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create Role
                </button>
            </form>
        </div>
    </div>

    <!-- Edit role modal -->
    <div id="editRoleModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Edit Role</h3>
                <button type="button" id="closeEditRole" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" id="editRoleForm" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" value="POST">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Key</label>
                    <p id="edit_role_key" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-600"></p>
                </div>

                <div>
                    <label for="edit_role_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="name" id="edit_role_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="edit_role_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_role_description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <div>
                        <label for="edit_role_color" class="block text-sm font-medium text-gray-700 mb-1">Colour</label>
                        <input type="color" name="color" id="edit_role_color" value="#6c757d"
                               class="w-20 h-9 p-1 border border-gray-300 rounded-md cursor-pointer">
                    </div>
                    <div class="pt-6 space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_active" id="edit_role_is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 accent-blue-600">
                            Active
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_admin" id="edit_role_is_admin" value="1" class="h-4 w-4 rounded border-gray-300 accent-blue-600">
                            Administrator role
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancelEditRole" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editRoleModal');
    const form = document.getElementById('editRoleForm');

    function openModal(button) {
        const key = button.getAttribute('data-role');

        document.getElementById('edit_role_key').textContent = key;
        document.getElementById('edit_role_name').value = button.getAttribute('data-name');
        document.getElementById('edit_role_description').value = button.getAttribute('data-description') || '';
        document.getElementById('edit_role_color').value = button.getAttribute('data-color') || '#6c757d';
        document.getElementById('edit_role_is_active').checked = button.getAttribute('data-is-active') === '1';
        document.getElementById('edit_role_is_admin').checked = button.getAttribute('data-is-admin') === '1';

        form.setAttribute('action', '{{ route('admin.roles.update', '__ROLE__') }}'.replace('__ROLE__', key));

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.querySelectorAll('.edit-role-button').forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(button);
        });
    });

    document.getElementById('closeEditRole').addEventListener('click', closeModal);
    document.getElementById('cancelEditRole').addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
@endsection
