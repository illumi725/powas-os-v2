<div
    {{ $attributes->merge(['class' => 'w-full grid grid-cols-1 md:grid-cols-3 my-2 p-4 text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md rounded-lg']) }}>
    <div class="w-full md:col-span-3">
        {{ $checkbox }}
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
            <div class="w-full text-right">
                <div class="font-bold grid grid-cols-3">
                    <div class="col-span-2 font-normal">
                        {{ __('Billing #: ') }}
                    </div>
                    <div>
                        {{ $billNumber }}
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full pl-4">
            <div class="w-full">
                <span>
                    {{ __('Billing Month: ') }}
                </span>
                <span class="font-bold">
                    {{ $billingMonth }}
                </span>
            </div>
            <div class="w-full">
                <span>
                    {{ __('Billing Period: ') }}
                </span>
                <span class="font-bold">
                    {{ $billingPeriod }}
                </span>
            </div>
            <div class="w-full">
                <span>
                    {{ __('Due Date: ') }}
                </span>
                <span class="font-bold">
                    {{ $dueDate }}
                </span>
            </div>
            <div class="w-full grid grid-cols-2">
                <div>
                    <span>
                        {{ __('Already saved?: ') }}
                    </span>
                    <span class="font-bold">
                        {{ $isExists }}
                    </span>
                </div>
                {{-- <div>
                    <span>
                        {{ __('Print Count: ') }}
                    </span>
                    <span class="font-bold">
                        {{ $printCount }}
                    </span>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="w-full md:col-span-2 grid grid-cols-1 md:grid-cols-3 pl-4 font-bold">
        <div class="w-full grid grid-cols-2 mt-2 md:mt-0">
            <div class="text-center uppercase col-span-2">
                {{ __('Reading') }}
            </div>
            <div class="text-center italic font-normal underline">
                {{ __('Present') }}
            </div>
            <div class="text-center italic font-normal underline">
                {{ __('Previous') }}
            </div>
            <div class="text-center">
                {{ $presentReading }}
            </div>
            <div class="text-center">
                {{ $previousReading }}
            </div>
            <div class="w-full font-normal">
                {!! __('Consumption:') !!}
            </div>
            <div class="w-full text-right">
                {{ $consumption }}
            </div>
            <div class="w-full col-span-2 grid grid-cols-3">
                <div class="w-full col-span-2 font-normal">
                    {{ $isMinimum }}
                </div>
                <div class="w-full text-right">
                    &#8369;{{ $billAmount }}
                </div>
            </div>
        </div>

        <div class="w-full grid grid-cols-2 mt-2 md:mt-0 md:pl-4">
            <div class="col-span-2">
                <div class="text-center uppercase">
                    {{ __('Discounts') }}
                </div>
                {{ $discountControl }}
            </div>
        </div>

        <div class="w-full grid grid-cols-2 mt-2 md:mt-0 md:pl-4">
            <div class="col-span-2">
                <div class="text-center uppercase">
                    {{ __('Penalties') }}
                </div>
                {{ $penaltyControl }}
            </div>
        </div>
    </div>
</div>
