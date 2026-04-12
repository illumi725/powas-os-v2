<div
    {{ $attributes->merge(['class' => 'w-full grid grid-cols-1 md:grid-cols-3 my-2 p-4 text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md rounded-lg']) }}>
    <div class="w-full md:col-span-3">
        {{ $readingIDs }}
    </div>
    <div class="w-full">
        <div class="w-full grid grid-cols-3">
            <div class="col-span-2">
                <div class="w-full font-bold">
                    <div class="text-center inline">
                        {{ $counter . '. ' }}
                    </div>
                    <div class="w-full inline">
                        {{ $memberName }}
                    </div>
                </div>
            </div>
            <div class="w-full">
                <div class="font-bold">
                    <div>
                        {{ $readCount }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-full md:col-span-2 grid grid-cols-1 md:grid-cols-4 pl-4 font-bold gap-4">
        <div class="w-full grid grid-cols-2 md:col-span-2 mt-2 md:mt-0 gap-2">
            <div>
                {{ $previousReading }}
            </div>
            <div>
                {{ $presentReading }}
            </div>
        </div>
        <div class="w-full">
            {{ $readingDate }}
        </div>
        <div class="w-full flex items-end">
            {{ $buttons }}
        </div>

        {{-- <div class="w-full grid grid-cols-2 mt-2 md:mt-0 md:pl-4">
            <div class="col-span-2">
                <div class="text-center uppercase">
                    {{ __('Penalties') }}
                </div>
                {{ $penaltyControl }}
            </div>
        </div> --}}
    </div>
</div>
