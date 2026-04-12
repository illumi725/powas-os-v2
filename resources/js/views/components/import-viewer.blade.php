@props(['id' => null, 'maxWidth' => 'full'])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="w-full">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>

                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 overflow-y-auto overflow-x-auto max-h-96 w-full">
                {{-- <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 overflow-x-auto overflow-y-auto max-h-96"> --}}
                    {{ $content }}
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end">
        {{ $footer }}
    </div>
</x-modal>
