<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIGAE') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-white focus:text-primary-600 focus:font-bold focus:rounded-br-lg focus:shadow-lg">
            Pular para o conteúdo principal
        </a>
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar Backdrop for mobile -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden"
                 x-transition.opacity
                 x-cloak></div>

            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-30 flex flex-col bg-primary-900 text-white transition-all duration-300 transform lg:relative lg:translate-x-0"
                   :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-0 -translate-x-full lg:w-0'">
                
                <!-- Logo area -->
                <div class="flex items-center justify-center h-16 border-b border-primary-700/50 bg-primary-900 overflow-hidden flex-shrink-0">
                    <span x-show="sidebarOpen" x-transition class="text-2xl font-bold tracking-wider text-white">SIGAE</span>
                </div>

                <!-- Menu -->
                <x-sidebar-menu />
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden">
                
                <!-- Topbar -->
                <x-topbar />

                <!-- Main Scrollable Area -->
                <main id="main-content" class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8" tabindex="-1">
                    <div class="max-w-7xl mx-auto">
                        @if(isset($breadcrumb))
                            {{ $breadcrumb }}
                        @endif

                        @isset($header)
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ $header }}
                                </h2>
                                @isset($actions)
                                    <div class="flex space-x-3">
                                        {{ $actions }}
                                    </div>
                                @endisset
                            </div>
                        @endisset

                        <!-- Flash Messages -->
                        <x-flash-alert />

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        <!-- Search Modal -->
        <x-search-modal />
    </body>
</html>
