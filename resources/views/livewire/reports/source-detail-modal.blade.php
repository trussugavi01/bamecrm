<div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity" wire:click="closeSourceModal"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $selectedSource }}</h2>
                            <p class="text-purple-100 text-sm mt-1">Detailed Source Analytics</p>
                        </div>
                    </div>
                    <button wire:click="closeSourceModal" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="p-8 space-y-6">
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Total Deals -->
                        <div wire:click="showMetricDetails('total_deals')" class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 cursor-pointer hover:shadow-lg hover:scale-105 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-purple-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div class="text-3xl font-bold text-purple-900">{{ $sourceDetails['totalDeals'] ?? 0 }}</div>
                            <div class="text-sm text-purple-700 font-medium mt-1">Total Deals</div>
                            <div class="text-xs text-purple-600 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Click for details →</div>
                        </div>

                        <!-- Total Value -->
                        <div wire:click="showMetricDetails('total_value')" class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200 cursor-pointer hover:shadow-lg hover:scale-105 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-green-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div class="text-3xl font-bold text-green-900">£{{ number_format(($sourceDetails['totalValue'] ?? 0) / 1000, 0) }}K</div>
                            <div class="text-sm text-green-700 font-medium mt-1">Total Value</div>
                            <div class="text-xs text-green-600 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Click for details →</div>
                        </div>

                        <!-- Conversion Rate -->
                        <div wire:click="showMetricDetails('conversion_rate')" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 cursor-pointer hover:shadow-lg hover:scale-105 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div class="text-3xl font-bold text-blue-900">{{ $sourceDetails['conversionRate'] ?? 0 }}%</div>
                            <div class="text-sm text-blue-700 font-medium mt-1">Conversion Rate</div>
                            <div class="text-xs text-blue-600 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Click for details →</div>
                        </div>

                        <!-- Avg Deal Value -->
                        <div wire:click="showMetricDetails('avg_deal_value')" class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border border-amber-200 cursor-pointer hover:shadow-lg hover:scale-105 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center group-hover:bg-amber-600 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-amber-400 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div class="text-3xl font-bold text-amber-900">£{{ number_format(($sourceDetails['avgDealValue'] ?? 0) / 1000, 0) }}K</div>
                            <div class="text-sm text-amber-700 font-medium mt-1">Avg Deal Size</div>
                            <div class="text-xs text-amber-600 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Click for details →</div>
                        </div>
                    </div>

                    <!-- Drill-Down Section -->
                    @if($drillDownMetric)
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            @if($drillDownMetric === 'total_deals') All Deals
                                            @elseif($drillDownMetric === 'total_value') Deals by Value
                                            @elseif($drillDownMetric === 'conversion_rate') Won Deals
                                            @elseif($drillDownMetric === 'avg_deal_value') Deal Value Distribution
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ count($drillDownDeals) }} deals found</p>
                                    </div>
                                </div>
                                <button wire:click="closeDrillDown" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="max-h-96 overflow-y-auto space-y-2">
                                @forelse($drillDownDeals as $deal)
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $deal->company_name }}</h4>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                                        {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $deal->tier === 'Silver' ? 'bg-gray-200 text-gray-800' : '' }}
                                                        {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                                        {{ $deal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                        {{ $deal->tier }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        {{ $deal->user->name ?? 'Unassigned' }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $deal->created_at->format('M d, Y') }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        {{ $deal->priority === 'Hot' ? 'bg-red-100 text-red-700' : '' }}
                                                        {{ $deal->priority === 'Warm' ? 'bg-amber-100 text-amber-700' : '' }}
                                                        {{ $deal->priority === 'Cold' ? 'bg-blue-100 text-blue-700' : '' }}">
                                                        {{ $deal->priority }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-lg font-bold text-indigo-600">£{{ number_format($deal->value / 1000, 0) }}K</div>
                                                <div class="text-xs text-gray-500">{{ $deal->stage }}</div>
                                                @if($deal->stage === 'Active Partnership')
                                                    <div class="text-xs text-green-600 font-medium mt-1">✓ Won</div>
                                                @elseif($deal->stage === 'Closed Lost')
                                                    <div class="text-xs text-red-600 font-medium mt-1">✗ Lost</div>
                                                @else
                                                    <div class="text-xs text-blue-600 font-medium mt-1">{{ $deal->probability }}% prob</div>
                                                @endif
                                            </div>
                                        </div>
                                        @if($deal->decision_maker_name)
                                            <div class="mt-2 pt-2 border-t border-gray-100 text-xs text-gray-600">
                                                <span class="font-medium">Contact:</span> {{ $deal->decision_maker_name }}
                                                @if($deal->decision_maker_email)
                                                    <span class="text-gray-400">•</span> {{ $deal->decision_maker_email }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="font-medium">No deals found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Status Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-green-900">{{ $sourceDetails['wonDeals'] ?? 0 }}</div>
                                    <div class="text-sm text-green-700 font-medium">Won Deals</div>
                                </div>
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-blue-900">{{ $sourceDetails['activeDeals'] ?? 0 }}</div>
                                    <div class="text-sm text-blue-700 font-medium">Active Deals</div>
                                </div>
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-red-900">{{ $sourceDetails['lostDeals'] ?? 0 }}</div>
                                    <div class="text-sm text-red-700 font-medium">Lost Deals</div>
                                </div>
                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Deals by Stage -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Deals by Stage
                            </h3>
                            <div class="space-y-3">
                                @foreach(($sourceDetails['dealsByStage'] ?? []) as $stage)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $stage->stage }}</div>
                                            <div class="text-xs text-gray-500 mt-1">£{{ number_format($stage->total_value ?? 0, 0) }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-purple-600">{{ $stage->count }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Deals by Tier -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Deals by Tier
                            </h3>
                            <div class="space-y-3">
                                @foreach(($sourceDetails['dealsByTier'] ?? []) as $tier)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                                {{ $tier->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $tier->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $tier->tier === 'Silver' ? 'bg-gray-200 text-gray-800' : '' }}
                                                {{ $tier->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $tier->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ $tier->tier }}
                                            </span>
                                            <div class="text-xs text-gray-500">£{{ number_format($tier->total_value ?? 0, 0) }}</div>
                                        </div>
                                        <div class="text-lg font-bold text-blue-600">{{ $tier->count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Priority Distribution -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Priority Distribution
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                <div class="text-3xl font-bold text-red-900">{{ $sourceDetails['dealsByPriority']['Hot'] ?? 0 }}</div>
                                <div class="text-sm text-red-700 font-medium mt-1">Hot</div>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                                <div class="text-3xl font-bold text-amber-900">{{ $sourceDetails['dealsByPriority']['Warm'] ?? 0 }}</div>
                                <div class="text-sm text-amber-700 font-medium mt-1">Warm</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <div class="text-3xl font-bold text-blue-900">{{ $sourceDetails['dealsByPriority']['Cold'] ?? 0 }}</div>
                                <div class="text-sm text-blue-700 font-medium mt-1">Cold</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Deals & Recent Deals -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Deals -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Top 5 Deals by Value
                            </h3>
                            <div class="space-y-2">
                                @forelse(($sourceDetails['topDeals'] ?? []) as $deal)
                                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-transparent rounded-lg border border-green-100">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $deal->company_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $deal->user->name ?? 'Unassigned' }}</div>
                                        </div>
                                        <div class="text-right ml-3">
                                            <div class="text-sm font-bold text-green-600">£{{ number_format($deal->value / 1000, 0) }}K</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-700' : '' }}
                                                {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $deal->tier === 'Silver' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-700' : '' }}">
                                                {{ $deal->tier }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-500 text-sm">No deals found</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Recent Deals -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Recent Deals
                            </h3>
                            <div class="space-y-2">
                                @forelse(($sourceDetails['recentDeals'] ?? []) as $deal)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $deal->company_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $deal->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="text-right ml-3">
                                            <div class="text-sm font-bold text-gray-900">£{{ number_format($deal->value / 1000, 0) }}K</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $deal->priority === 'Hot' ? 'bg-red-100 text-red-700' : '' }}
                                                {{ $deal->priority === 'Warm' ? 'bg-amber-100 text-amber-700' : '' }}
                                                {{ $deal->priority === 'Cold' ? 'bg-blue-100 text-blue-700' : '' }}">
                                                {{ $deal->priority }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-500 text-sm">No recent deals</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trend Chart -->
                    @if(count($sourceDetails['monthlyTrend'] ?? []) > 0)
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            6-Month Trend
                        </h3>
                        <div class="flex items-end justify-between gap-2 h-32">
                            @foreach($sourceDetails['monthlyTrend'] as $month)
                                @php
                                    $maxValue = collect($sourceDetails['monthlyTrend'])->max('count');
                                    $height = $maxValue > 0 ? ($month->count / $maxValue) * 100 : 0;
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-gradient-to-t from-purple-500 to-purple-400 rounded-t-lg hover:from-purple-600 hover:to-purple-500 transition-all cursor-pointer group relative" style="height: {{ max(8, $height) }}%;">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            {{ $month->count }} deals<br>£{{ number_format($month->total_value ?? 0, 0) }}
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">{{ \Carbon\Carbon::parse($month->month . '-01')->format('M') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end">
                    <button wire:click="closeSourceModal" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
