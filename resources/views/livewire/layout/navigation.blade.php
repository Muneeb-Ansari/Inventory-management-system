<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ 
         sidebarOpen: window.innerWidth >= 1024,
         isDesktop: window.innerWidth >= 1024,
         init() {
             // Ensure state is correct on initialization
             this.sidebarOpen = window.innerWidth >= 1024;
             this.isDesktop = window.innerWidth >= 1024;
             
             // Update isDesktop on resize
             window.addEventListener('resize', () => {
                 this.isDesktop = window.innerWidth >= 1024;
                 if (this.isDesktop) {
                     this.sidebarOpen = true;
                 }
             });
         }
     }">
    
    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Top Bar -->
    <div class="lg:ms-64">
        <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 right-0 left-0 lg:left-64 lg:sticky lg:top-0 z-30">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors"
                        type="button">
                    <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="flex-1 lg:flex-none ms-4 lg:ms-0">
                    <h1 class="text-lg font-semibold text-gray-900">
                        @if(request()->routeIs('dashboard'))
                            {{ __('Dashboard') }}
                        @elseif(request()->routeIs('products.*'))
                            {{ __('Products') }}
                        @elseif(request()->routeIs('purchases.*'))
                            {{ __('Purchases') }}
                        @elseif(request()->routeIs('sales.*'))
                            {{ __('Sales') }}
                        @elseif(request()->routeIs('reports.*'))
                            {{ __('Reports') }}
                        @elseif(request()->routeIs('locations.*'))
                            {{ __('Locations') }}
                        @elseif(request()->routeIs('opening-stock'))
                            {{ __('Opening Stock') }}
                        @else
                            {{ __('Inventory System') }}
                        @endif
                    </h1>
                </div>

                <!-- Desktop user menu -->
                <div class="hidden lg:flex lg:items-center lg:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold me-2">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </header>
    </div>
</div>
