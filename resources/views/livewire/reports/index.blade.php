<div>
<div class="bg-gray-50 min-h-screen -m-6 p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Reports & Analytics</h2>
                <p class="text-sm text-gray-600">
                    Showing data from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
                </p>
            </div>
            <!-- Export Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10 overflow-hidden">
                    <a href="{{ route('reports.export.csv', ['dateRange' => $dateRange, 'customStartDate' => $customStartDate, 'customEndDate' => $customEndDate, 'selectedUser' => $selectedUser, 'selectedTier' => $selectedTier, 'selectedStage' => $selectedStage]) }}" class="block px-4 py-3 hover:bg-gray-50 transition">
                        <div class="font-medium text-gray-900">All Deals (CSV)</div>
                        <div class="text-xs text-gray-500">Export all deal data</div>
                    </a>
                    <a href="{{ route('reports.export.pipeline', ['dateRange' => $dateRange, 'customStartDate' => $customStartDate, 'customEndDate' => $customEndDate]) }}" class="block px-4 py-3 hover:bg-gray-50 border-t border-gray-100 transition">
                        <div class="font-medium text-gray-900">Pipeline Report</div>
                        <div class="text-xs text-gray-500">Stage breakdown</div>
                    </a>
                    <a href="{{ route('reports.export.performance', ['dateRange' => $dateRange, 'customStartDate' => $customStartDate, 'customEndDate' => $customEndDate]) }}" class="block px-4 py-3 hover:bg-gray-50 border-t border-gray-100 transition">
                        <div class="font-medium text-gray-900">Performance Report</div>
                        <div class="text-xs text-gray-500">Team performance</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Date Range -->
            <select wire:model.live="dateRange" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="this_quarter">This Quarter</option>
                <option value="this_year">This Year</option>
                <option value="last_month">Last Month</option>
                <option value="last_quarter">Last Quarter</option>
                <option value="last_year">Last Year</option>
                <option value="custom">Custom Range</option>
            </select>
            
            @if($dateRange === 'custom')
            <input type="date" wire:model.live="customStartDate" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            <input type="date" wire:model.live="customEndDate" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            @endif
            
            <!-- Team Member -->
            <select wire:model.live="selectedUser" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">All Members</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            
            <!-- Tier -->
            <select wire:model.live="selectedTier" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">All Tiers</option>
                @foreach($tiers as $tier)
                    <option value="{{ $tier }}">{{ $tier }}</option>
                @endforeach
            </select>
            
            <!-- Stage -->
            <select wire:model.live="selectedStage" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">All Stages</option>
                @foreach($stages as $stage)
                    <option value="{{ $stage }}">{{ $stage }}</option>
                @endforeach
            </select>
        </div>

        <!-- Stats Summary -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Total Deals in Period:</span>
                <span class="font-semibold text-gray-900">{{ $dealsInPeriod }}</span>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid - Modern Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Deals in Period -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">{{ $dealsInPeriod }}</div>
            <p class="text-xs text-gray-500 font-medium">Deals in Period</p>
        </div>

        <!-- Value in Period -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">£{{ number_format($valueInPeriod / 1000, 0) }}K</div>
            <p class="text-xs text-gray-500 font-medium">Value in Period</p>
        </div>

        <!-- Pipeline Value -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600 mb-1">£{{ number_format($totalPipelineValue / 1000, 0) }}K</div>
            <p class="text-xs text-gray-500 font-medium">Pipeline Value</p>
        </div>

        <!-- Weighted Pipeline -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-pink-600 mb-1">£{{ number_format($weightedPipelineValue / 1000, 0) }}K</div>
            <p class="text-xs text-gray-500 font-medium">Weighted Pipeline</p>
        </div>

        <!-- Win Rate -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-teal-600 mb-1">
                {{ ($wonDeals + $lostDeals) > 0 ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1) : 0 }}%
            </div>
            <p class="text-xs text-gray-500 font-medium">Win Rate</p>
        </div>

        <!-- Stagnant Deals -->
        <div class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg {{ $stagnantDeals > 0 ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $stagnantDeals > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $stagnantDeals > 0 ? 'text-amber-600' : 'text-gray-900' }} mb-1">{{ $stagnantDeals }}</div>
            <p class="text-xs text-gray-500 font-medium">Stagnant Deals</p>
        </div>
    </div>

    <!-- Secondary Metrics - Modern Gradient Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Won Deals -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ $wonDeals }}</div>
                <p class="text-sm text-emerald-100">Won Deals</p>
                <p class="text-xs text-emerald-200 mt-1">Active Partnerships</p>
            </div>
        </div>

        <!-- Lost Deals -->
        <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ $lostDeals }}</div>
                <p class="text-sm text-red-100">Lost Deals</p>
                <p class="text-xs text-red-200 mt-1">Closed Lost</p>
            </div>
        </div>

        <!-- Avg Deal Size -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">£{{ number_format($avgDealSize, 0) }}</div>
                <p class="text-sm text-purple-100">Avg Deal Size</p>
                <p class="text-xs text-purple-200 mt-1">Per Deal</p>
            </div>
        </div>

        <!-- Total Deals -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold mb-1">{{ $allDeals->count() }}</div>
                <p class="text-sm text-blue-100">Total Deals</p>
                <p class="text-xs text-blue-200 mt-1">In selected period</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Pipeline by Stage -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pipeline by Stage</h3>
                    <p class="text-xs text-gray-500 mt-1">Deal distribution across stages</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($pipelineByStage as $stage)
                    <div class="group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">{{ $stage->stage }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-500">{{ $stage->count }} deals</span>
                                <span class="text-xs font-semibold text-purple-600">£{{ number_format($stage->total_value, 0) }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 transition-all duration-500 group-hover:from-purple-600 group-hover:to-pink-600" style="width: {{ $pipelineByStage->sum('total_value') > 0 ? ($stage->total_value / $pipelineByStage->sum('total_value')) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Deals by Source -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Deals by Source</h3>
                    <p class="text-xs text-gray-500 mt-1">Lead generation channels</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($dealsBySource as $source)
                    <div wire:click="openSourceDetails('{{ $source->source }}')" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-purple-50 hover:border-purple-200 border border-transparent transition-all group cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-purple-500 mr-3 group-hover:scale-125 transition-transform"></div>
                            <span class="text-sm font-medium text-gray-900 group-hover:text-purple-700">{{ $source->source }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900 bg-white px-2.5 py-1 rounded-md shadow-sm">{{ $source->count }}</span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Deals by Tier & Top Performers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Deals by Tier -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Deals by Tier</h3>
                    <p class="text-xs text-gray-500 mt-1">Sponsorship tier breakdown</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($dealsByTier as $tier)
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-white rounded-lg hover:from-amber-50 hover:to-orange-50 border border-transparent hover:border-amber-200 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $tier->tier === 'Platinum' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $tier->tier === 'Gold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $tier->tier === 'Silver' ? 'bg-gray-200 text-gray-700' : '' }}
                                {{ $tier->tier === 'Bronze' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $tier->tier === 'In-Kind' ? 'bg-blue-100 text-blue-700' : '' }}">
                                {{ $tier->tier }}
                            </span>
                            <span class="text-xs font-medium text-gray-500">{{ $tier->count }} deals</span>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-900 bg-white px-2.5 py-1 rounded-md shadow-sm">£{{ number_format($tier->total_value, 0) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Top Performers</h3>
                    <p class="text-xs text-gray-500 mt-1">Highest revenue generators</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($topPerformers as $index => $performer)
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-white rounded-lg hover:from-indigo-50 hover:to-purple-50 border border-transparent hover:border-indigo-200 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-sm shadow-md group-hover:scale-110 transition-transform">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $performer->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $performer->deal_count }} deals closed</div>
                            </div>
                        </div>
                        <div class="font-bold text-gray-900 bg-white px-2.5 py-1 rounded-md shadow-sm">£{{ number_format($performer->total_value, 0) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">6-Month Trend</h3>
                <p class="text-xs text-gray-500 mt-1">Deal performance over time</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Deals</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthlyTrend as $trend)
                        <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all group">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($trend->month . '-01')->format('F Y') }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 group-hover:bg-blue-200">
                                    {{ $trend->count }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">£{{ number_format($trend->total_value, 0) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Deals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Deals in Period</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $allDeals->count() }} total deals found</p>
            </div>
            <a href="{{ route('reports.export.csv', ['dateRange' => $dateRange, 'customStartDate' => $customStartDate, 'customEndDate' => $customEndDate, 'selectedUser' => $selectedUser, 'selectedTier' => $selectedTier, 'selectedStage' => $selectedStage]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tier</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Stage</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Value</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Owner</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allDeals->take(20) as $deal)
                        <tr class="hover:bg-purple-50 transition-colors group">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 group-hover:text-purple-700">{{ $deal->company_name }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <div class="font-medium">{{ $deal->decision_maker_name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $deal->decision_maker_email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $deal->tier === 'Silver' ? 'bg-gray-200 text-gray-700' : '' }}
                                    {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $deal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ $deal->tier }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $deal->stage }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-right font-bold text-gray-900">£{{ number_format($deal->value, 0) }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $deal->user->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $deal->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">No deals found in this period</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($allDeals->count() > 20)
            <div class="p-4 text-center text-sm text-gray-600 border-t border-gray-200 bg-gray-50">
                Showing 20 of {{ $allDeals->count() }} deals. 
                <a href="{{ route('reports.export.csv', ['dateRange' => $dateRange, 'customStartDate' => $customStartDate, 'customEndDate' => $customEndDate, 'selectedUser' => $selectedUser, 'selectedTier' => $selectedTier, 'selectedStage' => $selectedStage]) }}" class="text-purple-600 hover:text-purple-700 font-semibold hover:underline">Export all to CSV →</a>
            </div>
        @endif
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                <p class="text-xs text-gray-500 mt-1">Latest deal updates and actions</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-transparent hover:border-green-200 hover:from-green-50 hover:to-emerald-50 transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900">{{ $activity->activity_type }}</div>
                        <div class="text-xs text-gray-600 mt-1">
                            <span class="font-medium">{{ $activity->sponsorship->company_name ?? 'Unknown' }}</span> • {{ $activity->user->name ?? 'System' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No recent activities</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Source Detail Modal -->
@if($showSourceModal)
    @include('livewire.reports.source-detail-modal')
@endif
</div>
