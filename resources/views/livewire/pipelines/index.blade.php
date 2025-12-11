<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pipeline Management</h1>
            <p class="text-gray-500 mt-1">Create and manage custom sales pipelines.</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Pipeline
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Pipelines List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($pipelines as $pipeline)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $pipeline->name }}</h3>
                            @if($pipeline->is_default)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Default
                                </span>
                            @endif
                        </div>
                        @if($pipeline->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $pipeline->description }}</p>
                        @endif
                        <p class="text-xs text-gray-500">Created {{ $pipeline->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-1">
                        <button wire:click="editPipeline({{ $pipeline->id }})" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="deletePipeline({{ $pipeline->id }})" wire:confirm="Are you sure you want to delete this pipeline?" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Pipeline Stages -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Stages ({{ $pipeline->stages->count() }})</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pipeline->stages as $stage)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $stage->color }}20; color: {{ $stage->color }};">
                                {{ $stage->name }} ({{ $stage->probability }}%)
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $pipeline->sponsorships->count() }}</span> active deals
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium mb-2">No pipelines created yet</p>
                <p class="text-xs text-gray-400 mb-4">Create your first pipeline to customize your sales process</p>
                <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Your First Pipeline
                </button>
            </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>

            <!-- Modal -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-100 sticky top-0 bg-white z-10 rounded-t-xl">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $editingPipelineId ? 'Edit Pipeline' : 'Create New Pipeline' }}
                        </h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit="savePipeline" class="p-6">
                        <div class="space-y-6">
                            <!-- Pipeline Details -->
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Pipeline Details</h3>
                                
                                <div class="space-y-4">
                                    <!-- Pipeline Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pipeline Name</label>
                                        <input 
                                            type="text" 
                                            wire:model="name"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                            placeholder="e.g., Standard Sales Pipeline"
                                        >
                                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                                        <textarea 
                                            wire:model="description"
                                            rows="2"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                            placeholder="Describe this pipeline..."
                                        ></textarea>
                                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Is Default -->
                                    <div>
                                        <label class="flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                wire:model="is_default"
                                                class="w-4 h-4 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Set as default pipeline for new deals</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Pipeline Stages -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-semibold text-gray-900">Pipeline Stages</h3>
                                    <button 
                                        type="button"
                                        wire:click="addStage"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 text-sm font-medium rounded-lg transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Stage
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    @foreach($stages as $index => $stage)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="grid grid-cols-12 gap-3 items-start">
                                                <!-- Order -->
                                                <div class="col-span-1 flex items-center justify-center">
                                                    <span class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-sm font-semibold text-gray-600 border border-gray-300">
                                                        {{ $index + 1 }}
                                                    </span>
                                                </div>

                                                <!-- Stage Name -->
                                                <div class="col-span-5">
                                                    <input 
                                                        type="text" 
                                                        wire:model="stages.{{ $index }}.name"
                                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                                                        placeholder="Stage name"
                                                    >
                                                    @error("stages.{$index}.name") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                </div>

                                                <!-- Probability -->
                                                <div class="col-span-3">
                                                    <div class="flex items-center gap-2">
                                                        <input 
                                                            type="number" 
                                                            wire:model="stages.{{ $index }}.probability"
                                                            min="0"
                                                            max="100"
                                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                                                            placeholder="0"
                                                        >
                                                        <span class="text-sm text-gray-500">%</span>
                                                    </div>
                                                    @error("stages.{$index}.probability") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                </div>

                                                <!-- Color -->
                                                <div class="col-span-2">
                                                    <input 
                                                        type="color" 
                                                        wire:model="stages.{{ $index }}.color"
                                                        class="w-full h-10 bg-white border border-gray-300 rounded-lg cursor-pointer"
                                                    >
                                                </div>

                                                <!-- Remove Button -->
                                                <div class="col-span-1 flex items-center justify-center">
                                                    <button 
                                                        type="button"
                                                        wire:click="removeStage({{ $index }})"
                                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(count($stages) === 0)
                                        <div class="text-center py-8 text-gray-500 text-sm">
                                            No stages added yet. Click "Add Stage" to create your first stage.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition shadow-sm"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>{{ $editingPipelineId ? 'Update Pipeline' : 'Create Pipeline' }}</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
