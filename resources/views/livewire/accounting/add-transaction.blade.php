<div class="inline">
    <x-button class="block w-full md:w-auto md:inline mt-2 md:mt-0" type="button" wire:click="showAddTransactionModal"
        wire:loading.attr="disabled">
        <span>&nbsp;{{ __('Add Transaction') }}</span>
    </x-button>

    <x-dialog-modal wire:model.live="showingAddTransactionModal" maxWidth="sm">
        @slot('title')
            {{ __('Add Transaction') }}
        @endslot
        @slot('content')
            {{-- Bulk Upload Section --}}
            <div class="mb-6 p-4 border rounded-lg bg-indigo-50 border-indigo-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-indigo-900">{{ __('Bulk Upload') }}</h3>
                    <button type="button" wire:click="downloadTemplate" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Template
                    </button>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <input type="file" wire:model="importFile" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-xs file:font-semibold
                            file:bg-indigo-100 file:text-indigo-700
                            hover:file:bg-indigo-200
                        "/>
                        <x-input-error for="importFile" class="mt-1" />
                    </div>
                    
                    <div class="flex justify-end">
                        <x-button type="button" wire:click="importTransactions" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Import Excel') }}
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-4 text-gray-500 text-sm font-medium">{{ __('OR Add Manually') }}</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <form wire:submit="showConfirmSaveTransaction" method="POST">
                @csrf
                <div>
                    <x-label for="transactionType" value="{{ __('Transaction Type') }}" />
                    <x-combobox wire:model.live="transactionType" class="w-full" wire:change="updateAccountTypeSelection">
                        @slot('options')
                            <option value="" selected disabled>{{ __('-Select Transaction Type-') }}</option>
                            <option value="bank">{{ __('Bank Transaction') }}</option>
                            <option value="receipts">{{ __('Receipts') }}</option>
                            <option value="payments">{{ __('Payments') }}</option>
                            <option value="expenses">{{ __('Expenses') }}</option>
                        @endslot
                    </x-combobox>
                    <x-input-error class="text-sm" for="transactionType" />
                </div>
                <div class="mt-4">
                    <x-label for="accountName" value="{{ __('Account Name') }}" />
                    <x-combobox wire:model.live="accountName" class="w-full">
                        @slot('options')
                            <option value="" selected disabled>{{ __('-Select Account Name-') }}</option>

                            @if ($transactionType == 'bank')
                                <option value="deposit">{{ __('DEPOSIT') }}</option>
                                <option value="withdraw">{{ __('WITHDRAW') }}</option>
                            @else
                                @foreach ($accountNameList as $accountNum => $account)
                                    <option value="{{ $accountNum }}">{{ $account['account_name'] }}</option>
                                @endforeach
                            @endif
                        @endslot
                    </x-combobox>
                    <x-input-error class="text-sm" for="accountName" />
                    
                    {{-- Conditional Attachment Links --}}
                    @if(in_array($accountName, ['502', '503', '507']))
                        <div class="mt-2 text-sm text-indigo-600 hover:text-indigo-900">
                            <a href="{{ route('allowance-attachment') }}" target="_blank" class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                                Generate Allowance Disbursement Attachment
                            </a>
                        </div>
                    @elseif($accountName == '505')
                        <div class="mt-2 text-sm text-indigo-600 hover:text-indigo-900">
                            <a href="{{ route('printing-expenses-attachment') }}" target="_blank" class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                                Generate Printing Expenses Attachment
                            </a>
                        </div>
                    @elseif(in_array($accountName, ['201', '206', '208', '209', '508', '509', '510']))
                         <div class="mt-2 text-sm text-indigo-600 hover:text-indigo-900">
                            <a href="{{ route('acknowledgement-receipt') }}" target="_blank" class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Generate Acknowledgement Receipt
                            </a>
                        </div>
                    @endif
                </div>
                <div class="mt-4">
                    <x-label for="transactionDate" value="{{ __('Transaction Date') }}" />
                    <x-input class="w-full" type="date" wire:model.live="transactionDate" />
                    <x-input-error class="text-sm" for="transactionDate" />
                </div>
                <div class="mt-4">
                    <x-label for="transactionAmount" value="{{ __('Transaction Amount') }}" />
                    <x-input class="w-full" type="number" wire:model.live="transactionAmount" />
                    <x-input-error class="text-sm" for="transactionAmount" />
                </div>
                <div class="mt-4">
                    <x-label for="receiveFromOrPaidTo" value="{{ __('Received From or Paid To') }}" />
                    <x-input class="w-full" type="text" wire:model.live="receiveFromOrPaidTo" list="membersName" />
                    <datalist id="membersName">
                        @foreach ($members as $key => $value)
                            <option value="{{ $value->lastname . ', ' . $value->firstname . ' ' . $value->middlename }}">
                            </option>
                        @endforeach
                    </datalist>
                    <x-input-error class="text-sm" for="receiveFromOrPaidTo" />
                </div>

                <div class="mt-4">
                    <x-label for="transactionDescription" value="{{ __('Transaction Description') }}" />
                    <x-textarea class="w-full" type="text" wire:model.live="transactionDescription" />
                    <x-input-error class="text-sm" for="transactionDescription" />
                </div>

                @if ($transactionType == 'payments' || $transactionType == 'expenses')
                    <div class="mt-4">
                        <x-label for="receiptImageRaw" value="{{ __('Upload Receipt Image') }}" />
                        <input
                            id="receiptImageRaw"
                            type="file"
                            accept="image/*"
                            class="block mt-1 w-full text-sm text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                            onchange="powasCompressReceipt(this, @this)">
                        <p id="receiptCompressStatus" class="mt-1 text-xs text-gray-500 hidden"></p>
                        @if ($receiptImage)
                            <div class="mt-3 w-full text-center">
                                <img class="w-96 mx-auto" src="{{ $receiptImage->temporaryUrl() }}" alt="Preview">
                            </div>
                        @endif
                        <x-input-error for="receiptImage" class="mt-1" />
                    </div>
                @endif
            </form>
        @endslot
        @slot('footer')
            <x-button type="button" wire:click="showConfirmAddTransactionModal" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>
            <x-danger-button class="ms-3" wire:click="$toggle('showingAddTransactionModal')" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-danger-button>
        @endslot
    </x-dialog-modal>

    <x-confirmation-modal wire:model.live="showingConfirmAddTrasactionModal" maxWidth="sm">
        @slot('title')
            <span>
                {{ __('Confirm Add Transaction') }}
            </span>
        @endslot
        @slot('content')
            <div>
                {{ __('Are you sure to want to save transaction?') }}
            </div>

            @if (!Auth::user()->hasRole('admin'))
                <div class="mt-2">
                    {{ __('Please note that this action is irreversible and any changes would require administrative approval.') }}
                </div>
            @endif
        @endslot
        @slot('footer')
            <x-secondary-button type="button" wire:click="transact{{ $accountName }}" wire:loading.attr="disabled">
                {{ __('Yes') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmAddTrasactionModal')"
                wire:loading.attr="disabled">
                {{ __('No') }}
            </x-danger-button>
        @endslot
    </x-confirmation-modal>

    <x-confirmation-modal wire:model.live="showingPrintVoucherConfirmation" maxWidth="sm">
        @slot('title')
            <span>
                {{ __('Confirm Print Voucher') }}
            </span>
        @endslot
        @slot('content')
            <div>
                {{ __('Do you want to print voucher for this transaction?') }}
            </div>
        @endslot
        @slot('footer')
            <x-button-link id="voucherPrint" onclick="return openPopup('voucherPrint');"
                wire:click="$toggle('showingPrintVoucherConfirmation')"
                href="{{ route('print-voucher', ['powasID' => $powasID, 'voucherID' => $toPrintVoucher]) }}"
                wire:loading.attr="disabled">
                {{ __('Yes') }}
            </x-button-link>

            <x-danger-button class="ms-3" wire:click="$toggle('showingPrintVoucherConfirmation')"
                wire:loading.attr="disabled">
                {{ __('No') }}
            </x-danger-button>
        @endslot
    </x-confirmation-modal>

    @if ($printIDs != null && $trxnIDs != null)
        {{-- Print Receipt --}}
        <x-confirmation-modal wire:model.live="printing" maxWidth="sm">
            <x-slot name="title">
                {{ __('Print Receipt') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Do you want to print receipt?') }}
            </x-slot>

            <x-slot name="footer">
                @if (count($trxnIDs))
                    <x-button-link id="receiptLink" wire:click="$toggle('printing')"
                        href="{{ route('other-receipt.view', ['trxnID' => json_encode($trxnIDs), 'printID' => json_encode($printIDs), 'receiptNumber' => $receiptNumber, 'powasID' => $powasID]) }}"
                        wire:loading.attr="disabled" onclick="return openPopup('receiptLink');"
                        wire:loading.attr="disabled">
                        <i class="fa-solid fa-check"></i>&nbsp;
                        {{ __('Yes') }}
                    </x-button-link>
                @endif

                <x-danger-button class="ms-3" wire:click="$toggle('printing')" wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                    {{ __('No') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    @endif

    {{-- Bulk Import Preview Modal --}}
    <x-dialog-modal wire:model.live="showingBulkPreviewModal" maxWidth="2xl">
        @slot('title')
            {{ __('Verify and Attach Files') }}
        @endslot
        @slot('content')
            <div class="mt-4">
                <p class="text-sm text-gray-600 mb-4">{{ __('Please verify the transactions below and upload receipt images where necessary. When ready, click "Process All".') }}</p>
                
                <div class="overflow-x-auto border rounded-lg max-h-96">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="bg-gray-400 border border-collapse sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-black font-semibold">Type</th>
                                <th class="px-4 py-2 text-black font-semibold">Account</th>
                                <th class="px-4 py-2 text-black font-semibold">Date</th>
                                <th class="px-4 py-2 text-black font-semibold">Amount</th>
                                <th class="px-4 py-2 text-black font-semibold">Description</th>
                                <th class="px-4 py-2 text-black font-semibold">Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingTransactions as $index => $row)
                            <tr class="border-b even:bg-gray-100 odd:bg-slate-200 hover:bg-gray-300">
                                <td class="px-4 py-2 font-medium text-gray-900 border-r">{{ $row['transaction_type'] }}</td>
                                <td class="px-4 py-2 border-r">
                                    <div class="font-semibold text-indigo-700">{{ $row['account_name'] }}</div>
                                    <div class="text-xs text-gray-600 font-bold">{{ $row['received_from_paid_to'] }}</div>
                                </td>
                                <td class="px-4 py-2 border-r">{{ isset($row['date_yyyy_mm_dd']) ? \Carbon\Carbon::parse($row['date_yyyy_mm_dd'])->format('M d, Y') : '-' }}</td>
                                <td class="px-4 py-2 font-mono border-r">₱{{ number_format((float)$row['amount'], 2) }}</td>
                                <td class="px-4 py-2 border-r max-w-xs truncate" title="{{ $row['description'] }}">{{ $row['description'] }}</td>
                                <td class="px-4 py-2">
                                    @if(in_array($row['transaction_type'], ['Expenses', 'Payments']))
                                        <input type="file" wire:model="bulkAttachments.{{ $index }}" 
                                            class="block w-full text-xs text-slate-500
                                            file:mr-2 file:py-1 file:px-2
                                            file:rounded-full file:border-0
                                            file:text-xs file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100" accept="image/*" />
                                        @if(isset($bulkAttachments[$index]))
                                            <span class="text-xs text-green-600 font-bold mt-1 block">Attached ✓</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic text-xs">Not required</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endslot
        @slot('footer')
             <x-button type="button" wire:click="saveBulkTransactions" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700">
                {{ __('Process All Transactions') }}
            </x-button>

            <x-danger-button class="ms-3" wire:click="cancelBulkImport" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-danger-button>
        @endslot
    </x-dialog-modal>
</div>

<script>
function powasCompressReceipt(input, component) {
    const file = input.files[0];
    if (!file) return;

    const statusEl = document.getElementById('receiptCompressStatus');
    statusEl.classList.remove('hidden');

    const TARGET_KB = 1024; // 1 MB target
    const MAX_DIM   = 1600; // max pixel dimension
    const QUALITY   = 0.80; // JPEG quality

    // If already small enough, upload directly without re-encoding
    if (file.size <= TARGET_KB * 1024) {
        statusEl.textContent = '📎 Uploading...';
        component.upload(
            'receiptImage', file,
            () => { statusEl.textContent = '✅ Image ready (' + (file.size / 1024).toFixed(0) + ' KB)'; },
            () => { statusEl.textContent = '❌ Upload failed. Try again.'; },
            (pct) => { statusEl.textContent = '⏳ Uploading... ' + pct + '%'; }
        );
        return;
    }

    statusEl.textContent = '⏳ Compressing image...';

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            let w = img.width, h = img.height;

            // Scale down proportionally
            if (w > MAX_DIM || h > MAX_DIM) {
                if (w >= h) { h = Math.round(h * MAX_DIM / w); w = MAX_DIM; }
                else        { w = Math.round(w * MAX_DIM / h); h = MAX_DIM; }
            }

            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            canvas.toBlob(function (blob) {
                const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });
                const kb = (compressed.size / 1024).toFixed(0);
                statusEl.textContent = '⏳ Uploading compressed image (' + kb + ' KB)...';

                component.upload(
                    'receiptImage', compressed,
                    () => { statusEl.textContent = '✅ Image ready (' + kb + ' KB)'; },
                    () => { statusEl.textContent = '❌ Upload failed. Try again.'; },
                    (pct) => { statusEl.textContent = '⏳ Uploading... ' + pct + '%'; }
                );
            }, 'image/jpeg', QUALITY);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>
