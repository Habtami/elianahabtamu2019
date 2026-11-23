@extends('layouts.app')

@section('title', 'All Contracts')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-light: #E8F4FD;
        --epss-blue-dark: #1664a0;
        --success: #10B981;
        --warning: hsla(59, 92%, 50%, 0.941);
        --danger: #EF4444;
        --info: #3B82F6;
    }

    .page-container {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
    }

    .content-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .content-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #F3F4F6;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .table-header {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
    }

    .contracts-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .table-header th {
        padding: 0.75rem 0.625rem;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #6B7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
        vertical-align: middle;
    }

    .table-header th.text-right {
        text-align: right;
    }

    .table-header th.text-center {
        text-align: center;
    }

    .table-th-sort {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .table-th-sort:hover {
        background: #F3F4F6;
    }

    .sort-arrow {
        font-size: 9px;
    }

    @media (max-width: 1400px) {
        .table-header th {
            padding: 0.75rem 0.375rem;
            letter-spacing: 0;
        }

        .table-cell {
            padding: 0.75rem 0.4375rem;
        }

        .sort-arrow {
            font-size: 8px;
        }
    }

    @media (max-width: 1200px) {
        .table-header th {
            padding: 0.625rem 0.25rem;
            font-size: 9px;
        }

        .table-cell {
            padding: 0.625rem 0.3125rem;
        }
    }

    @media (max-width: 1100px) {
        .table-header th {
            white-space: normal;
            line-height: 1.15;
        }
    }

    .table-row {
        border-bottom: 1px solid #F3F4F6;
        transition: background-color 0.15s ease;
    }

    .table-row:last-child {
        border-bottom: none;
    }

    .table-row:hover {
        background: #F9FAFB;
    }

    .table-cell {
        padding: 0.75rem 0.625rem;
        text-align: left;
        vertical-align: middle;
        overflow: hidden;
        min-width: 0;
    }

    .table-cell.text-right {
        text-align: right;
    }

    .table-cell.text-center {
        text-align: center;
    }

    .status-badge {
        font-size: 0.6875rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        border-width: 1px;
        border-style: solid;
        line-height: 1.25;
        white-space: nowrap;
    }

    .filter-card {
        background: #ffffff;
        border: 1px solid #F3F4F6;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .filter-control {
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        background: #ffffff;
    }

    .filter-control:focus {
        outline: none;
        border-color: #3B82F6;
    }

    .filter-pill {
        border: 1px solid #BBF7D0;
        border-radius: 9999px;
    }

    body.dark-theme .filter-card {
        background: #111a2b;
        border-color: #1e293b;
    }

    body.dark-theme .filter-control {
        background-color: #0f1a2c;
        border-color: #334155;
        color: #e2e8f0;
    }

    body.dark-theme .filter-control::placeholder {
        color: #64748b;
    }

    body.dark-theme .filter-pill {
        background-color: #122b22;
        border-color: #166534;
        color: #86efac;
    }

    .action-button {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.15);
    }

    .empty-state {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        border: 2px dashed #CBD5E1;
        border-radius: 1rem;
    }

    .contract-link {
        transition: color 0.2s ease;
    }

    .contract-link:hover {
        color: var(--epss-blue) !important;
    }

    body.dark-theme .table-header th {
        color: #94a3b8;
    }

    body.dark-theme .table-th-sort:hover {
        background: #1b2a45;
    }

    body.dark-theme .table-row:hover {
        background: #16233a;
    }

    body.dark-theme .table-footer {
        background: #111a2b;
        border-color: #1e293b;
    }

    body.dark-theme .table-footer .bg-blue-50 {
        background: #1e3a5f !important;
        border-color: #1d4ed8 !important;
        color: #93c5fd !important;
    }

    body.dark-theme .table-footer a:hover {
        background: #16233a;
    }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="kpi-card blue" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('total-contracts', 'Total Contracts')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Total Contracts</p>
                    <p class="kpi-value">{{ $totalContracts }}</p>
                    <p class="kpi-subtitle">All contracts in system</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-file-contract"></i></div>
            </div>

            <div class="kpi-card green" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('active-contracts', 'Active Contracts')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Active</p>
                    <p class="kpi-value">{{ $totalActive }}</p>
                    <p class="kpi-subtitle">Currently active contracts</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>

            <div class="kpi-card yellow" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('expiring-contracts', 'Expiring Soon')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Expiring Soon</p>
                    <p class="kpi-value">{{ $totalExpiring }}</p>
                    <p class="kpi-subtitle">Require attention</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </div>

            <div class="kpi-card red" role="button" tabindex="0" style="cursor:pointer"
                 onclick="openStatModal('expired-contracts', 'Expired Contracts')"
                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <div>
                    <p class="kpi-title">Expired</p>
                    <p class="kpi-value">{{ $totalExpired }}</p>
                    <p class="kpi-subtitle">Past expiration date</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
            </div>
        </div>

        <!-- Spacer -->
        <div class="mb-6"></div>

        <!-- Header -->
        <div class="content-card px-5 py-4 mb-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md shrink-0">
                        <i class="fas fa-file-contract text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-gray-800 leading-tight">All Contracts</h1>
                        <p class="text-sm text-gray-500 truncate">Manage and monitor all contracts in the system</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('contracts.export-list', request()->only(['search', 'status', 'department'])) }}"
                       class="inline-flex items-center rounded-lg bg-green-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}"
                       class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        New Contract
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
                    <a href="{{ route('contracts.reports') }}"
                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-2 text-sm font-semibold text-blue-700 transition-colors hover:bg-blue-100">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Reports
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <form method="GET" action="{{ route('contracts.index') }}" id="contractFilterForm" class="filter-card p-4 mb-4">
            @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if(request('sort_direction'))
            <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif
            @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <!-- Top Row: Search on the left, dropdowns on the right -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text"
                           id="contractSearch"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by contract name..."
                           autocomplete="off"
                           class="filter-control w-full pl-9 pr-4 py-2 text-sm transition-shadow">
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="filter-control w-full md:w-auto px-3 py-2 text-sm cursor-pointer">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>

                    @if(auth()->user()->canViewAllContracts())
                    <select name="department"
                            onchange="this.form.submit()"
                            class="filter-control w-full md:w-auto px-3 py-2 text-sm cursor-pointer">
                        <option value="">All Departments</option>
                        @foreach($departments as $key => $value)
                            <option value="{{ $key }}" {{ request('department') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                </div>
            </div>

            <!-- Bottom Row: Active filters & Clear all -->
            @if(request()->filled('search')
                || (request()->filled('status') && array_key_exists(request('status'), $statuses))
                || (request()->filled('department') && auth()->user()->canViewAllContracts() && array_key_exists(request('department'), $departments)))
            <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500 flex items-center gap-1.5">
                    <i class="fas fa-filter text-sm"></i> Active:
                </span>

                @if(request()->filled('search'))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Search: {{ request('search') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('search'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                @if(request()->filled('status') && array_key_exists(request('status'), $statuses))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Status: {{ $statuses[request('status')] }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('status'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                @if(request()->filled('department') && auth()->user()->canViewAllContracts() && array_key_exists(request('department'), $departments))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Department: {{ $departments[request('department')] }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('department'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                <a href="{{ route('contracts.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                    Clear all
                </a>
            </div>
            @endif
        </form>

        <!-- Contracts Table -->
        <div class="table-container">
            @if($contracts->count() > 0)
                <table class="contracts-table text-left">
                    <thead class="table-header">
                        @php
                            $sortLink = function (string $column, string $label, ?string $icon = null) use ($sortBy, $sortDirection) {
                                $nextDirection = ($sortBy === $column && $sortDirection === 'asc') ? 'desc' : 'asc';
                                $url = route('contracts.index', array_merge(request()->query(), [
                                    'sort_by' => $column,
                                    'sort_direction' => $nextDirection,
                                    'page' => 1,
                                ]));
                                $sortIcon = 'fa-sort text-gray-400';
                                if ($sortBy === $column) {
                                    $sortIcon = ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') . ' text-blue-600';
                                }
                                $html = '<a href="' . e($url) . '" class="inline-flex max-w-full items-center gap-1 transition-colors hover:text-gray-700">';
                                if ($icon) {
                                    $html .= '<i class="fas ' . e($icon) . ' mr-1 shrink-0"></i>';
                                }
                                $html .= '<span class="min-w-0 truncate">' . e($label) . '</span>';
                                $html .= '<i class="fas ' . $sortIcon . ' sort-arrow shrink-0"></i></a>';
                                return $html;
                            };
                        @endphp
                        <tr>
                            <th class="table-th-sort" style="width:5%">{!! $sortLink('id', 'ID') !!}</th>
                            <th class="table-th-sort" style="width:18%">{!! $sortLink('name', 'Contract Name') !!}</th>
                            <th class="table-th-sort" style="width:9.5%">{!! $sortLink('supplier_name', 'Supplier') !!}</th>
                            <th style="width:11%">Created By</th>
                            <th class="table-th-sort" style="width:9.5%">{!! $sortLink('start_date', 'Start Date') !!}</th>
                            <th class="table-th-sort" style="width:9.5%">{!! $sortLink('end_date', 'End Date') !!}</th>
                            <th class="table-th-sort text-right" style="width:9%">{!! $sortLink('performance_guarantee_amount', 'PG Amount') !!}</th>
                            <th style="width:9%">PG End Date</th>
                            <th class="table-th-sort text-center" style="width:8%">{!! $sortLink('status', 'Status') !!}</th>
                            <th class="text-right" style="width:11.5%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr class="table-row">
                            <td class="table-cell text-xs text-gray-500 font-mono whitespace-nowrap">#{{ $contract->id }}</td>
                            <td class="table-cell">
                                <a href="{{ route('contracts.show', $contract) }}"
                                   title="{{ $contract->name }}"
                                   class="contract-link block truncate text-sm font-semibold text-gray-900">
                                    {{ $contract->name }}
                                </a>
                                @if($contract->description)
                                <p class="mt-0.5 truncate text-xs text-gray-500" title="{{ $contract->description }}">
                                    {{ $contract->description }}
                                </p>
                                @endif
                            </td>
                            <td class="table-cell">
                                <span class="block truncate text-sm text-gray-700" title="{{ $contract->supplier_name }}">{{ $contract->supplier_name ?? 'N/A' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="block truncate text-sm text-gray-900" title="{{ $contract->creator->name }}">{{ $contract->creator->name }}</div>
                                <div class="block truncate text-xs text-gray-400" title="{{ $contract->creator->getRoleDisplayName() }}">{{ $contract->creator->getRoleDisplayName() }}</div>
                            </td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">{{ $contract->start_date->format('Y-m-d') }}</td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">
                                {{ $contract->end_date->format('Y-m-d') }}
                                @if($contract->extended_count > 0)
                                <span class="mt-0.5 block truncate text-[10px] font-medium text-amber-600" title="Extended {{ $contract->extended_count }}x">Extended {{ $contract->extended_count }}x</span>
                                @endif
                            </td>
                            <td class="table-cell text-right text-xs font-medium text-gray-900 whitespace-nowrap">
                                @if($contract->performance_guarantee_amount)
                                ${{ number_format($contract->performance_guarantee_amount, 2) }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">
                                @if($contract->performance_guarantee_end_date)
                                {{ $contract->performance_guarantee_end_date->format('Y-m-d') }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <span class="status-badge
                                    @if($contract->status === 'active') bg-emerald-50 text-emerald-700 border-emerald-100
                                    @elseif($contract->status === 'expiring') bg-amber-50 text-amber-700 border-amber-100
                                    @elseif($contract->status === 'expired') bg-red-50 text-red-700 border-red-100
                                    @else bg-gray-50 text-gray-600 border-gray-200 @endif">
                                    {{ ucfirst($contract->status) }}
                                </span>
                                @if($contract->status === 'expiring')
                                <div class="mt-1 truncate text-[10px] text-amber-600" title="{{ $contract->getDaysUntilContractEnd() }} days left">
                                    {{ $contract->getDaysUntilContractEnd() }} days left
                                </div>
                                @endif
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('contracts.show', $contract) }}"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="View Contract">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($contract->canUserEdit(auth()->user()))
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif

                                    @if($contract->canUserDelete(auth()->user()))
                                    <form method="POST"
                                          action="{{ route('contracts.destroy', $contract) }}"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this contract?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            <!-- Pagination Footer -->
            @php
                $paginator = $contracts->appends(request()->query());
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $visiblePages = collect(range(1, max($lastPage, 1)))->filter(function ($page) use ($currentPage, $lastPage) {
                    return $page === 1 || $page === $lastPage || abs($page - $currentPage) <= 1;
                });
                $previousVisiblePage = null;
            @endphp
            <div class="table-footer bg-white border-t border-gray-200 px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
                    to <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
                    of <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> results
                </div>

                @if($paginator->hasPages())
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    @if($paginator->onFirstPage())
                    <span class="p-2 rounded-lg text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                        <span class="sr-only">Previous</span>
                    </span>
                    @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                    @endif

                    @foreach($visiblePages as $page)
                        @if($previousVisiblePage !== null && $page - $previousVisiblePage > 1)
                        <span class="px-2 text-gray-400">...</span>
                        @endif

                        @if($page === $currentPage)
                        <span class="px-3.5 py-1.5 text-sm font-medium bg-blue-50 text-blue-600 rounded-lg border border-blue-100">{{ $page }}</span>
                        @else
                        <a href="{{ $paginator->url($page) }}" class="px-3.5 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">{{ $page }}</a>
                        @endif

                        @php $previousVisiblePage = $page; @endphp
                    @endforeach

                    @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                    @else
                    <span class="p-2 rounded-lg text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                        <span class="sr-only">Next</span>
                    </span>
                    @endif
                </nav>
                @endif
            </div>

            @else
            <!-- Empty State -->
            <div class="empty-state p-16 text-center">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-file-contract text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">No contracts found</h3>
                    <p class="text-gray-500 text-lg max-w-md mx-auto">
                        @if(request()->has('search') || request()->has('status') || request()->has('department'))
                        Try adjusting your filters or search terms to find what you're looking for
                        @else
                        Get started by creating your first contract to begin managing your agreements
                        @endif
                    </p>
                </div>
                @if(auth()->user()->canCreateContracts())
                <a href="{{ route('contracts.create') }}"
                   class="action-button bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Contract
                </a>
                @endif
            </div>
            @endif
        </div>


    </div>
</div>

<!-- Stat detail modal -->
<div id="statModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="statModalTitle">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatModal()"></div>
    <div class="relative mx-auto mt-10 mb-10 w-full max-w-2xl px-4 max-h-[calc(100vh-5rem)] overflow-y-auto">
        <div class="bg-white rounded-xl shadow-xl">
            <div class="flex items-start justify-between px-5 py-4 border-b border-gray-200">
                <div>
                    <h3 id="statModalTitle" class="text-lg font-semibold text-gray-800"></h3>
                    <p id="statModalMeta" class="text-sm text-gray-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeStatModal()" aria-label="Close"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="p-5">
                <div id="statModalLoading" class="text-center py-10 text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                </div>
                <div id="statModalEmpty" class="hidden text-center py-10">
                    <i class="fas fa-inbox text-3xl text-gray-300"></i>
                    <p class="mt-3 text-gray-600">No records found.</p>
                </div>
                <div id="statModalList" class="hidden divide-y divide-gray-100"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let statRequestSeq = 0;

const statTones = {
    green: 'bg-green-100 text-green-800',
    amber: 'bg-amber-100 text-amber-800',
    red: 'bg-red-100 text-red-800',
    blue: 'bg-blue-100 text-blue-800',
    gray: 'bg-gray-100 text-gray-700',
};

function escapeStatHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (ch) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
}

function openStatModal(scope, title) {
    const modal = document.getElementById('statModal');
    if (!modal) return;

    document.getElementById('statModalTitle').textContent = title;
    document.getElementById('statModalMeta').textContent = '';
    document.getElementById('statModalList').innerHTML = '';
    document.getElementById('statModalList').classList.add('hidden');
    document.getElementById('statModalEmpty').classList.add('hidden');
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
        const response = await fetch('{{ url('/dashboard/summary') }}/' + encodeURIComponent(scope), {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json().catch(() => ({}));

        if (!response.ok) throw new Error(data.message || 'Unable to load details.');
        if (seq !== statRequestSeq) return;

        renderStatSummary(data);
    } catch (error) {
        if (seq !== statRequestSeq) return;
        document.getElementById('statModalLoading').classList.add('hidden');
        const empty = document.getElementById('statModalEmpty');
        empty.querySelector('p').textContent = error.message;
        empty.classList.remove('hidden');
    }
}

function renderStatSummary(data) {
    document.getElementById('statModalLoading').classList.add('hidden');
    document.getElementById('statModalMeta').textContent = (data.count ?? 0) + ' record(s)';

    const empty = document.getElementById('statModalEmpty');
    const list = document.getElementById('statModalList');
    const items = Array.isArray(data.items) ? data.items : [];

    if (!items.length) {
        empty.classList.remove('hidden');
        return;
    }

    list.innerHTML = items.map(function (item, index) {
        const title = item.url
            ? '<a href="' + escapeStatHtml(item.url) + '" class="font-medium text-[#1C75BC] hover:underline">' + escapeStatHtml(item.title) + '</a>'
            : '<span class="font-medium text-gray-800">' + escapeStatHtml(item.title) + '</span>';

        const badge = item.badge
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium ' +
                (statTones[item.tone] || statTones.gray) + '">' + escapeStatHtml(item.badge) + '</span>'
            : '';

        return '<div class="flex items-start gap-3 py-2.5">' +
            '<div class="w-6 shrink-0 pt-0.5 text-right text-xs font-semibold text-gray-400">' + (index + 1) + '</div>' +
            '<div class="min-w-0 flex-1">' + title +
            (item.subtitle ? '<div class="text-sm text-gray-600 truncate">' + escapeStatHtml(item.subtitle) + '</div>' : '') +
            (item.meta ? '<div class="text-xs text-gray-500 mt-0.5">' + escapeStatHtml(item.meta) + '</div>' : '') +
            '</div>' +
            (badge ? '<div class="shrink-0">' + badge + '</div>' : '') +
            '</div>';
    }).join('');

    list.classList.remove('hidden');
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeStatModal();
});

(function () {
    const form = document.getElementById('contractFilterForm');
    const input = document.getElementById('contractSearch');
    if (!form || !input) return;

    let timer = null;
    let appliedValue = input.value;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if (input.value === appliedValue) return;
            appliedValue = input.value;
            form.submit();
        }, 600);
    });

    form.addEventListener('submit', function () {
        clearTimeout(timer);
    });
})();
</script>
@endsection