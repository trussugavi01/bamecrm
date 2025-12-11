<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'B.A.M.E CRM' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased">
    {{ $slot }}

    <!-- Footer -->
    <footer class="fixed bottom-0 left-0 right-0 py-4 bg-gradient-to-r from-gray-50/95 via-purple-50/95 to-gray-50/95 backdrop-blur-sm border-t border-gray-200">
        <div class="text-center space-y-1">
            <p class="text-sm text-gray-600">
                &copy; {{ date('Y') }} B.A.M.E CRM. All rights reserved.
            </p>
            <p class="text-xs text-gray-500">
                Designed by 
                <a href="https://smartdeskstudio.ng" target="_blank" rel="noopener noreferrer" class="text-purple-600 hover:text-purple-700 font-medium transition-colors">
                    SmartDesk Studio
                </a>
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
