<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard - B.A.M.E CRM' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .nav-item-active {
            background: linear-gradient(90deg, rgba(107, 70, 193, 0.12) 0%, rgba(107, 70, 193, 0.05) 100%);
            border-left: 3px solid #6B46C1;
        }
        .nav-item-hover:hover {
            background: rgba(107, 70, 193, 0.06);
        }
        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(107, 70, 193, 0.2) 0%, transparent 100%);
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Light Theme -->
        <aside class="w-64 bg-white flex flex-col border-r border-gray-200">
            <!-- Logo -->
            <div class="h-16 flex items-center px-5 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-md" style="background: linear-gradient(135deg, #6B46C1 0%, #553C9A 100%);">
                        BAME
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">B.A.M.E CRM</div>
                        <div class="text-xs text-gray-500">Sponsorship Platform</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <!-- Main Menu Section -->
                <div>
                    <p class="section-label px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Main Menu</p>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('dashboard') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('dashboard') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </div>
                        Dashboard
                    </a>

                    <a href="{{ route('sponsorships.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('sponsorships.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('sponsorships.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('sponsorships.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        Sponsorships
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isConsultant())
                    <a href="{{ route('contacts.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('contacts.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('contacts.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('contacts.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        Contacts
                    </a>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->isExecutive())
                    <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('reports.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('reports.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        Reports
                    </a>
                    @endif
                </div>

                @if(auth()->user()->isAdmin())
                <!-- Administration Section -->
                <div class="pt-3 mt-2 border-t border-gray-100">
                    <p class="section-label px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Administration</p>
                    
                    <a href="{{ route('form-builder.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('form-builder.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('form-builder.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('form-builder.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Form Builder
                    </a>

                    <a href="{{ route('pipelines.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('pipelines.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('pipelines.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('pipelines.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        Pipelines
                    </a>

                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('users.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('users.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        Users
                    </a>

                    <a href="{{ route('workflows.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('workflows.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('workflows.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('workflows.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        Workflows
                    </a>

                    <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? 'nav-item-active text-purple-700' : 'text-gray-600 nav-item-hover hover:text-gray-900' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('settings.*') ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <svg class="w-4.5 h-4.5 {{ request()->routeIs('settings.*') ? 'text-purple-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    Settings
                </a>
                @endif
                </div>
            </nav>

            <!-- User Profile -->
            <div class="mt-auto p-3 border-t border-gray-100">
                <div class="flex items-center justify-between bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-semibold text-sm" style="background: linear-gradient(135deg, #6B46C1 0%, #553C9A 100%);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $header ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="search" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 focus:bg-white transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-purple-500 rounded-full"></span>
                    </button>

                    <!-- Divider -->
                    <div class="h-8 w-px bg-gray-200"></div>

                    <!-- User Avatar -->
                    <div class="flex items-center gap-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-semibold text-sm" style="background: linear-gradient(135deg, #6B46C1 0%, #553C9A 100%);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 px-6 pb-6 {{ request()->routeIs('dashboard') && auth()->user()->role === 'consultant' ? 'pt-0' : 'pt-6' }}">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
