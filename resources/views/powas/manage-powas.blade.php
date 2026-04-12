<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('POWAS Management') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('powas.powas-edit', ['powas_id' => $powas_id])
            <x-section-border />
            @livewire('settings.powas-settings', ['powas_id' => $powas_id])
            <x-section-border />
            @livewire('settings.powas-officers', ['powas_id' => $powas_id])
            <x-section-border />
            @livewire('settings.powas-beginning-balances', ['powas_id' => $powas_id])
            <x-section-border />
        </div>
    </div>
</x-app-layout>
