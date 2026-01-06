@extends('layouts.app')

@section('title', 'All Contracts')

@section('styles')
<style>
    .table-container {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        overflow: hidden;
    }
    
    .table-header {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .table-row {
        border-bottom: 1px solid #E5E7EB;
        transition: background-color 0.2s;
    }
    
    .table-row:hover {
        background-color: #F9FAFB;
    }
    
    .table-cell {
        padding: 1rem;
        text-align: left;
        vertical-align: middle;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        display: inline-block;
    }
    
    .search-input {
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        width: 100%;
        transition: all 0.2s;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #1C75BC;
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
    }
    
    .filter-select {
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        color: #374151;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">All Contracts</h1>
                    <p class="text-gray-600 mt-2">Manage and monitor all contracts in the system</p>
                </div>
                
                <div class="mt-4 lg:mt-0 flex space-x-3">
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}" 
                       class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        New Contract
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
                    <a href="{{ route('contracts.reports') }}"
                       class="bg-white border border-[#1C75BC] text-[#1C75BC] hover:bg-[#1C75BC]/5 font-semibold py-2 px-4 rounded-lg flex items-center">
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
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
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
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
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
                        <select name="per_page"
                                onchange="this.form.submit()"
                                class="filter-select w-full">
                            <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ request('per_page', 10) == '10' ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Active Filters -->
            @if(request()->has('search') || request()->has('status') || request()->has('department'))
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                
                @if(request()->has('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    Search: {{ request('search') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('search'), ['page' => 1])) }}" 
                       class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                
                @if(request()->has('status'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    Status: {{ $statuses[request('status')] ?? request('status') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('status'), ['page' => 1])) }}" 
                       class="ml-2 text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                
                @if(request()->has('department') && auth()->user()->canViewAllContracts())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                    Department: {{ $departments[request('department')] ?? request('department') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('department'), ['page' => 1])) }}"
                       class="ml-2 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif

                <a href="{{ route('contracts.index') }}" class="text-sm text-[#1C75BC] hover:text-[#1664a0] font-medium">
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
                        <tr>
                            <th class="table-cell font-semibold text-gray-700">ID</th>
                            <th class="table-cell font-semibold text-gray-700">Contract Name</th>
                            <th class="table-cell font-semibold text-gray-700">Created By</th>
                            <th class="table-cell font-semibold text-gray-700">Start Date</th>
                            <th class="table-cell font-semibold text-gray-700">End Date</th>
                            <th class="table-cell font-semibold text-gray-700">PG Amount</th>
                            <th class="table-cell font-semibold text-gray-700">PG End Date</th>
                            <th class="table-cell font-semibold text-gray-700">Status</th>
                            <th class="table-cell font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr class="table-row">
                            <td class="table-cell text-gray-600">#{{ $contract->id }}</td>
                            <td class="table-cell">
                                <a href="{{ route('contracts.show', $contract) }}" 
                                   class="font-medium text-gray-800 hover:text-[#1C75BC]">
                                    {{ \Illuminate\Support\Str::limit($contract->name, 30) }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ \Illuminate\Support\Str::limit($contract->description, 40) }}
                                </p>
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
            
            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200">
                {{ $contracts->links() }}
            </div>
            
            @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-file-contract text-5xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No contracts found</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->has('search') || request()->has('status') || request()->has('department'))
                    Try adjusting your filters or search terms
                    @else
                    Get started by creating your first contract
                    @endif
                </p>
                @if(auth()->user()->canCreateContracts())
                <a href="{{ route('contracts.create') }}" 
                   class="inline-flex items-center bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Contract
                </a>
                @endif
            </div>
            @endif
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Contracts</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $contracts->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-file-contract text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $contracts->where('status', 'active')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Expiring Soon</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $contracts->where('status', 'expiring')->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-2 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Expired</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $contracts->where('status', 'expired')->count() }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-2 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection