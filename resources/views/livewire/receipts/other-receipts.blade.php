<div class="w-full">
    {{-- Paper Size Selector --}}
    <div class="no-print sticky top-0 z-50 bg-black text-white py-4 mb-4">
        <div class="mx-auto w-96">
            <div class="px-2 py-2 text-center text-sm w-full border border-white border-dashed rounded-xl">
                <x-alert-message class="me-3" on="alert" />
                <div>
                    <label class="mr-2 font-bold">{{ __('Paper Size: ') }}</label>

                    <label class="inline mr-4" for="a6">
                        <input type="radio" id="a6" value="105mm" wire:model.live="receipt_paper_size" />
                        {{ __('A6 Paper') }}
                    </label>
                    <label class="inline" for="eighty">
                        <input type="radio" id="eighty" value="80mm" wire:model.live="receipt_paper_size" />
                        {{ __('80mm Thermal Paper') }}
                    </label>
                </div>
            </div>
            
             {{-- Print Button --}}
            <div class="mt-4 mx-auto text-center">
                <x-button type="button" wire:click="updatePrintLog" onclick="window.print();" wire:loading.attr="disabled"
                    title="Print">Print</x-button>
            </div>
        </div>
    </div>

    {{-- @dd($receiptNumber) --}}

    @php
        if ($receipt_paper_size == '105mm') {
            $wxh = 'width: 105mm; height: 74mm;';
            $padding_y = 'py-1';
            $borders = 'border-2 border-black';
            $margin_bottom = 'mb-1';
        } elseif ($receipt_paper_size == '80mm') {
            $wxh = 'width: 80mm;';
            $padding_y = 'py-6';
            $borders = 'border-t-2 border-b-2 border-dashed border-black';
            $margin_bottom = 'mb-4';
        }
    @endphp

    <div class="text-center py-4">
        <span class="jetbrains x-small-text uppercase">{{ __('--- Start of Print ---') }}</span>
    </div>
    {{-- @foreach ($trxnList as $trxn) --}}
    <div class="mx-auto receipt px-2 text-xs {{ $padding_y }} {{ $borders }}" style="{{ $wxh }}">
        {{-- <div class="flex items-center justify-center">
            <img src="{{ asset('storage/assets/logo-modified.png') }}" width="32" alt="">
        </div> --}}
        <div class="text-center font-bold">
            <span class="mx-2 jetbrains">{{ $trxnList[0]['powas_name'] }}</span>
        </div>

        <div class="text-center italic powas-address mb-4">
            <span class="jetbrains">{{ $trxnList[0]['powas_address'] }}</span>
        </div>

        <div class="text-center">
            <hr style="border-style: solid;">
            <span class="font-black jetbrains">{{ __('RECEIPT') }}</span>
            <hr style="border-style: solid;">
        </div>

        <div class="mt-4 jetbrains">
            {!! __('<b class="jetbrains">Receipt No.:</b> ') . $receiptNumber !!}
        </div>
        <div class="jetbrains">
            {!! __('<b class="jetbrains">Transacted by:</b> ') . $trxnList[0]['transact_by'] !!}
        </div>
        <div class="jetbrains">
            {!! __('<b class="jetbrains">Date:</b> ' . $trxnList[0]['transact_date']) !!}
        </div>
        <div class="mt-2 mb-4 jetbrains">
            {!! __('<b class="jetbrains">Received from:</b> ') . $trxnList[0]['received_from'] !!}
        </div>
        <div class="grid grid-cols-3 mb-4">
            <div class="col-span-2 text-center custom-border font-bold jetbrains">{{ __('Particulars') }}</div>
            <div class="text-center custom-border font-bold jetbrains">{{ __('Amount') }}</div>
            @php
                $totalAmount = 0;
                $particulars = '';
            @endphp

            @if ($thisReceipt->description == null)
                @php
                    // Check if this is an Application/Membership Fee receipt
                    $hasApplicationFee = false;
                    $hasMembershipFee = false;
                    
                    foreach ($trxnList as $trxn) {
                        if (str_contains($trxn['description'], 'Application Fee')) {
                            $hasApplicationFee = true;
                        }
                        if (str_contains($trxn['description'], 'Membership Fee')) {
                            $hasMembershipFee = true;
                        }
                    }
                @endphp
                
                @if ($hasApplicationFee && $hasMembershipFee)
                    {{-- Show combined Application and Membership Fee --}}
                    <div class="col-span-2 jetbrains">Application and Membership Fee</div>
                    <div class="text-right jetbrains">{{ number_format(array_sum(array_column($trxnList, 'amount')), 2) }}</div>
                    @php
                        $totalAmount = array_sum(array_column($trxnList, 'amount'));
                        $particulars = 'Application and Membership Fee';
                    @endphp
                @elseif ($hasApplicationFee)
                    {{-- Show Application Fee only --}}
                    <div class="col-span-2 jetbrains">Application Fee</div>
                    <div class="text-right jetbrains">{{ number_format($trxnList[0]['amount'], 2) }}</div>
                    @php
                        $totalAmount = $trxnList[0]['amount'];
                        $particulars = 'Application Fee';
                    @endphp
                @elseif ($hasMembershipFee)
                    {{-- Show Membership Fee only --}}
                    <div class="col-span-2 jetbrains">Membership Fee</div>
                    <div class="text-right jetbrains">{{ number_format($trxnList[0]['amount'], 2) }}</div>
                    @php
                        $totalAmount = $trxnList[0]['amount'];
                        $particulars = 'Membership Fee';
                    @endphp
                @else
                    {{-- Show regular account aliases --}}
                    @foreach ($trxnList as $trxn)
                        <div class="col-span-2 jetbrains">{{ $trxn['alias'] }}</div>
                        <div class="text-right jetbrains">{{ number_format($trxn['amount'], 2) }}</div>
                        @php
                            $totalAmount = $totalAmount + $trxn['amount'];
                            $particulars = $particulars . $trxn['alias'] . '/';
                        @endphp
                    @endforeach
                @endif
            @else
                @foreach ($trxnList as $trxn)
                    <div class="col-span-2 jetbrains">{{ $thisReceipt->description }}</div>
                    <div class="text-right jetbrains">{{ number_format($trxn['amount'], 2) }}</div>
                    @php
                        $totalAmount = $totalAmount + $trxn['amount'];
                        $particulars = $particulars . $trxn['alias'] . '/';
                    @endphp
                @endforeach
            @endif

            <div class="col-span-3">
                <hr style="border-style: solid;">
            </div>
            <div class="col-span-2 font-bold jetbrains">{{ __('Total') }}</div>
            <div class="text-right font-bold jetbrains">{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="italic mb-4 jetbrains text-justify">
            {!! __('This receipt is a valid proof of your payment for <b class="jetbrains">') .
                rtrim($particulars, '/') .
                '</b> to <b class="jetbrains">' .
                $trxnList[0]['powas_name'] .
                '</b>.' !!}
        </div>
        <div class="mb-4 text-center text-base font-black jetbrains">
            {{ __('Thank you!') }}
        </div>
        <div class="text-center jetbrains">
            &copy; {{ date('Y') . ' ' . config('app.name') }}
        </div>
    </div>
    {{-- @endforeach --}}

    <div class="text-center py-4">
        <span class="jetbrains x-small-text uppercase">{{ __('--- End of Print ---') }}</span>
    </div>

    {{-- Print Button --}}
    <div class="no-print mt-5 mx-auto text-center">
        <x-button type="button" wire:click="updatePrintLog" onclick="window.print();" wire:loading.attr="disabled"
            title="Print">Print</x-button>
    </div>
</div>
