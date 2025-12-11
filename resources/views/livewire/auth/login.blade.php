<div class="min-h-screen flex">
    <!-- Left Side - Gradient Background -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-pink-500 via-purple-600 to-purple-800 relative overflow-hidden">
        <!-- Decorative waves -->
        <div class="absolute inset-0">
            <svg class="absolute bottom-0 left-0 w-full h-full" viewBox="0 0 1200 800" preserveAspectRatio="none">
                <path d="M0,400 Q300,200 600,400 T1200,400 L1200,800 L0,800 Z" fill="rgba(236, 72, 153, 0.3)"/>
                <path d="M0,500 Q300,300 600,500 T1200,500 L1200,800 L0,800 Z" fill="rgba(168, 85, 247, 0.3)"/>
                <path d="M0,600 Q300,400 600,600 T1200,600 L1200,800 L0,800 Z" fill="rgba(147, 51, 234, 0.3)"/>
            </svg>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-900 px-6 py-12">
        <div class="w-full max-w-md">
            <!-- Logo/Icon -->
            <div class="flex justify-center mb-8">
                <div class="w-16 h-16 bg-bame-pink rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Welcome Text -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
                <p class="text-gray-400">Sign in to manage your sponsorship lifecycle.</p>
            </div>

            <!-- Login Form -->
            <form wire:submit="login" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent transition"
                        placeholder="Enter your email"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="{{ $showPassword ? 'text' : 'password' }}" 
                            id="password" 
                            wire:model="password"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent transition pr-12"
                            placeholder="Enter your password"
                            required
                        >
                        <button 
                            type="button"
                            wire:click="togglePasswordVisibility"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300 transition"
                        >
                            @if($showPassword)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot Password Link -->
                <div class="flex items-center justify-end">
                    <a href="{{ route('password.request') }}" class="text-sm text-bame-pink hover:text-pink-400 transition">
                        Forgot Password?
                    </a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-bame-pink hover:bg-pink-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:ring-offset-2 focus:ring-offset-gray-900"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Login</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
