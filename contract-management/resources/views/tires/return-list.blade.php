@extends('layouts.app')

@section('title', 'Return Tire')

@section('page-title', 'Return Tire')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.5rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #EDF2F7;
        margin-bottom: 1.5rem;
    }
    .header-title h1 { font-size: 1.5rem; }
    .header-user .user-name { color: #1C75BC; font-weight: 600; }
    .header-user .user-role {
        color: #64748B; font-size: 0.85rem;
        padding: 0.25rem 0.75rem; background: #F1F5F9;
        border-radius: 9999px;
    }
    a { text-decoration: none !important; }
    .search-input {
        border: 2px solid #E2E8F0; border-radius: 0.75rem;
        padding: 0.6rem 1rem; width: 100%;
        background: white; font-size: 0.875rem; transition: all 0.3s;
    }
    .search-input:focus {
        outline: none; border-color: var(--epss-blue);
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
    }
    .tire-card {
        background: #fff; border-radius: 12px;
        border: 1px solid #E5E7EB; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
        padding: 1rem 1.25rem; margin-bottom: 0.75rem;
        transition: all 0.2s; display: flex;
        align-items: center; justify-content: space-between;
    }
    .tire-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px -2px rgba(0,0,0,0.08); }
    .tire-info .serial { font-size: 1rem; font-weight: 600; color: #1C75BC; }
    .tire-info .type { font-size: 0.85rem; color: #64748B; margin-top: 0.25rem; }
    .tire-info .driver { font-size: 0.8rem; color: #94A3B8; }
    .return-btn {
        background: linear-gradient(135deg, #10B981, #059669);
        color: #fff; padding: 0.6rem 1.25rem;
        border-radius: 0.5rem; font-weight: 600;
        border: none; cursor: pointer; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 0.4rem;
    }
    .return-btn:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
    }
    .empty-state {
        text-align: center; padding: 3rem 2rem;
        background: #F8FAFC; border-radius: 12px;
    }
    .empty-icon {
        font-size: 3rem; color: #CBD5E1; margin-bottom: 1rem;
    }
    @media (max-width: 768px) { .header-user { flex-direction: column; align-items: flex-start; } }
</style>
@endsection

@section('content')
<div class="page-container py-8" style="background:#F8FAFC;min-height:100vh;">
    <div class="container mx-auto px-4">

        {{-- ========== HEADER BAR ========== --}}
        <div class="header-bar">
            <div class="header-title">
                <h1 class="text-2xl font-bold text-gray-800">Return Tire</h1>
            </div>
            <div class="header-user">
                <span class="user-name">Welcome back, {{ auth()->user()->name }}</span>
                <span class="user-role">{{ auth()->user()->role }}</span>
            </div>
        </div>

        {{-- ========== SEARCH ========== --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('tires.return.list') }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by serial number or driver name..."
                           class="search-input w-full max-w-md pr-10">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            @if(request('search') && $gives->isEmpty())
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                    <div>
                        <p class="font-semibold text-red-800">Tire Not Found</p>
                        <p class="text-sm text-red-600">No tire found with serial number containing "{{ request('search') }}". 
                            Please check the serial number and try again. The tire must already be registered and currently given out.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ========== TIRE LIST ========== --}}
        <div class="space-y-3">
            @forelse($gives as $give)
            <div class="tire-card">
                <div class="tire-info">
                    <div class="serial">{{ $give->tire->serial_number }}</div>
                    <div class="type">{{ $give->tire->tire_type }}</div>
                    <div class="driver">
                        Given to: {{ $give->driver->name ?? 'Unknown' }}
                        on {{ $give->give_date->format('M d, Y') }}
                    </div>
                </div>
                <a href="{{ route('tires.return', $give) }}" class="return-btn">
                    <i class="fas fa-undo"></i> Return Tire
                </a>
            </div>
            @empty
                @if(!request('search'))
                <div class="empty-state">
                    <i class="fas fa-tire"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Tires Out for Return</h3>
                    <p class="text-gray-500">All tires have been returned. Use the search above to find a specific tire.</p>
                </div>
                @endif
            @endforelse
        </div>

    </div>
</div>
@endsection