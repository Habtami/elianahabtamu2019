@extends('layouts.app')

@section('title', 'Driver Management')

@section('page-title', 'Driver Management')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }
    .stats-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #E2E8F0;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--epss-blue) 0%, var(--epss-blue-light) 100%);
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .stats-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .table-cell { padding: 1rem; text-align: left; vertical-align: middle; }
    .table-row { border-bottom: 1px solid #F1F5F9; transition: all 0.2s ease; }
    .table-row:hover { background: #FAFBFC; }
    .table-header { background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border-bottom: 2px solid #E5E7EB; }
    .search-input { border: 2px solid #E2E8F0; border-radius: 0.75rem; padding: 0.75rem 1rem; width: 100%; background: white; font-size: 0.875rem; }
    .search-input:focus { outline: none; border-color: var(--epss-blue); box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1); }
    .status-badge { font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Driver Management</h1>
                    <p class="text-gray-600 mt-2">Manage all tire drivers and view their statistics</p>
                </div>
                @if(auth()->user()->canEditDrivers())
                <a href="{{ route('drivers.create') }}" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add New Driver
                </a>
                @endif
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Drivers</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalDrivers }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-blue-400 to-blue-600">
                        <i class="fas fa-user-friends text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeDrivers }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-green-400 to-green-600">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Inactive</p>
                        <p class="text-3xl font-bold text-gray-500 mt-2">{{ $inactiveDrivers }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-gray-400 to-gray-500">
                        <i class="fas fa-pause-circle text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">License Expiring</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $licenseExpiring }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-yellow-400 to-orange-500">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">License Expired</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $licenseExpired }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-red-400 to-red-600">
                        <i class="fas fa-times-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <form method="GET" action="{{ route('drivers.index') }}">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, license, phone..." class="search-input pr-10">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div>
                    <form method="GET" action="{{ route('drivers.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="status" onchange="this.form.submit()" class="search-input">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="license_expiring" {{ request('status') == 'license_expiring' ? 'selected' : '' }}>License Expiring</option>
                            <option value="license_expired" {{ request('status') == 'license_expired' ? 'selected' : '' }}>License Expired</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Drivers Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($drivers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-cell font-semibold text-gray-700">ID</th>
                            <th class="table-cell font-semibold text-gray-700">Name</th>
                            <th class="table-cell font-semibold text-gray-700">License</th>
                            <th class="table-cell font-semibold text-gray-700">Phone</th>
                            <th class="table-cell font-semibold text-gray-700">License Expiry</th>
                            <th class="table-cell font-semibold text-gray-700">Status</th>
                            <th class="table-cell font-semibold text-gray-700">Tires</th>
                            <th class="table-cell font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        <tr class="table-row">
                            <td class="table-cell text-gray-600">#{{ $driver->id }}</td>
                            <td class="table-cell font-medium text-gray-800">{{ $driver->name }}</td>
                            <td class="table-cell text-gray-600">{{ $driver->license_number ?? '-' }}</td>
                            <td class="table-cell text-gray-600">{{ $driver->phone ?? '-' }}</td>
                            <td class="table-cell text-gray-600">
                                @if($driver->license_expiry)
                                    {{ $driver->license_expiry->format('Y-m-d') }}
                                    @if($driver->isLicenseExpired())
                                        <span class="status-badge bg-red-100 text-red-800 ml-2">Expired</span>
                                    @elseif($driver->isLicenseExpiringSoon())
                                        <span class="status-badge bg-yellow-100 text-yellow-800 ml-2">Expiring</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <form method="POST" action="{{ route('drivers.toggleStatus', $driver) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $driver->status ? 'status-badge bg-green-100 text-green-800' : 'status-badge bg-gray-100 text-gray-800' }}">
                                        {{ $driver->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="table-cell text-gray-600">{{ $driver->active_tires_count }} active</td>
                            <td class="table-cell">
                                <div class="flex space-x-2">
                                    <a href="{{ route('drivers.show', $driver) }}" class="text-[#1C75BC] hover:text-[#1664a0]" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('drivers.edit', $driver) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(auth()->user()->canManageTires())
                                    <form method="POST" action="{{ route('drivers.destroy', $driver) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this driver?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
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

            <div class="px-6 pt-4 pb-2 border-t border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <p class="text-sm text-gray-600">
                    Showing
                    <span class="font-semibold text-gray-800">{{ $drivers->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-semibold text-gray-800">{{ $drivers->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-gray-800">{{ $drivers->total() }}</span>
                    drivers
                </p>
                <p class="text-sm text-gray-500">Page {{ $drivers->currentPage() }} of {{ $drivers->lastPage() }}</p>
            </div>

            <div class="px-6 pb-6 flex justify-center">
                {{ $drivers->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-user-times text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No drivers found</h3>
                <p class="text-gray-500 mb-4">Get started by adding your first driver</p>
                <a href="{{ route('drivers.create') }}" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Driver
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection