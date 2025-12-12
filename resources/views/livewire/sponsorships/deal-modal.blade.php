<div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="close"></div>

    <!-- Modal -->
    <div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
        <div class="relative bg-gray-900 rounded-t-2xl sm:rounded-lg shadow-xl w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <!-- Mobile drag handle -->
            <div class="sm:hidden w-12 h-1.5 bg-gray-600 rounded-full mx-auto mt-3 mb-2"></div>
            
            <!-- Header -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-700">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-white">{{ $dealId ? 'Edit Deal' : 'Create New Deal' }}</h2>
                    @if($dealId && $deal)
                        <p class="text-sm text-gray-400 mt-1">Last activity: {{ $deal->last_activity_at->diffForHumans() }}</p>
                    @endif
                </div>
                <button wire:click="close" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit="save" class="p-4 sm:p-6">
                <div class="space-y-4 sm:space-y-6">
                    <!-- Deal Information Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Deal Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <!-- Company Name -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-white mb-2">Company Name</label>
                                <input 
                                    type="text" 
                                    wire:model="company_name"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                    placeholder="Acme Corporation"
                                >
                                @error('company_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Decision Maker -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Decision Maker</label>
                                <input 
                                    type="text" 
                                    wire:model="decision_maker_name"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                    placeholder="John Doe"
                                >
                                @error('decision_maker_name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Email</label>
                                <input 
                                    type="email" 
                                    wire:model="decision_maker_email"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                    placeholder="john@acme.com"
                                >
                                @error('decision_maker_email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Deal Value -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Deal Value</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400">£</span>
                                    <input 
                                        type="number" 
                                        wire:model="value"
                                        class="w-full pl-8 pr-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink"
                                        placeholder="50,000"
                                        step="0.01"
                                    >
                                </div>
                                @error('value') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tier -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Tier</label>
                                <select 
                                    wire:model="tier"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                    <option value="">Select tier</option>
                                    <option value="Platinum">Platinum</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Bronze">Bronze</option>
                                    <option value="In-Kind">In-Kind</option>
                                </select>
                                @error('tier') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Stage -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Stage</label>
                                <select 
                                    wire:model="stage"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                    @foreach(\App\Models\Sponsorship::STAGES as $stageOption)
                                        <option value="{{ $stageOption }}">{{ $stageOption }}</option>
                                    @endforeach
                                </select>
                                @error('stage') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Probability (Read-only, auto-calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Probability</label>
                                <div class="relative">
                                    <input 
                                        type="range" 
                                        wire:model="probability"
                                        min="0" 
                                        max="100"
                                        class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-bame-pink"
                                        disabled
                                    >
                                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                                        <span>0%</span>
                                        <span class="font-semibold text-white">{{ $probability }}%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Priority</label>
                                <select 
                                    wire:model="priority"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                    <option value="Hot">Hot</option>
                                    <option value="Warm">Warm</option>
                                    <option value="Cold">Cold</option>
                                </select>
                                @error('priority') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Source -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Source</label>
                                <select 
                                    wire:model="source"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                    @foreach(\App\Models\Sponsorship::SOURCES as $sourceOption)
                                        <option value="{{ $sourceOption }}">{{ $sourceOption }}</option>
                                    @endforeach
                                </select>
                                @error('source') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Proposal Sent Date -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Proposal Sent Date</label>
                                <input 
                                    type="date" 
                                    wire:model="proposal_sent_date"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                @error('proposal_sent_date') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contract Signed Date -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Contract Signed Date</label>
                                <input 
                                    type="date" 
                                    wire:model="contract_signed_date"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                >
                                @error('contract_signed_date') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Loss Reason (if applicable) -->
                            @if($stage === 'Closed Lost')
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-white mb-2">Loss Reason</label>
                                    <input 
                                        type="text" 
                                        wire:model="loss_reason"
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                        placeholder="Reason for losing this deal"
                                    >
                                    @error('loss_reason') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Notes -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-white mb-2">Notes</label>
                                <textarea 
                                    wire:model="notes"
                                    rows="4"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink text-sm sm:text-base"
                                    placeholder="Add any additional notes about this deal..."
                                ></textarea>
                                @error('notes') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 sm:space-x-3 mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-700">
                    <button 
                        type="button"
                        wire:click="close"
                        class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-sm sm:text-base"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-bame-pink hover:bg-pink-600 text-white rounded-lg transition text-sm sm:text-base"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
