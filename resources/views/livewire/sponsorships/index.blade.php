<div x-data="{ viewMode: window.innerWidth < 768 ? 'list' : 'kanban' }" x-init="window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode === 'kanban') viewMode = 'list'; })">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Sponsorship Kanban</h2>
            <p class="text-xs sm:text-sm text-gray-600">Manage your sponsorship pipeline</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- View Toggle - Mobile Only -->
            <div class="md:hidden inline-flex rounded-lg bg-gray-100 p-1">
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-3 py-1.5 text-xs font-medium rounded-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>
                <button @click="viewMode = 'kanban'" :class="viewMode === 'kanban' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-3 py-1.5 text-xs font-medium rounded-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                </button>
            </div>
            <button wire:click="openCreateModal" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-3 sm:px-4 rounded-lg flex items-center text-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">New Deal</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Kanban Board - Desktop/Tablet -->
    <div x-show="viewMode === 'kanban'" class="hidden md:flex space-x-4 overflow-x-auto pb-4 -mx-4 px-4 lg:mx-0 lg:px-0">
        @foreach($stages as $stage)
            <div class="flex-shrink-0 w-72 lg:w-80">
                <!-- Column Header -->
                <div class="bg-gray-100 rounded-t-lg px-4 py-3 border-b-2 border-gray-300">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $stage }}</h3>
                        <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded-full">
                            {{ $stageCounts[$stage] }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600">
                        £{{ number_format($stageTotals[$stage], 0) }}
                    </div>
                </div>

                <!-- Cards Container -->
                <div 
                    class="bg-gray-50 rounded-b-lg p-2 lg:p-3 min-h-[400px] lg:min-h-[500px] space-y-2 lg:space-y-3 kanban-column"
                    data-stage="{{ $stage }}"
                >
                    @forelse($dealsByStage[$stage] as $deal)
                        <div 
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4 cursor-pointer hover:shadow-md transition kanban-card
                                {{ $deal->priority === 'Hot' ? 'border-l-4 border-l-red-500' : '' }}
                                {{ $deal->priority === 'Warm' ? 'border-l-4 border-l-orange-500' : '' }}
                                {{ $deal->priority === 'Cold' ? 'border-l-4 border-l-blue-500' : '' }}
                                {{ $deal->isStagnant() ? 'ring-2 ring-red-300' : '' }}"
                            data-deal-id="{{ $deal->id }}"
                            wire:click="openDeal({{ $deal->id }})"
                        >
                            <!-- Stagnant Warning -->
                            @if($deal->isStagnant())
                                <div class="flex items-center text-red-600 text-xs mb-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Stagnant
                                </div>
                            @endif

                            <!-- Company Name -->
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $deal->company_name }}</h4>
                            
                            <!-- Tier Badge -->
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $deal->tier === 'Silver' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $deal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $deal->tier }}
                                </span>
                            </div>

                            <!-- Value -->
                            <div class="text-base lg:text-lg font-bold text-gray-900 mb-2">
                                £{{ number_format($deal->value, 0) }}
                            </div>

                            <!-- Decision Maker -->
                            @if($deal->decision_maker_name)
                                <div class="text-xs text-gray-600 mb-2">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $deal->decision_maker_name }}
                                </div>
                            @endif

                            <!-- Last Activity -->
                            <div class="text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
                                Last activity: {{ $deal->last_activity_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-sm py-8">
                            No deals in this stage
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Mobile Kanban View -->
    <div x-show="viewMode === 'kanban'" class="md:hidden flex space-x-3 overflow-x-auto pb-4 -mx-4 px-4 snap-x snap-mandatory">
        @foreach($stages as $stage)
            <div class="flex-shrink-0 w-[85vw] snap-center">
                <!-- Column Header -->
                <div class="bg-gray-100 rounded-t-lg px-3 py-2 border-b-2 border-gray-300">
                    <div class="flex items-center justify-between mb-0.5">
                        <h3 class="font-semibold text-gray-900 text-xs truncate">{{ $stage }}</h3>
                        <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-0.5 rounded-full ml-2">
                            {{ $stageCounts[$stage] }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600">
                        £{{ number_format($stageTotals[$stage], 0) }}
                    </div>
                </div>
                <!-- Cards Container -->
                <div class="bg-gray-50 rounded-b-lg p-2 min-h-[300px] space-y-2">
                    @forelse($dealsByStage[$stage] as $deal)
                        <div 
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 cursor-pointer active:bg-gray-50 transition
                                {{ $deal->priority === 'Hot' ? 'border-l-4 border-l-red-500' : '' }}
                                {{ $deal->priority === 'Warm' ? 'border-l-4 border-l-orange-500' : '' }}
                                {{ $deal->priority === 'Cold' ? 'border-l-4 border-l-blue-500' : '' }}"
                            wire:click="openDeal({{ $deal->id }})">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $deal->company_name }}</h4>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium mt-1
                                        {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $deal->tier === 'Silver' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $deal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $deal->tier }}
                                    </span>
                                </div>
                                <div class="text-right ml-2">
                                    <div class="text-sm font-bold text-gray-900">£{{ number_format($deal->value, 0) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-xs py-6">No deals</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Mobile List View -->
    <div x-show="viewMode === 'list'" class="md:hidden space-y-4">
        @foreach($stages as $stage)
            @if($stageCounts[$stage] > 0)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $stage }}</h3>
                        <p class="text-xs text-gray-500">£{{ number_format($stageTotals[$stage], 0) }}</p>
                    </div>
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                        {{ $stageCounts[$stage] }}
                    </span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($dealsByStage[$stage] as $deal)
                        <div wire:click="openDeal({{ $deal->id }})" class="px-4 py-3 flex items-center justify-between cursor-pointer active:bg-gray-50 transition
                            {{ $deal->priority === 'Hot' ? 'border-l-4 border-l-red-500' : '' }}
                            {{ $deal->priority === 'Warm' ? 'border-l-4 border-l-orange-500' : '' }}
                            {{ $deal->priority === 'Cold' ? 'border-l-4 border-l-blue-500' : '' }}">
                            <div class="flex-1 min-w-0 mr-3">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $deal->company_name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                        {{ $deal->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $deal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $deal->tier === 'Silver' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $deal->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $deal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $deal->tier }}
                                    </span>
                                    @if($deal->isStagnant())
                                    <span class="text-xs text-red-600 font-medium">Stagnant</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">£{{ number_format($deal->value, 0) }}</p>
                                <p class="text-xs text-gray-500">{{ $deal->last_activity_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
        @if(collect($stageCounts)->sum() === 0)
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No deals yet</h3>
                <p class="text-sm text-gray-500 mb-4">Create your first sponsorship deal</p>
                <button wire:click="openCreateModal" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg text-sm">
                    Create Deal
                </button>
            </div>
        @endif
    </div>

    <!-- Deal Modal Component -->
    @if($showModal)
        <livewire:sponsorships.deal-modal :dealId="$selectedDeal" wire:key="deal-modal-{{ $selectedDeal ?? 'new' }}-{{ now()->timestamp }}" />
    @endif
</div>
