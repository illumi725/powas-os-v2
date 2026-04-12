<x-app-layout>
    {{-- @dd(Auth::user()->getAccountStatus()) --}}
    @if (Auth::user()->isActive())
        <x-slot name="header">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="flex items-center">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Dashboard') }}
                    </h2>
                </div>

                {{-- @canany(['create bill payment', 'edit bill payment']) --}}
                    @livewire('powas.add-payment')
                {{-- @endcanany --}}
            </div>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- <x-welcome /> --}}
                @if (Auth::user()->hasRole('member'))
                    @livewire('dashboards.member')
                @elseif (Auth::user()->hasRole('admin'))
                    @livewire('dashboards.admin')
                    {{-- @elseif (Auth::user()->hasRole('president|vice-president|secretary|treasurer|auditor|board|barangay-coordinator')) --}}
                @elseif (Auth::user()->hasRole('president|vice-president|secretary|treasurer|auditor|collector-reader|board'))
                    @livewire('dashboards.officer')
                @endif
            </div>
        </div>
    @else
        @if (Auth::user()->getAccountStatus() == 'INACTIVE')
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Initial Login') }}
                </h2>
            </x-slot>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mt-10 sm:mt-0">
                        @livewire('profile.update-password-form')
                    </div>
                    <x-section-border />
                </div>
            </div>
        @elseif(Auth::user()->getAccountStatus() == 'DEACTIVATED')
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Account Deactivated') }}
                </h2>
            </x-slot>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mt-10 sm:mt-0 text-center text-xl text-red-500 font-bold">
                        {{ __('Your account is deactivated! Please contact your web administrator!') }}
                    </div>
                    <x-section-border />
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
