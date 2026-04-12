<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {!! __('Add POWAS Application') !!}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg py-4 px-4 space-y-4">
                @livewire('members.add-member-manually', ['selectedPOWAS' => $selectedPOWAS])
            </div>
        </div>
    </div>
</x-app-layout>
