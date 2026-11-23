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
        border: 1px solid #E5E7EB;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .table-header {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        border-bottom: 2px solid #E5E7EB;
    }

    .table-row {
        border-bottom: 1px solid #F1F5F9;
        transition: all 0.2s ease;
    }

    .table-row:hover {
        background: linear-gradient(135deg, #FAFBFC 0%, #F8FAFC 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table-cell {
        padding: 1.25rem 1rem;
        text-align: left;
        vertical-align: middle;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .search-input {
        border: 2px solid #E2E8F0;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
        background: white;
        font-size: 0.875rem;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--epss-blue);
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
        transform: translateY(-1px);
    }

    .filter-select {
        border: 2px solid #E2E8F0;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        background: white;
        color: #374151;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--epss-blue);
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
        transform: translateY(-1px);
    }

    .filter-tag {
        background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
        border: 1px solid #93C5FD;
        color: #1E40AF;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .filter-tag:hover {
        background: linear-gradient(135deg, #BFDBFE 0%, #93C5FD 100%);
        transform: translateY(-1px);
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
        transition: all 0.2s ease;
        position: relative;
    }

    .contract-link:hover {
        color: var(--epss-blue) !important;
        transform: translateX(2px);
    }

    .page-link {
        border-radius: 0.5rem;
        margin: 0 0.125rem;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background: var(--epss-blue);
        color: white;
        transform: translateY(-1px);
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .page-item {
        margin: 0 0.125rem;
    }

    .page-item.active .page-link {
        background: var(--epss-blue);
        border-color: var(--epss-blue);
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
        <div class="mb-12"></div>

        <!-- Header -->
        <div class="content-card p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-file-contract text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-1">All Contracts</h1>
                            <div class="h-1 w-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-lg">Manage and monitor all contracts in the system with advanced filtering and search capabilities</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('contracts.export-list', request()->only(['search', 'status', 'department'])) }}"
                       class="action-button bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white flex items-center justify-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}"
                       class="action-button bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        New Contract
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
                    <a href="{{ route('contracts.reports') }}"
                       class="action-button bg-white border-2 border-blue-500 text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Reports
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <form method="GET" action="{{ route('contracts.index') }}">
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by contract name..."
                                   class="search-input">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Status Filter -->
                <div>
                    <form method="GET" action="{{ route('contracts.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="department" value="{{ request('department') }}">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                        <select name="status"
                                onchange="this.form.submit()"
                                class="filter-select w-full">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Department Filter -->
                @if(auth()->user()->canViewAllContracts())
                <div>
                    <form method="GET" action="{{ route('contracts.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                        <select name="department"
                                onchange="this.form.submit()"
                                class="filter-select w-full">
                            <option value="">All Departments</option>
                            @foreach($departments as $key => $value)
                                <option value="{{ $key }}" {{ request('department') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif

                <!-- Per Page -->
                <div>
                    <form method="GET" action="{{ route('contracts.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="department" value="{{ request('department') }}">
                        <input type="hidden" name="page" value="1">
                        <select name="per_page"
                                onchange="this.form.submit()"
                                class="filter-select w-full">
                            <option value="5" {{ request('per_page', 5) == '5' ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Active Filters -->
            @if(request()->has('search') || request()->has('status') || request()->has('department'))
            <div class="mt-6 flex flex-wrap gap-3 items-center">
                <span class="text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded-full">
                    <i class="fas fa-filter mr-1"></i>
                    Active filters:
                </span>

                @if(request()->has('search'))
                <span class="filter-tag inline-flex items-center px-4 py-2 rounded-full text-sm">
                    <i class="fas fa-search mr-2"></i>
                    Search: {{ request('search') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('search'), ['page' => 1])) }}"
                       class="ml-3 text-blue-700 hover:text-blue-900 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                </span>
                @endif

                @if(request()->has('status'))
                <span class="filter-tag inline-flex items-center px-4 py-2 rounded-full text-sm bg-gradient-to-r from-green-100 to-emerald-100 border-green-300 text-green-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Status: {{ $statuses[request('status')] ?? request('status') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('status'), ['page' => 1])) }}"
                       class="ml-3 text-green-700 hover:text-green-900 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                </span>
                @endif

                @if(request()->has('department') && auth()->user()->canViewAllContracts())
                <span class="filter-tag inline-flex items-center px-4 py-2 rounded-full text-sm bg-gradient-to-r from-purple-100 to-violet-100 border-purple-300 text-purple-800">
                    <i class="fas fa-building mr-2"></i>
                    Department: {{ $departments[request('department')] ?? request('department') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('department'), ['page' => 1])) }}"
                       class="ml-3 text-purple-700 hover:text-purple-900 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                </span>
                @endif

                <a href="{{ route('contracts.index') }}"
                   class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-full">
                    <i class="fas fa-trash-alt mr-1"></i>
                    Clear all filters
                </a>
            </div>
            @endif
        </div>

        <!-- Contracts Table -->
        <div class="table-container">
            @if($contracts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
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
                                $html = '<a href="' . e($url) . '" class="inline-flex items-center hover:text-blue-600">';
                                if ($icon) {
                                    $html .= '<i class="fas ' . e($icon) . ' mr-1"></i>';
                                }
                                $html .= e($label) . ' <i class="fas ' . $sortIcon . ' ml-1"></i></a>';
                                return $html;
                            };
                        @endphp
                        <tr>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('id', 'ID') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('name', 'Contract Name') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('supplier_name', 'Supplier Name') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">Created By</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('start_date', 'Start Date') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('end_date', 'End Date') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('performance_guarantee_amount', 'PG Amount') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">PG End Date</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">{!! $sortLink('status', 'Status') !!}</th>
                            <th class="table-cell font-semibold text-gray-700 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr class="table-row">
                            <td class="table-cell text-gray-600">#{{ $contract->id }}</td>
                            <td class="table-cell">
                                <a href="{{ route('contracts.show', $contract) }}"
                                   class="contract-link font-semibold text-gray-800 block">
                                    {{ \Illuminate\Support\Str::limit($contract->name, 30) }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ \Illuminate\Support\Str::limit($contract->description, 40) }}
                                </p>
                            </td>
                            <td class="table-cell">
                                <span class="font-medium text-gray-800">{{ $contract->supplier_name ?? 'N/A' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800">{{ $contract->creator->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $contract->creator->getRoleDisplayName() }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-gray-600">{{ $contract->start_date->format('Y-m-d') }}</td>
                            <td class="table-cell text-gray-600">
                                {{ $contract->end_date->format('Y-m-d') }}
                                @if($contract->extended_count > 0)
                                <span class="text-xs text-yellow-600 font-medium ml-1">Extended {{ $contract->extended_count }}x</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600">
                                @if($contract->performance_guarantee_amount)
                                ${{ number_format($contract->performance_guarantee_amount, 2) }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600">
                                @if($contract->performance_guarantee_end_date)
                                {{ $contract->performance_guarantee_end_date->format('Y-m-d') }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <span class="status-badge
                                    @if($contract->status === 'active') bg-green-100 text-green-800
                                    @elseif($contract->status === 'expiring') bg-yellow-100 text-yellow-800
                                    @elseif($contract->status === 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($contract->status) }}
                                </span>
                                @if($contract->status === 'expiring')
                                <p class="text-xs text-yellow-600 mt-1">
                                    {{ $contract->getDaysUntilContractEnd() }} days left
                                </p>
                                @endif
                            </td>
                            <td class="table-cell">
                                <div class="flex space-x-2">
                                    <a href="{{ route('contracts.show', $contract) }}"
                                       class="text-[#1C75BC] hover:text-[#1664a0]"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($contract->canUserEdit(auth()->user()))
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="text-green-600 hover:text-green-800"
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
                                                class="text-red-600 hover:text-red-800"
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

        <!-- Pagination -->
        @if($contracts->count() > 0)
        <div class="mt-6 flex justify-center">
            {{ $contracts->appends(request()->query())->links() }}
        </div>
        @endif


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

    list.innerHTML = items.map(function (item) {
        const title = item.url
            ? '<a href="' + escapeStatHtml(item.url) + '" class="font-medium text-[#1C75BC] hover:underline">' + escapeStatHtml(item.title) + '</a>'
            : '<span class="font-medium text-gray-800">' + escapeStatHtml(item.title) + '</span>';

        const badge = item.badge
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium ' +
                (statTones[item.tone] || statTones.gray) + '">' + escapeStatHtml(item.badge) + '</span>'
            : '';

        return '<div class="flex items-start justify-between gap-3 py-2.5">' +
            '<div class="min-w-0">' + title +
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
</script>
@endsection