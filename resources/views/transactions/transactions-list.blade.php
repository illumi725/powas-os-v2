<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <span class="italic font-bold block text-xs">{{ __('[') . $powas->powas_id . __(']') }}</span>
            {{ $powas->barangay . ' POWAS ' . $powas->phase }}
            <span
                class="block font-normal text-xs">{{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}</span>
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-3 sm:px-6 lg:px-8">
            @livewire('components.transactions-nav', ['powasID' => $powasID, 'powas' => $powas])
        </div>
    </div>
</x-app-layout>
