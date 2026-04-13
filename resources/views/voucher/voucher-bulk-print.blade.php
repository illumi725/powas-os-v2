<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POWAS-OS') }} — Bulk Voucher Print</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/powas.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <style>
        @media print {
            .no-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }

            .to-print {
                width: 8.5in;
                min-height: 5.5in;
                page-break-inside: avoid;
            }
        }

        .segoeUI {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .grid-cols-16 {
            grid-template-columns: repeat(16, minmax(0, 1fr));
        }

        .grid-cols-15 {
            grid-template-columns: repeat(15, minmax(0, 1fr));
        }

        .col-span-13 {
            grid-column: span 13 / span 13;
        }

        .col-span-14 {
            grid-column: span 14 / span 14;
        }

        .col-span-15 {
            grid-column: span 15 / span 15;
        }

        .col-span-16 {
            grid-column: span 16 / span 16;
        }
    </style>
</head>

<body>
    {{-- Toolbar (hidden during print) --}}
    <div class="no-print sticky top-0 z-10 py-4 bg-black mb-4 flex justify-center items-center gap-4">
        <span class="text-white text-sm font-semibold segoeUI">
            {{ count($vouchers) }} Voucher(s) &nbsp;|&nbsp;
            {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
        </span>
        <button
            onclick="window.print()"
            class="inline-flex items-center px-5 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
            🖨 Print All
        </button>
        <button
            onclick="window.close()"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
            ✕ Close
        </button>
    </div>

    @if (count($vouchers) === 0)
        <div class="flex justify-center items-center h-64 text-gray-500 text-lg font-semibold segoeUI">
            No vouchers found for the selected date range.
        </div>
    @else
        @foreach ($vouchers as $index => $voucherInfo)
            @php
                $transactionInfo = \App\Models\Transactions::where('trxn_id', $voucherInfo->trxn_id)->first();
                $inWordsFormatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                $inWords = strtoupper($inWordsFormatter->format($voucherInfo->amount)) . ' PESOS ONLY';
            @endphp

            {{-- Each voucher wrapped in page-break div --}}
            <div class="to-print flex flex-col {{ $index < count($vouchers) - 1 ? 'page-break' : '' }} mb-8">
                <div class="bg-white mx-auto text-sm">
                    <div class="grid grid-cols-16 border border-black">
                        <div class="col-span-13 pl-1">
                            <span class="segoeUI font-bold">
                                {{ $powas->barangay . ' POWAS ' . $powas->phase }}
                                {{ '(' . $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province . ')' }}
                            </span>
                        </div>
                        <div class="col-span-3 pl-1 border-l border-black">
                            <span class="segoeUI">
                                {{ 'Vr. No.:' }}
                                <span class="text-red-600 segoeUI border-b border-black">
                                    {{ $voucherInfo->voucher_number }}
                                </span>
                            </span>
                        </div>
                        <div class="col-span-13 pl-1">
                            <span class="segoeUI font-bold">
                                {{ __('VOUCHER') }}
                            </span>
                        </div>
                        <div class="col-span-3 pl-1 border-l border-black">
                            <span class="segoeUI">
                                {{ 'Date:' }}
                                <span class="text-red-600 segoeUI border-b border-black">
                                    {{ \Carbon\Carbon::parse($voucherInfo->voucher_date)->format('m-d-Y') }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-1 grid grid-cols-16 px-1 border border-black">
                        <div class="col-span-2">
                            <span class="segoeUI">{{ __('Paid to:') }}</span>
                        </div>
                        <div class="col-span-14 border-b border-black mr-8">
                            <span class="segoeUI">{{ $voucherInfo->received_by }}</span>
                        </div>
                        <div class="col-span-2 mb-1">
                            <span class="segoeUI">{{ __('Address:') }}</span>
                        </div>
                        <div class="col-span-14 border-b border-black mr-8 mb-1">
                            <span class="segoeUI">{{ __('Zone 1, Pinili, San Jose City, Nueva Ecija') }}</span>
                        </div>
                    </div>

                    {{-- Particulars --}}
                    <div class="mt-1 grid grid-cols-16 border border-black">
                        <div class="bg-gray-300 col-span-2 border-r border-black flex items-center justify-center">
                            <span class="segoeUI font-bold">{{ __('CODE') }}</span>
                        </div>
                        <div class="bg-gray-300 col-span-11 border-r border-black flex items-center justify-center">
                            <span class="segoeUI font-bold">{{ __('PARTICULARS') }}</span>
                        </div>
                        <div class="bg-gray-300 text-center col-span-3">
                            <span class="segoeUI font-bold block">{{ __('AMOUNT') }}</span>
                            <span class="segoeUI font-bold block">{{ __('PHP') }}</span>
                        </div>

                        {{-- Row 1: Account line --}}
                        <div class="col-span-2 border-r border-t border-black flex items-center justify-center py-1">
                            <span class="segoeUI text-red-600 font-bold">{{ $transactionInfo->account_number }}</span>
                        </div>
                        <div class="col-span-11 border-r border-t px-1 border-black flex items-center py-1">
                            <span class="segoeUI">{{ \App\Models\ChartOfAccounts::find($transactionInfo->account_number)->account_name }}</span>
                        </div>
                        <div class="pr-1 col-span-2 border-r border-t border-black text-right py-1">
                            <span class="segoeUI">{{ number_format($voucherInfo->amount, 2) }}</span>
                        </div>
                        <div class="text-center border-t border-black py-1">
                            <span class="segoeUI font-bold block">&nbsp;</span>
                        </div>

                        {{-- Row 2: Particulars description --}}
                        <div class="col-span-2 border-r border-t border-black flex items-center justify-center py-1">
                            <span class="segoeUI text-red-600">&nbsp;</span>
                        </div>
                        <div class="col-span-11 border-r border-t pl-10 pr-1 border-black flex items-center py-1">
                            <span class="segoeUI uppercase">{{ $voucherInfo->voucherparticulars[0]->description ?? '' }}</span>
                        </div>
                        <div class="pr-1 col-span-2 border-r border-t border-black text-right py-1">
                            <span class="segoeUI">&nbsp;</span>
                        </div>
                        <div class="text-center border-t border-black py-1">
                            <span class="segoeUI font-bold block">&nbsp;</span>
                        </div>

                        {{-- Rows 3-5: Empty lines --}}
                        @for ($r = 0; $r < 3; $r++)
                        <div class="col-span-2 border-r border-t border-black flex items-center justify-center py-1">
                            <span class="segoeUI text-red-600">&nbsp;</span>
                        </div>
                        <div class="col-span-11 border-r border-t pl-10 pr-1 border-black flex items-center py-1">
                            <span class="segoeUI uppercase">&nbsp;</span>
                        </div>
                        <div class="pr-1 col-span-2 border-r border-t border-black text-right py-1">
                            <span class="segoeUI">&nbsp;</span>
                        </div>
                        <div class="text-center border-t border-black py-1">
                            <span class="segoeUI font-bold block">&nbsp;</span>
                        </div>
                        @endfor

                        {{-- Total --}}
                        <div class="col-span-13 border-r border-t pl-1 pr-1 border-black flex items-center justify-center py-1">
                            <span class="segoeUI uppercase font-bold">{{ __('Total') }}</span>
                        </div>
                        <div class="pr-1 col-span-2 border-r border-t border-black text-right py-1">
                            <span class="segoeUI font-bold text-red-600">{{ number_format($voucherInfo->amount, 2) }}</span>
                        </div>
                        <div class="text-center border-t border-black py-1">
                            <span class="segoeUI font-bold block">&nbsp;</span>
                        </div>
                    </div>

                    {{-- Other Details --}}
                    <div class="mt-1 grid grid-cols-16 border border-black">
                        <div class="px-1 col-span-13 border-r border-black flex items-center">
                            <span class="segoeUI">{{ __('Total Peso (in words):') }}&nbsp;</span>
                            <span class="segoeUI border-b border-black font-bold">{{ $inWords }}</span>
                        </div>
                        <div class="px-1 col-span-3 grid grid-rows-2">
                            <div><span class="segoeUI">{{ __('Received by:') }}</span></div>
                            <div class="text-center overflow-x-hidden mt-2">
                                <span class="underline whitespace-nowrap" style="font-size: 10px;">{{ $voucherInfo->received_by }}</span>
                            </div>
                        </div>

                        <div class="px-1 py-1 col-span-7 border-t border-black flex items-center">
                            <span class="segoeUI">{{ __('Cash/Check No.: _____________________________________') }}</span>
                        </div>
                        <div class="px-1 py-1 col-span-6 border-r border-t border-black flex items-center">
                            <span class="segoeUI">{{ __('Date: ') }}&nbsp;</span>
                            <span class="segoeUI border-b border-black text-red-600">
                                {{ \Carbon\Carbon::parse($voucherInfo->voucher_date)->format('m-d-Y') }}
                            </span>
                        </div>

                        <div class="px-1 col-span-3 border-t border-black">
                            <div class="flex items-start justify-center">
                                <span class="segoeUI">{{ __('Signature') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Signatures --}}
                    <div class="mt-1 grid grid-cols-15 gap-8">
                        <div class="mt-12 col-span-5 mx-8 text-center">
                            <div class="border-b border-black">
                                <span class="segoeUI">
                                    @if ($voucherInfo->prepared_by != null)
                                        {{ \App\Models\User::find($voucherInfo->prepared_by)?->userinfo?->lastname . ', ' . \App\Models\User::find($voucherInfo->prepared_by)?->userinfo?->firstname }}
                                    @else
                                        &nbsp;
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-12 border-b border-black col-span-5 mx-8 text-center">
                            <span class="segoeUI">
                                @if ($voucherInfo->checked_by != null)
                                    {{ \App\Models\User::find($voucherInfo->checked_by)?->userinfo?->lastname . ', ' . \App\Models\User::find($voucherInfo->checked_by)?->userinfo?->firstname }}
                                @else
                                    &nbsp;
                                @endif
                            </span>
                        </div>
                        <div class="mt-12 border-b border-black col-span-5 mx-8 text-center">
                            <span class="segoeUI">
                                @if ($voucherInfo->approved_by != null)
                                    {{ \App\Models\User::find($voucherInfo->approved_by)?->userinfo?->lastname . ', ' . \App\Models\User::find($voucherInfo->approved_by)?->userinfo?->firstname }}
                                @else
                                    &nbsp;
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-15 gap-8 mb-2">
                        <div class="text-center col-span-5 mx-8">
                            <span class="segoeUI">{{ __('Prepared by') }}</span>
                        </div>
                        <div class="text-center col-span-5 mx-8">
                            <span class="segoeUI">{{ __('Checked by') }}</span>
                        </div>
                        <div class="text-center col-span-5 mx-8">
                            <span class="segoeUI">{{ __('Approved by') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Receipt image --}}
                <div class="justify-center items-center flex-1 mt-8">
                    <img style="border: 1px solid gray; height: 140mm;"
                         class="mx-auto my-auto"
                         src="{{ url('https://powas-os2.infinityfreeapp.com/powas-os/public/uploads/voucher_receipts/' . ($voucherInfo->voucherparticulars[0]->voucher_id ?? '') . '.jpg') }}"
                         alt="{{ $voucherInfo->voucherparticulars[0]->voucher_id ?? '' }}">
                </div>
            </div>
        @endforeach
    @endif

    @livewireScripts
</body>

</html>
