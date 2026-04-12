<div class="w-full">
    <div class="no-print sticky top-0 bg-black text-white py-4">
        <div class="mx-auto text-center">
            <x-button type="button" onclick="window.print()" title="Print" wire:loading.attr="disabled">
                {{ __('Print') }}
            </x-button>
        </div>
    </div>

    <div>
        <div class="mx-auto jetbrains" style="width: 215.9mm;">
            @php
                $ctr = 1;
            @endphp

            <div class="text-center">
                <span class="font-bold text-xl block">{{ $powas->barangay . ' POWAS ' . $powas->phase }}</span>
                <span class="font-bold text-xs block">
                    {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                </span>
            </div>

            <div class="text-center">
                <span class="font-bold text-lg block my-2">
                    {{ 'Collection Sheet for the Month of ' . $validReadingsList[$membersList[0]['member_id']]['billing_month'] }}
                </span>
            </div>

            <div class="w-full grid grid-cols-2 text-sm px-2 border border-gray-700 rounded-lg border-dashed mt-4 pb-2">
                <div class="col-span-2 text-center my-2">
                    <span class="font-bold">
                        {{ __('Cash Collection Summary') }}
                    </span>
                </div>

                @php
                    $grandTotal =
                        $billingSummary['total_balances_from_previous'] +
                        $billingSummary['total_billing_amount'] +
                        $billingSummary['total_penalties'] +
                        $billingSummary['total_monthly_dues'];
                    $grandTotal = $grandTotal - $billingSummary['total_discounts'];
                @endphp

                <div class="grid grid-cols-3 pr-8 border-b border-gray-700 mb-1 pb-1">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Cubic Meter Used: ') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold">
                            {{ number_format($billingSummary['total_cubic_meter_used'], 1) }} m<sup>3</sup>
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8 border-b border-gray-700 mb-1 pb-1">
                    <div class="col-span-2">
                        <span>
                            {{ __('Target Collection: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold">
                        <span>
                            &#8369;{{ number_format($grandTotal, 2) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Balances from Previous Bills: ') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold">
                            &#8369;{{ number_format($billingSummary['total_balances_from_previous'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Payments Received: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold border border-black">
                        <span>

                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Billing Amount: ') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold">
                            &#8369;{{ number_format($billingSummary['total_billing_amount'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Plus - Additional Penalties*: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold border border-black">
                        <span>

                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Micro-Savings: ') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold">
                            &#8369;{{ number_format($billingSummary['total_monthly_dues'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Plus - Other Collections**: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold border border-black">
                        <span>

                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Total Penalties [Other]: ') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold">
                            &#8369;{{ number_format($billingSummary['total_penalties'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Less - Deductions***: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold border border-black">
                        <span>

                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Less - Total Discounts: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold">
                        <span>
                            -
                        </span>
                        <span>
                            &#8369;{{ number_format($billingSummary['total_discounts'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2 text-center font-bold italic">
                        <span>
                            {{ __('TOTAL') }}
                        </span>
                    </div>
                    <div class="text-right font-bold italic text-red-700 border border-black">
                        <span>

                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pr-8">
                    <div class="col-span-2 text-center font-bold italic">
                        <span>
                            {{ __('TOTAL') }}
                        </span>
                    </div>
                    <div class="text-right font-bold italic text-red-700">
                        <span>
                            &#8369;{{ number_format($grandTotal, 2) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 pl-8">
                    <div class="col-span-2">
                        <span>
                            {{ __('Remit Date: ') }}
                        </span>
                    </div>
                    <div class="text-right font-bold border border-black">
                        <span>
                            {{-- {{ Carbon\Carbon::parse($validReadingsList[$membersList[0]['member_id']]['due_date'])->format('M') . __(' ___, ') . Carbon\Carbon::parse($validReadingsList[$membersList[0]['member_id']]['due_date'])->format('Y') }} --}}
                            {{ __('___/___/______') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-xs italic">
                <span class="block">
                    {!! __('*Additional Penalties: Sum of all penalties') !!}
                </span>
                <span class="block">
                    {!! __(
                        '**Other Collections: Collections such as Membership Fee/Application Fee, Advance Payments, Other Income, etc. that is not included in the billing',
                    ) !!}
                </span>
                <span class="block">
                    {!! __('***Deductions: Any expenses that is paid from the cash collection') !!}
                </span>
            </div>
            <x-section-border />

            <table class="border border-gray-600 table-auto w-full">
                <thead class="bg-green-600 text-white">
                    <tr class="">
                        <th class="border border-gray-600 px-2">
                            {{ __('SL #') }}
                        </th>
                        {{-- <th class="border px-2">
                            {{ __('QR Code') }}
                        </th> --}}
                        <th class="border border-gray-600 px-2">
                            {{ __('Name') }}
                        </th>
                        <th class="border border-gray-600 px-2 whitespace-nowrap">
                            {{ __('Amount Due') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Penalty') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Payment Received') }}
                        </th>
                        <th class="border border-gray-600 px-2 whitespace-nowrap">
                            {{ __('Date Paid') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Received By ID') }}
                        </th>
                        {{-- <th class="border border-gray-600 px-2 whitespace-nowrap">
                            {{ __('Paid?') }}
                        </th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDues = 0;
                        $totalPenalties = 0;
                        $totalPaymentReceived = 0;
                        $billingCount = 0;
                    @endphp
                    @foreach ($validReadingsList as $item)
                        <tr class="border border-gray-600 odd:bg-gray-50 even:bg-gray-100">
                            <th class="border border-gray-600">
                                {{ $ctr++ }}
                            </th>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2">
                                {{ $item['member_name'] }}
                            </td>

                            @php
                                $penalty = \App\Models\Billings::find($item['billing_id'])->penalty;
                            @endphp

                            <td class="border border-gray-600 pr-2 pl-8 text-right">
                                @if ($item['bill_status'] == 'PAID')
                                    &#8369;{{ number_format($totalAmountDues[$item['billing_id']] - $penalty, 2) }}
                                @else
                                    &#8369;{{ number_format($totalAmountDues[$item['billing_id']], 2) }}
                                @endif
                            </td>
                            <td class="border border-gray-600 text-right">
                                @if ($item['bill_status'] == 'PAID')
                                    @php
                                        $reconnectionFee = 0;

                                        $isReconnectionFeeExists = \App\Models\Transactions::where(
                                            'paid_to',
                                            $item['billing_id'],
                                        )
                                            ->where('transaction_side', 'CREDIT')
                                            ->where('account_number', '404')
                                            ->exists();

                                        if ($isReconnectionFeeExists == true) {
                                            $reconnectionFee = \App\Models\Transactions::where(
                                                'paid_to',
                                                $item['billing_id'],
                                            )
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '404')
                                                ->first()->amount;
                                        }

                                        $totalPenalty = $penalty + $reconnectionFee;
                                    @endphp
                                    @if ($totalPenalty > 0)
                                        &#8369;{{ number_format($totalPenalty, 2) }}
                                    @endif
                                @endif
                            </td>
                            <td class="border border-gray-600 text-right">
                                @if ($item['bill_status'] == 'PAID')
                                    @php
                                        $amountDue = 0;
                                        $monthlyDue = 0;
                                        $discounts = 0;
                                        $excessPayments = 0;

                                        $isAmountDueExists = \App\Models\Transactions::where(
                                            'paid_to',
                                            $item['billing_id'],
                                        )
                                            ->where('transaction_side', 'CREDIT')
                                            ->where('account_number', '105')
                                            ->exists();

                                        $isMonthlyDueExists = \App\Models\Transactions::where(
                                            'paid_to',
                                            $item['billing_id'],
                                        )
                                            ->where('transaction_side', 'CREDIT')
                                            ->where('account_number', '201')
                                            ->exists();

                                        $isDiscountsExists = \App\Models\Transactions::where(
                                            'paid_to',
                                            $item['billing_id'],
                                        )
                                            ->where('transaction_side', 'DEBIT')
                                            ->where('account_number', '407')
                                            ->exists();

                                        $isExcessPaymentsExists = \App\Models\Transactions::where(
                                            'paid_to',
                                            $item['billing_id'],
                                        )
                                            ->where('transaction_side', 'CREDIT')
                                            ->where('account_number', '208')
                                            ->exists();

                                        if ($isAmountDueExists == true) {
                                            $amountDue = \App\Models\Transactions::where('paid_to', $item['billing_id'])
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '105')
                                                ->first()->amount;
                                        }

                                        if ($isMonthlyDueExists == true) {
                                            $monthlyDue = \App\Models\Transactions::where(
                                                'paid_to',
                                                $item['billing_id'],
                                            )
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '201')
                                                ->first()->amount;
                                        }

                                        if ($isDiscountsExists == true) {
                                            $discounts = \App\Models\Transactions::where('paid_to', $item['billing_id'])
                                                ->where('transaction_side', 'DEBIT')
                                                ->where('account_number', '407')
                                                ->first()->amount;
                                        }

                                        if ($isExcessPaymentsExists == true) {
                                            $excessPayments = \App\Models\Transactions::where(
                                                'paid_to',
                                                $item['billing_id'],
                                            )
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '208')
                                                ->first()->amount;
                                        }

                                        $withExcessStyle = '';

                                        $paymentReceived =
                                            $amountDue + $monthlyDue + $totalPenalty + $excessPayments - $discounts;
                                        $billingCount++;
                                        $totalPaymentReceived = $totalPaymentReceived + $paymentReceived;
                                    @endphp
                                    @if (
                                        $amountDue +
                                            $monthlyDue +
                                            $totalPenalty +
                                            $excessPayments -
                                            $discounts -
                                            ($amountDue + $monthlyDue + $totalPenalty - $discounts) >
                                            0)
                                        @php
                                            $withExcessStyle = 'text-blue-700';
                                        @endphp
                                    @endif
                                    <span class="{{ $withExcessStyle }}">
                                        &#8369;{{ number_format($paymentReceived, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="border border-gray-600 text-center">
                                @if ($item['bill_status'] == 'PAID')
                                    @php
                                        if (\App\Models\Billings::find($item['billing_id'])->bill_status == 'PAID') {
                                            $datePaid = \App\Models\Transactions::where('paid_to', $item['billing_id'])
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '105')
                                                ->first()->transaction_date;
                                        }
                                    @endphp
                                    {{ \Carbon\Carbon::parse($datePaid)->format('m/d/y') }}
                                @endif
                            </td>
                            <td class="border border-gray-600 text-center">
                                @if ($item['bill_status'] == 'PAID')
                                    @php
                                        if (\App\Models\Billings::find($item['billing_id'])->bill_status == 'PAID') {
                                            $userID = \App\Models\Transactions::where('paid_to', $item['billing_id'])
                                                ->where('transaction_side', 'CREDIT')
                                                ->where('account_number', '105')
                                                ->first()->recorded_by_id;
                                        }
                                    @endphp
                                    {{ $userID }}
                                @endif
                            </td>
                            {{-- <td class="border border-gray-600 text-center">
                                @if ($item['bill_status'] == 'PAID')
                                    {{ __('YES') }}
                                @endif
                            </td> --}}
                        </tr>
                        @php
                            $totalDues = $totalDues + $totalAmountDues[$item['billing_id']];
                            $totalPenalties = $totalPenalties + $penalty;
                            if ($item['bill_status'] == 'PAID') {
                                $totalDues = $totalDues - $penalty;
                            }
                        @endphp
                    @endforeach
                    <tr class="border border-gray-600">
                        <td colspan="2" class="text-center font-bold italic border border-gray-600">
                            {{ __('TOTAL') }}
                        </td>
                        <td class="text-right text-red-700 font-bold border border-gray-600 italic pr-2 pl-8">
                            &#8369;{{ number_format($totalDues, 2) }}
                        </td>
                        <td class="text-right text-red-700 font-bold border border-gray-600 italic pr-2 pl-8">
                            @if ($billingSummary['total_billings'] == $billingCount)
                                &#8369;{{ number_format($totalPenalties, 2) }}
                            @endif
                        </td>
                        <td class="text-right text-red-700 font-bold border border-gray-600 italic pr-2 pl-8">
                            @if ($billingSummary['total_billings'] == $billingCount)
                                &#8369;{{ number_format($totalPaymentReceived, 2) }}
                            @endif
                        </td>
                        <td class="text-right text-red-700 font-bold border border-gray-600 italic pr-2 pl-8">

                        </td>
                        <td class="text-right text-red-700 font-bold border border-gray-600 italic pr-2 pl-8">

                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="w-full grid grid-cols-3 text-sm px-2 mt-12 pb-2 gap-4">
                <div>
                    <div class="text-left font-bold">
                        {{ __('Checked by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('President') }}
                    </div>
                </div>
                <div>
                    <div class="text-left font-bold">
                        {{ __('Collected/Remitted by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('Collector') }}
                    </div>
                </div>
                <div>
                    <div class="text-left font-bold">
                        {{ __('Received/Verified by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('Treasurer') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
