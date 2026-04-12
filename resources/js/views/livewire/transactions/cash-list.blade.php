<div class="py-4 px-4 space-y-4" x-data="{ expanded: '' }" id="cashList">
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
                            <th class="px-6 py-2">
                                <span>
                                    {{ __('Balance') }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    {{-- @dd($transactions) --}}
                    <tbody class="text-sm">
                        @php
                            $runningBalance = $newBeginningBalances['101'];
                        @endphp
                        <tr class="even:bg-gray-100 odd:bg-slate-200 hover:font-bold hover:bg-gray-300 cursor-pointer">
                            <th class="flex justify-center px-3 py-1 whitespace-nowrap">
                                <span>
                                    {{ \Carbon\Carbon::parse($selectedMonthYear)->format('Y-m-d') }}
                                </span>
                            </th>
                            <th class="px-3 py-1 uppercase italic text-left" colspan="4">
                                {{ __('Cash-On-Hand Beginning Balance') }}
                            </th>
                            <th class="text-right px-4 py-1 uppercase italic">
                                <span>
                                    {{ number_format($runningBalance, 2) }}
                                </span>
                            </th>
                        </tr>
                        @foreach ($transactionsList as $journalEntryNumber => $transaction)
                            <tr class="even:bg-gray-100 odd:bg-slate-200 hover:font-bold hover:bg-gray-300 cursor-pointer"
                                wire:key="{{ $journalEntryNumber }}">

                                <th class="flex justify-center px-3 py-1 whitespace-nowrap">
                                    <span>
                                        {{ $transactionsList[$journalEntryNumber][0]->transaction_date }}
                                    </span>
                                </th>

                                <td class="px-3 py-1">
                                    @foreach ($transaction as $item)
                                        @if ($item->account_number == '101')
                                            <div>
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

                                <td class="text-right px-4 py-1">
                                    @foreach ($transaction as $item)
                                        @if ($item->transaction_side == 'DEBIT' && $item->account_number == '101')
                                            <div>
                                                <span>
                                                    {{ $item->amount }}
                                                </span>
                                            </div>

                                            @php
                                                $totalDebit = $totalDebit + $item->amount;
                                                $runningBalance = $runningBalance + $item->amount;
                                            @endphp
                                        @endif
                                    @endforeach
                                </td>

                                <td class="text-right px-4 py-1">
                                    @foreach ($transaction as $item)
                                        @if ($item->transaction_side == 'CREDIT' && $item->account_number == '101')
                                            <div>
                                                <span>
                                                    {{ $item->amount }}
                                                </span>
                                            </div>
                                            @php
                                                $totalCredit = $totalCredit + $item->amount;
                                                $runningBalance = $runningBalance - $item->amount;
                                            @endphp
                                        @endif
                                    @endforeach
                                </td>

                                <td class="text-right px-4 py-1">
                                    <div>
                                        <span>
                                            {{ number_format($runningBalance, 2) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-white">
                            <td colspan="3" class="text-center font-bold italic py-1">
                                <span>
                                    {{ __('TOTAL') }}
                                </span>
                            </td>
                            <td class="text-right px-2 py-1 font-bold text-red-600 italic">
                                <span>
                                    {{ number_format($totalDebit, 2) }}
                                </span>
                            </td>
                            <td class="text-right px-2 py-1 font-bold text-red-600 italic">
                                <span>
                                    {{ number_format($totalCredit, 2) }}
                                </span>
                            </td>
                            <td class="text-right px-2 py-1 font-bold text-red-600 italic">
                                <span>
                                    {{ number_format($runningBalance, 2) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
