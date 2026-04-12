<?php

namespace App\Livewire\Accounting;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\BankSlipPictures;
use App\Models\ChartOfAccounts;
use App\Models\IssuedReceipts;
use App\Models\PowasMembers;
use App\Models\Transactions;
use App\Models\User;
use App\Models\VoucherExpenseReceipts;
use App\Models\Vouchers;
use App\Models\VouchersParticulars;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditTransaction extends Component
{
    use WithFileUploads;
    
    public $powasID;
    public $powas;
    public $journalEntryNumber;
    public $showingEditTransactionModal = false;
    public $showingConfirmEditTransactionModal = false;
    
    // Transaction data
    public $transactionType = '';
    public $accountName = '';
    public $transactionAmount = '';
    public $transactionDescription = '';
    public $receiptImage;
    public $existingReceiptImage;
    public $transactionDate;
    public $receiveFromOrPaidTo;
    
    // For vouchers
    public $powasOfficers;
    public $preparedBy = null;
    public $checkedBy = null;
    public $approvedBy = null;
    
    // Original data (for comparison/audit)
    public $originalTransactions;
    public $originalAmount;
    public $originalDate;
    public $originalDescription;
    
    public function mount($powasID, $powas)
    {
        $this->powas = $powas;
        $this->powasID = $powasID;

        $this->powasOfficers = User::with('roles')
            ->join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
            ->where('users.powas_id', $this->powasID)
            ->where(function ($query) {
                $query->where('account_status', 'ACTIVE')
                    ->orWhere('account_status', 'INACTIVE');
            })
            ->get();

        if (count($this->powasOfficers) != 0) {
            foreach ($this->powasOfficers as $key => $value) {
                if ($value->hasRole('secretary')) {
                    $this->preparedBy = $value->user_id;
                }
                if ($value->hasRole('treasurer')) {
                    $this->checkedBy = $value->user_id;
                }
                if ($value->hasRole('president')) {
                    $this->approvedBy = $value->user_id;
                }
            }
        }
    }

    #[On('showEdit')]
    public function showEditTransactionModal($journalEntryNumber)
    {
        $this->journalEntryNumber = $journalEntryNumber;
        
        // Load transactions for this journal entry
        $this->originalTransactions = Transactions::where('journal_entry_number', $journalEntryNumber)
            ->where('powas_id', $this->powasID)
            ->orderBy('account_number', 'asc')
            ->get();

        if ($this->originalTransactions->isEmpty()) {
            $this->dispatch('alert', [
                'message' => 'Transaction not found!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
            return;
        }

        // Determine transaction type and load data
        $firstTransaction = $this->originalTransactions->first();
        
        // Store original values for audit
        $this->originalAmount = $firstTransaction->amount;
        $this->originalDate = $firstTransaction->transaction_date;
        $this->originalDescription = $firstTransaction->description;
        
        // Pre-populate form fields
        $this->transactionDate = $firstTransaction->transaction_date;
        $this->transactionAmount = $firstTransaction->amount;
        $this->receiveFromOrPaidTo = $firstTransaction->received_from ?? $firstTransaction->paid_to;
        
        // Determine transaction type based on account numbers
        $accountNumbers = $this->originalTransactions->pluck('account_number')->toArray();
        
        // Income/receipts: will have accounts like 406, 408, 202-209 with CREDIT
        // Payments: will have liability accounts with DEBIT  
        // Expenses: will have expense accounts (501-512)
        
        $hasIncomeAccount = in_array(406, $accountNumbers) || in_array(408, $accountNumbers);
        $hasLiabilityCredit = false;
        $hasExpenseAccount = false;
        
        foreach ($this->originalTransactions as $trxn) {
            if (in_array($trxn->account_number, [202, 203, 204, 205, 206, 209]) && $trxn->transaction_side == 'CREDIT') {
                $hasLiabilityCredit = true;
            }
            if ($trxn->account_number >= 501 && $trxn->account_number <= 512) {
                $hasExpenseAccount = true;
            }
        }
        
        if ($hasIncomeAccount || $hasLiabilityCredit) {
            $this->transactionType = 'receipts';
        } elseif ($hasExpenseAccount) {
            $this->transactionType = 'expenses';
        } else {
            $this->transactionType = 'payments';
        }
        
        // Find the main account (not cash/101)
        $mainTransaction = $this->originalTransactions->firstWhere('account_number', '!=', 101);
        if ($mainTransaction) {
            $this->accountName = $mainTransaction->account_number;
            
            // Extract description (remove the standard prefix/suffix)
            $desc = $mainTransaction->description;
            if ($this->transactionType == 'receipts') {
                // Pattern: "ACCOUNT received from NAME for DESCRIPTION"
                if (preg_match('/received from .+ for (.+)$/i', $desc, $matches)) {
                    $this->transactionDescription = trim($matches[1]);
                } else {
                    $this->transactionDescription = $desc;
                }
            } else {
                // Pattern: "ACCOUNT paid to NAME for DESCRIPTION"
                if (preg_match('/paid to .+ for (.+)$/i', $desc, $matches)) {
                    $this->transactionDescription = trim($matches[1]);
                } else if (preg_match('/for (.+)$/i', $desc, $matches)) {
                    $this->transactionDescription = trim($matches[1]);
                } else {
                    $this->transactionDescription = $desc;
                }
            }
        }
        
        // Load existing receipt image if payment/expense
        if ($this->transactionType == 'payments' || $this->transactionType == 'expenses') {
            $voucher = Vouchers::where('trxn_id', $firstTransaction->trxn_id)->first();
            if ($voucher) {
                $voucherReceipt = VoucherExpenseReceipts::where('voucher_id', $voucher->voucher_id)->first();
                if ($voucherReceipt) {
                    $this->existingReceiptImage = $voucherReceipt->receipt_path;
                }
            }
        }
        
        $this->showingEditTransactionModal = true;
    }

    public function showConfirmEditTransactionModal()
    {
        $this->validate([
            'transactionAmount' => 'required|numeric|min:0.01',
            'transactionDate' => 'required|date',
            'transactionDescription' => 'required',
            'receiveFromOrPaidTo' => 'required',
        ]);

        if ($this->transactionType == 'expenses' || $this->transactionType == 'payments') {
            // Receipt image is optional if already exists, required if doesn't
            if (!$this->existingReceiptImage && !$this->receiptImage) {
                $this->validate([
                    'receiptImage' => 'required|image|max:2048',
                ]);
            } elseif ($this->receiptImage) {
                $this->validate([
                    'receiptImage' => 'image|max:2048',
                ]);
            }
        }

        $this->showingConfirmEditTransactionModal = true;
        $this->showingEditTransactionModal = false;
    }

    public function updateTransaction()
    {
        DB::transaction(function () {
            // Get member ID if applicable
            $memberID = null;
            $memberFullNames = [];

            if (strlen($this->receiveFromOrPaidTo) != 0 || $this->receiveFromOrPaidTo != '') {
                $queryMembers = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                    ->selectRaw('CONCAT(powas_applications.lastname, ", ", powas_applications.firstname, " ", powas_applications.middlename) AS fullName, powas_members.member_id')
                    ->get();

                foreach ($queryMembers as $key => $value) {
                    $memberFullNames[$value->fullName] = $value->member_id;
                }

                if (isset($memberFullNames[$this->receiveFromOrPaidTo])) {
                    $memberID = $memberFullNames[$this->receiveFromOrPaidTo];
                }
            }

            // Update all transaction rows for this journal entry
            foreach ($this->originalTransactions as $transaction) {
                $description = '';
                
                // Reconstruct description based on account and type
                if ($transaction->account_number == 101) {
                    // Cash account
                    if ($this->transactionType == 'receipts') {
                        $description = 'Cash received from ' . $this->receiveFromOrPaidTo . ' for ' . strtoupper($this->transactionDescription);
                    } else {
                        $description = 'Cash paid to ' . $this->receiveFromOrPaidTo . ' for ' . strtoupper($this->transactionDescription);
                    }
                } else {
                    // Main account
                    $accountRecord = ChartOfAccounts::find($transaction->account_number);
                    $accountName = $accountRecord ? $accountRecord->account_name : 'Unknown Account';
                    if ($this->transactionType == 'receipts') {
                        $description = $accountName . ' received from ' . $this->receiveFromOrPaidTo . ' for ' . $this->transactionDescription;
                    } else {
                        $description = $accountName . ' paid to ' . $this->receiveFromOrPaidTo . ' for ' . $this->transactionDescription;
                    }
                }

                $transaction->update([
                    'description' => $description,
                    'amount' => $this->transactionAmount,
                    'received_from' => strtoupper($this->receiveFromOrPaidTo),
                    'paid_to' => strtoupper($this->receiveFromOrPaidTo),
                    'member_id' => $memberID,
                    'transaction_date' => $this->transactionDate,
                ]);
            }

            // Update related records
            $firstTransaction = $this->originalTransactions->first();

            // Update IssuedReceipts if exists
            $issuedReceipt = null;
            foreach ($this->originalTransactions as $trxn) {
                $foundReceipt = IssuedReceipts::where('trxn_id', $trxn->trxn_id)->first();
                if ($foundReceipt) {
                    $issuedReceipt = $foundReceipt;
                    break;
                }
            }
            
            if ($issuedReceipt) {
                $issuedReceipt->update([
                    'description' => strtoupper($this->transactionDescription),
                    'transaction_date' => $this->transactionDate,
                ]);
            }

            // Update Voucher if exists
            // Iterate through all transactions to find the one linked to a voucher
            $voucher = null;
            foreach ($this->originalTransactions as $trxn) {
                $foundVoucher = Vouchers::where('trxn_id', $trxn->trxn_id)->first();
                if ($foundVoucher) {
                    $voucher = $foundVoucher;
                    break;
                }
            }

            if ($voucher) {
                $voucher->update([
                    'amount' => $this->transactionAmount,
                    'received_by' => strtoupper($this->receiveFromOrPaidTo),
                    'voucher_date' => $this->transactionDate,
                ]);

                // Update VoucherParticulars
                $voucherParticulars = VouchersParticulars::where('voucher_id', $voucher->voucher_id)->first();
                if ($voucherParticulars) {
                    $voucherParticulars->update([
                        'description' => strtoupper($this->transactionDescription),
                    ]);
                }

                // Update VoucherExpenseReceipts if new image uploaded
                if ($this->receiptImage) {
                    $voucherReceipt = VoucherExpenseReceipts::where('voucher_id', $voucher->voucher_id)->first();
                    
                    // Delete old image
                    if ($voucherReceipt && $voucherReceipt->receipt_path) {
                        Storage::disk('public')->delete('voucher_receipts/' . $voucherReceipt->receipt_path);
                    }

                    // Store new image
                    $this->receiptImage->storeAs('voucher_receipts', $voucher->voucher_id . '.' . $this->receiptImage->extension(), 'public');

                    if ($voucherReceipt) {
                        $voucherReceipt->update([
                            'receipt_path' => $voucher->voucher_id . '.' . $this->receiptImage->extension(),
                        ]);
                    } else {
                        VoucherExpenseReceipts::create([
                            'voucher_id' => $voucher->voucher_id,
                            'receipt_path' => $voucher->voucher_id . '.' . $this->receiptImage->extension(),
                        ]);
                    }
                }
            }

            // Log the edit action
            $changes = [];
            if ($this->originalAmount != $this->transactionAmount) {
                $changes[] = 'amount from ₱' . number_format($this->originalAmount, 2) . ' to ₱' . number_format($this->transactionAmount, 2);
            }
            if ($this->originalDate != $this->transactionDate) {
                $changes[] = 'date from ' . Carbon::parse($this->originalDate)->format('M d, Y') . ' to ' . Carbon::parse($this->transactionDate)->format('M d, Y');
            }
            if (!str_contains($this->originalDescription, $this->transactionDescription)) {
                $changes[] = 'description';
            }

            $changesText = !empty($changes) ? 'Changed ' . implode(', ', $changes) : 'Updated transaction details';

            $userLastname = Auth::user()->userinfo ? Auth::user()->userinfo->lastname : Auth::user()->name;
            $userFirstname = Auth::user()->userinfo ? Auth::user()->userinfo->firstname : '';
            
            $accountRecordForLog = ChartOfAccounts::find($this->accountName);
            $accountNameForLog = $accountRecordForLog ? $accountRecordForLog->account_name : 'Unknown Account';

            $log_message = '<b><u>' . $userLastname . ', ' . $userFirstname . '</u></b> edited transaction for <b><i>' . strtoupper($accountNameForLog) . '</i></b> (Journal Entry: ' . $this->journalEntryNumber . '). ' . $changesText . '.';

            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        });

        $this->dispatch('alert', [
            'message' => 'Transaction successfully updated!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
        
        $this->showingConfirmEditTransactionModal = false;
        $this->dispatch('transaction-updated');
        
        // Reset form
        $this->reset([
            'journalEntryNumber',
            'transactionType',
            'accountName',
            'transactionAmount',
            'transactionDescription',
            'receiptImage',
            'existingReceiptImage',
            'transactionDate',
            'receiveFromOrPaidTo',
            'originalTransactions',
        ]);
    }

    public function cancelEdit()
    {
        $this->showingEditTransactionModal = false;
        $this->showingConfirmEditTransactionModal = false;
        
        $this->reset([
            'journalEntryNumber',
            'transactionType',
            'accountName',
            'transactionAmount',
            'transactionDescription',
            'receiptImage',
            'existingReceiptImage',
            'transactionDate',
            'receiveFromOrPaidTo',
            'originalTransactions',
        ]);
    }

    public function render()
    {
        return view('livewire.accounting.edit-transaction');
    }
}
