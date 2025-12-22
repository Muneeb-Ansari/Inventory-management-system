
<!-- Mobile sidebar backdrop -->
@php
use App\Models\Inventory;
@endphp
<div x-show="sidebarOpen && !isDesktop" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40"
    x-cloak>
</div>

<!-- Sidebar Drawer -->
<aside x-show="sidebarOpen || isDesktop" :class="(sidebarOpen || isDesktop) ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out flex flex-col h-screen"
    @click.away="if (!isDesktop) sidebarOpen = false" x-cloak>

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 flex-shrink-0">
        <a href="{{ route('dashboard') }}" wire:navigate class="text-xl font-bold text-gray-800">
            Inventory System
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden min-h-0">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" wire:navigate
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ __('Dashboard') }}
        </a>

        <!-- Products -->
        @can('viewAny', Inventory::class)
            <a href="{{ route('products.index') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ __('Products') }}
            </a>
        @endcan

        <!-- Create Purchases -->
        @can('createPurchase', Inventory::class)
            <a href="{{ route('purchases.create') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('purchases.create') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Create Purchases') }}
            </a>
        @endcan

        <!-- Create Sales -->
        @can('createSale', Inventory::class)
            <a href="{{ route('sales.create') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('sales.create') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                {{ __('Create Sales') }}
            </a>
        @endcan
        {{-- @can('createLocation', App\Policies\InventoryPolicy::class)

            <a href="{{ route('locations.create') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
                {{ request()->routeIs('locations.create') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">

                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>

                {{ __('Create Locationdsfasdf') }}
            </a>
        @endcan --}}
        <!-- Generate Reports -->
        @can('viewReport', Inventory::class)
            <a href="{{ route('reports.inventory') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Generate Reports') }}
            </a>
        @endcan

        <!-- Create Locations -->
        @can('create', Inventory::class)
            <a href="{{ route('locations.create') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('locations.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ __('Create Locations') }}
            </a>
        @endcan

        <!-- Opening Stock -->
        @can('setOpeningStock', Inventory::class)
            <a href="{{ route('opening-stock') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('opening-stock') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ __('Opening Stock') }}
            </a>
        @endcan
    </nav>

    <!-- Sidebar Footer -->
    <div class="border-t border-gray-200 p-4 flex-shrink-0">
        <div class="flex items-center px-4 py-2 mb-3">
            <div class="flex-shrink-0">
                <div
                    class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="ms-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <div class="space-y-1">
            <a href="{{ route('profile') }}" wire:navigate
                class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ __('Profile') }}
            </a>

            <button wire:click="logout"
                class="w-full flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-150 text-start">
                <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ __('Log Out') }}
            </button>
        </div>
    </div>
</aside>
