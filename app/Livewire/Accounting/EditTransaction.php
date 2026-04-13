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
    public function showEditTransactionModal($journalEntryNumber, $date = null)
    {
        $this->journalEntryNumber = $journalEntryNumber;
        
        // Load transactions for this journal entry
        $query = Transactions::where('journal_entry_number', $journalEntryNumber)
            ->where('powas_id', $this->powasID);
            
        // If a date is provided, filter by its year and month to avoid JEN collisions across years
        if ($date) {
            $parsedDate = Carbon::parse($date);
            $query->whereYear('transaction_date', $parsedDate->year)
                  ->whereMonth('transaction_date', $parsedDate->month);
        }

        $this->originalTransactions = $query->orderBy('account_number', 'asc')->get();

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
            $correctionDate = $this->transactionDate ?? Carbon::now()->format('Y-m-d');
            $newJournalEntryNumber = CustomNumberFactory::journalEntryNumber($this->powasID, $correctionDate);
            $correctedByName = Auth::user()->userinfo
                ? Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname
                : Auth::user()->name;

            // Resolve member ID from the typed name
            $memberID = null;
            $queryMembers = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->selectRaw('CONCAT(powas_applications.lastname, ", ", powas_applications.firstname, " ", powas_applications.middlename) AS fullName, powas_members.member_id')
                ->get();
            $memberFullNames = [];
            foreach ($queryMembers as $m) {
                $memberFullNames[$m->fullName] = $m->member_id;
            }
            if (isset($memberFullNames[$this->receiveFromOrPaidTo])) {
                $memberID = $memberFullNames[$this->receiveFromOrPaidTo];
            }

            // STEP 1 – Reversing entries (flip DEBIT <-> CREDIT for each original line)
            foreach ($this->originalTransactions as $original) {
                $reverseSide = $original->transaction_side === 'DEBIT' ? 'CREDIT' : 'DEBIT';
                Transactions::create([
                    'trxn_id'              => CustomNumberFactory::getRandomID(),
                    'account_number'       => $original->account_number,
                    'description'          => '[REVERSAL of JE#' . $this->journalEntryNumber . '] ' . $original->description . ' — Corrected by ' . $correctedByName,
                    'or_number'            => $original->or_number,
                    'journal_entry_number' => $newJournalEntryNumber,
                    'amount'               => $original->amount,
                    'transaction_side'     => $reverseSide,
                    'received_from'        => $original->received_from,
                    'paid_to'              => $original->paid_to,
                    'member_id'            => $original->member_id,
                    'powas_id'             => $this->powasID,
                    'recorded_by_id'       => Auth::user()->user_id,
                    'transaction_date'     => $correctionDate,
                ]);
            }

            // STEP 2 – Corrected replacement entries (same sides as original, new values)
            $correctionJENumber = CustomNumberFactory::journalEntryNumber($this->powasID, $correctionDate);
            foreach ($this->originalTransactions as $original) {
                $accountRecord = ChartOfAccounts::find($original->account_number);
                $accountNameStr = $accountRecord ? $accountRecord->account_name : 'Unknown Account';
                if ($original->account_number == 101) {
                    $desc = ($this->transactionType === 'receipts')
                        ? 'Cash received from ' . $this->receiveFromOrPaidTo . ' for ' . strtoupper($this->transactionDescription)
                        : 'Cash paid to ' . $this->receiveFromOrPaidTo . ' for ' . strtoupper($this->transactionDescription);
                } else {
                    $desc = ($this->transactionType === 'receipts')
                        ? $accountNameStr . ' received from ' . $this->receiveFromOrPaidTo . ' for ' . $this->transactionDescription
                        : $accountNameStr . ' paid to ' . $this->receiveFromOrPaidTo . ' for ' . $this->transactionDescription;
                }
                Transactions::create([
                    'trxn_id'              => CustomNumberFactory::getRandomID(),
                    'account_number'       => $original->account_number,
                    'description'          => '[CORRECTION of JE#' . $this->journalEntryNumber . '] ' . $desc,
                    'or_number'            => $original->or_number,
                    'journal_entry_number' => $correctionJENumber,
                    'amount'               => $this->transactionAmount,
                    'transaction_side'     => $original->transaction_side,
                    'received_from'        => strtoupper($this->receiveFromOrPaidTo),
                    'paid_to'              => strtoupper($this->receiveFromOrPaidTo),
                    'member_id'            => $memberID,
                    'powas_id'             => $this->powasID,
                    'recorded_by_id'       => Auth::user()->user_id,
                    'transaction_date'     => $correctionDate,
                ]);
            }

            // STEP 3 – Replace voucher receipt image if provided
            if ($this->receiptImage) {
                $voucher = null;
                foreach ($this->originalTransactions as $trxn) {
                    $foundVoucher = Vouchers::where('trxn_id', $trxn->trxn_id)->first();
                    if ($foundVoucher) { $voucher = $foundVoucher; break; }
                }
                if ($voucher) {
                    $voucherReceipt = VoucherExpenseReceipts::where('voucher_id', $voucher->voucher_id)->first();
                    if ($voucherReceipt && $voucherReceipt->receipt_path) {
                        Storage::disk('public')->delete('voucher_receipts/' . $voucherReceipt->receipt_path);
                    }
                    $this->receiptImage->storeAs('voucher_receipts', $voucher->voucher_id . '.' . $this->receiptImage->extension(), 'public');
                    $newPath = $voucher->voucher_id . '.' . $this->receiptImage->extension();
                    $voucherReceipt
                        ? $voucherReceipt->update(['receipt_path' => $newPath])
                        : VoucherExpenseReceipts::create(['voucher_id' => $voucher->voucher_id, 'receipt_path' => $newPath]);
                }
            }

            // STEP 4 – Audit log
            $changes = [];
            if ($this->originalAmount != $this->transactionAmount) {
                $changes[] = 'amount from ₱' . number_format($this->originalAmount, 2) . ' to ₱' . number_format($this->transactionAmount, 2);
            }
            if ($this->originalDate != $correctionDate) {
                $changes[] = 'date from ' . Carbon::parse($this->originalDate)->format('M d, Y') . ' to ' . Carbon::parse($correctionDate)->format('M d, Y');
            }
            if (!str_contains($this->originalDescription, $this->transactionDescription)) {
                $changes[] = 'description';
            }
            $changesText = !empty($changes) ? 'Changed ' . implode(', ', $changes) : 'Correction via reversing entry';
            $accountRecordForLog = ChartOfAccounts::find($this->accountName);
            $accountNameForLog = $accountRecordForLog ? $accountRecordForLog->account_name : 'Unknown Account';
            ActionLogger::dispatch(
                'update',
                '<b><u>' . $correctedByName . '</u></b> issued reversing+correction entries for JE#' . $this->journalEntryNumber . ' (<b><i>' . strtoupper($accountNameForLog) . '</i></b>). ' . $changesText . '. New Correction JE#: ' . $correctionJENumber,
                Auth::user()->user_id,
                'transactions',
                $this->powasID
            );
        });

        $this->dispatch('alert', [
            'message' => 'Correction posted as reversing+correcting journal entries. Original entry is preserved.',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->showingConfirmEditTransactionModal = false;
        $this->dispatch('transaction-updated');

        $this->reset([
            'journalEntryNumber', 'transactionType', 'accountName',
            'transactionAmount', 'transactionDescription', 'receiptImage',
            'existingReceiptImage', 'transactionDate', 'receiveFromOrPaidTo',
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
