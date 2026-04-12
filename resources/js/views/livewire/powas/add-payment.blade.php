<div class="w-full inline">
    <x-alert-message class="me-3" on="alert" />
    <form class="w-full md:flex md:justify-end md:items-center mt-4 md:mt-0" wire:submit.prevent="showAddPaymentModal"
        method="post">
        @csrf
        <x-label class="md:inline mr-2" for="selectedBillIDInput" value="{!! __('Quick Payment Entry:') !!}" />
        {{-- <div class="flex items-center"> --}}
        <x-input class="w-full md:w-2/3 inline-flex" type="text" id="selectedBillIDInput" name="selectedBillIDInput"
            wire:model.live="selectedBillIDInput" autocomplete="off"
            placeholder="Search bill reference or member name..." list="unpaid" autofocus />

        <datalist id="unpaid">
            @foreach ($unpaidBills as $bill)
                <option value="{{ $bill->billing_id }}">
                    <span class="block">
                        {{ $bill->lastname . ', ' . $bill->firstname }}
                    </span>
                    <span class="italic">
                        {{ '[' . \Carbon\Carbon::parse($bill->billing_month)->format('F Y') . ']' }}
                    </span>
                </option>
            @endforeach
        </datalist>

        {{-- <x-button class="ml-2" id="qrCodeScanner" type="button" wire:click="showQRCodeScanner">
                <i class="fa-solid fa-qrcode"></i>&nbsp;
                <span>
                    {{ __('Scan QR') }}
                </span>
            </x-button> --}}
        {{-- </div> --}}
    </form>

    <div class="w-full md:text-end">
        <x-input-error for="selectedBillIDInput" />
    </div>

    @if (isset($selectedBill))
        <x-dialog-modal wire:model.live="showingAddPaymentModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Add Payment') }}
                </span>
            @endslot
            @slot('content')
                @if (count($saveError) > 0)
                    <div class="text-sm text-red-600 dark:text-red-400">
                        <div class="block w-full">
                            {{ __('Some chart of accounts to be used for saving payments is missing!') }}
                        </div>
                        <div class="block w-full mt-2">
                            <ul class="w-full list-disc ml-8">
                                @foreach ($saveError as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    @if ($selectedBill->bill_status == 'PAID')
                        <div class="w-full my-4 text-center">
                            <span class="text-base text-red-600 dark:text-red-400 font-black">
                                {{ __('This bill is already settled!') }}
                            </span>
                        </div>
                    @else
                        <div class="w-full grid grid-cols-2 gap-1">
                            <div class="w-full col-span-2 grid grid-cols-2 py-1 px-1 border border-dashed rounded-md mb-4">
                                <div class="w-full">
                                    <x-label value="{{ __('Reference Number: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold" value="{{ $selectedBill->billing_id }}" />
                                </div>
                                <div class="w-full">
                                    <x-label value="{{ __('Account Number: ') }}" />
                                </div>

                                @php
                                    $memberInfo = \App\Models\PowasMembers::join(
                                        'powas_applications',
                                        'powas_members.application_id',
                                        '=',
                                        'powas_applications.application_id',
                                    )
                                        ->where('powas_members.member_id', $this->selectedBill->member_id)
                                        ->first();
                                @endphp

                                <div class="w-full">
                                    <x-label class="inline font-bold" value="{{ $memberInfo->member_id }}" />
                                </div>
                                <div class="w-full">
                                    <x-label value="{{ __('Account Name: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold"
                                        value="{{ $memberInfo->lastname . ', ' . $memberInfo->firstname }}" />
                                </div>
                                <div class="w-full">
                                    <x-label value="{{ __('Billing Month: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold"
                                        value="{{ \Carbon\Carbon::parse($selectedBill->billing_month)->format('F Y') }}" />
                                </div>
                            </div>

                            <div class="w-full">
                                <x-label value="{{ __('Bill Amount: ') }}" />
                            </div>
                            <div class="w-full text-right">
                                <x-label class="inline font-bold" value="{{ '₱' . $selectedBill->billing_amount }}" />
                            </div>

                            @if ($powasSettings->members_micro_savings > 0)
                                <div class="w-full">
                                    <x-label value="{{ __('Micro-Savings: ') }}" />
                                </div>
                                <div class="w-full text-right">
                                    <x-label class="inline font-bold"
                                        value="{{ '₱' . $powasSettings->members_micro_savings }}" />
                                </div>
                            @endif

                            <div class="w-full">
                                <x-label value="{{ __('Penalty: ') }}" />
                            </div>
                            <div class="w-full text-right">
                                <x-label class="inline font-bold" value="{{ '₱' . $selectedBill->penalty }}" />
                            </div>

                            <div class="w-full">
                                <x-label value="{{ __('Discount: ') }}" />
                            </div>
                            <div class="w-full text-right">
                                <x-label class="inline font-bold" value="{{ '₱' . $selectedBill->discount_amount }}" />
                            </div>

                            <div class="w-full">
                                <x-label value="{{ __('Excess Payment: ') }}" />
                            </div>
                            <div class="w-full text-right">
                                <x-label class="inline font-bold"
                                    value="{{ '₱' . number_format($excessPaymentFromDB, 2) }}" />
                            </div>

                            <form class="w-full grid grid-cols-2 gap-1 col-span-2" wire:submit="confirmSave" method="post">
                                @csrf
                                <div class="w-full flex items-center">
                                    <x-label value="{{ __('Payment Date: ') }}" />
                                </div>
                                <div class="w-full text-right">
                                    <x-input class="w-full" type="date" wire:model.live="paymentDate" autofocus />
                                </div>
                                <div class="w-full col-span-2">
                                    <x-input-error class="text-sm" for="paymentDate" />
                                </div>

                                @if ($daysPassedAfterDueDate > 0)
                                    <div class="w-full flex items-center">
                                        <x-label value="{{ __('After Due Date Penalty: ') }}" />
                                    </div>
                                    <div class="w-full text-right">
                                        <x-input class="w-full text-right" type="number"
                                            wire:model.live="afterDuePenalty" />
                                    </div>
                                    <div class="w-full col-span-2">
                                        <x-input-error class="text-sm" for="afterDuePenalty" />
                                    </div>
                                @endif

                                @if ($withReconnectionFee == true)
                                    <div class="w-full flex items-center">
                                        <x-label value="{{ __('Reconnection Fee: ') }}" />
                                    </div>
                                    <div class="w-full text-right">
                                        <x-input class="w-full text-right" type="number"
                                            wire:model.live="reconnectionFee" />
                                    </div>
                                    <div class="w-full col-span-2">
                                        <x-input-error class="text-sm" for="reconnectionFee" />
                                    </div>
                                @endif

                                <div class="w-full flex items-center">
                                    <x-label value="{{ __('Amount to Pay: ') }}" />
                                </div>
                                <div class="w-full text-right">
                                    <x-label class="inline font-bold"
                                        value="{{ '₱' . number_format($amountToPay, 2) }}" />
                                </div>

                                <div class="w-full flex items-center">
                                    <x-label value="{{ __('Payment Amount: ') }}" />
                                </div>
                                <div class="w-full text-right">
                                    <x-input class="w-full text-right" type="number" wire:model.live="paymentAmount" />
                                </div>
                                <div class="w-full col-span-2">
                                    <x-input-error class="text-sm" for="paymentAmount" />
                                </div>
                            </form>
                        </div>
                    @endif
                @endif
            @endslot
            @slot('footer')
                @can('create bill payment')
                    @if ($selectedBill->bill_status == 'UNPAID')
                        <x-secondary-button type="button" wire:click="confirmSave" wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </x-secondary-button>
                    @endif
                @endcan
                <x-danger-button class="ms-3" wire:click="$toggle('showingAddPaymentModal')"
                    wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-danger-button>
            @endslot
        </x-dialog-modal>

        <x-confirmation-modal wire:model.live="showingConfirmSaveModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Confirm Add Payment') }}
                </span>
            @endslot
            @slot('content')
                <div>
                    {{ __('Are you sure to want to save payment?') }}
                </div>

                @if (!Auth::user()->hasRole('admin'))
                    <div class="mt-2">
                        {{ __('Please note that this action is irreversible and any changes would require administrative approval.') }}
                    </div>
                @endif
            @endslot
            @slot('footer')
                @if ($isReceiptPrint == true && $isAutoPrint == true)
                    <x-button-link id="saveAndPrint" type="button" wire:click="savePayment"
                        href="{{ route('billing-receipts', ['billingIDs' => json_encode($toPrintReceipts)]) }}"
                        onclick="return openPopup('saveAndPrint');" wire:loading.attr="disabled">
                        {{ __('Save and Print') }}
                    </x-button-link>
                @else
                    <x-secondary-button type="button" wire:click="savePayment" wire:loading.attr="disabled">
                        {{ __('Yes') }}
                    </x-secondary-button>
                @endif

                <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmSaveModal')"
                    wire:loading.attr="disabled">
                    {{ __('No') }}
                </x-danger-button>
            @endslot
        </x-confirmation-modal>

        <x-confirmation-modal wire:model.live="showingConfirmPrintModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Confirm Receipt Printing') }}
                </span>
            @endslot

            @slot('content')
                <div>
                    {{ __('Do you want to print bills payment receipt?') }}
                </div>
                <div class="w-full grid grid-cols-2 mt-2">
                    <div>
                        <x-input type="checkbox" class="inline" wire:model.live="isReceiptPrint" />
                        <x-label class="inline" for="isReceiptPrint" value="{{ __('Print receipt') }}" />
                    </div>
                    <div>
                        <x-input type="checkbox" class="inline" wire:model.live="isAutoPrint"
                            disabled="{{ !$isReceiptPrint }}" />
                        <x-label class="inline" for="isAutoPrint" value="{{ __('Auto print') }}" />
                    </div>
                    <div class="w-full col-span-2">
                        <span>
                            {{ __('Resets when this page is reloaded or refreshed') }}
                        </span>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <x-button-link id="billReceipt" wire:click="$toggle('showingConfirmPrintModal')"
                    href="{{ route('billing-receipts', ['billingIDs' => json_encode($toPrintReceipts)]) }}"
                    onclick="return openPopup('billReceipt');" wire:loading.attr="disabled">
                    {{ __('Yes') }}
                </x-button-link>
                <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmPrintModal')"
                    wire:loading.attr="disabled">
                    {{ __('No') }}
                </x-danger-button>
            @endslot
        </x-confirmation-modal>
    @endif
</div>
