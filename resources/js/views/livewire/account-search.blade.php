<x-action-section>
    <x-slot name="title">
        {{ __('Billing Inquiry') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Enter your account number for billing inquiry.') }}
    </x-slot>

    <x-slot name="content">
        <!-- Account Number -->
        <form wire:submit="showSearchResultModal" method="get">
            <div class="col-span-6">
                <x-label for="accountnumber" value="{{ __('Account Number') }}" />
                <x-input id="accountnumber" placeholder="NEC-SJC-PIN-001-1234" type="text"
                    class="mt-1 block w-full uppercase" wire:model="accountnumber" autocomplete="off" />
                <x-input-error for="accountnumber" class="mt-2" />
            </div>
        </form>

        <div class="mt-5 flex justify-center">
            <x-button type="button" wire:click="showSearchResultModal" wire:loading.attr="disabled">
                <i class="fa-solid fa-magnifying-glass"></i>&nbsp;{{ __('Search') }}
            </x-button>

            @isset($billingInfo)
                <x-dialog-modal wire:model.live="showingSearchResultModal" maxWidth="xl">
                    <x-slot name="title">
                        {{ __('Billing Inquiry') }}
                    </x-slot>

                    <x-slot name="content">
                        <div class="w-full">
                            <div class="w-full block">
                                <span class="font-bold">{{ __('Account Number:') }}</span>
                                {{ $accountnumber }}
                            </div>
                            <div class="w-full block">
                                <span class="font-bold">{{ __('Account Name:') }}</span>
                                {{ $memberInfo->applicationinfo->lastname . ', ' . $memberInfo->applicationinfo->firstname }}
                            </div>
                            <div class="w-full block">
                                <span class="font-bold">{{ __('POWAS Coop:') }}</span>
                                {{ $powasInfo->barangay . ' POWAS ' . $powasInfo->phase }}
                            </div>
                            <div class="mt-2 overflow-x-auto overflow-y-auto">
                                <table
                                    class="w-full table-auto border-collapse border border-gray-400 dark:border-gray-400">
                                    <thead>
                                        <tr class="border border-gray-400 dark:border-gray-400">
                                            <th class="py-3 border border-gray-400 dark:border-gray-400">
                                                {{ __('Billing Month') }}
                                            </th>
                                            <th class="py-3 border border-gray-400 dark:border-gray-400">
                                                {!! __('M<sup>3</sup> Used') !!}
                                            </th>
                                            <th class="py-3 border border-gray-400 dark:border-gray-400">
                                                {!! __('Due Date') !!}
                                            </th>
                                            <th class="py-3 border border-gray-400 dark:border-gray-400 px-2">
                                                {!! __('Amount') !!}
                                            </th>
                                            <th class="py-3 border border-gray-400 dark:border-gray-400">
                                                {!! __('Reference Number') !!}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAmountToPay = 0;
                                        @endphp
                                        @forelse ($billingInfo as $key => $value)
                                            <tr class="border border-gray-400 dark:border-gray-400">
                                                <td
                                                    class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400 whitespace-nowrap">
                                                    {{ Carbon\Carbon::parse($value->billing_month)->format('M Y') }}
                                                </td>
                                                <td
                                                    class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400 whitespace-nowrap">
                                                    {{ number_format($value->cubic_meter_used, 2) }}
                                                </td>
                                                <td
                                                    class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400 whitespace-nowrap">
                                                    {{ Carbon\Carbon::parse($value->due_date)->format('M d, Y') }}
                                                </td>
                                                <th
                                                    class="py-2 px-2 text-right border border-gray-400 dark:border-gray-400 whitespace-nowrap">
                                                    {{ number_format($value->billing_amount, 2) }}
                                                    @php
                                                        $totalAmountToPay = $totalAmountToPay + $value->billing_amount;
                                                    @endphp
                                                </th>
                                                <th
                                                    class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400 whitespace-nowrap">
                                                    {{ $value->billing_id }}
                                                </th>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    {{ __('All bills are settled!') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                        <tr class="border border-gray-400 dark:border-gray-400">
                                            <td class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400"
                                                colspan="3">
                                                <span class="font-bold italic">
                                                    {{ __('TOTAL') }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-2 text-center border border-gray-400 dark:border-gray-400">
                                                <span class="text-red-600 dark:text-red-400 font-bold text-right">
                                                    {{ number_format($totalAmountToPay, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-2">
                                    <span class="italic">
                                        {{ __('Please take note that the amounts reflected here may not be accurate because unpaid bills may have incurred penalties.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <x-danger-button wire:click="$toggle('showingSearchResultModal')" wire:loading.attr="disabled">
                            <i class="fa-solid fa-circle-xmark"></i>&nbsp;{{ __('Close') }}
                        </x-danger-button>
                    </x-slot>
                </x-dialog-modal>
            @endisset

            <x-alert-message class="me-3" on="notfound" />
        </div>
    </x-slot>
</x-action-section>
