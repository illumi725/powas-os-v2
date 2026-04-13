<div class="w-full">
    <div class="no-print sticky top-0 bg-black text-white py-4">
        @if ($isAdvancePrinting == null)
        {{-- Paper Size --}}
        <div class="mx-auto w-96 z-50  mb-4">
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

                    @if ($old_paper_size != $receipt_paper_size)
                    <button class="uppercase px-2 rounded-full shadow font-bold bg-green-300"
                        wire:click="savePageSettings">
                        <span class="x-small-text text-black">{{ __('Save Page Settings') }}</span>
                    </button>
                    @endif
                </div>
                @if ($receipt_paper_size == '80mm')
                <div class="mt-2">
                    <label class="mr-2 font-bold">{{ __('Print Mode: ') }}</label>
                    <x-combobox wire:model.live="printDuplicate">
                        @slot('options')
                        <option value="both">{{ __('Both') }}</option>
                        <option value="original">{{ __('Original Copy') }}</option>
                        <option value="consumer">{{ __('Consumer\'s Copy') }}</option>
                        @endslot
                    </x-combobox>
                    {{-- <div class="text-left">
                            <label class="block text-xs mr-2">{{ __('Print Modes ') }}</label>
                    <ul>
                        <li>{{ __('Both: Best for advanced receipt printing') }}</li>
                    </ul>
                </div>
                <label>
                    <input type="checkbox" id="printDuplicate" wire:model.live="printDuplicate">
                    {{ __('Print Duplicate') }}
                </label> --}}
            </div>
            <div class="block mt-4">
                <span class="block">
                    {!! __('<b>Reminder:</b> Please always check if the remaining thermal paper is enough for this print.') !!}
                </span>
            </div>
            @else
            <div class="block mt-4">
                <span class="block">
                    {!! __('<b>Note:</b> A6 Paper Size is best for advanced receipt printing.') !!}
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="mx-auto text-center">
        <x-button type="button" {{-- x-on:click="$wire.updatePrintCount()" --}} onclick="window.print()" wire:loading.attr="disabled"
            title="Print">
            {{ __('Print') }}
        </x-button>
    </div>
</div>

@php
if ($receipt_paper_size == '105mm') {
$wxh = 'width: 105mm; height: 74mm;'; // orignal height = 148mm
// $wxh = 'width: 105mm';
$padding_y = 'py-1';
$borders = 'border-2 border-black';
$margin_bottom = 'mb-1';
} elseif ($receipt_paper_size == '80mm') {
$wxh = 'width: 80mm;';
$padding_y = 'py-6';
$borders = 'border-t-2 border-b-2 border-dashed border-black';
$margin_bottom = 'mb-2';
}

if ($printDuplicate == 'both') {
$duplicates = ['Original Copy', 'Consumer\'s Copy'];
} elseif ($printDuplicate == 'consumer') {
$duplicates = ['Consumer\'s Copy'];
} elseif ($printDuplicate == 'original') {
$duplicates = ['Original Copy'];
}
@endphp

{{-- @dd($chartOfAccounts) --}}

<div>
    @if ($receipt_paper_size == '80mm')
    {{-- For 80mm Thermal Paper --}}
    @foreach ($billingIDs as $billingID)
    <div class="text-center py-4">
        <span class="jetbrains x-small-text uppercase">
            {{ __('--- Start of Print ---') }}
        </span>
    </div>
    @foreach ($duplicates as $duplicate)
    <div id="receipt_paper"
        class="mx-auto billing-print text-xs px-2 {{ $padding_y }} {{ $borders }}"
        style="{{ $wxh }}">

        {{-- Header --}}
        <div class="text-center font-bold">
            <span class="mx-2 jetbrains">
                {{ $powas->barangay . ' POWAS ' . $powas->phase }}
            </span>
        </div>

        <div class="text-center italic powas-address {{ $margin_bottom }}">
            <span
                class="jetbrains">{{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
            </span>
        </div>

        <div class="text-center mb-3">
            <hr style="border-style: solid;">
            <span class="font-black jetbrains">{{ __('OFFICIAL RECEIPT') }}</span>
            <hr style="border-style: solid;">
            @if($powasSettings->current_serial_number)
            <span class="jetbrains x-small-text">OR No.: <b>{{ $powasSettings->current_serial_number }}</b></span>
            @endif
        </div>

        {{-- Content --}}
        <div class="{{ $margin_bottom }}">
            <div class="w-full">
                <div>
                    <span class="jetbrains font-bold">{{ __('Account Number: ') }}</span>
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['member_account_number'] }}
                    </span>
                </div>
                <div>
                    <span class="jetbrains font-bold">{{ __('Account Name: ') }}</span>
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['member_account_name'] }}
                    </span>
                </div>
                <div>
                    <span class="jetbrains font-bold">{{ __('Reference Number: ') }}</span>
                    <span class="jetbrains">{{ $billingID }}</span>
                </div>
                <div>
                    <span class="jetbrains font-bold">{{ __('Billing Month: ') }}</span>
                    <span class="jetbrains">{{ $transactionSets[$billingID]['billing_month'] }}</span>
                </div>
                <div>
                    <span class="jetbrains font-bold">{{ __('Transact By: ') }}</span>
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['transact_by'] }}
                    </span>
                </div>
                <div>
                    <span class="jetbrains font-bold">{{ __('Date: ') }}</span>
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['transact_date'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="w-full grid grid-cols-3 mt-4">
            <div class="col-span-2 jetbrains uppercase text-center font-bold border-y border-black">
                {{ __('Particulars') }}
            </div>
            <div class="jetbrains uppercase text-center font-bold border-y border-black">
                {{ __('Amount') }}
            </div>

            {{-- Amount Due --}}
            <div class="jetbrains col-span-2">
                {{ $transactionSets[$billingID]['amount_due']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['amount_due']['amount'] }}
            </div>

            {{-- Monthly Due --}}
            @if ($hasMonthlyDue == true)
            @if ($transactionSets[$billingID]['monthly_due']['amount'] != 0 && $duplicate != 'Original Copy')
            <div class="jetbrains col-span-2">
                {{ $transactionSets[$billingID]['monthly_due']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['monthly_due']['amount'] }}
            </div>
            @endif
            @endif

            {{-- Penalties --}}
            @if (count($transactionSets[$billingID]['penalties']) != 0)
            <div class="jetbrains col-span-2">
                {{ $transactionSets[$billingID]['penalties']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['penalties']['amount'] }}
            </div>
            @endif

            {{-- Reconnection Fee --}}
            @if (count($transactionSets[$billingID]['reconnection_fee']) != 0)
            <div class="jetbrains col-span-2">
                {{ $transactionSets[$billingID]['reconnection_fee']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['reconnection_fee']['amount'] }}
            </div>
            @endif

            {{-- Discounts --}}
            @if (count($transactionSets[$billingID]['discounts']) != 0)
            <div class="jetbrains col-span-2">
                {{ $transactionSets[$billingID]['discounts']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['discounts']['amount'] }}
            </div>
            @endif

            {{-- Excess Payment --}}
            @if (count($transactionSets[$billingID]['debited_excess_payment']) != 0)
            <div class="jetbrains col-span-2">
                {{ 'LESS: ' . $transactionSets[$billingID]['debited_excess_payment']['alias'] }}
            </div>

            <div class="jetbrains text-right">
                {{ $transactionSets[$billingID]['debited_excess_payment']['amount'] }}
            </div>
            @endif

            {{-- Total Due --}}
            <div class="jetbrains col-span-2 border-t border-black uppercase font-bold text-center">
                {{ __('Total Amount Due ') }}
            </div>

            <div class="jetbrains text-right border-t border-black font-bold">
                @if (count($transactionSets[$billingID]['monthly_due']) != 0 && $duplicate != 'Original Copy')
                {{ number_format($transactionSets[$billingID]['total_amount_due'], 2) }}
                @else
                {{ number_format($transactionSets[$billingID]['total_amount_due'] - $transactionSets[$billingID]['monthly_due']['amount'], 2) }}
                @endif
            </div>

            {{-- Amount Paid --}}
            <div class="col-span-3 grid grid-cols-3 border-2 border-black border-dotted my-3 px-1">
                <div class="jetbrains col-span-2 uppercase font-bold text-base">
                    {{ __('Amount Paid:  ') }}
                </div>

                <div class="jetbrains text-right font-bold text-base">
                    @if (count($transactionSets[$billingID]['monthly_due']) != 0 && $duplicate != 'Original Copy')
                    {{ number_format($transactionSets[$billingID]['total_amount_due'], 2) }}
                    @else
                    {{ number_format($transactionSets[$billingID]['total_amount_due'] - $transactionSets[$billingID]['monthly_due']['amount'], 2) }}
                    @endif
                </div>
            </div>
            {{-- Excess Payment --}}
            @if (count($transactionSets[$billingID]['excess_payment']) != 0)
            <div class="jetbrains col-span-2 uppercase mt-1">
                {{ '*' . $transactionSets[$billingID]['excess_payment']['alias'] }}
            </div>

            <div class="jetbrains text-right mt-1">
                {{ $transactionSets[$billingID]['excess_payment']['amount'] }}
            </div>

            <div class="jetbrains col-span-3 x-small-text italic mb-4">
                {{ __('*To be debited on the next billing') }}
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="text-center italic mb-4 jetbrains">
            {!! __('This receipt is a valid proof of your payment for billing reference number <b class="jetbrains">') .
                $billingID .
                '</b>.' !!}
        </div>
        <div class="mb-4 text-center text-base font-black jetbrains">
            {{ __('Thank you!') }}
        </div>
        <div class="text-center mt-1 font-bold">
            <span class="jetbrains x-small-text">
                {{ $duplicate }}
            </span>
        </div>
        <div class="text-center">
            <span class="jetbrains x-small-text">&copy;
                {{ date('Y') . ' ' . config('app.name') }}</span>
        </div>
        {{-- BIR ATP Footer --}}
        @if ($powasSettings->printer_name)
        <div class="border-t border-black mt-2 pt-1" style="font-size: 7px;">
            <div class="text-center jetbrains">Printed by: {{ $powasSettings->printer_name }}</div>
            @if($powasSettings->printer_address)<div class="text-center jetbrains">{{ $powasSettings->printer_address }}</div>@endif
            <div class="text-center jetbrains">
                @if($powasSettings->printer_tin)TIN: {{ $powasSettings->printer_tin }} @endif
                @if($powasSettings->printer_accreditation_no)/ Accreditation No.: {{ $powasSettings->printer_accreditation_no }}@endif
            </div>
            <div class="text-center jetbrains">
                @if($powasSettings->atp_number)ATP No.: {{ $powasSettings->atp_number }}@endif
                @if($powasSettings->atp_valid_until) / Valid Until: {{ \Carbon\Carbon::parse($powasSettings->atp_valid_until)->format('m/d/Y') }}@endif
            </div>
            @if($powasSettings->serial_number_start && $powasSettings->serial_number_end)
            <div class="text-center jetbrains">Series: {{ $powasSettings->serial_number_start }}&ndash;{{ $powasSettings->serial_number_end }}</div>
            @endif
        </div>
        @endif
    </div>
    @endforeach
    <div class="text-center py-4">
        <span class="jetbrains x-small-text uppercase">
            {{ __('--- End of Print ---') }}
        </span>
    </div>
    @endforeach
    @elseif ($receipt_paper_size == '105mm')
    @php
    $duplicates = ['Consumer\'s Copy']; // $duplicates = ['Original Copy', 'Consumer\'s Copy'];
    @endphp

    @foreach ($billingIDs as $billingID)
    <div id="receipt_paper" class="mx-auto billing-print text-xs px-4 {{ $padding_y }}"
        style="{{ $wxh }}">

        @foreach ($duplicates as $duplicate)
        <div class="{{ $borders }} px-2 py-2 x-small-text">
            {{-- Header --}}
            <div class="text-center font-bold">
                <span class="mx-2 jetbrains">
                    {{ $powas->barangay . ' POWAS ' . $powas->phase }}
                </span>
            </div>

            <div class="text-center italic powas-address {{ $margin_bottom }}">
                <span class="jetbrains" style="font-size: 8px;">
                    {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                </span>
            </div>

            <div class="text-center">
                <span class="font-black jetbrains">{{ __('OFFICIAL RECEIPT') }}</span>
            </div>
            <div class="grid grid-cols-2">
                <div class="" style="font-size: 8px;">
                    <span class="jetbrains font-bold">
                        {{ __('Ref. No.:') }}
                    </span>
                    <span class="jetbrains italic">{{ $billingID }}</span>
                </div>
                <div class="text-right" style="font-size: 8px;">
                    <span class="jetbrains font-bold">
                        {{ __('Due Date:') }}
                    </span>
                    <span class="jetbrains italic">
                        {{ $transactionSets[$billingID]['due_date'] }}
                    </span>
                </div>
            </div>

            <div class="border border-black border-collapse grid grid-cols-2">
                {{-- Account Name/Account Number --}}
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Account Name') }}
                    </span>
                </div>
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Account Number') }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['member_account_name'] }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['member_account_number'] }}
                    </span>
                </div>

                {{-- Billing Month/Cubic Meter Used --}}
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Billing Month') }}
                    </span>
                </div>
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Cubic Meter Used') }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['billing_month'] }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{ $transactionSets[$billingID]['cubic_meter_used'] }}
                    </span>
                </div>

                {{-- Total Amount Due/Total Amount Received --}}
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Total Amount Due') }}
                    </span>
                </div>
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Total Amount Received') }}
                    </span>
                </div>
                <div
                    class="grid grid-cols-2 font-bold justify-center text-center border border-black border-collapse">
                    <div>
                        <span class="jetbrains" style="font-size: 14px;">
                            @if (count($transactionSets[$billingID]['monthly_due']) != 0 && $duplicate != 'Original Copy')
                            {{ number_format($transactionSets[$billingID]['total_amount_due'], 2) }}
                            @else
                            {{ number_format($transactionSets[$billingID]['total_amount_due'] - $transactionSets[$billingID]['monthly_due']['amount'], 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="text-right pr-1">
                        <span class="jetbrains" style="font-size: 8px;">
                            {{ __('Penalty: ______') }}
                        </span>
                    </div>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{-- {{ $transactionSets[$billingID]['cubic_meter_used'] }} --}}
                    </span>
                </div>

                {{-- Payment Data/Received By --}}
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Payment Date') }}
                    </span>
                </div>
                <div class="border border-black border-collapse text-center bg-gray-200">
                    <span class="jetbrains font-bold">
                        {{ __('Received By') }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains text-xs">
                        {{ \Carbon\Carbon::now()->format('__/__/Y') }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-center text-center border border-black border-collapse">
                    <span class="jetbrains">
                        {{-- {{ $transactionSets[$billingID]['cubic_meter_used'] }} --}}
                    </span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="grid grid-cols-2" style="font-size: 5px;">
                <div class="text-left">
                    <span class="jetbrains x-small-text">
                        &copy;{{ date('Y') . ' ' . config('app.name') }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="jetbrains x-small-text italic">
                        {{ $duplicate }}
                    </span>
                </div>
            </div>
            {{-- BIR ATP Footer --}}
            @if ($powasSettings->printer_name)
            <div class="border-t border-black mt-1 pt-1 text-center" style="font-size: 5px;">
                <div class="jetbrains">{{ $powasSettings->printer_name }}</div>
                @if($powasSettings->printer_address)<div class="jetbrains">{{ $powasSettings->printer_address }}</div>@endif
                <div class="jetbrains">
                    @if($powasSettings->printer_tin)TIN: {{ $powasSettings->printer_tin }}@endif
                    @if($powasSettings->printer_accreditation_no) / Accr. No.: {{ $powasSettings->printer_accreditation_no }}@endif
                </div>
                <div class="jetbrains">
                    @if($powasSettings->atp_number)ATP: {{ $powasSettings->atp_number }}@endif
                    @if($powasSettings->atp_valid_until) / Until: {{ \Carbon\Carbon::parse($powasSettings->atp_valid_until)->format('m/d/Y') }}@endif
                </div>
                @if($powasSettings->serial_number_start && $powasSettings->serial_number_end)
                <div class="jetbrains">Series: {{ $powasSettings->serial_number_start }}&ndash;{{ $powasSettings->serial_number_end }}</div>
                @endif
            </div>
            @endif
        </div>

        @if ($duplicate == 'Original Copy')
        <div class="py-4">
            <hr>
        </div>
        @endif
        @endforeach
    </div>
    @endforeach
    @endif
</div>
</div>