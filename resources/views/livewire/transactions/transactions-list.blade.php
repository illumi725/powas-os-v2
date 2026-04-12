<div class="py-4 px-4 space-y-4" x-data="{ expanded: '' }" id="transactionsList">
    <x-alert-message class="me-3" on="alert" />

    {{-- Filter --}}
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4">
        <div class="w-full grid grid-cols-1 md:flex md:items-center gap-4">
            <span class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Journal Entries') }}
            </span>

            <x-button-link id="fisPrint" onclick="return openPopup('fisPrint');"
                href="{{ route('accounting', ['powasID' => $powasID, 'transactionMonth' => $selectedMonthYear]) }}"
                wire:loading.attr="disabled">
                {{ __('Financial Statement') }}
            </x-button-link>
        </div>
        <div class="w-full md:flex md:justify-end md:items-center gap-2">
            <x-label class="block md:inline" value="{{ __('Transaction Month: ') }}" />
            <x-combobox class="block w-full md:w-auto md:inline" wire:model="selectedMonthYear"
                wire:change="fetchData2">
                @slot('options')
                    @foreach ($monthYear as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                @endslot
            </x-combobox>
            @can('create transaction')
                @livewire('accounting.add-transaction', ['powasID' => $powasID, 'powas' => $powas])
            @endcan
            {{-- Temporarily disabled until EditTransaction files are uploaded
            @can('edit transaction')
                @livewire('accounting.edit-transaction', ['powasID' => $powasID, 'powas' => $powas])
            @endcan
            --}}
        </div>
    </div>

    <div wire:loading wire:target="fetchData2" class="my-2 w-full text-center">
        <x-label class="text-xl font-bold my-16" value="{{ __('Loading data... Please wait...') }}" />
    </div>

    <div class="w-full px-4 pb-4" wire:loading.class="hidden" wire:target="fetchData2">
        @if (count($transactionsList) == 0 || $transactionsList == null)
            <div class="my-2 w-full text-center">
                <x-label class="text-xl font-black my-16" value="{{ __('No records found!') }}" />
            </div>
        @else
            <div class="overflow-x-auto overflow-y-auto">
                <table class="border border-collapse min-w-max md:w-full">
                    <thead class="bg-gray-400 border border-collapse">
                        <tr>
                            <th class=" py-2">
                                <span>
                                    {{ __('Date') }}
                                </span>
                            </th>
                            <th class="py-2" style="width: 620px;">
                                <span>
                                    {{ __('Description') }}
                                </span>
                            </th>
                            <th class="px-2 py-2">
                                <span>
                                    {{ __('Entry #') }}
                                </span>
                            </th>
                            <th class="py-2 whitespace-nowrap">
                                <span>
                                    {{ __('Account #') }}
                                </span>
                            </th>
                            <th class="px-6 py-2">
                                <span>
                                    {{ __('Debit') }}
                                </span>
                            </th>
                            <th class="px-6 py-2">
                                <span>
                                    {{ __('Credit') }}
                                </span>
                            </th>
                            <th class="px-2 py-2">
                                <span>
                                    {{ __('Action') }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    {{-- @dd($transactions) --}}
                    <tbody class="text-sm">
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp
                        @foreach ($transactionsList as $journalEntryNumber => $transaction)
                            @php
                                $debitCounter = 0;
                                $creditCounter = 0;
                            @endphp
                            @foreach ($transaction as $item)
                                @if ($item->transaction_side == 'DEBIT')
                                    @php
                                        $debitCounter++;
                                    @endphp
                                @endif
                                @if ($item->transaction_side == 'CREDIT')
                                    @php
                                        $creditCounter++;
                                    @endphp
                                @endif
                            @endforeach
                            <tr class="even:bg-gray-100 odd:bg-slate-200 hover:font-bold hover:bg-gray-300 cursor-pointer"
                                wire:key="{{ $journalEntryNumber }}">

                                <th class="flex justify-center px-3 py-1 whitespace-nowrap">
                                    <span>
                                        {{ $transactionsList[$journalEntryNumber][0]->transaction_date }}
                                    </span>
                                </th>

                                <td class=" px-3 py-1">
                                    @foreach ($transaction as $item)
                                        @if ($item->transaction_side == 'DEBIT')
                                            <div>
                                                <span>
                                                    {{ $item->description }}
                                                </span>
                                            </div>
                                        @endif

                                        @if ($item->transaction_side == 'CREDIT')
                                            <div class="ml-8">
                                                <span>
                                                    {{ $item->description }}
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>

                                <td class="text-center py-1">
                                    <div>
                                        <span>
                                            {{ $journalEntryNumber }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-center py-1">
                                    @foreach ($transaction as $item)
                                        <div>
                                            <span>
                                                {{ $item->account_number }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>

                                <td class="text-right px-4 py-1">
                                    @foreach ($transaction as $item)
                                        @if ($item->transaction_side == 'DEBIT')
                                            <div>
                                                <span>
                                                    {{ number_format($item->amount, 2) }}
                                                </span>
                                            </div>
                                            @php
                                                $totalDebit = $totalDebit + $item->amount;
                                            @endphp
                                        @else
                                            <div>
                                                <span>

                                                </span>
                                            </div>
                                        @endif
                                    @endforeach

                                    @for ($i = 0; $i < $creditCounter; $i++)
                                        <div>
                                            <span>
                                                &nbsp;
                                            </span>
                                        </div>
                                    @endfor
                                </td>

                                <td class="text-right px-4 py-1">
                                    @for ($i = 0; $i < $debitCounter; $i++)
                                        <div>
                                            <span>
                                                &nbsp;
                                            </span>
                                        </div>
                                    @endfor
                                    @foreach ($transaction as $item)
                                        @if ($item->transaction_side == 'CREDIT')
                                            <div>
                                                <span>
                                                    {{ number_format($item->amount, 2) }}
                                                </span>
                                            </div>
                                            @php
                                                $totalCredit = $totalCredit + $item->amount;
                                            @endphp
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center py-1">
                                    <div class="flex justify-center gap-2">
                                        @can('edit transaction')
                                            <button wire:click="$dispatch('showEdit', { journalEntryNumber: '{{ $journalEntryNumber }}' })" 
                                                class="font-bold text-indigo-600 hover:text-indigo-800" 
                                                title="Edit Transaction">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                        @endcan
                                        
                                        @if(isset($receiptsList[$journalEntryNumber]))
                                            @php
                                                $receipts = $receiptsList[$journalEntryNumber];
                                                $trxnIDs = array_map(fn($r) => $r->trxn_id, $receipts);
                                                $printIDs = array_map(fn($r) => $r->print_id, $receipts);
                                                $receiptNumber = $receipts[0]->receipt_number;
                                            @endphp
                                            <a href="{{ route('other-receipt.view', [
                                                'trxnID' => json_encode($trxnIDs),
                                                'printID' => json_encode($printIDs),
                                                'receiptNumber' => $receiptNumber,
                                                'powasID' => $powasID,
                                            ]) }}" target="_blank" class="font-bold text-blue-600 hover:text-blue-800" title="Reprint Receipt">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-white">
                            <td colspan="4" class="text-center font-bold italic py-1">
                                <span>
                                    {{ __('TOTAL') }}
                                </span>
                            </td>
                            @php
                                $difference = abs($totalDebit - $totalCredit);
                                $isBalanced = $difference < 0.01; // Account for floating point precision
                                $colorClass = $isBalanced ? 'text-green-600' : 'text-red-600';
                            @endphp
                            <td class="text-right px-2 py-1 font-bold italic {{ $colorClass }}">
                                <span>
                                    {{ number_format($totalDebit, 2) }}
                                </span>
                            </td>
                            <td class="text-right px-2 py-1 font-bold italic {{ $colorClass }}">
                                <span>
                                    {{ number_format($totalCredit, 2) }}
                                </span>
                            </td>
                            <td class="text-center py-1">
                                @if(!$isBalanced)
                                    <span class="text-xs text-red-600 font-bold" title="Unbalanced!">⚠</span>
                                @else
                                    <span class="text-xs text-green-600 font-bold" title="Balanced">✓</span>
                                @endif
                            </td>
                        </tr>
                        @if(!$isBalanced)
                            <tr class="bg-red-50">
                                <td colspan="4" class="text-center font-bold py-1 text-red-600">
                                    <span class="text-xs">
                                        {{ __('⚠ UNBALANCED - Difference: ₱') }}{{ number_format($difference, 2) }}
                                    </span>
                                </td>
                                <td colspan="3" class="text-center text-xs text-red-600 py-1">
                                    @if($totalDebit > $totalCredit)
                                        {{ __('Debit exceeds Credit') }}
                                    @else
                                        {{ __('Credit exceeds Debit') }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
