<style>
    /* Default normal font size */
    .timesnewroman {
        font-size: clamp(0.65rem, 2vw, 0.875rem) !important; /* Scales down only when needed */
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    
    /* Account name columns with width constraint to trigger ellipsis/scaling */
    .pl-10, .pl-5 {
        max-width: 250px !important; /* Constrains long names, normal names unaffected */
    }
    
    /* Specific override for very long account names (50+ chars) */
    .pl-10 .timesnewroman {
        font-size: clamp(0.6rem, 1.8vw, 0.75rem) !important;
    }
    
    @media print {
        body, html {
            margin: 0 !important;
            padding: 0 !important;
        }
        .print-container {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            background: white !important;
        }
        .to-print {
            width: 8.5in !important;
            height: auto !important;
            min-height: 11in !important;
        }
        .no-print {
            display: none !important;
        }
        .page-break {
            page-break-after: always !important;
        }
        /* Force page break before Statement of Comprehensive Income */
        .statement-break {
            page-break-before: always !important;
        }
        /* Ensure checking value is visible and stays with content */
        .checking-row {
            display: grid !important;
            visibility: visible !important;
            page-break-inside: avoid !important;
            page-break-before: avoid !important;
        }
        /* Print-specific sizing */
        .timesnewroman {
            font-size: 0.75rem !important; /* Fixed smaller size in print */
            line-height: 1.2 !important;
            white-space: nowrap !important;
        }
        .pl-10 .timesnewroman {
            font-size: 0.65rem !important; /* Even smaller for long indented names in print */
        }
        .pl-10, .pl-5 {
            max-width: 250px !important;
        }
    }
</style>
<div class="w-full bg-gray-100 min-h-screen py-8 flex flex-col items-center gap-8 print-container">
    {{-- Headings --}}
    <div class="no-print sticky top-0 py-6 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-center">
                <button 
                    type="button" 
                    onclick="window.print()" 
                    class="inline-flex items-center gap-3 px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-110 transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-400 focus:ring-offset-2 focus:ring-offset-gray-900 animate-pulse hover:animate-none"
                    wire:loading.attr="disabled"
                >
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg> -->
                    <span>{{ __('🖨️ PRINT') }}</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mb-6 no-print pt-4">
        <div class="flex items-center justify-center">
            <span class="font-semibold text-xl leading-tight">
                {{ __('Financial Statement') }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:flex md:items-center md:justify-center mt-0 pt-0">
            <div class="flex items-center justify-center">
                <span class="font-semibold text-xl leading-tight mr-2">
                    {{ __('for the Month of ') }}
                </span>
            </div>
            <div class="flex items-center justify-center">
                <x-combobox class="block w-auto md:w-auto md:inline" wire:model.live="selectedMonthYear"
                    wire:click="fetchData">
                    @slot('options')
                        @php
                            $limitter = 0;
                        @endphp
                        @foreach ($monthYear as $option)
                            @php
                                $limitter++;
                            @endphp
                            @if ($limitter <= 12)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endif
                        @endforeach
                    @endslot
                </x-combobox>
            </div>
        </div>
    </div>

    <hr class="no-print mb-4">

    {{-- Bond Paper --}}
    <div class="to-print w-full flex justify-center">
        <div id="" class="bg-white max-w-5xl mx-auto shadow-2xl p-8 w-full">
            <div class="grid grid-cols-7">
                <div class="col-span-7 text-center">
                    <span class="font-bold text-lg timesnewroman">
                        {{ __('POTABLE WATER SYSTEM (POWAS)') }}
                    </span>
                </div>
                <div class="col-span-7 text-center">
                    <span class="font-bold timesnewroman">
                        {{ $powas->barangay . ' POWAS ' . $powas->phase }}
                    </span>
                </div>
                <div class="col-span-7 text-center">
                    <span class="text-xs italic timesnewroman">
                        {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                    </span>
                </div>
                <div class="col-span-7 w-full py-2">
                    <div class="text-center">
                        <span class="font-bold timesnewroman">
                            {{ __('Statement of Financial Position') }}
                        </span>
                    </div>
                    <div class="text-center">
                        <span class="font-bold timesnewroman">
                            {{ $selectedMonthYear }}
                        </span>
                    </div>
                </div>

                <div class="col-span-3">

                </div>

                <div class="col-span-4 grid grid-cols-4 text-sm font-bold border-y border-gray-800">
                    <div class="bg-gray-300 text-center border-l border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Previous Balance') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-l border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Debit') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Credit') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Current Balance') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Assets --}}
            <div>
                {{-- Assets 1 --}}
                <div class="text-sm">
                    <div class="font-bold">
                        <span class="timesnewroman">
                            {{ __('ASSETS') }}
                        </span>
                    </div>

                    @php
                        $totalPreviousBalanceAsset1 = 0;
                        $totalDebitAsset1 = 0;
                        $totalCreditAsset1 = 0;
                        $totalCurrentBalanceAsset1 = 0;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if (
                            $value->account_number == '101' ||
                                $value->account_number == '102' ||
                                $value->account_number == '103' ||
                                $value->account_number == '104')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                    <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                            @php
                                                $totalPreviousBalanceAsset1 =
                                                    $totalPreviousBalanceAsset1 +
                                                    $newBeginningBalances[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div
                                        class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            @if ($debits[$value->account_number] > 0)
                                                {{ number_format($debits[$value->account_number], 2) }}
                                            @endif
                                            @php
                                                $totalDebitAsset1 = $totalDebitAsset1 + $debits[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div
                                        class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            @if ($credits[$value->account_number] > 0)
                                                {{ number_format($credits[$value->account_number], 2) }}
                                            @endif
                                            @php
                                                $totalCreditAsset1 =
                                                    $totalCreditAsset1 + $credits[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                        @php
                                            $currentBalance =
                                                $newBeginningBalances[$value->account_number] +
                                                $debits[$value->account_number] -
                                                $credits[$value->account_number];
                                        @endphp
                                        <span class="timesnewroman">
                                            {{ number_format($currentBalance, 2) }}

                                            @php
                                                $totalCurrentBalanceAsset1 =
                                                    $totalCurrentBalanceAsset1 + $currentBalance;
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <div class="grid grid-cols-7 font-bold">
                        <div class="pl-5 col-span-3 py-1">
                            <span class="timesnewroman">
                                {{ __('Sub-Total of Cash on Hand and Cash in Bank') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceAsset1, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitAsset1, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditAsset1, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceAsset1, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assets 2 --}}
                <div class="text-sm mt-1">
                    @php
                        $totalPreviousBalanceAsset2 = 0;
                        $totalDebitAsset2 = 0;
                        $totalCreditAsset2 = 0;
                        $totalCurrentBalanceAsset2 = 0;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_number == '105' || $value->account_number == '106' || $value->account_number == '107')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                    <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                            @php
                                                $totalPreviousBalanceAsset2 =
                                                    $totalPreviousBalanceAsset2 +
                                                    $newBeginningBalances[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div
                                        class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            @if ($debits[$value->account_number] > 0)
                                                {{ number_format($debits[$value->account_number], 2) }}
                                            @endif
                                            @php
                                                $totalDebitAsset2 = $totalDebitAsset2 + $debits[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div
                                        class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                        <span class="timesnewroman">
                                            @if ($credits[$value->account_number] > 0)
                                                {{ number_format($credits[$value->account_number], 2) }}
                                            @endif
                                            @php
                                                $totalCreditAsset2 =
                                                    $totalCreditAsset2 + $credits[$value->account_number];
                                            @endphp
                                        </span>
                                    </div>
                                    <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                        @php
                                            $currentBalance =
                                                $newBeginningBalances[$value->account_number] +
                                                $debits[$value->account_number] -
                                                $credits[$value->account_number];
                                        @endphp
                                        <span class="timesnewroman">
                                            {{ number_format($currentBalance, 2) }}

                                            @php
                                                $totalCurrentBalanceAsset2 =
                                                    $totalCurrentBalanceAsset2 + $currentBalance;
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <div class="grid grid-cols-7 font-bold">
                        <div class="pl-5 col-span-3 py-1">
                            <span class="timesnewroman">
                                {{ __('Sub-Total of Receivables') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceAsset2, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Assets --}}
                <div class="text-sm mt-1">
                    <div class="grid grid-cols-7 font-bold">
                        <div class="col-span-3 py-1 uppercase">
                            <span class="timesnewroman">
                                {{ __('Total Assets') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceAsset1 + $totalPreviousBalanceAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitAsset1 + $totalDebitAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditAsset1 + $totalCreditAsset2, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceAsset1 + $totalCurrentBalanceAsset2, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Liabilities --}}
            <div>
                <div class="text-sm">
                    <div class="font-bold">
                        <span class="timesnewroman">
                            {{ __('LIABILITIES') }}
                        </span>
                    </div>

                    @php
                        $totalPreviousBalanceLiabilities = 0;
                        $totalDebitLiabilities = 0;
                        $totalCreditLiabilities = 0;
                        $totalCurrentBalanceLiabilities = 0;
                    @endphp

                    @php
                        $lastAccountNumber = null;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'LIABILITY')
                            @php
                                $lastAccountNumber = $value->account_number;
                            @endphp
                        @endif
                    @endforeach

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'LIABILITY')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 {{ $value->account_number == $lastAccountNumber ? 'border-b border-gray-800 border-collapse' : '' }}">
                                    <div class="grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                        <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                                @php
                                                    $totalPreviousBalanceLiabilities =
                                                        $totalPreviousBalanceLiabilities +
                                                        $newBeginningBalances[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($debits[$value->account_number] > 0)
                                                    {{ number_format($debits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalDebitLiabilities =
                                                        $totalDebitLiabilities + $debits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($credits[$value->account_number] > 0)
                                                    {{ number_format($credits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalCreditLiabilities =
                                                        $totalCreditLiabilities + $credits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            @php
                                                $currentBalance =
                                                    $newBeginningBalances[$value->account_number] -
                                                    $debits[$value->account_number] +
                                                    $credits[$value->account_number];
                                            @endphp
                                            <span class="timesnewroman">
                                                {{ number_format($currentBalance, 2) }}

                                                @php
                                                    $totalCurrentBalanceLiabilities =
                                                        $totalCurrentBalanceLiabilities + $currentBalance;
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Total Liabilities --}}
                <div class="text-sm mt-1">
                    <div class="grid grid-cols-7 font-bold">
                        <div class="col-span-3 py-1 uppercase">
                            <span class="timesnewroman">
                                {{ __('Total Liabilities') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceLiabilities, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitLiabilities, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditLiabilities, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceLiabilities, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equity --}}
            <div>
                <div class="text-sm">
                    <div class="font-bold">
                        <span class="timesnewroman">
                            {{ __('EQUITY') }}
                        </span>
                    </div>

                    @php
                        $totalPreviousBalanceEquity = 0;
                        $totalDebitEquity = 0;
                        $totalCreditEquity = 0;
                        $totalCurrentBalanceEquity = 0;
                    @endphp

                    @php
                        $lastAccountNumber = null;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'EQUITY')
                            @php
                                $lastAccountNumber = $value->account_number;
                            @endphp
                        @endif
                    @endforeach

                    @php
                        $revExPreviousBalance = 0;
                        $revExDebit = 0;
                        $revExCredit = 0;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'REVENUE' || $value->account_type == 'EXPENSE')
                            @php
                                if ($value->account_type == 'REVENUE') {
                                    $revExPreviousBalance =
                                        $revExPreviousBalance + $newBeginningBalances[$value->account_number];
                                }

                                if ($value->account_type == 'EXPENSE') {
                                    $revExPreviousBalance =
                                        $revExPreviousBalance - $newBeginningBalances[$value->account_number];
                                }

                                $revExDebit = $revExDebit + $debits[$value->account_number];
                                $revExCredit = $revExCredit + $credits[$value->account_number];
                            @endphp
                        @endif
                    @endforeach

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'EQUITY')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 {{ $value->account_number == $lastAccountNumber ? 'border-b border-gray-800 border-collapse' : '' }}">
                                    <div class="grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                        <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($value->account_number == '302')
                                                    @php
                                                        $newBeginningBalances[
                                                            $value->account_number
                                                        ] = $revExPreviousBalance;
                                                    @endphp
                                                @endif
                                                {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                                @php
                                                    $totalPreviousBalanceEquity =
                                                        $totalPreviousBalanceEquity +
                                                        $newBeginningBalances[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($value->account_number == '302')
                                                    @php
                                                        $debits[$value->account_number] =
                                                            $debits[$value->account_number] + $revExDebit;
                                                    @endphp
                                                @endif
                                                @if ($debits[$value->account_number] > 0)
                                                    {{ number_format($debits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalDebitEquity =
                                                        $totalDebitEquity + $debits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($value->account_number == '302')
                                                    @php
                                                        $credits[$value->account_number] =
                                                            $credits[$value->account_number] + $revExCredit;
                                                    @endphp
                                                @endif
                                                @if ($credits[$value->account_number] > 0)
                                                    {{ number_format($credits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalCreditEquity =
                                                        $totalCreditEquity + $credits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            @php
                                                $currentBalance =
                                                    $newBeginningBalances[$value->account_number] -
                                                    $debits[$value->account_number] +
                                                    $credits[$value->account_number];
                                            @endphp
                                            <span class="timesnewroman">
                                                {{ number_format($currentBalance, 2) }}

                                                @php
                                                    $totalCurrentBalanceEquity =
                                                        $totalCurrentBalanceEquity + $currentBalance;
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Total Equity --}}
                <div class="text-sm mt-1">
                    <div class="grid grid-cols-7 font-bold">
                        <div class="col-span-3 py-1 uppercase">
                            <span class="timesnewroman">
                                {{ __('Total Equity') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceEquity, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitEquity, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditEquity, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceEquity, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Liabilities and Equity --}}
            <div class="text-sm mt-1">
                <div class="grid grid-cols-7 font-bold">
                    <div class="col-span-3 py-1 uppercase">
                        <span class="timesnewroman">
                            {{ __('Total Liabilities and Equity') }}
                        </span>
                    </div>
                    <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                        <div class="text-right py-1 pr-1 border-l border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalPreviousBalanceLiabilities + $totalPreviousBalanceEquity, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalDebitLiabilities + $totalDebitEquity, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalCreditLiabilities + $totalCreditEquity, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalCurrentBalanceLiabilities + $totalCurrentBalanceEquity, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-7 checking-row">
                <div class="col-span-5">

                </div>
                <div class="align-text-top text-right italic font-bold">
                    <span class="text-sm timesnewroman">
                        {{ __('must be 0:') }}
                    </span>
                </div>
                <div class="align-text-top text-right italic font-bold">
                    <span class="text-sm timesnewroman">
                        @php
                            $checking =
                                $totalCurrentBalanceLiabilities +
                                $totalCurrentBalanceEquity -
                                ($totalCurrentBalanceAsset1 + $totalCurrentBalanceAsset2);
                        @endphp
                        {{ number_format($checking, 2) }}
                    </span>
                    @if (abs($checking) > 0)
                        <div class="mt-2 no-print text-red-600 text-xs italic">
                            <i class="fa fa-exclamation-triangle"></i> Out of Balance. Please review transaction entries for discrepancies.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Bond Paper --}}
    <div class="to-print w-full flex justify-center">
        <div id="" class="bg-white max-w-5xl mx-auto shadow-2xl p-8 w-full">
            <div class="grid grid-cols-7">
                <div class="col-span-7 text-center">
                    <span class="font-bold text-lg timesnewroman">
                        {{ __('POTABLE WATER SYSTEM (POWAS)') }}
                    </span>
                </div>
                <div class="col-span-7 text-center">
                    <span class="font-bold timesnewroman">
                        {{ $powas->barangay . ' POWAS ' . $powas->phase }}
                    </span>
                </div>
                <div class="col-span-7 text-center">
                    <span class="text-xs italic timesnewroman">
                        {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                    </span>
                </div>
                <div class="col-span-7 w-full py-2">
                    <div class="text-center">
                        <span class="font-bold timesnewroman">
                            {{ __('Statement of Comprehensive Income') }}
                        </span>
                    </div>
                    <div class="text-center">
                        <span class="font-bold timesnewroman">
                            {{ $selectedMonthYear }}
                        </span>
                    </div>
                </div>

                <div class="col-span-3">

                </div>

                <div class="col-span-4 grid grid-cols-4 text-sm font-bold border-y border-gray-800">
                    <div class="bg-gray-300 text-center border-l border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Previous Balance') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-l border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Debit') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Credit') }}
                        </span>
                    </div>
                    <div class="bg-gray-300 text-center border-r border-gray-800 border-collapse">
                        <span class="timesnewroman">
                            {{ __('Current Balance') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div>
                <div class="text-sm">
                    <div class="font-bold">
                        <span class="timesnewroman">
                            {{ __('REVENUES') }}
                        </span>
                    </div>

                    @php
                        $totalPreviousBalanceRevenues = 0;
                        $totalDebitRevenues = 0;
                        $totalCreditRevenues = 0;
                        $totalCurrentBalanceRevenues = 0;
                    @endphp

                    @php
                        $lastAccountNumber = null;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'REVENUE')
                            @php
                                $lastAccountNumber = $value->account_number;
                            @endphp
                        @endif
                    @endforeach

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'REVENUE')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 {{ $value->account_number == $lastAccountNumber ? 'border-b border-gray-800 border-collapse' : '' }}">
                                    <div class="grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                        <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                                @php
                                                    $totalPreviousBalanceRevenues =
                                                        $totalPreviousBalanceRevenues +
                                                        $newBeginningBalances[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($debits[$value->account_number] > 0)
                                                    {{ number_format($debits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalDebitRevenues =
                                                        $totalDebitRevenues + $debits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($credits[$value->account_number] > 0)
                                                    {{ number_format($credits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalCreditRevenues =
                                                        $totalCreditRevenues + $credits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            @php
                                                $currentBalance =
                                                    $newBeginningBalances[$value->account_number] -
                                                    $debits[$value->account_number] +
                                                    $credits[$value->account_number];
                                            @endphp
                                            <span class="timesnewroman">
                                                {{ number_format($currentBalance, 2) }}

                                                @php
                                                    $totalCurrentBalanceRevenues =
                                                        $totalCurrentBalanceRevenues + $currentBalance;
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Total Revenues --}}
                <div class="text-sm mt-1">
                    <div class="grid grid-cols-7 font-bold">
                        <div class="col-span-3 py-1 uppercase">
                            <span class="timesnewroman">
                                {{ __('Total Revenues') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceRevenues, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitRevenues, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditRevenues, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceRevenues, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expenses --}}
            <div>
                <div class="text-sm">
                    <div class="font-bold">
                        <span class="timesnewroman">
                            {{ __('EXPENSES') }}
                        </span>
                    </div>

                    @php
                        $totalPreviousBalanceExpenses = 0;
                        $totalDebitExpenses = 0;
                        $totalCreditExpenses = 0;
                        $totalCurrentBalanceExpenses = 0;
                    @endphp

                    @php
                        $lastAccountNumber = null;
                    @endphp

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'EXPENSE')
                            @php
                                $lastAccountNumber = $value->account_number;
                            @endphp
                        @endif
                    @endforeach

                    @foreach ($chartOfAccount as $account_number => $value)
                        @if ($value->account_type == 'EXPENSE')
                            <div class="grid grid-cols-7">
                                <div class="pl-10 col-span-3 py-1">
                                    <span class="timesnewroman">
                                        {{ \App\Livewire\Accounting\Fis::convertString($value->account_name) }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-4 {{ $value->account_number == $lastAccountNumber ? 'border-b border-gray-800 border-collapse' : '' }}">
                                    <div class="grid grid-cols-4 text-sm border-t border-gray-800 border-collapse">
                                        <div class="text-right py-1 pr-1 border-l border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                {{ number_format($newBeginningBalances[$value->account_number], 2) }}
                                                @php
                                                    $totalPreviousBalanceExpenses =
                                                        $totalPreviousBalanceExpenses +
                                                        $newBeginningBalances[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-l border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($debits[$value->account_number] > 0)
                                                    {{ number_format($debits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalDebitExpenses =
                                                        $totalDebitExpenses + $debits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div
                                            class="bg-yellow-100 text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            <span class="timesnewroman">
                                                @if ($credits[$value->account_number] > 0)
                                                    {{ number_format($credits[$value->account_number], 2) }}
                                                @endif
                                                @php
                                                    $totalCreditExpenses =
                                                        $totalCreditExpenses + $credits[$value->account_number];
                                                @endphp
                                            </span>
                                        </div>
                                        <div class="text-right py-1 pr-1 border-r border-gray-800 border-collapse">
                                            @php
                                                $currentBalance =
                                                    $newBeginningBalances[$value->account_number] +
                                                    $debits[$value->account_number] -
                                                    $credits[$value->account_number];
                                            @endphp
                                            <span class="timesnewroman">
                                                {{ number_format($currentBalance, 2) }}

                                                @php
                                                    $totalCurrentBalanceExpenses =
                                                        $totalCurrentBalanceExpenses + $currentBalance;
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Total Expenses --}}
                <div class="text-sm mt-1">
                    <div class="grid grid-cols-7 font-bold">
                        <div class="col-span-3 py-1 uppercase">
                            <span class="timesnewroman">
                                {{ __('Total Expenses') }}
                            </span>
                        </div>
                        <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                            <div class="text-right py-1 pr-1 border-l border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalPreviousBalanceExpenses, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalDebitExpenses, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCreditExpenses, 2) }}
                                </span>
                            </div>
                            <div class="text-right py-1 pr-1 border-r border-gray-800">
                                <span class="timesnewroman">
                                    {{ number_format($totalCurrentBalanceExpenses, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Comprehensive Income --}}
            <div class="text-sm mt-1">
                <div class="grid grid-cols-7 font-bold">
                    <div class="col-span-3 py-1 uppercase">
                        <span class="timesnewroman">
                            {{ __('Total Comprehensive Income') }}
                        </span>
                    </div>
                    <div class="col-span-4 grid grid-cols-4 text-sm border-t-2 border-b-2 border-gray-800">
                        <div class="text-right py-1 pr-1 border-l border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalPreviousBalanceRevenues - $totalPreviousBalanceExpenses, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-l border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalDebitRevenues + $totalDebitExpenses, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalCreditRevenues + $totalCreditExpenses, 2) }}
                            </span>
                        </div>
                        <div class="text-right py-1 pr-1 border-r border-gray-800">
                            <span class="timesnewroman">
                                {{ number_format($totalCurrentBalanceRevenues - $totalCurrentBalanceExpenses, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
