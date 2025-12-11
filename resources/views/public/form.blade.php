<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Partner With Us - B.A.M.E Sponsorship</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-purple-900 via-purple-800 to-purple-900 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-bame-pink rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Partner With Us</h1>
                <p class="text-gray-300">Fill out the form below to learn more about our sponsorship opportunities.</p>
            </div>

            <!-- Form Card -->
            <div class="bg-gray-800 rounded-lg shadow-2xl p-8 border border-gray-700">
                @if (session('success'))
                    <div class="mb-6 bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('form.submit', $form->uuid) }}" class="space-y-6">
                    @csrf

                    <!-- Honeypot field (hidden) -->
                    <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                    @if($form->form_schema['company']['visible'])
                        <div>
                            <label for="company" class="block text-sm font-medium text-white mb-2">
                                {{ $form->form_schema['company']['label'] }}
                                @if($form->form_schema['company']['required'])
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <input 
                                type="text" 
                                id="company"
                                name="company"
                                value="{{ old('company') }}"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent"
                                placeholder="Enter your company name"
                                {{ $form->form_schema['company']['required'] ? 'required' : '' }}
                            >
                            @error('company')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($form->form_schema['name']['visible'])
                        <div>
                            <label for="name" class="block text-sm font-medium text-white mb-2">
                                {{ $form->form_schema['name']['label'] }}
                                @if($form->form_schema['name']['required'])
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <input 
                                type="text" 
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent"
                                placeholder="Enter your full name"
                                {{ $form->form_schema['name']['required'] ? 'required' : '' }}
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($form->form_schema['email']['visible'])
                        <div>
                            <label for="email" class="block text-sm font-medium text-white mb-2">
                                {{ $form->form_schema['email']['label'] }}
                                @if($form->form_schema['email']['required'])
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <input 
                                type="email" 
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent"
                                placeholder="Enter your email address"
                                {{ $form->form_schema['email']['required'] ? 'required' : '' }}
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($form->form_schema['tier']['visible'])
                        <div>
                            <label for="tier" class="block text-sm font-medium text-white mb-2">
                                {{ $form->form_schema['tier']['label'] }}
                                @if($form->form_schema['tier']['required'])
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <select 
                                id="tier"
                                name="tier"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent"
                                {{ $form->form_schema['tier']['required'] ? 'required' : '' }}
                            >
                                <option value="">Select a tier</option>
                                <option value="Platinum" {{ old('tier') === 'Platinum' ? 'selected' : '' }}>Platinum</option>
                                <option value="Gold" {{ old('tier') === 'Gold' ? 'selected' : '' }}>Gold</option>
                                <option value="Silver" {{ old('tier') === 'Silver' ? 'selected' : '' }}>Silver</option>
                                <option value="Bronze" {{ old('tier') === 'Bronze' ? 'selected' : '' }}>Bronze</option>
                                <option value="In-Kind" {{ old('tier') === 'In-Kind' ? 'selected' : '' }}>In-Kind</option>
                            </select>
                            @error('tier')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($form->form_schema['message']['visible'])
                        <div>
                            <label for="message" class="block text-sm font-medium text-white mb-2">
                                {{ $form->form_schema['message']['label'] }}
                                @if($form->form_schema['message']['required'])
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <textarea 
                                id="message"
                                name="message"
                                rows="4"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:border-transparent"
                                placeholder="Tell us more about your sponsorship goals..."
                                {{ $form->form_schema['message']['required'] ? 'required' : '' }}
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-bame-pink hover:bg-pink-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-bame-pink focus:ring-offset-2 focus:ring-offset-gray-800"
                    >
                        {{ $form->submit_button_text }}
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-gray-400 text-sm">
                <p>Powered by B.A.M.E CRM</p>
            </div>
        </div>
    </div>
</body>
</html>
