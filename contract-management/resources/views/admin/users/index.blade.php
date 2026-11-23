@extends('layouts.app')

@section('title', 'Manage Users - Admin')

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
                    <span class="text-sm font-medium text-gray-500">Users</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                User Management
            </h1>
            <p class="text-gray-600 mt-2">Manage system users and their roles</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200">
                <i class="fas fa-plus mr-2"></i>
                New User
            </a>
            <a href="{{ route('admin.permissions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-shield-alt mr-2"></i>
                Permissions
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Errors:</strong>
            </div>
            <ul class="mt-2 ml-6 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Quick Stats
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-600 rounded-full">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600">Total Users</p>
                        <p class="text-2xl font-bold text-blue-900">{{ App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg border border-green-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-600 rounded-full">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600">Active Users</p>
                        <p class="text-2xl font-bold text-green-900">{{ App\Models\User::where('status', true)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-600 rounded-full">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-600">Admins</p>
                        <p class="text-2xl font-bold text-purple-900">{{ App\Models\User::where('role', 'admin')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-lg border border-red-200">
                <div class="flex items-center">
                    <div class="p-3 bg-red-600 rounded-full">
                        <i class="fas fa-times-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-red-600">Inactive Users</p>
                        <p class="text-2xl font-bold text-red-900">{{ App\Models\User::where('status', false)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                    All Users
                </h2>
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-600">
                        Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </div>
                    <a href="{{ route('admin.users.export', request()->only(['search', 'status_filter', 'role_filter'])) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Toolbar: search + per page directly above the table -->
        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row gap-3 md:items-center">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-1 gap-2 items-center">
                    <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                    <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
                    @if($statusFilter !== '')
                        <input type="hidden" name="status_filter" value="{{ $statusFilter }}">
                    @endif
                    @if($roleFilter !== '')
                        <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
                    @endif
                    <div class="relative flex-1 md:max-w-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </span>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Search by name, email, or role..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition duration-200" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    @if($search || $statusFilter !== '' || $roleFilter !== '')
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 transition duration-200" title="Clear filters">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    @endif
                </form>
                <div class="md:w-40">
                    <select id="per_page" onchange="changePerPage(this.value)" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $statusFilter !== '' || $roleFilter !== '')
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active filters:</span>
                    @if($search)
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                            Search: "{{ $search }}"
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['search' => ''])) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($statusFilter !== '')
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                            Status: {{ $statusFilter == '1' ? 'Active' : 'Inactive' }}
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['status_filter' => ''])) }}" class="ml-1 text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($roleFilter !== '')
                        <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                            Role: {{ ucfirst(str_replace('_', ' ', $roleFilter)) }}
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role_filter' => ''])) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <div class="overflow-hidden">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden border-b border-gray-200">
                        <table class="w-full divide-y divide-gray-200">
                            @php
                                $sortLink = function (string $column, string $label, string $icon) use ($sortBy, $sortDirection) {
                                    $nextDirection = ($sortBy === $column && $sortDirection === 'asc') ? 'desc' : 'asc';
                                    $url = route('admin.users.index', array_merge(request()->query(), [
                                        'sort_by' => $column,
                                        'sort_direction' => $nextDirection,
                                    ]));
                                    $sortIcon = 'fa-sort text-gray-400';
                                    if ($sortBy === $column) {
                                        $sortIcon = ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') . ' text-blue-600';
                                    }
                                    return '<a href="' . e($url) . '" class="inline-flex items-center hover:text-blue-600">'
                                        . '<i class="fas ' . e($icon) . ' mr-1"></i>' . e($label)
                                        . ' <i class="fas ' . $sortIcon . ' ml-1"></i></a>';
                                };
                            @endphp
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        {!! $sortLink('name', 'Name', 'fa-user') !!}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        {!! $sortLink('email', 'Email', 'fa-envelope') !!}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        {!! $sortLink('role', 'Role', 'fa-user-tag') !!}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        {!! $sortLink('department', 'Department', 'fa-building') !!}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        <i class="fas fa-cogs mr-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center min-w-0">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4 min-w-0">
                                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 break-words max-w-xs">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                                <i class="fas fa-user-tag mr-1"></i>{{ $user->getRoleDisplayName() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 break-words max-w-xs">
                                            {{ $user->department ? $user->getDepartmentDisplayName() : '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-wrap gap-1">
                                                <button type="button" onclick="openUserHistory({{ $user->id }})" class="inline-flex items-center px-2 py-1 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700 transition duration-200" title="View History">
                                                    <i class="fas fa-history"></i>
                                                </button>

                                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition duration-200" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if ($user->id !== auth()->id())
                                                    @if (!$user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reset this user\'s password to the default?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-orange-600 text-white text-xs font-medium rounded hover:bg-orange-700 transition duration-200" title="Reset Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 {{ $user->status ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-xs font-medium rounded transition duration-200" title="{{ $user->status ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas fa-{{ $user->status ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>

                                                    @if (!$user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition duration-200" title="Delete User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-12 text-gray-500">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg">No users found</p>
                                            <p class="text-sm mt-2">
                                                <a href="{{ route('admin.users.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">Create your first user</a>
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                    Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                </div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- User History Modal -->
<div id="userHistoryModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-gray-900 bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[85vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center min-w-0">
                <i class="fas fa-history text-purple-600 mr-2"></i>
                <span class="truncate">Activity History — <span id="historyUserName">...</span></span>
            </h3>
            <button type="button" id="closeUserHistory" class="text-gray-400 hover:text-gray-600 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="historyContent" class="px-6 py-4 overflow-y-auto">
            <p class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin mr-2"></i>Loading history...</p>
        </div>
    </div>
</div>

<style>
/* Additional CSS for better responsiveness on small screens */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
    
    table td, table th {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .actions-column .flex {
        justify-content: flex-start;
    }
}
</style>

<script>
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

const historyModal = document.getElementById('userHistoryModal');
const historyContent = document.getElementById('historyContent');
const historyUserName = document.getElementById('historyUserName');
const historyBaseUrl = '{{ url('/admin/users') }}';

const severityStyles = {
    info: 'bg-blue-100 text-blue-800',
    warning: 'bg-yellow-100 text-yellow-800',
    danger: 'bg-red-100 text-red-800',
};

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

function openUserHistory(userId) {
    historyUserName.textContent = '...';
    historyContent.innerHTML = '<p class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin mr-2"></i>Loading history...</p>';
    historyModal.classList.remove('hidden');
    historyModal.classList.add('flex');

    fetch(historyBaseUrl + '/' + userId + '/history', { headers: { 'Accept': 'application/json' } })
        .then(function (res) {
            if (!res.ok) throw new Error('Request failed');
            return res.json();
        })
        .then(function (data) {
            historyUserName.textContent = data.user.name + ' (' + data.user.email + ')';
            renderUserHistory(data.logs);
        })
        .catch(function () {
            historyContent.innerHTML = '<p class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle mr-2"></i>Could not load history.</p>';
        });
}

function renderUserHistory(logs) {
    if (!logs.length) {
        historyContent.innerHTML = '<p class="text-center text-gray-500 py-8"><i class="fas fa-history text-3xl text-gray-300 mb-3 block"></i>No activity recorded for this user yet.</p>';
        return;
    }

    let html = '<ul class="divide-y divide-gray-200">';
    logs.forEach(function (log) {
        const sev = severityStyles[log.severity] || severityStyles.info;
        const meta = [log.created_at, log.ip_address ? 'IP: ' + log.ip_address : null, log.reason]
            .filter(Boolean)
            .map(escapeHtml)
            .join(' &middot; ');
        html += '<li class="py-3">'
            + '<div class="flex items-start justify-between gap-4">'
            + '<div class="min-w-0">'
            + '<p class="text-sm font-medium text-gray-900">' + escapeHtml(log.description) + '</p>'
            + '<p class="text-xs text-gray-500 mt-1">' + meta + '</p>'
            + '</div>'
            + '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + sev + ' whitespace-nowrap capitalize">' + escapeHtml(String(log.type).replace(/_/g, ' ')) + '</span>'
            + '</div>'
            + '</li>';
    });
    html += '</ul>';
    if (logs.length >= 50) {
        html += '<p class="text-xs text-gray-400 text-center py-3">Showing the latest 50 entries</p>';
    }
    historyContent.innerHTML = html;
}

function closeUserHistory() {
    historyModal.classList.add('hidden');
    historyModal.classList.remove('flex');
}

document.getElementById('closeUserHistory').addEventListener('click', closeUserHistory);
historyModal.addEventListener('click', function (event) {
    if (event.target === historyModal) closeUserHistory();
});
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !historyModal.classList.contains('hidden')) closeUserHistory();
});
</script>
@endsection