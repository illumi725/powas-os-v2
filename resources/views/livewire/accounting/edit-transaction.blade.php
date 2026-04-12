<div>
    <x-dialog-modal wire:model.live="showingEditTransactionModal" maxWidth="sm">
        @slot('title')
            {{ __('Edit Transaction') }}
        @endslot
        @slot('content')
            @if($showingEditTransactionModal)
            <form method="POST">
                @csrf
                <div>
                    <x-label for="accountName" value="{{ __('Account Name') }}" />
                    <x-input class="w-full bg-gray-100" type="text" value="{{ $accountName ? \App\Models\ChartOfAccounts::find($accountName)?->account_name : '' }}" disabled />
                    <p class="text-xs text-gray-500 mt-1">Account type cannot be changed</p>
                </div>
                <div class="mt-4">
                    <x-label for="transactionDate" value="{{ __('Transaction Date') }}" />
                    <x-input class="w-full" type="date" wire:model.live="transactionDate" />
                    <x-input-error class="text-sm" for="transactionDate" />
                </div>
                <div class="mt-4">
                    <x-label for="transactionAmount" value="{{ __('Transaction Amount') }}" />
                    <x-input class="w-full" type="number" step="0.01" wire:model.live="transactionAmount" />
                    <x-input-error class="text-sm" for="transactionAmount" />
                </div>
                <div class="mt-4">
                    <x-label for="receiveFromOrPaidTo" value="{{ __('Received From or Paid To') }}" />
                    <x-input class="w-full" type="text" wire:model.live="receiveFromOrPaidTo" />
                    <x-input-error class="text-sm" for="receiveFromOrPaidTo" />
                </div>

                <div class="mt-4">
                    <x-label for="transactionDescription" value="{{ __('Transaction Description') }}" />
                    <x-textarea class="w-full" wire:model.live="transactionDescription" />
                    <x-input-error class="text-sm" for="transactionDescription" />
                </div>

                @if ($transactionType == 'payments' || $transactionType == 'expenses')
                    <div class="mt-4">
                        <x-label for="receiptImage" value="{{ __('Receipt Image') }}" />
                        
                        @if ($existingReceiptImage)
                            <div class="mt-2 mb-3">
                                <p class="text-sm text-gray-600 mb-2">Current receipt:</p>
                                <img class="w-64 border rounded" src="{{ asset('storage/voucher_receipts/' . $existingReceiptImage) }}" alt="Current Receipt">
                            </div>
                        @endif
                        
                        <x-label value="{{ __('Upload New Receipt Image (optional)') }}" class="text-sm font-normal text-gray-600" />
                        <x-input id="receiptImage" class="block mt-1 w-full" type="file" wire:model="receiptImage"
                            accept="image/*" />
                        @if ($receiptImage)
                            <div class="mt-3 w-full">
                                <p class="text-sm text-green-600 mb-2">New receipt preview:</p>
                                <img class="w-64 border rounded" src="{{ $receiptImage->temporaryUrl() }}" alt="New Receipt Preview">
                            </div>
                        @endif
                        <x-input-error for="receiptImage" class="mt-1" />
                    </div>
                @endif
            </form>
            @endif
        @endslot
        @slot('footer')
            <x-button type="button" wire:click="showConfirmEditTransactionModal" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
            </x-button>
            <x-danger-button class="ms-3" wire:click="cancelEdit" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-danger-button>
        @endslot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.live="showingConfirmEditTransactionModal" maxWidth="sm">
        @slot('title')
            <span>
                {{ __('Confirm Edit Transaction') }}
            </span>
        @endslot
        @slot('content')
            @if($showingConfirmEditTransactionModal)
            <div>
                {{ __('Are you sure you want to update this transaction?') }}
            </div>

            <div class="mt-3 text-sm text-gray-600">
                <strong>Journal Entry Number:</strong> {{ $journalEntryNumber }}
            </div>

            @if ($originalAmount != $transactionAmount || $originalDate != $transactionDate)
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm font-semibold text-yellow-800 mb-1">Changes to be made:</p>
                    <ul class="text-sm text-gray-700 list-disc list-inside">
                        @if ($originalAmount != $transactionAmount)
                            <li>Amount: ₱{{ number_format($originalAmount, 2) }} → ₱{{ number_format($transactionAmount, 2) }}</li>
                        @endif
                        @if ($originalDate != $transactionDate)
                            <li>Date: {{ \Carbon\Carbon::parse($originalDate)->format('M d, Y') }} → {{ \Carbon\Carbon::parse($transactionDate)->format('M d, Y') }}</li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="mt-2 text-sm text-gray-500">
                {{ __('This will update all related records including vouchers and receipts.') }}
            </div>
            @endif
        @endslot>
        @slot('footer')
            <x-secondary-button type="button" wire:click="updateTransaction" wire:loading.attr="disabled">
                {{ __('Yes, Update') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="cancelEdit"
                wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-danger-button>
        @endslot>
    </x-confirmation-modal>
</div>
