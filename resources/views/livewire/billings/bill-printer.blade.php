<div class="w-full" x-data="{ divHeightPixels: 0, divHeightMillimeters: 0, divTotalHeight: 0, }">
    <div class="no-print sticky top-0 bg-black text-white py-4">
        {{-- Paper Size --}}
        <div class="mx-auto w-96 z-50">
            <div class="px-2 py-2 text-center text-sm w-full border border-white border-dashed rounded-xl">
                <x-alert-message class="me-3" on="alert" />

                <label class="mr-2 font-bold">{{ __('Paper Size: ') }}</label>

                <label class="inline mr-4" for="a6">
                    <input type="radio" id="a6" value="105mm" wire:model.live="paper_size" />
                    {{ __('A6 Paper') }}
                </label>
                <label class="inline mr-4" for="eighty">
                    <input type="radio" id="eighty" value="80mm" wire:model.live="paper_size" />
                    {{ __('80mm Thermal Paper') }}
                </label>

                @if ($old_paper_size != $paper_size)
                    <button class="uppercase px-2 rounded-full shadow font-bold bg-green-300"
                        wire:click="savePageSettings">
                        <span class="x-small-text text-black">{{ __('Save Page Settings') }}</span>
                    </button>
                @endif

                @if ($paper_size == '80mm')
                    <div class="block mt-4">
                        {{-- <div>
                            <span class="font-bold">{{ __('Document Height: ') }}</span>
                            <span>{{ number_format($divHeight, 0) }}</span>
                            <span>{{ __('mm') }}</span>
                        </div>
                        <div>
                            <span class="font-bold">{{ __('Estimated Thermal Paper Length: ') }}</span>
                            <span>{{ number_format($thermal_paper['length'], 0) }}</span>
                            <span>{{ __('mm') }}</span>
                            <span
                                class="text-xs uppercase bg-blue-400 px-2 py-0.5 font-bold cursor-pointer shadow hover:bg-blue-500 active:bg-blue-700 mx-1 rounded-full"
                                wire:click="resetThermal">
                                {{ __('Reset') }}
                            </span>
                        </div> --}}
                        <span class="block">
                            {!! __('<b>Reminder:</b> Please always check if the remaining thermal paper is enough for this print.') !!}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- @if ($divHeight > $thermal_paper['length'])
            <div class="mx-auto text-center mt-4">
                <span class="text-red-400 font-bold text-xs">
                    {{ __('Document height is greater than the thermal paper length! Please check the installed thermal paper and/or reset thermal length!') }}
                </span>
            </div>
        @else --}}
        {{-- @if ($divHeight > 0) --}}
        {{-- Print Button --}}
        <div class="mx-auto text-center mt-4">
            <x-button type="button" x-on:click="$wire.updatePrintCount()" onclick="window.print()" title="Print"
                wire:loading.attr="disabled">
                {{ __('Print') }}
            </x-button>
        </div>
        {{-- @endif --}}
        {{-- @endif --}}
    </div>

    @php
        if ($paper_size == '105mm') {
            $wxh = 'width: 105mm; height: 148mm;';
            $padding_y = 'py-1';
            $borders = 'border-2 border-black';
            $margin_bottom = 'mb-2';
        } elseif ($paper_size == '80mm') {
            $wxh = 'width: 80mm;';
            $padding_y = 'py-6';
            $borders = 'border-t-2 border-b-2 border-dashed border-black';
            $margin_bottom = 'mb-2';
        }
    @endphp

    <div x-ref="receiptBody" x-init="divHeightPixels = $refs.receiptBody.clientHeight;
    divTotalHeight = divTotalHeight + divHeightPixels;
    $wire.getPaperHeight(divTotalHeight.toFixed(0));">
        @foreach ($billings as $item)
            @php
                $billingQR = \App\Models\Billings::find($item['billing_id']);
            @endphp
            @if ($paper_size == '80mm')
                <div class="text-center py-4">
                    <span class="jetbrains x-small-text uppercase">{{ __('--- Start of Print ---') }}</span>
                </div>
            @endif
            <div id="billing_paper" class="mx-auto billing-print text-xs px-2 {{ $padding_y }} {{ $borders }}"
                style="{{ $wxh }}">

                {{-- @if ($paper_size == '80mm')
                    <div class="flex items-center justify-center">
                        <img src="{{ asset('storage/assets/logo-modified.png') }}" width="32" alt="">
                    </div>
                @endif --}}

                <div class="text-center font-bold">
                    <span class="mx-2 jetbrains">{{ $item['powas_name'] }}</span>
                </div>

                <div class="text-center italic powas-address {{ $margin_bottom }}">
                    <span class="jetbrains">{{ $item['powas_address'] }}</span>
                </div>

                <div class="text-center mb-3">
                    <hr style="border-style: solid;">
                    <span class="font-black jetbrains">{{ __('WATER BILLING') }}</span>
                    <hr style="border-style: solid;">
                </div>

                <div class="{{ $margin_bottom }}">
                    <div class="w-full">
                        <div>
                            <span class="jetbrains font-bold">{{ __('Account Number: ') }}</span>
                            <span class="jetbrains">{{ $item['account_number'] }}</span>
                        </div>
                        <div>
                            <span class="jetbrains font-bold">{{ __('Account Name: ') }}</span>
                            <span class="jetbrains">{{ $item['account_name'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="border border-black border-collapse border-double {{ $margin_bottom }}">
                    <div class="w-full grid grid-cols-2 border border-black">
                        <div class="text-center border-b border-r border-black">
                            <span class="jetbrains font-bold">{{ __('Bill Number') }}</span>
                        </div>
                        <div class="text-center border-b border-black">
                            <span class="jetbrains font-bold">{{ __('Billing Month') }}</span>
                        </div>
                        <div class="text-center py-2 border-r border-black">
                            <span class="jetbrains">{{ $item['bill_number'] }}</span>
                        </div>
                        <div class="text-center py-2">
                            <span class="jetbrains">{{ $item['billing_month'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="border border-black border-collapse border-double {{ $margin_bottom }}">
                    <div class="w-full grid grid-cols-3 border border-black">
                        <div class="col-span-2 grid grid-cols-2">
                            <div class="col-span-2 text-center border-r border-b border-black">
                                <span class="jetbrains font-bold">{{ __('Reading') }}</span>
                            </div>
                            <div class="text-center border-r border-b border-black">
                                <span class="jetbrains font-bold">{{ __('Present') }}</span>
                            </div>
                            <div class="text-center border-r border-b border-black">
                                <span class="jetbrains font-bold">{{ __('Previous') }}</span>
                            </div>
                            <div class="text-center py-2 w-full border-r border-black">
                                <span class="jetbrains">{{ $item['present_reading'] }}</span>
                            </div>
                            <div class="text-center py-2 w-full border-r border-black">
                                <span class="jetbrains">{{ $item['previous_reading'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-center border-b border-black">
                                <span class="jetbrains font-bold">{{ __('Usage') }}</span>
                            </div>
                            <div class="flex justify-center text-center items-center py-4">
                                <span class="jetbrains">{{ $item['cubic_meter_used'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-black border-collapse border-double">
                    <div class="w-full grid grid-cols-2 border border-black">
                        <div class="text-center border-b border-r border-black">
                            <span class="jetbrains font-bold">{{ __('Billing Period') }}</span>
                        </div>
                        <div class="text-center border-b border-black">
                            <span class="jetbrains font-bold">{{ __('Due Date') }}</span>
                        </div>
                        <div class="text-center py-2 border-r border-black">
                            <span class="jetbrains">{!! $item['billing_period'] !!}</span>
                        </div>
                        <div class="text-center py-4">
                            <span class="jetbrains">{{ $item['due_date'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="w-full {{ $margin_bottom }}">
                    <span class="jetbrains font-bold x-small-text">
                        {{ __('Notice: Disconnection will be on ') . $item['disconnection_date'] }}
                    </span>
                </div>

                @php
                    if ($paper_size == '80mm') {
                        $text_size = 'text-lg';
                    } else {
                        $text_size = 'text-sm';
                    }
                @endphp

                <div class="w-full {{ $margin_bottom }} grid grid-cols-2">
                    <div class="w-full border-2 border-white">
                        <span class="jetbrains font-bold {{ $text_size }}">
                            {{ __('Amount Due: ') }}
                        </span>
                    </div>
                    <div class="text-right w-full border-2 border-black">
                        <span class="jetbrains font-bold {{ $text_size }} pr-1">
                            {{ $item['total_amount_due'] }}
                        </span>
                    </div>
                </div>

                @if ($paper_size == '80mm')
                    <div class="w-full {{ $margin_bottom }} grid grid-cols-3 border-2 border-black">
                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                {{ __('Previous Unpaid Bill: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['previous_balance'] }}
                            </span>
                        </div>
                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                @if ($item['is_minimum'] == true)
                                    {{ __('Current Bill [MINIMUM]: ') }}
                                @else
                                    {{ __('Current Bill: ') }}
                                @endif
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['billing_amount'] }}
                            </span>
                        </div>
                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                {{ __('Monthly Due: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['members_micro_savings'] }}
                            </span>
                        </div>
                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                {{ __('Penalties: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['penalty'] }}
                            </span>
                        </div>

                        @if ($item['reconnection_fee'] > 0)
                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    {{ __('Reconnection Fee: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['reconnection_fee'] }}
                                </span>
                            </div>
                        @endif

                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                {{ __('Less - Discounts: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['discount_amount'] }}
                            </span>
                        </div>

                        <div class="w-full col-span-2 pl-1">
                            <span class="jetbrains">
                                {{ __('Less - Excess Payments: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1">
                                {{ $item['excess_payment'] }}
                            </span>
                        </div>

                        <div class="w-full col-span-2 text-right">
                            <span class="jetbrains">
                                {{ __('Total Amount Due: ') }}
                            </span>
                        </div>
                        <div class="text-right w-full">
                            <span class="jetbrains font-bold pr-1 italic">
                                {{ $item['total_amount_due'] }}
                            </span>
                        </div>
                    </div>

                    <div class=" w-full {{ $margin_bottom }} text-center">
                        <span class="block font-bold jetbrains">{{ __('PAALALA:') }}</span>
                        <span class="block jetbrains text-justify">
                            {!! __('Mangyaring pakibayaran ang ating bill bago o pagsapit ng <b class="jetbrains">') .
                                $item['due_date'] .
                                __('</b> upang maiwasan ang pagkakaroon ng penalty. Maraming salamat po!') !!}
                        </span>
                    </div>

                    <div class="flex justify-center w-full mb-2">
                        <span class="jetbrains font-bold">
                            {{ __('Reference Number') }}
                        </span>
                    </div>

                    <div class="flex justify-center w-full mb-2">
                        {{-- {{ QrCode::size(64)->backgroundColor(255, 255, 255)->generate($item['billing_id']) }} --}}
                        {!! $billingQR->getQRCode($item['billing_id']) !!}
                    </div>

                    <div class="flex justify-center w-full {{ $margin_bottom }} jetbrains font-bold">
                        {{ $item['billing_id'] }}
                    </div>
                    
                    {{-- <div class="my-2 py-2 border-2 border-dashed border-black">
                        <div class="text-center">
                            <span class="jetbrains text-base font-bold underline underline-offset-2">{{__('PAANYAYA')}}</span>
                        </div>
                        <div class="px-1 py-1">
                            <p class="text-justify jetbrains">
                                {{__('Kayo po ay malugod na inaanyayahan sa gaganaping')}} <span class="font-bold jetbrains uppercase underline underline-offset-1">{{__('Pangkalahatang Pagpupulong')}}</span> {{__('ng ating POWAS.')}}
                            </p>
                        </div>
                        <div class="px-1 py-1">
                            <div>
                                <span class="font-bold jetbrains">{{__('Petsa:')}}</span> <span class="jetbrains">{{__('May 30, 2025')}}</span>
                            </div>
                            <div>
                                <span class="font-bold jetbrains">{{__('Oras:')}}</span> <span class="jetbrains">{{__('2:00 ng hapon')}}</span>
                            </div>
                            <div>
                                <span class="font-bold jetbrains">{{__('Lugar:')}}</span> <span class="jetbrains">{{__('Antonio Rafael Residence')}}</span>
                            </div>
                        </div>
                        <div class="px-1 py-1">
                            <p class="text-justify jetbrains">
                                {{__('Inaasahan po ng pamunuan ang inyong pagdalo dahil mahalaga po ang pagpupulong na ito.')}}
                            </p>
                        </div>
                        <div class="px-1 py-1">
                            <p class="text-center jetbrains">
                                {{__('Maraming salamat po!')}}
                            </p>
                        </div>
                    </div> --}}
                @else
                    <div class="w-full grid grid-cols-3">
                        <div class="w-full col-span-2 grid grid-cols-3 border-2 border-black x-small-text">
                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    {{ __('Previous Unpaid Bill: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['previous_balance'] }}
                                </span>
                            </div>
                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    @if ($item['is_minimum'] == true)
                                        {{ __('Current Bill [MINIMUM]: ') }}
                                    @else
                                        {{ __('Current Bill: ') }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['billing_amount'] }}
                                </span>
                            </div>
                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    {{ __('Monthly Due: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['members_micro_savings'] }}
                                </span>
                            </div>

                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    {{ __('Penalties: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['penalty'] }}
                                </span>
                            </div>

                            @if ($item['reconnection_fee'] > 0)
                                <div class="w-full col-span-2 pl-1">
                                    <span class="jetbrains">
                                        {{ __('Reconnection Fee: ') }}
                                    </span>
                                </div>
                                <div class="text-right w-full">
                                    <span class="jetbrains font-bold pr-1">
                                        {{ $item['reconnection_fee'] }}
                                    </span>
                                </div>
                            @endif

                            <div class="w-full col-span-2 pl-1">
                                <span class="jetbrains">
                                    {{ __('Less - Discounts: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1">
                                    {{ $item['discount_amount'] }}
                                </span>
                            </div>
                            <div class="w-full col-span-2 text-right">
                                <span class="jetbrains">
                                    {{ __('Total Amount Due: ') }}
                                </span>
                            </div>
                            <div class="text-right w-full">
                                <span class="jetbrains font-bold pr-1 italic">
                                    {{ $item['total_amount_due'] }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-center w-full mb-2">
                                <span class="jetbrains font-bold x-small-text">
                                    {{ __('Reference Number') }}
                                </span>
                            </div>

                            <div class="flex justify-center w-full mb-2">
                                {{-- {{ QrCode::size(48)->backgroundColor(255, 255, 255)->generate($item['billing_id']) }} --}}
                                {!! $billingQR->getQRCode($item['billing_id']) !!}
                            </div>

                            <div class="flex justify-center w-full {{ $margin_bottom }} font-bold">
                                <span class="jetbrains x-small-text">
                                    {{ $item['billing_id'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                <div>
                    @if ($paper_size == '80mm')
                        <div class="text-center">
                            <span class="jetbrains x-small-text block">
                                {{ __('Bill Generated: ') . $item['timestamp'] }}
                            </span>
                            <span class="jetbrains x-small-text block">
                                {{ __('Print Count: ') . ($printCount[$item['billing_id']] + 1) }}
                            </span>
                        </div>
                    @else
                        <div class="w-full grid grid-cols-3">
                            <span class="jetbrains x-small-text col-span-2">
                                {{ __('Bill Generated: ') . $item['timestamp'] }}
                            </span>
                            <span class="jetbrains x-small-text text-right">
                                {{ __('Print Count: ') . ($printCount[$item['billing_id']] + 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="text-center mt-1">
                    <span class="jetbrains x-small-text">&copy; {{ date('Y') . ' ' . config('app.name') }}</span>
                </div>
            </div>
        @endforeach

        @if ($paper_size == '80mm')
            <div class="text-center py-4">
                <span class="jetbrains x-small-text uppercase">{{ __('--- End of Print ---') }}</span>
            </div>
        @endif
    </div>
</div>
