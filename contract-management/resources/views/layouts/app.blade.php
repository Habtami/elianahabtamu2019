<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Contract Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('styles')
</head>
<body class="bg-gray-50">
    @if(auth()->check())
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-[#1C75BC] to-[#2B8CDB] text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo/Brand -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/epss_logo.png') }}" alt="EPSS Logo" class="h-12 w-auto">
                    <div>
                        <h1 class="text-xl font-bold">Contract Management System</h1>
                        <p class="text-sm text-white/80">{{ auth()->user()->getRoleDisplayName() }}</p>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('dashboard') }}" class="hover:text-white/90 {{ request()->routeIs('dashboard') ? 'font-semibold' : '' }}">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('contracts.index') }}" class="hover:text-white/90 {{ request()->routeIs('contracts.*') && !request()->routeIs('contracts.alerts') ? 'font-semibold' : '' }}">
                            <i class="fas fa-list mr-1"></i> All Contracts
                        </a>
                        @if(auth()->user()->canCreateContracts())
                        <a href="{{ route('contracts.create') }}" class="hover:text-white/90 {{ request()->routeIs('contracts.create') ? 'font-semibold' : '' }}">
                            <i class="fas fa-plus mr-1"></i> New Contract
                        </a>
                        @endif
                        @php
                            $alertCount = 0;
                            if(auth()->check()) {
                                $user = auth()->user();
                                $contracts = \App\Models\Contract::forUser($user)->get();
                                foreach ($contracts as $contract) {
                                    if ($contract->isExpiringSoon() || ($contract->performance_guarantee_end_date && $contract->isPerformanceGuaranteeExpiringSoon())) {
                                        $alertCount++;
                                    }
                                }
                            }
                        @endphp
                        <a href="{{ route('contracts.alerts') }}" class="hover:text-white/90 {{ request()->routeIs('contracts.alerts') ? 'font-semibold' : '' }} relative">
                            <i class="fas fa-bell mr-1"></i> Alerts
                            @if($alertCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $alertCount }}</span>
                            @endif
                        </a>
                        @if(auth()->user()->canViewAllContracts())
                        <a href="{{ route('contracts.reports') }}" class="hover:text-white/90 {{ request()->routeIs('contracts.reports') ? 'font-semibold' : '' }}">
                            <i class="fas fa-chart-bar mr-1"></i> Reports
                        </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="hover:text-white/90 {{ request()->routeIs('admin.*') ? 'font-semibold' : '' }}">
                            <i class="fas fa-users mr-1"></i> Admin
                        </a>
                        @endif
                    </div>
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-2 hover:text-white/90 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 hidden group-hover:block">
                            <div class="px-4 py-2 border-b">
                                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-[#1C75BC] font-medium">{{ auth()->user()->getRoleDisplayName() }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="md:hidden py-2 border-t border-white/20">
                <div class="flex justify-around">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-sm {{ request()->routeIs('dashboard') ? 'font-semibold' : '' }}">
                        <i class="fas fa-home mb-1"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('contracts.index') }}" class="flex flex-col items-center text-sm {{ request()->routeIs('contracts.*') && !request()->routeIs('contracts.alerts') ? 'font-semibold' : '' }}">
                        <i class="fas fa-list mb-1"></i>
                        <span>Contracts</span>
                    </a>
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}" class="flex flex-col items-center text-sm {{ request()->routeIs('contracts.create') ? 'font-semibold' : '' }}">
                        <i class="fas fa-plus mb-1"></i>
                        <span>New</span>
                    </a>
                    @endif
                    <a href="{{ route('contracts.alerts') }}" class="flex flex-col items-center text-sm {{ request()->routeIs('contracts.alerts') ? 'font-semibold' : '' }} relative">
                        <div class="relative">
                            <i class="fas fa-bell mb-1"></i>
                            @if($alertCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center text-[10px]">{{ $alertCount }}</span>
                            @endif
                        </div>
                        <span>Alerts</span>
                    </a>
                    @if(auth()->user()->canViewAllContracts())
                    <a href="{{ route('contracts.reports') }}" class="flex flex-col items-center text-sm {{ request()->routeIs('contracts.reports') ? 'font-semibold' : '' }}">
                        <i class="fas fa-chart-bar mb-1"></i>
                        <span>Reports</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    @endif
    
    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Footer -->
    @if(auth()->check())
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-shield-check text-[#1C75BC] mr-1"></i>
                        Contract Management System v1.0
                    </p>
                    <p class="text-xs text-gray-500 mt-1">© {{ date('Y') }} Ethiopian Pharmaceutical Supply Service</p>
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ auth()->user()->name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ auth()->user()->getRoleDisplayName() }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ now()->format('M d, Y h:i A') }}</span>
                </div>
            </div>
            <div class="mt-4 text-center text-xs text-gray-500">
                Contract Management System v1.0<br>
                © 2026 Ethiopian Pharmaceutical Supply Service ICT developers
            </div>
        </div>
            </div>
        </div>
    </footer>
    @endif
    
    @yield('scripts')
    
    <script>
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.style.transition = 'opacity 0.5s';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 500);
                }, 5000);
            });
            
            // Mobile menu toggle
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.group');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(event.target)) {
                        const menu = dropdown.querySelector('.hidden.group-hover\\:block');
                        if (menu) {
                            menu.classList.add('hidden');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>