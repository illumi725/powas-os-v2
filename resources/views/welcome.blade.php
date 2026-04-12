<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>POWAS-OS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/powas.ico') }}" type="image/x-icon">
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <link rel="stylesheet" href="../css/tailwind.css" /> --}}
    @livewireStyles
</head>

<body class="antialiased">
    {{-- @php
            phpinfo();
        @endphp --}}

    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log
                        in</a>

                    @if (Route::has('apply'))
                        <a href="{{ route('apply') }}"
                            class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Apply</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <img src="{{ URL::asset('assets/logo.png') }}" alt="POWAS-OS" width="64">
            </div>
            <div class="flex justify-center sm:text-center mt-2">
                <span class="text-xl font-bold dark:text-white">{{ __('POWAS-OS') }}</span>
            </div>

            <div class="mt-4">
                <div class="grid grid-cols-1 mx-auto md:w-2/3 sm:w-full">
                    @livewire('account-search')
                    <x-section-border />
                    @livewire('application-follow-up')
                </div>
            </div>
        </div>
    </div>
    @livewire('chatbot.poca')
    @livewireScripts
</body>

</html>
