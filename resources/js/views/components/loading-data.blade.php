@props(['on'])

<div x-data="{ shown: $wire.entangle('shown'), timeout: null, returnView: '' }"
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg my-2 w-full text-center"
    x-init="@this.on('{{ $on }}', (data) => {
        console.log(data);
        shown = true;
        returnView = data[0].returnView;
        $wire.setView(returnView);
    })" x-show="shown">
    <x-label class="text-xl font-bold my-16" value="{{ __('Loading data... Please wait...') }}" />
</div>
