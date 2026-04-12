@php
$slNo = 0;
@endphp
@foreach ($transactions as $transaction)
@php
$slNo++;
@endphp
<tr class="even:bg-gray-100 odd:bg-slate-200 hover:font-bold hover:bg-gray-300 cursor-pointer" wire:key="{{ $transaction->trxn_id }}">
    <th class="text-center py-1">
        <span>
            {{ $transaction->journal_entry_number }}
        </span>
    </th>
    <td class="text-center px-3 py-1 whitespace-nowrap">
        <span>
            {{ $transaction->transaction_date }}
        </span>
    </td>
    <td class=" px-3 py-1">
        <span>
            {{ $transaction->description }}
        </span>
    </td>
    <td class="text-center py-1">
        <span>
            {{ $transaction->account_number }}
        </span>
    </td>
    <td class="text-right px-4 py-1">
        <span>
            {{ $transaction->transaction_side == 'DEBIT' ? $transaction->amount : '' }}
            @php
            if ($transaction->transaction_side == 'DEBIT') {
            $totalDebit = $totalDebit + $transaction->amount;
            }
            @endphp
        </span>
    </td>
    <td class="text-right px-4 py-1">
        <span>
            {{ $transaction->transaction_side == 'CREDIT' ? $transaction->amount : '' }}

            @php
            if ($transaction->transaction_side == 'CREDIT') {
            $totalCredit = $totalCredit + $transaction->amount;
            }
            @endphp
        </span>
    </td>
</tr>
@endforeach