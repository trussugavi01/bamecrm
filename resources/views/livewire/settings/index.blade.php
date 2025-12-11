<div class="bg-gray-50 min-h-screen -m-6 p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">Settings</h2>
        <p class="text-sm text-gray-600">Manage your account and application settings</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button 
                    wire:click="$set('activeTab', 'profile')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeTab === 'profile' ? 'border-bame-pink text-bame-pink' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Profile
                </button>
                <button 
                    wire:click="$set('activeTab', 'password')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeTab === 'password' ? 'border-bame-pink text-bame-pink' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Password
                </button>
                <button 
                    wire:click="$set('activeTab', 'api')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeTab === 'api' ? 'border-bame-pink text-bame-pink' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    API Keys
                </button>
                <button 
                    wire:click="$set('activeTab', 'system')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeTab === 'system' ? 'border-bame-pink text-bame-pink' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    System
                </button>
            </nav>
        </div>
    </div>

    <!-- Profile Tab -->
    @if($activeTab === 'profile')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
            <form wire:submit="updateProfile" class="space-y-4 max-w-xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input 
                        type="text" 
                        wire:model="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                    >
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        wire:model="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                    >
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <input 
                        type="text" 
                        value="{{ ucfirst(auth()->user()->role) }}"
                        disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                    >
                    <p class="text-xs text-gray-500 mt-1">Contact an administrator to change your role</p>
                </div>

                <button 
                    type="submit"
                    class="bg-bame-pink hover:bg-pink-600 text-white font-semibold py-2 px-6 rounded-lg transition"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving...</span>
                </button>
            </form>
        </div>
    @endif

    <!-- Password Tab -->
    @if($activeTab === 'password')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
            <form wire:submit="updatePassword" class="space-y-4 max-w-xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        wire:model="current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                    >
                    @error('current_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input 
                        type="password" 
                        wire:model="new_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                    >
                    @error('new_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        wire:model="new_password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bame-pink"
                    >
                </div>

                <button 
                    type="submit"
                    class="bg-bame-pink hover:bg-pink-600 text-white font-semibold py-2 px-6 rounded-lg transition"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Update Password</span>
                    <span wire:loading>Updating...</span>
                </button>
            </form>
        </div>
    @endif

    <!-- API Keys Tab -->
    @if($activeTab === 'api')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">API Configuration</h3>
            
            <div class="space-y-6 max-w-2xl">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-blue-900 mb-1">API Key Information</h4>
                            <p class="text-sm text-blue-800">Use this key to authenticate API requests. Keep it secure and never share it publicly.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 relative">
                            <input 
                                type="{{ $show_api_key ? 'text' : 'password' }}" 
                                value="{{ $api_key }}"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-mono text-sm"
                            >
                        </div>
                        <button 
                            type="button"
                            wire:click="toggleApiKeyVisibility"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                            title="{{ $show_api_key ? 'Hide' : 'Show' }}"
                        >
                            @if($show_api_key)
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                        <button 
                            type="button"
                            wire:click="regenerateApiKey"
                            wire:confirm="Are you sure? This will invalidate the current API key."
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition"
                        >
                            Regenerate
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">After regenerating, update the API_KEY_SALT in your .env file</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">API Endpoint</h4>
                    <code class="text-sm text-gray-700 bg-white px-3 py-2 rounded border border-gray-300 block">
                        POST {{ url('/api/leads/ingest') }}
                    </code>
                    <p class="text-xs text-gray-600 mt-2">Include header: <code class="bg-white px-2 py-1 rounded">X-API-KEY: your-api-key</code></p>
                </div>
            </div>
        </div>
    @endif

    <!-- System Tab -->
    @if($activeTab === 'system')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Settings</h3>
            
            <div class="space-y-6 max-w-xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quarterly Goal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">£</span>
                        <input 
                            type="number" 
                            wire:model="quarterly_goal"
                            disabled
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Configure in .env file: QUARTERLY_GOAL</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                    <input 
                        type="text" 
                        value="{{ $currency }}"
                        disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                    >
                    <p class="text-xs text-gray-500 mt-1">British Pound (£)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                    <input 
                        type="text" 
                        value="{{ $timezone }}"
                        disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                    >
                    <p class="text-xs text-gray-500 mt-1">Configure in config/app.php</p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-yellow-900 mb-1">System Configuration</h4>
                            <p class="text-sm text-yellow-800">Most system settings require server-level configuration. Contact your system administrator for changes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
