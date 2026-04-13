<div class="w-full min-h-screen bg-gray-100 pb-12">
    @php
        $totalDebit  = collect($entries)->where('type', '!=', 'header')->sum(fn($e) => $e['debit'] ?? 0);
        $totalCredit = collect($entries)->where('type', '!=', 'header')->sum(fn($e) => $e['credit'] ?? 0);
    @endphp

    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 10pt; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 2px 4px; font-size: 9pt; }
        }
    </style>

    {{-- Toolbar --}}
    <div class="sticky top-0 z-50 bg-white border-b shadow-sm no-print">
        <div class="max-w-screen-2xl mx-auto px-4 py-3 flex flex-wrap items-center gap-4">

            {{-- Book Selector --}}
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">Book:</label>
                <select wire:model.live="selectedBook"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    @foreach ($books as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Account filter for GL --}}
            @if ($selectedBook === 'general_ledger')
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">Account:</label>
                <select wire:model.live="selectedAccountNumber"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">All Accounts</option>
                    @foreach ($chartOfAccounts as $coa)
                        <option value="{{ $coa->account_number }}">{{ $coa->account_number }} – {{ Str::limit($coa->account_name, 40) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Date Range --}}
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">From:</label>
                <input type="date" wire:model.live="dateFrom"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" />
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">To:</label>
                <input type="date" wire:model.live="dateTo"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" />
            </div>

            <div class="ml-auto flex gap-3">
                {{-- Export CSV --}}
                <button wire:click="exportCsv"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg shadow transition">
                    ⬇️ Export CSV
                </button>
                {{-- Print --}}
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow transition">
                    🖨️ Print
                </button>
            </div>
        </div>
    </div>

    {{-- Report Area --}}
    <div class="max-w-screen-2xl mx-auto px-4 mt-6">
        {{-- Report Header --}}
        <div class="text-center mb-4">
            <p class="text-lg font-bold text-gray-800">{{ $powas->barangay }} POWAS {{ $powas->phase }}</p>
            <p class="text-xs text-gray-500">{{ $powas->zone }}, {{ $powas->barangay }}, {{ $powas->municipality }}, {{ $powas->province }}</p>
            <p class="text-base font-semibold mt-2 text-gray-700">{{ $books[$selectedBook] }}</p>
            <p class="text-xs text-gray-500">For the period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p>
        </div>

        <div wire:loading.class="opacity-50">
            @if (count($entries) == 0)
                <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="font-semibold">No transactions found for the selected period and book.</p>
                </div>
            @elseif ($selectedBook === 'general_ledger')
                {{-- ==================== GENERAL LEDGER ==================== --}}
                @foreach ($entries as $row)
                    @if ($row['type'] === 'header')
                        @if (!$loop->first) 
                            </tbody>
                        </table>
                        <div class="mb-6"></div> 
                        @endif
                        <div class="bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-t-lg flex justify-between" style="background-color: #1d4ed8; color: #ffffff !important;">
                            <span style="color: #ffffff;">{{ $row['account'] }}</span>
                            <span style="color: #ffffff;">Opening Balance: {{ number_format($row['opening'], 2) }}</span>
                        </div>
                        <table class="w-full text-xs bg-white shadow rounded-b-lg overflow-hidden mb-1">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="text-left px-3 py-2 border-b">Date</th>
                                    <th class="text-left px-3 py-2 border-b">JE No.</th>
                                    <th class="text-left px-3 py-2 border-b">OR No.</th>
                                    <th class="text-left px-3 py-2 border-b w-1/3">Description</th>
                                    <th class="text-right px-3 py-2 border-b">Debit</th>
                                    <th class="text-right px-3 py-2 border-b">Credit</th>
                                    <th class="text-right px-3 py-2 border-b">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                    @else
                        <tr class="border-b border-gray-100 hover:bg-gray-50 @if(str_starts_with($row['description'], '[REVERSAL')) bg-red-50 @elseif(str_starts_with($row['description'], '[CORRECTION')) bg-yellow-50 @endif">
                            <td class="px-3 py-1.5 whitespace-nowrap">{{ $row['date'] }}</td>
                            <td class="px-3 py-1.5 whitespace-nowrap font-mono text-xs text-gray-500">{{ $row['journal_entry_number'] }}</td>
                            <td class="px-3 py-1.5 whitespace-nowrap font-mono text-xs">{{ $row['or_number'] ?? '—' }}</td>
                            <td class="px-3 py-1.5 break-words max-w-xs">{{ $row['description'] }}</td>
                            <td class="px-3 py-1.5 text-right font-mono">{{ $row['debit']  !== null ? number_format($row['debit'], 2)  : '' }}</td>
                            <td class="px-3 py-1.5 text-right font-mono">{{ $row['credit'] !== null ? number_format($row['credit'], 2) : '' }}</td>
                            <td class="px-3 py-1.5 text-right font-mono font-semibold {{ $row['balance'] < 0 ? 'text-red-600' : '' }}">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @if ($loop->last)
                            </tbody>
                        </table>
                        @endif
                    @endif
                @endforeach

            @else
                {{-- ==================== JOURNALS ==================== --}}
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="text-left px-3 py-2">Date</th>
                                <th class="text-left px-3 py-2">JE No.</th>
                                <th class="text-left px-3 py-2">OR No.</th>
                                <th class="text-left px-3 py-2">Acct No.</th>
                                <th class="text-left px-3 py-2">Account Name</th>
                                <th class="text-left px-3 py-2 w-1/3">Description</th>
                                <th class="text-right px-3 py-2">Debit</th>
                                <th class="text-right px-3 py-2">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $e)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 @if(str_starts_with($e['description'], '[REVERSAL')) bg-red-50 @elseif(str_starts_with($e['description'], '[CORRECTION')) bg-yellow-50 @endif">
                                <td class="px-3 py-1.5 whitespace-nowrap">{{ $e['date'] }}</td>
                                <td class="px-3 py-1.5 whitespace-nowrap font-mono text-gray-500">{{ $e['journal_entry_number'] }}</td>
                                <td class="px-3 py-1.5 whitespace-nowrap font-mono">{{ $e['or_number'] ?? '—' }}</td>
                                <td class="px-3 py-1.5">{{ $e['account_number'] }}</td>
                                <td class="px-3 py-1.5 {{ $e['credit'] !== null ? 'pl-8 text-gray-600' : '' }}">{{ $e['account_name'] }}</td>
                                <td class="px-3 py-1.5 break-words {{ $e['credit'] !== null ? 'pl-8 text-gray-600' : '' }}">{{ $e['description'] }}</td>
                                <td class="px-3 py-1.5 text-right font-mono">{{ $e['debit']  !== null ? number_format($e['debit'], 2)  : '' }}</td>
                                <td class="px-3 py-1.5 text-right font-mono">{{ $e['credit'] !== null ? number_format($e['credit'], 2) : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold text-sm">
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-right">TOTALS</td>
                                <td class="px-3 py-2 text-right font-mono border-t-2 border-gray-800">{{ number_format($totalDebit, 2) }}</td>
                                <td class="px-3 py-2 text-right font-mono border-t-2 border-gray-800">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                            @if (round($totalDebit, 2) != round($totalCredit, 2))
                            <tr class="bg-red-100">
                                <td colspan="8" class="px-3 py-2 text-center text-red-700 font-bold text-xs">
                                    ⚠️ Debits and Credits do not balance! Difference: {{ number_format(abs($totalDebit - $totalCredit), 2) }}
                                </td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Legend --}}
        <div class="mt-4 text-xs text-gray-400 no-print flex gap-4">
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-sm bg-red-100"></span> Reversal Entry</span>
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-sm bg-yellow-100"></span> Correction Entry</span>
        </div>
    </div>
</div>
