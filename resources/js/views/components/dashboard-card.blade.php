<div class="w-full bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg hover:scale-105">
    <div class="py-4 px-6 border-b border-slate-600 font-bold text-slate-800 dark:text-gray-200 grid grid-cols-2">
        <div class="flex items-center">
            <div class="text-7xl">{{ $content}}</div>
        </div>

        @if (isset($icon))
            <div class="w-full text-right text-8xl opacity-25">
                {{ $icon }}
            </div>
        @endif

        <div class="font-bold dark:text-gray-200 col-span-2">
            {{ $title }}
        </div>
    </div>
    <div class="py-4 px-6 border-b-4 border-slate-600 text-center">
        {{ $actions }}
    </div>
</div>
