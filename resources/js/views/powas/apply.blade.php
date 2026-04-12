<x-guest-layout>
    <div class="py-6 bg-gray-100 dark:bg-gray-900">
        <x-application-card class="w-4/5">
            <x-slot name="logo">
                <div>
                    <x-authentication-card-logo />
                </div>
            </x-slot>

            <div class="flex justify-center sm:text-center mt-1 mb-6">
                <span class="text-xl font-bold dark:text-white">{{ __('POWAS Application') }}</span>
            </div>

            @livewire('powas.apply')
        </x-application-card>
    </div>

</x-guest-layout>
