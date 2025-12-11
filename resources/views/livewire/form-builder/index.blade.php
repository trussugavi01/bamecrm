<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Builder</h1>
            <p class="text-gray-500 mt-1">Create and manage public sponsorship interest forms.</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Form
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

    <!-- Forms List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($forms as $form)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $form->name }}</h3>
                        <p class="text-xs text-gray-500">Created {{ $form->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-1">
                        <button wire:click="editForm({{ $form->id }})" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="deleteForm({{ $form->id }})" wire:confirm="Are you sure you want to delete this form?" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form Stats -->
                <div class="mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                            {{ collect($form->form_schema)->where('visible', true)->count() }} visible fields
                        </span>
                    </div>
                </div>

                <!-- Links -->
                <div class="space-y-3">
                    <div x-data="{ copied: false }">
                        <label class="text-xs font-medium text-gray-500 mb-1.5 block">Direct Link</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                value="{{ $form->public_url }}" 
                                readonly
                                class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500/20"
                            >
                            <button 
                                @click="navigator.clipboard.writeText('{{ $form->public_url }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 hover:text-purple-600 transition"
                                title="Copy link"
                            >
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="copied" x-cloak x-transition class="text-xs text-green-600 mt-1">Link copied!</p>
                    </div>

                    <div x-data="{ copied: false }">
                        <label class="text-xs font-medium text-gray-500 mb-1.5 block">Embed on Your Website</label>
                        <div class="flex items-center gap-2">
                            <textarea 
                                readonly
                                rows="2"
                                class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-purple-500/20"
                            >{{ $form->embed_code }}</textarea>
                            <button 
                                @click="navigator.clipboard.writeText(`{{ addslashes($form->embed_code) }}`); copied = true; setTimeout(() => copied = false, 2000)"
                                class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 hover:text-purple-600 transition self-start"
                                title="Copy embed code"
                            >
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="copied" x-cloak x-transition class="text-xs text-green-600 mt-1">Embed code copied!</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium mb-2">No forms created yet</p>
                <p class="text-xs text-gray-400 mb-4">Create your first form to start collecting leads</p>
                <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Your First Form
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
                <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full" @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $editingFormId ? 'Edit Form' : 'Create New Form' }}
                        </h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit="saveForm" class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Left Column: Configure Form Fields -->
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Configure Form Fields</h3>
                                
                                <div class="space-y-2">
                                    @foreach($schema as $field => $config)
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <!-- Field Icon -->
                                                    <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                                                        @if($field === 'company')
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        @elseif($field === 'name')
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        @elseif($field === 'email')
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        @elseif($field === 'tier')
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900 text-sm">{{ $config['label'] }}</div>
                                                        <div class="text-xs text-gray-500 capitalize">{{ $field }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center space-x-4">
                                                    <!-- Visible Toggle -->
                                                    <label class="flex items-center cursor-pointer">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model="schema.{{ $field }}.visible"
                                                            class="sr-only peer"
                                                        >
                                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                                        <span class="ml-2 text-xs text-gray-500">Visible</span>
                                                    </label>
                                                    
                                                    <!-- Required Toggle -->
                                                    <label class="flex items-center cursor-pointer">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model="schema.{{ $field }}.required"
                                                            class="w-4 h-4 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                                                        >
                                                        <span class="ml-2 text-xs text-gray-500">Required</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Right Column: Form Settings -->
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Form Settings</h3>
                                
                                <div class="space-y-4">
                                    <!-- Form Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Form Name (Internal)</label>
                                        <input 
                                            type="text" 
                                            wire:model.blur="name"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                            placeholder="e.g., Main Sponsorship Form"
                                        >
                                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Submit Button Text -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Submit Button Text</label>
                                        <input 
                                            type="text" 
                                            wire:model.blur="submit_button_text"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                        >
                                        @error('submit_button_text') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Success Message -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Success Message</label>
                                        <textarea 
                                            wire:model.blur="success_message"
                                            rows="3"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                        ></textarea>
                                        @error('success_message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Redirect URL (Optional) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Redirect URL (Optional)</label>
                                        <input 
                                            type="text" 
                                            wire:model.blur="redirect_url"
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500"
                                            placeholder="https://example.com/thank-you"
                                        >
                                        <p class="text-xs text-gray-500 mt-1">Leave empty to show success message</p>
                                        @error('redirect_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
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
                                <span wire:loading.remove>{{ $editingFormId ? 'Update Form' : 'Create Form' }}</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
