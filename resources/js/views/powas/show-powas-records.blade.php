<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <span class="italic font-bold block text-xs">{{ __('[') . $powas->powas_id . __(']') }}</span>
            {{ $powas->barangay . ' POWAS ' . $powas->phase }}
            <span
                class="block font-normal text-xs">{{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (count($powasErrors) > 0)
                <div
                    class="bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 overflow-hidden shadow-xl sm:rounded-lg py-6 flex justify-center">
                    <div class="mx-2 md:mx-0">
                        <div class="block">
                            {{ __('There are issues with this POWAS!') }}
                        </div>
                        <div class="block mt-2">
                            <ul class="list-disc ml-8">
                                @foreach ($powasErrors as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    @livewire('powas.powas-readings', ['powasID' => $powasID])
                </div>

                <x-section-border />

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    @livewire('powas.powas-billings', ['powasID' => $powasID])
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
