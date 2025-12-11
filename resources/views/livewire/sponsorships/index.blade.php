<div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Sponsorship Kanban</h2>
            <p class="text-sm text-gray-600">Manage your sponsorship pipeline</p>
        </div>
        <button wire:click="openCreateModal" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Deal
        </button>
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

    <!-- Kanban Board -->
    <div class="flex space-x-4 overflow-x-auto pb-4">
        @foreach($stages as $stage)
            <div class="flex-shrink-0 w-80">
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
                    class="bg-gray-50 rounded-b-lg p-3 min-h-[500px] space-y-3 kanban-column"
                    data-stage="{{ $stage }}"
                >
                    @forelse($dealsByStage[$stage] as $deal)
                        <div 
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md transition kanban-card
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
                            <div class="text-lg font-bold text-gray-900 mb-2">
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

    <!-- Deal Modal Component -->
    @if($showModal)
        <livewire:sponsorships.deal-modal :dealId="$selectedDeal" wire:key="deal-modal-{{ $selectedDeal ?? 'new' }}-{{ now()->timestamp }}" />
    @endif
</div>
