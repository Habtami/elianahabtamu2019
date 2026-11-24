@extends('layouts.app')

@section('title', 'Manage Users - Admin')

@section('styles')
<style>
    .tsr-page,
    .users-modal-overlay {
        --tsr-bg: #f1f5f9;
        --tsr-surface: #ffffff;
        --tsr-text-main: #0f172a;
        --tsr-text-muted: #64748b;
        --tsr-text-light: #94a3b8;
        --tsr-primary: #0284c7;
        --tsr-primary-light: #e0f2fe;
        --tsr-success: #10b981;
        --tsr-danger: #ef4444;
        --tsr-border: #e2e8f0;
        --tsr-radius-sm: 8px;
        --tsr-radius-md: 12px;
        --tsr-shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
        --tsr-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .tsr-page {
        width: 100%;
        max-width: 1350px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        background: var(--tsr-bg);
        color: var(--tsr-text-main);
        font-family: var(--tsr-font-sans);
        -webkit-font-smoothing: antialiased;
    }

    .tsr-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .tsr-kpi-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 1.15rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--tsr-shadow-sm);
        border: 1px solid var(--tsr-border);
        border-left: 4px solid var(--card-color, var(--tsr-primary));
        overflow: hidden;
    }

    .tsr-kpi-label {
        display: block;
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--tsr-text-muted);
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .tsr-kpi-content .tsr-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        margin: 0.2rem 0;
        line-height: 1;
        color: var(--tsr-text-main);
    }

    .tsr-kpi-sub {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: var(--tsr-text-light);
    }

    .tsr-kpi-hint {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.55rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--tsr-text-light);
    }

    .tsr-kpi-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: var(--card-color, var(--tsr-primary));
        opacity: 0.2;
        flex: none;
    }

    .tsr-kpi-card.tsr-kpi-clickable {
        width: 100%;
        appearance: none;
        font-family: inherit;
        color: var(--tsr-text-main);
        text-align: left;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .tsr-kpi-card.tsr-kpi-clickable:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 6px 16px rgb(15 23 42 / 0.08);
    }

    .tsr-kpi-card.tsr-kpi-clickable:hover .tsr-kpi-hint {
        color: var(--tsr-primary);
    }

    .tsr-kpi-card.tsr-kpi-clickable:focus-visible {
        outline: 2px solid var(--tsr-primary);
        outline-offset: 2px;
    }

    .tsr-table-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        border: 1px solid var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
        overflow: hidden;
    }

    .users-table-wrap {
        width: 100%;
        overflow: hidden;
    }

    .users-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .users-table th,
    .users-table td {
        padding: 10px 8px;
        min-width: 0;
        vertical-align: middle;
        overflow: hidden;
    }

    .users-table th {
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 1.2;
        white-space: normal;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: var(--tsr-text-muted);
    }

    .users-table td {
        font-size: 0.8rem;
        line-height: 1.25;
    }

    .users-table tbody tr {
        border-bottom: 1px solid var(--tsr-border);
    }

    .users-table tbody tr:last-child {
        border-bottom: 0;
    }

    .users-sort-link {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .users-name-cell,
    .users-email-cell {
        min-width: 0;
    }

    .users-name-text,
    .users-email-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-role-badge {
        display: inline-flex;
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
        line-height: 1.2;
    }

    .users-role-badge i {
        flex: none;
    }

    .users-department {
        overflow-wrap: anywhere;
    }

    .users-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
    }

    .users-action-form {
        display: flex;
        margin: 0;
    }

    .user-action-btn {
        width: 28px;
        height: 28px;
        display: inline-flex;
        flex: none;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .users-col-name { width: 19%; }
    .users-col-email { width: 24%; }
    .users-col-role { width: 19%; }
    .users-col-department { width: 17%; }
    .users-col-actions { width: 21%; }

    .users-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1300;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgb(15 23 42 / 0.55);
    }

    .users-modal-overlay.is-open {
        display: flex;
    }

    .users-modal [hidden] {
        display: none !important;
    }

    .users-modal {
        width: 100%;
        max-width: 920px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: var(--tsr-surface);
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-md);
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.35);
    }

    .users-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--tsr-border);
    }

    .users-modal-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 0;
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--tsr-text-main);
    }

    .users-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 24px;
        padding: 0 0.5rem;
        border-radius: 999px;
        background: var(--tsr-primary-light);
        color: var(--tsr-primary);
        font-size: 0.75rem;
        font-weight: 700;
    }

    .users-modal-close {
        width: 34px;
        height: 34px;
        display: inline-flex;
        flex: none;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--tsr-border);
        border-radius: 8px;
        background: #fff;
        color: var(--tsr-text-muted);
    }

    .users-modal-close:hover {
        background: #f1f5f9;
        color: var(--tsr-text-main);
    }

    .users-modal-body {
        overflow: auto;
    }

    .users-modal-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .users-modal-table th,
    .users-modal-table td {
        padding: 10px 12px;
        min-width: 0;
        vertical-align: middle;
        text-align: left;
        overflow: hidden;
    }

    .users-modal-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
        border-bottom: 1px solid var(--tsr-border);
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .users-modal-table td {
        border-bottom: 1px solid var(--tsr-border);
        font-size: 0.82rem;
        line-height: 1.25;
    }

    .users-modal-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .users-modal-table tbody tr:hover {
        background: #f8fafc;
    }

    .users-modal-person {
        min-width: 0;
    }

    .users-modal-name,
    .users-modal-email {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-modal-name {
        font-weight: 600;
        color: var(--tsr-text-main);
    }

    .users-modal-email {
        margin-top: 0.15rem;
        font-size: 0.72rem;
        color: var(--tsr-text-muted);
    }

    .users-modal-text {
        overflow-wrap: anywhere;
    }

    .users-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .users-status-active {
        background: #dcfce7;
        color: #166534;
    }

    .users-status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .users-modal-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .users-modal-action {
        width: 28px;
        height: 28px;
        display: inline-flex;
        flex: none;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 6px;
        color: #fff;
    }

    .users-modal-col-person { width: 30%; }
    .users-modal-col-role { width: 22%; }
    .users-modal-col-department { width: 20%; }
    .users-modal-col-status { width: 13%; }
    .users-modal-col-actions { width: 15%; }

    .users-modal-state {
        padding: 2.5rem 1rem;
        text-align: center;
        color: var(--tsr-text-muted);
    }

    .users-modal-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1.4rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
    }

    .users-modal-summary {
        font-size: 0.8rem;
        color: var(--tsr-text-muted);
    }

    .users-modal-pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.3rem;
    }

    .users-modal-page-btn {
        min-width: 30px;
        height: 30px;
        padding: 0 0.55rem;
        border: 1px solid var(--tsr-border);
        border-radius: 6px;
        background: #fff;
        color: var(--tsr-text-muted);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .users-modal-page-btn:hover:not(:disabled) {
        border-color: var(--tsr-primary);
        color: var(--tsr-primary);
    }

    .users-modal-page-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .users-modal-page-label {
        padding: 0 0.35rem;
        font-size: 0.75rem;
        color: var(--tsr-text-muted);
    }

    .users-modal-done {
        padding: 0.45rem 0.9rem;
        border: 1px solid var(--tsr-border);
        border-radius: 6px;
        background: #fff;
        color: var(--tsr-text-main);
        font-size: 0.8rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .tsr-page { padding: 1rem; }
        .users-col-name { width: 18%; }
        .users-col-email { width: 23%; }
        .users-col-role { width: 20%; }
        .users-col-department { width: 17%; }
        .users-col-actions { width: 22%; }
        .users-table th,
        .users-table td {
            padding: 8px 6px;
        }
        .users-avatar {
            display: none;
        }
        .users-name-content {
            margin-left: 0;
        }
        .users-modal-col-person { width: 28%; }
        .users-modal-col-status { width: 15%; }
        .user-action-btn,
        .users-modal-action {
            width: 26px;
            height: 26px;
        }
        .users-modal-overlay {
            padding: 0.75rem;
        }
        .users-modal {
            max-height: 92vh;
        }
        .users-modal-table th,
        .users-modal-table td {
            padding: 8px;
        }
    }

    @media (max-width: 400px) {
        .users-col-name { width: 20%; }
        .users-col-email { width: 25%; }
        .users-col-role { width: 20%; }
        .users-col-department { width: 15%; }
        .users-col-actions { width: 20%; }
        .users-actions,
        .users-modal-actions {
            gap: 3px;
        }
    }
</style>
@endsection

@section('content')
<div class="tsr-page">
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
    <h2 class="sr-only">Quick Stats</h2>
    <div class="tsr-kpi-grid" role="region" aria-label="Quick Stats">
        <button type="button" class="tsr-kpi-card tsr-kpi-clickable" style="--card-color: #3b82f6;"
                data-scope="all" data-label="Total Users" data-count="{{ $userCounts['all'] }}"
                aria-haspopup="dialog" aria-controls="userKpiModal" aria-label="View all {{ $userCounts['all'] }} users">
            <span class="tsr-kpi-content">
                <span class="tsr-kpi-label">Total Users</span>
                <span class="tsr-value">{{ $userCounts['all'] }}</span>
                <span class="tsr-kpi-sub">{{ $userCounts['all'] ? 'All accounts' : 'No accounts yet' }}</span>
                <span class="tsr-kpi-hint"><i class="fas fa-arrow-right"></i> View list</span>
            </span>
            <span class="tsr-kpi-icon"><i class="fas fa-users"></i></span>
        </button>
        <button type="button" class="tsr-kpi-card tsr-kpi-clickable" style="--card-color: #10b981;"
                data-scope="active" data-label="Active Users" data-count="{{ $userCounts['active'] }}"
                aria-haspopup="dialog" aria-controls="userKpiModal" aria-label="View {{ $userCounts['active'] }} active users">
            <span class="tsr-kpi-content">
                <span class="tsr-kpi-label">Active Users</span>
                <span class="tsr-value">{{ $userCounts['active'] }}</span>
                <span class="tsr-kpi-sub">{{ $userCounts['active'] ? 'Enabled accounts' : 'No active users' }}</span>
                <span class="tsr-kpi-hint"><i class="fas fa-arrow-right"></i> View list</span>
            </span>
            <span class="tsr-kpi-icon"><i class="fas fa-check-circle"></i></span>
        </button>
        <button type="button" class="tsr-kpi-card tsr-kpi-clickable" style="--card-color: #8b5cf6;"
                data-scope="admins" data-label="Admins" data-count="{{ $userCounts['admins'] }}"
                aria-haspopup="dialog" aria-controls="userKpiModal" aria-label="View {{ $userCounts['admins'] }} admin users">
            <span class="tsr-kpi-content">
                <span class="tsr-kpi-label">Admins</span>
                <span class="tsr-value">{{ $userCounts['admins'] }}</span>
                <span class="tsr-kpi-sub">{{ $userCounts['admins'] ? 'System administrators' : 'No admin accounts' }}</span>
                <span class="tsr-kpi-hint"><i class="fas fa-arrow-right"></i> View list</span>
            </span>
            <span class="tsr-kpi-icon"><i class="fas fa-user-shield"></i></span>
        </button>
        <button type="button" class="tsr-kpi-card tsr-kpi-clickable" style="--card-color: #ef4444;"
                data-scope="inactive" data-label="Inactive Users" data-count="{{ $userCounts['inactive'] }}"
                aria-haspopup="dialog" aria-controls="userKpiModal" aria-label="View {{ $userCounts['inactive'] }} inactive users">
            <span class="tsr-kpi-content">
                <span class="tsr-kpi-label">Inactive Users</span>
                <span class="tsr-value">{{ $userCounts['inactive'] }}</span>
                <span class="tsr-kpi-sub">{{ $userCounts['inactive'] ? 'Disabled accounts' : 'All clear — none inactive' }}</span>
                <span class="tsr-kpi-hint"><i class="fas fa-arrow-right"></i> View list</span>
            </span>
            <span class="tsr-kpi-icon"><i class="fas fa-times-circle"></i></span>
        </button>
    </div>

    <!-- Users Table Card -->
    <div class="tsr-table-card">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                    All Users
                </h2>
                <div class="flex flex-wrap items-center gap-3">
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

        <div class="users-table-wrap">
            <table class="users-table">
                <colgroup>
                    <col class="users-col-name">
                    <col class="users-col-email">
                    <col class="users-col-role">
                    <col class="users-col-department">
                    <col class="users-col-actions">
                </colgroup>
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
                        return '<a href="' . e($url) . '" class="users-sort-link hover:text-blue-600">'
                            . '<i class="fas ' . e($icon) . '"></i>' . e($label)
                            . ' <i class="fas ' . $sortIcon . '"></i></a>';
                    };
                @endphp
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col">
                            {!! $sortLink('name', 'Name', 'fa-user') !!}
                        </th>
                        <th scope="col">
                            {!! $sortLink('email', 'Email', 'fa-envelope') !!}
                        </th>
                        <th scope="col">
                            {!! $sortLink('role', 'Role', 'fa-user-tag') !!}
                        </th>
                        <th scope="col">
                            {!! $sortLink('department', 'Department', 'fa-building') !!}
                        </th>
                        <th scope="col">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td>
                                            <div class="flex items-center min-w-0">
                                                <div class="users-avatar flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                </div>
                                                <div class="users-name-content ml-3 min-w-0">
                                                    <div class="users-name-text text-sm font-medium text-gray-900" title="{{ $user->name }}">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="users-email-text text-gray-900" title="{{ $user->email }}">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            <span class="users-role-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-user-tag mr-1"></i>{{ $user->getRoleDisplayName() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="users-department text-gray-900">{{ $user->department ? $user->getDepartmentDisplayName() : '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="users-actions">
                                                <button type="button" onclick="openUserHistory({{ $user->id }})" class="user-action-btn bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700 transition duration-200" title="View History">
                                                    <i class="fas fa-history"></i>
                                                </button>

                                                <a href="{{ route('admin.users.edit', $user) }}" class="user-action-btn bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition duration-200" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if ($user->id !== auth()->id())
                                                    @if (!$user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" class="users-action-form" onsubmit="return confirm('Are you sure you want to reset this user\'s password to the default?');">
                                                        @csrf
                                                        <button type="submit" class="user-action-btn bg-orange-600 text-white text-xs font-medium rounded hover:bg-orange-700 transition duration-200" title="Reset Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="users-action-form">
                                                        @csrf
                                                        <button type="submit" class="user-action-btn {{ $user->status ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-xs font-medium rounded transition duration-200" title="{{ $user->status ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas fa-{{ $user->status ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>

                                                    @if (!$user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="users-action-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        <button type="submit" class="user-action-btn bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition duration-200" title="Delete User">
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

        <!-- Pagination Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="users-pagination flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                    Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                </div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<div class="users-modal-overlay" id="userKpiModal" role="dialog" aria-modal="true" aria-labelledby="userKpiModalTitle" aria-hidden="true">
    <div class="users-modal">
        <div class="users-modal-header">
            <h3 class="users-modal-title" id="userKpiModalTitle">
                <i class="fas fa-users text-blue-600"></i>
                <span id="userKpiModalLabel">Users</span>
                <span class="users-count-badge" id="userKpiModalCount">0</span>
            </h3>
            <button type="button" class="users-modal-close" id="closeUserKpiModal" aria-label="Close user list">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="users-modal-body" id="userKpiModalBody" aria-live="polite">
            <div class="users-modal-state" id="userKpiModalLoading">
                <i class="fas fa-spinner fa-spin mr-2"></i>Loading users...
            </div>
            <table class="users-modal-table" id="userKpiModalTable" hidden>
                <colgroup>
                    <col class="users-modal-col-person">
                    <col class="users-modal-col-role">
                    <col class="users-modal-col-department">
                    <col class="users-modal-col-status">
                    <col class="users-modal-col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">User</th>
                        <th scope="col">Role</th>
                        <th scope="col">Department</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="userKpiModalRows"></tbody>
            </table>
            <div class="users-modal-state" id="userKpiModalEmpty" hidden></div>
        </div>
        <div class="users-modal-footer">
            <div class="users-modal-summary" id="userKpiModalSummary">Loading...</div>
            <div class="users-modal-pagination" id="userKpiModalPagination" hidden>
                <button type="button" class="users-modal-page-btn" data-kpi-page="first" aria-label="First page">First</button>
                <button type="button" class="users-modal-page-btn" data-kpi-page="prev" aria-label="Previous page">Prev</button>
                <span class="users-modal-page-label" id="userKpiModalPageLabel">Page 1 of 1</span>
                <button type="button" class="users-modal-page-btn" data-kpi-page="next" aria-label="Next page">Next</button>
                <button type="button" class="users-modal-page-btn" data-kpi-page="last" aria-label="Last page">Last</button>
            </div>
            <button type="button" class="users-modal-done" id="doneUserKpiModal">Close</button>
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

const userKpiModal = document.getElementById('userKpiModal');
const userKpiModalLabel = document.getElementById('userKpiModalLabel');
const userKpiModalCount = document.getElementById('userKpiModalCount');
const userKpiModalLoading = document.getElementById('userKpiModalLoading');
const userKpiModalTable = document.getElementById('userKpiModalTable');
const userKpiModalRows = document.getElementById('userKpiModalRows');
const userKpiModalEmpty = document.getElementById('userKpiModalEmpty');
const userKpiModalSummary = document.getElementById('userKpiModalSummary');
const userKpiModalPagination = document.getElementById('userKpiModalPagination');
const userKpiModalPageLabel = document.getElementById('userKpiModalPageLabel');
const userKpiEmptyMessages = {
    all: 'No user accounts found.',
    active: 'No active users found.',
    admins: 'No system administrator accounts found.',
    inactive: 'All clear — no inactive users.',
};

let activeKpiCard = null;
let activeKpiScope = 'all';
let activeKpiPage = 1;
let kpiRequestController = null;

function showUserKpiLoading() {
    userKpiModalLoading.hidden = false;
    userKpiModalTable.hidden = true;
    userKpiModalEmpty.hidden = true;
    userKpiModalPagination.hidden = true;
    userKpiModalSummary.textContent = 'Loading...';
}

function renderUserKpiRows(users) {
    userKpiModalRows.innerHTML = users.map(function (user) {
        const statusClass = user.status ? 'users-status-active' : 'users-status-inactive';
        const statusLabel = user.status ? 'Active' : 'Inactive';
        const name = escapeHtml(user.name);
        const email = escapeHtml(user.email);
        const role = escapeHtml(user.role);
        const department = escapeHtml(user.department);
        const editUrl = escapeHtml(user.edit_url);

        return '<tr>'
            + '<td><div class="users-modal-person" title="' + name + '">'
            + '<span class="users-modal-name">' + name + '</span>'
            + '<span class="users-modal-email" title="' + email + '">' + email + '</span>'
            + '</div></td>'
            + '<td><div class="users-modal-text">' + role + '</div></td>'
            + '<td><div class="users-modal-text">' + department + '</div></td>'
            + '<td><span class="users-status-badge ' + statusClass + '">' + statusLabel + '</span></td>'
            + '<td><div class="users-modal-actions">'
            + '<button type="button" class="users-modal-action bg-purple-600 hover:bg-purple-700" title="View History" aria-label="View history for ' + name + '" onclick="closeUserKpiModal();openUserHistory(' + Number(user.id) + ')">'
            + '<i class="fas fa-history"></i></button>'
            + '<a href="' + editUrl + '" class="users-modal-action bg-blue-600 hover:bg-blue-700" title="Edit User" aria-label="Edit ' + name + '">'
            + '<i class="fas fa-edit"></i></a>'
            + '</div></td>'
            + '</tr>';
    }).join('');
}

function renderUserKpiPagination(data) {
    const hasMultiplePages = data.last_page > 1;
    userKpiModalPagination.hidden = !hasMultiplePages;
    userKpiModalPageLabel.textContent = 'Page ' + data.page + ' of ' + data.last_page;

    const firstButton = userKpiModalPagination.querySelector('[data-kpi-page="first"]');
    const prevButton = userKpiModalPagination.querySelector('[data-kpi-page="prev"]');
    const nextButton = userKpiModalPagination.querySelector('[data-kpi-page="next"]');
    const lastButton = userKpiModalPagination.querySelector('[data-kpi-page="last"]');

    firstButton.disabled = data.page <= 1;
    prevButton.disabled = data.page <= 1;
    nextButton.disabled = data.page >= data.last_page;
    lastButton.disabled = data.page >= data.last_page;
}

async function loadUserKpiPage(page) {
    activeKpiPage = page;
    showUserKpiLoading();

    if (kpiRequestController) {
        kpiRequestController.abort();
    }

    kpiRequestController = new AbortController();
    const params = new URLSearchParams({ scope: activeKpiScope, page: String(page) });

    try {
        const response = await fetch(historyBaseUrl + '/details?' + params.toString(), {
            headers: { 'Accept': 'application/json' },
            signal: kpiRequestController.signal,
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        const data = await response.json();
        userKpiModalLoading.hidden = true;
        userKpiModalCount.textContent = data.total;
        userKpiModalSummary.textContent = data.first_item
            ? 'Showing ' + data.first_item + '–' + data.last_item + ' of ' + data.total + ' users'
            : 'Showing 0 of ' + data.total + ' users';

        if (data.users.length) {
            renderUserKpiRows(data.users);
            userKpiModalTable.hidden = false;
            userKpiModalEmpty.hidden = true;
        } else {
            userKpiModalRows.innerHTML = '';
            userKpiModalTable.hidden = true;
            userKpiModalEmpty.textContent = userKpiEmptyMessages[activeKpiScope] || userKpiEmptyMessages.all;
            userKpiModalEmpty.hidden = false;
        }

        renderUserKpiPagination(data);
    } catch (error) {
        if (error.name === 'AbortError') {
            return;
        }

        userKpiModalLoading.hidden = true;
        userKpiModalTable.hidden = true;
        userKpiModalEmpty.textContent = 'Could not load users. Close this window and try again.';
        userKpiModalEmpty.hidden = false;
        userKpiModalSummary.textContent = 'Unable to load the user list.';
    }
}

function openUserKpiModal(card) {
    activeKpiCard = card;
    activeKpiScope = card.dataset.scope;
    userKpiModalLabel.textContent = card.dataset.label || 'Users';
    userKpiModalCount.textContent = card.dataset.count || '0';
    userKpiModal.classList.add('is-open');
    userKpiModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    document.getElementById('closeUserKpiModal').focus();
    loadUserKpiPage(1);
}

function closeUserKpiModal() {
    if (!userKpiModal.classList.contains('is-open')) {
        return;
    }

    if (kpiRequestController) {
        kpiRequestController.abort();
    }

    userKpiModal.classList.remove('is-open');
    userKpiModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    if (activeKpiCard) {
        activeKpiCard.focus();
    }
}

document.querySelectorAll('.tsr-kpi-card[data-scope]').forEach(function (card) {
    card.addEventListener('click', function () {
        openUserKpiModal(card);
    });
});

document.getElementById('closeUserKpiModal').addEventListener('click', closeUserKpiModal);
document.getElementById('doneUserKpiModal').addEventListener('click', closeUserKpiModal);
userKpiModal.addEventListener('click', function (event) {
    if (event.target === userKpiModal) {
        closeUserKpiModal();
    }
});
userKpiModalPagination.addEventListener('click', function (event) {
    const button = event.target.closest('[data-kpi-page]');
    if (!button || button.disabled) {
        return;
    }

    const targetPage = {
        first: 1,
        prev: activeKpiPage - 1,
        next: activeKpiPage + 1,
        last: Number(userKpiModalPageLabel.textContent.match(/of (\d+)/)?.[1] || 1),
    }[button.dataset.kpiPage];

    loadUserKpiPage(targetPage);
});
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && userKpiModal.classList.contains('is-open')) {
        closeUserKpiModal();
    }
});

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