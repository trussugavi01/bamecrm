<div class="space-y-4">
@if(auth()->user()->role !== 'consultant')
    <!-- Welcome Header - Clean Light Design -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                @endphp
                {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-gray-500 mt-1">Here's your sponsorship pipeline overview for today.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Period Selector -->
            <div class="inline-flex rounded-lg bg-white border border-gray-200 p-1 shadow-sm">
                <button wire:click="$set('dateRange', 'month')" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all duration-200 {{ $dateRange === 'month' ? 'bg-purple-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Month
                </button>
                <button wire:click="$set('dateRange', 'quarter')" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all duration-200 {{ $dateRange === 'quarter' ? 'bg-purple-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Quarter
                </button>
                <button wire:click="$set('dateRange', 'year')" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all duration-200 {{ $dateRange === 'year' ? 'bg-purple-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Year
                </button>
            </div>
            <a href="{{ route('sponsorships.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Deal
            </a>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-medium text-gray-700">{{ $activeDeals }} Active Deals</span>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm">
            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">{{ count($upcomingFollowUps) }} Follow-ups</span>
        </div>
        @if(count($stagnantDeals) > 0)
        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-medium text-amber-700">{{ count($stagnantDeals) }} Need Attention</span>
        </div>
        @endif
    </div>
@endif

<!-- KPI Cards Row - Colored Cards for Better Clarity -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Pipeline Value Card - Purple -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-white/80 bg-white/20 px-2.5 py-1 rounded-full">Pipeline</span>
            </div>
            <p class="text-3xl font-bold text-white">£{{ number_format($pipelineHealth / 1000, 0) }}K</p>
            <p class="text-sm text-white/70 mt-1">Weighted value</p>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-white/70">Goal: £{{ number_format($quarterlyGoal / 1000000, 1) }}M</span>
                    <span class="font-semibold text-white">{{ $pipelineProgress }}%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2">
                    <div class="h-2 rounded-full bg-white transition-all duration-500" style="width: {{ max(2, $pipelineProgress) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Active Deals Card - Blue -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-white/80 bg-white/20 px-2.5 py-1 rounded-full">Active</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $activeDeals }}</p>
            <p class="text-sm text-white/70 mt-1">Deals in pipeline</p>
            <div class="mt-4 flex items-center gap-3">
                <div class="flex items-center gap-1.5 bg-white/20 px-2 py-1 rounded">
                    <div class="w-2 h-2 bg-green-300 rounded-full"></div>
                    <span class="text-xs font-medium text-white">{{ $closedWonDeals }} won</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white/20 px-2 py-1 rounded">
                    <div class="w-2 h-2 bg-red-300 rounded-full"></div>
                    <span class="text-xs font-medium text-white">{{ $closedLostDeals }} lost</span>
                </div>
            </div>
        </div>

        <!-- Win Rate Card - Teal/Green -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-white/80 bg-white/20 px-2.5 py-1 rounded-full">Win Rate</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $winLossRate }}%</p>
            <p class="text-sm text-white/70 mt-1">Conversion rate</p>
            <div class="mt-4 space-y-2">
                <div class="w-full bg-white/20 rounded-full h-2">
                    <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ max(2, $winLossRate) }}%"></div>
                </div>
                <p class="text-xs text-right text-white/80 font-medium">
                    {{ $winLossRate >= 50 ? 'Above target' : 'Room to improve' }}
                </p>
            </div>
        </div>

        <!-- New Deals Card - Orange -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-white/80 bg-white/20 px-2.5 py-1 rounded-full">New</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $newDealsCount }}</p>
            <p class="text-sm text-white/70 mt-1">This {{ $dateRange === 'month' ? 'month' : ($dateRange === 'year' ? 'year' : 'quarter') }}</p>
            <div class="mt-4 flex items-center justify-between bg-white/20 rounded-lg px-3 py-2">
                <span class="text-xs text-white/80">Avg. value</span>
                <span class="text-sm font-bold text-white">£{{ number_format($avgDealValue / 1000, 0) }}K</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sales Funnel - Takes 2 columns -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sales Pipeline</h3>
                    <p class="text-sm text-gray-500">Deal progression through stages</p>
                </div>
                <a href="{{ route('sponsorships.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 hover:text-purple-700 transition">
                    View all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <!-- Visual Funnel -->
            <div class="space-y-3">
                @php
                    $stages = [
                        'Prospect Identification' => ['label' => 'Prospect', 'color' => '#6b7280'],
                        'Initial Outreach' => ['label' => 'Outreach', 'color' => '#3b82f6'],
                        'Qualification & Discovery' => ['label' => 'Qualification', 'color' => '#06b6d4'],
                        'Proposal Development' => ['label' => 'Proposal', 'color' => '#8b5cf6'],
                        'Negotiation' => ['label' => 'Negotiation', 'color' => '#f59e0b'],
                        'Contract & Commitment' => ['label' => 'Contract', 'color' => '#10b981'],
                    ];
                    $counts = array_map(fn($item) => is_array($item) ? ($item['count'] ?? 0) : 0, $funnelData);
                    $maxCount = !empty($counts) ? max($counts) : 1;
                    if ($maxCount == 0) $maxCount = 1;
                @endphp
                
                @foreach($stages as $stage => $config)
                    @php
                        $count = $funnelData[$stage]['count'] ?? 0;
                        $value = $funnelData[$stage]['value'] ?? 0;
                        $barWidth = $maxCount > 0 ? max(15, ($count / $maxCount) * 100) : 15;
                    @endphp
                    <div class="group">
                        <div class="flex items-center gap-3">
                            <div class="w-24 text-right shrink-0">
                                <span class="text-xs font-medium text-gray-600">{{ $config['label'] }}</span>
                            </div>
                            <div class="flex-1 relative">
                                <div class="h-9 bg-gray-100 rounded-lg overflow-hidden">
                                    <div class="h-full rounded-lg flex items-center justify-between px-3 transition-all duration-300 group-hover:opacity-90" style="width: {{ $barWidth }}%; min-width: 70px; background: {{ $config['color'] }};">
                                        <span class="text-white text-xs font-semibold">{{ $count }}</span>
                                        @if($value > 0)
                                        <span class="text-white/80 text-xs">£{{ number_format($value / 1000, 0) }}K</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pipeline Summary -->
            <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold text-gray-900">{{ $activeDeals }}</p>
                    <p class="text-xs text-gray-500">Total Deals</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold text-purple-600">£{{ number_format($totalPipelineValue / 1000, 0) }}K</p>
                    <p class="text-xs text-gray-500">Total Value</p>
                </div>
            </div>
        </div>

        <!-- Priority & Tier Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Deal Breakdown</h3>
            <p class="text-sm text-gray-500 mb-5">By priority and tier</p>
            
            <!-- Priority Section -->
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Priority Level</h4>
                <div class="space-y-3">
                    @php
                        $priorities = [
                            'Hot' => ['color' => '#ef4444', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
                            'Warm' => ['color' => '#f59e0b', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                            'Cold' => ['color' => '#3b82f6', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                        ];
                        $totalPriority = array_sum($dealsByPriority) ?: 1;
                    @endphp
                    
                    @foreach($priorities as $priority => $config)
                        @php
                            $count = $dealsByPriority[$priority] ?? 0;
                            $percentage = round(($count / $totalPriority) * 100);
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="w-14 shrink-0">
                                <span class="inline-flex items-center justify-center w-full px-2 py-1 rounded text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                    {{ $priority }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ max(5, $percentage) }}%; background: {{ $config['color'] }};"></div>
                                </div>
                            </div>
                            <div class="w-6 text-right">
                                <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tier Breakdown -->
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Sponsorship Tier</h4>
                <div class="space-y-1.5">
                    @php
                        $tierColors = [
                            'Platinum' => ['color' => '#475569', 'bg' => 'bg-slate-50'],
                            'Gold' => ['color' => '#d97706', 'bg' => 'bg-amber-50'],
                            'Silver' => ['color' => '#6b7280', 'bg' => 'bg-gray-50'],
                            'Bronze' => ['color' => '#ea580c', 'bg' => 'bg-orange-50'],
                            'In-Kind' => ['color' => '#0d9488', 'bg' => 'bg-teal-50'],
                        ];
                    @endphp
                    @foreach(['Platinum', 'Gold', 'Silver', 'Bronze', 'In-Kind'] as $tier)
                        @php
                            $tierData = $dealsByTier[$tier] ?? ['count' => 0, 'value' => 0];
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-lg {{ $tierColors[$tier]['bg'] }} hover:shadow-sm transition">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" style="background: {{ $tierColors[$tier]['color'] }};"></div>
                                <span class="text-xs font-medium text-gray-700">{{ $tier }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($tierData['value'] > 0)
                                <span class="text-xs text-gray-400">£{{ number_format($tierData['value'] / 1000, 0) }}K</span>
                                @endif
                                <span class="text-xs font-bold text-gray-700 bg-white px-1.5 py-0.5 rounded shadow-sm">{{ $tierData['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Recent Deals, Follow-ups, Needs Attention -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Deals -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Recent Deals</h3>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Last 5</span>
            </div>
            
            @if(count($recentDeals) > 0)
            <div class="space-y-2">
                @foreach($recentDeals as $deal)
                <div wire:click="openDeal({{ $deal['id'] }})" class="block p-3 rounded-lg border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 transition group cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0 mr-3">
                            <p class="text-sm font-medium text-gray-900 truncate group-hover:text-purple-700">{{ $deal['company_name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $deal['user_name'] }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-900">£{{ number_format($deal['value'] / 1000, 0) }}K</p>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                {{ $deal['priority'] === 'Hot' ? 'bg-red-100 text-red-600' : ($deal['priority'] === 'Warm' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') }}">
                                {{ $deal['priority'] }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">No deals yet</p>
                <a href="{{ route('sponsorships.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-purple-600 hover:text-purple-700 mt-2">
                    Create first deal
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>

        <!-- Upcoming Follow-ups -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Follow-ups</h3>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded">7 days</span>
            </div>
            
            @if(count($upcomingFollowUps) > 0)
            <div class="space-y-2">
                @foreach($upcomingFollowUps as $followUp)
                @php
                    $daysUntil = now()->diffInDays($followUp['next_follow_up_date'], false);
                    $isToday = $daysUntil === 0;
                    $isTomorrow = $daysUntil === 1;
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $isToday ? 'border-red-200 bg-red-50' : ($isTomorrow ? 'border-amber-200 bg-amber-50' : 'border-gray-100 hover:border-gray-200') }} transition">
                    <div class="w-10 h-10 rounded-lg flex flex-col items-center justify-center shrink-0 {{ $isToday ? 'bg-red-500 text-white' : ($isTomorrow ? 'bg-amber-500 text-white' : 'bg-blue-100 text-blue-700') }}">
                        <span class="text-[9px] font-bold uppercase">{{ \Carbon\Carbon::parse($followUp['next_follow_up_date'])->format('M') }}</span>
                        <span class="text-sm font-bold leading-none">{{ \Carbon\Carbon::parse($followUp['next_follow_up_date'])->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $followUp['company_name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $followUp['stage'] }}</p>
                    </div>
                    @if($isToday)
                    <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-medium rounded">Today</span>
                    @elseif($isTomorrow)
                    <span class="px-2 py-0.5 bg-amber-500 text-white text-xs font-medium rounded">Tomorrow</span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">All caught up!</p>
                <p class="text-xs text-gray-400 mt-1">No follow-ups this week</p>
            </div>
            @endif
        </div>

        <!-- Stagnant Deals Alert -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900">Needs Attention</h3>
                    @if(count($stagnantDeals) > 0)
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    @endif
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded">14+ days</span>
            </div>
            
            @if(count($stagnantDeals) > 0)
            <div class="space-y-2">
                @foreach($stagnantDeals as $deal)
                <div class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 border border-amber-100">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $deal['company_name'] }}</p>
                        <p class="text-xs text-amber-600">
                            {{ $deal['last_activity_at'] ? \Carbon\Carbon::parse($deal['last_activity_at'])->diffForHumans() : 'No activity' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-900">£{{ number_format($deal['value'] / 1000, 0) }}K</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">Looking good!</p>
                <p class="text-xs text-gray-400 mt-1">All deals active</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Activity Timeline & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Recent Activity</h3>
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">Latest</span>
            </div>
            
            @if(count($recentActivities) > 0)
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                    <div class="relative flex gap-3 pl-10">
                        <div class="absolute left-2.5 w-4 h-4 rounded-full border-2 border-white shadow flex items-center justify-center
                            {{ $activity['type'] === 'stage_change' ? 'bg-purple-500' : 
                               ($activity['type'] === 'note' ? 'bg-blue-500' : 
                               ($activity['type'] === 'email' ? 'bg-green-500' : 'bg-gray-400')) }}">
                        </div>
                        <div class="flex-1 bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">{{ $activity['user_name'] }}</span>
                                {{ $activity['description'] }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs font-medium text-purple-600">{{ $activity['sponsorship_name'] }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-10">
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">No recent activity</p>
                <p class="text-xs text-gray-400 mt-1">Activities will appear as you work</p>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-2">
                <a href="{{ route('sponsorships.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-purple-200 hover:bg-purple-50/50 transition group">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-purple-700">New Deal</p>
                        <p class="text-xs text-gray-500">Create sponsorship</p>
                    </div>
                </a>

                @if(auth()->user()->isAdmin() || auth()->user()->isConsultant())
                <a href="{{ route('contacts.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Contacts</p>
                        <p class="text-xs text-gray-500">Manage contacts</p>
                    </div>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isExecutive())
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-green-200 hover:bg-green-50/50 transition group">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-green-700">Reports</p>
                        <p class="text-xs text-gray-500">View analytics</p>
                    </div>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('form-builder.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-orange-200 hover:bg-orange-50/50 transition group">
                    <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-orange-700">Lead Forms</p>
                        <p class="text-xs text-gray-500">Capture leads</p>
                    </div>
                </a>
                @endif
            </div>

            <!-- Monthly Trend Mini Chart -->
            <div class="mt-5 pt-4 border-t border-gray-100">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">6-Month Trend</h4>
                <div class="flex items-end justify-between gap-1.5 h-20">
                    @foreach($monthlyTrend as $month)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full flex flex-col items-center gap-0.5">
                            <div class="w-full bg-green-500 rounded-t transition-all hover:opacity-80" style="height: {{ max(4, $month['won'] * 8) }}px;"></div>
                            <div class="w-full bg-purple-500 rounded-b transition-all hover:opacity-80" style="height: {{ max(4, $month['created'] * 4) }}px;"></div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">{{ $month['month'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center gap-4 mt-3 text-xs">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-purple-500 rounded"></div>
                        <span class="text-gray-500">Created</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-green-500 rounded"></div>
                        <span class="text-gray-500">Won</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deal Modal -->
@if($showModal)
    @livewire('sponsorships.deal-modal', ['dealId' => $selectedDeal])
@endif
</div>
