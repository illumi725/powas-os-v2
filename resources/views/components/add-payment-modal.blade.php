@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    {{-- <div class="px-6 py-4"> --}}
    <div class="">
        {{-- border-b border-gray-200 dark:border-gray-700 --}}
        <div
            class="text-lg font-bold text-gray-900 dark:text-gray-100 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            {{ $title }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end">
        {{ $footer }}
    </div>
</x-modal>
