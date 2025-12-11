<div class="bg-gray-50 min-h-screen -m-6 p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">Contacts</h2>
        <p class="text-sm text-gray-600">Manage all decision makers and company contacts</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="search" 
                    wire:model.live="search"
                    placeholder="Search by name, company, or email"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                >
            </div>

            <!-- Tier Filter -->
            <select wire:model.live="tierFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bame-pink">
                <option value="">All Tiers</option>
                <option value="Platinum">Platinum</option>
                <option value="Gold">Gold</option>
                <option value="Silver">Silver</option>
                <option value="Bronze">Bronze</option>
                <option value="In-Kind">In-Kind</option>
            </select>

            <!-- Stage Filter -->
            <select wire:model.live="stageFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bame-pink">
                <option value="">All Stages</option>
                @foreach(\App\Models\Sponsorship::STAGES as $stage)
                    <option value="{{ $stage }}">{{ $stage }}</option>
                @endforeach
            </select>
        </div>

        <!-- Stats -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Total Contacts:</span>
                <span class="font-semibold text-gray-900">{{ $totalContacts }}</span>
            </div>
        </div>
    </div>

    <!-- Contacts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($groupedContacts as $companyName => $contacts)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <!-- Company Header -->
                <div class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $companyName }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $contacts->first()->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $contacts->first()->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $contacts->first()->tier === 'Silver' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $contacts->first()->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $contacts->first()->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ $contacts->first()->tier }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">£{{ number_format($contacts->first()->value, 0) }}</div>
                        <div class="text-xs text-gray-500">Deal Value</div>
                    </div>
                </div>

                <!-- Contacts List -->
                <div class="space-y-3">
                    @foreach($contacts as $contact)
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-bame-pink text-white flex items-center justify-center font-semibold text-sm mr-3 flex-shrink-0">
                                {{ strtoupper(substr($contact->decision_maker_name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $contact->decision_maker_name }}</div>
                                <div class="text-sm text-gray-600 truncate">{{ $contact->decision_maker_email }}</div>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $contact->stage }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Actions -->
                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-xs text-gray-500">
                        Owner: {{ $contacts->first()->user->name }}
                    </div>
                    <button wire:click="viewDeal({{ $contacts->first()->id }})" class="text-sm text-bame-pink hover:text-pink-600 font-medium">
                        View Deal →
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No contacts found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search or filters</p>
                <a href="{{ route('sponsorships.index') }}" class="inline-flex items-center px-4 py-2 bg-bame-pink hover:bg-pink-600 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Deal
                </a>
            </div>
        @endforelse
    </div>

    <!-- Deal Detail Modal -->
    @if($showDetailModal && $selectedDeal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-pink-500 to-pink-600 rounded-t-lg">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $selectedDeal->company_name }}</h2>
                            <p class="text-pink-100 text-sm mt-1">Deal Details</p>
                        </div>
                        <button wire:click="closeModal" class="text-white hover:text-pink-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <!-- Contact Information -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Contact Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-4">
                                    <div class="w-14 h-14 rounded-full bg-bame-pink text-white flex items-center justify-center font-bold text-lg mr-4">
                                        {{ strtoupper(substr($selectedDeal->decision_maker_name ?? 'N/A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-lg">{{ $selectedDeal->decision_maker_name ?? 'No contact name' }}</div>
                                        <div class="text-gray-600">{{ $selectedDeal->decision_maker_email ?? 'No email' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deal Information -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Deal Information</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900">£{{ number_format($selectedDeal->value, 0) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Deal Value</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $selectedDeal->tier === 'Platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $selectedDeal->tier === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $selectedDeal->tier === 'Silver' ? 'bg-gray-200 text-gray-800' : '' }}
                                        {{ $selectedDeal->tier === 'Bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $selectedDeal->tier === 'In-Kind' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $selectedDeal->tier }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-2">Tier</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-lg font-semibold text-gray-900">{{ $selectedDeal->stage }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Stage</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $selectedDeal->probability }}%</div>
                                    <div class="text-xs text-gray-500 mt-1">Probability</div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Additional Details</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Priority</div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $selectedDeal->priority === 'Hot' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $selectedDeal->priority === 'Warm' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $selectedDeal->priority === 'Cold' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $selectedDeal->priority }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Source</div>
                                    <div class="font-medium text-gray-900">{{ $selectedDeal->source ?? 'Not specified' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Owner</div>
                                    <div class="font-medium text-gray-900">{{ $selectedDeal->user->name ?? 'Unassigned' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Last Activity</div>
                                    <div class="font-medium text-gray-900">{{ $selectedDeal->last_activity_at?->diffForHumans() ?? 'No activity' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Important Dates</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Created</div>
                                    <div class="font-medium text-gray-900">{{ $selectedDeal->created_at->format('M d, Y') }}</div>
                                </div>
                                @if($selectedDeal->proposal_sent_date)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Proposal Sent</div>
                                    <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($selectedDeal->proposal_sent_date)->format('M d, Y') }}</div>
                                </div>
                                @endif
                                @if($selectedDeal->contract_signed_date)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Contract Signed</div>
                                    <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($selectedDeal->contract_signed_date)->format('M d, Y') }}</div>
                                </div>
                                @endif
                                @if($selectedDeal->expected_close_date)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Expected Close</div>
                                    <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($selectedDeal->expected_close_date)->format('M d, Y') }}</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($selectedDeal->notes)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Notes</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $selectedDeal->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Recent Activity -->
                        @if($selectedDeal->activities && $selectedDeal->activities->count() > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Recent Activity</h3>
                            <div class="space-y-3">
                                @foreach($selectedDeal->activities as $activity)
                                <div class="flex items-start space-x-3 bg-gray-50 rounded-lg p-3">
                                    <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }} by {{ $activity->user->name ?? 'System' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                        <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                            Close
                        </button>
                        <a href="{{ route('sponsorships.index') }}" class="px-4 py-2 bg-bame-pink hover:bg-pink-600 text-white rounded-lg transition">
                            Go to Sponsorships
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
