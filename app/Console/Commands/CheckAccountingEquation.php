<?php

namespace App\Console\Commands;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAccountingEquation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-accounting-equation {--period= : Specific period to check (YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Assets = Liabilities + Equity (accounting equation balance)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');
        
        if ($period) {
            $this->checkPeriod($period);
        } else {
            $this->info('Checking all periods...');
            $this->newLine();
            
            // Get all unique periods from transactions
            $periods = Transactions::selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as period")
                ->distinct()
                ->orderBy('period')
                ->pluck('period');
            
            foreach ($periods as $period) {
                $this->checkPeriod($period);
            }
        }
    }
    
    private function checkPeriod($period)
    {
        $this->info("Checking period: {$period}");
        
        // Parse period - Fis.php uses the FIRST day of the month
        [$year, $month] = explode('-', $period);
        $firstDayOfMonth = date('Y-m-d', strtotime("{$year}-{$month}-01"));
        
        // Calculate "Previous Balance" date (end of previous month)
        // This matches Fis.php line 117: Carbon::parse($this->selectedMonthYear)->subDay()
        $previousBalanceDate = date('Y-m-d', strtotime($firstDayOfMonth . ' -1 day'));
        
        // Calculate end of current month for current balance
        $endDate = date('Y-m-t', strtotime($firstDayOfMonth));
        
        // Get all accounts with their types
        $accounts = ChartOfAccounts::all();
        
        $assetAccounts = $accounts->where('account_type', 'ASSET');
        $liabilityAccounts = $accounts->where('account_type', 'LIABILITY');
        $equityAccounts = $accounts->where('account_type', 'EQUITY');
        $revenueAccounts = $accounts->where('account_type', 'REVENUE');
        $expenseAccounts = $accounts->where('account_type', 'EXPENSE');
        
        // Calculate "Current Balance" (end of month) for balance sheet accounts
        // Note: Exclude account 302 (NET INCOME) from equity calculation
        // The financial statement uses calculated net income (revenues - expenses) instead
        $totalAssets = $this->calculateTotalBalance($assetAccounts, $endDate);
        $totalLiabilities = $this->calculateTotalBalance($liabilityAccounts, $endDate);
        $totalEquity = $this->calculateTotalBalance($equityAccounts->where('account_number', '!=', 302), $endDate);
        
        // Calculate Net Income (Revenue - Expenses) at end of month
        $totalRevenue = $this->calculateTotalBalance($revenueAccounts, $endDate);
        $totalExpenses = $this->calculateTotalBalance($expenseAccounts, $endDate);
        $netIncome = $totalRevenue - $totalExpenses;
        
        // True Equity includes Net Income (as shown in Fis.php line 216)
        $trueEquity = $totalEquity + $netIncome;
        
        // Check equation: Assets = Liabilities + Equity
        $leftSide = $totalAssets;
        $rightSide = $totalLiabilities + $trueEquity;
        $difference = $leftSide - $rightSide;
        
        $this->line("  Total Assets:      " . number_format($totalAssets, 2));
        $this->line("  Total Liabilities: " . number_format($totalLiabilities, 2));
        $this->line("  Total Equity:      " . number_format($totalEquity, 2));
        $this->line("  Net Income:        " . number_format($netIncome, 2));
        $this->line("  True Equity (E+NI):" . number_format($trueEquity, 2));
        $this->line("  L + E:             " . number_format($rightSide, 2));
        
        if (abs($difference) < 0.01) {
            $this->info("  ✓ BALANCED");
        } else {
            $this->error("  ✗ IMBALANCE: " . number_format($difference, 2));
            $this->warn("  Assets - (Liabilities + Equity) = {$difference}");
            
            // Detailed breakdown
            $this->newLine();
            $this->warn("  Detailed Asset Breakdown:");
            foreach ($assetAccounts as $account) {
                $balance = $this->calculateAccountBalance($account, $endDate);
                if (abs($balance) > 0.01) {
                    $this->line("    {$account->account_number} {$account->account_name}: " . number_format($balance, 2));
                }
            }
            
            $this->newLine();
            $this->warn("  Detailed Liability Breakdown:");
            foreach ($liabilityAccounts as $account) {
                $balance = $this->calculateAccountBalance($account, $endDate);
                if (abs($balance) > 0.01) {
                    $this->line("    {$account->account_number} {$account->account_name}: " . number_format($balance, 2));
                }
            }
            
            $this->newLine();
            $this->warn("  Detailed Equity Breakdown:");
            foreach ($equityAccounts as $account) {
                $balance = $this->calculateAccountBalance($account, $endDate);
                if (abs($balance) > 0.01) {
                    $this->line("    {$account->account_number} {$account->account_name}: " . number_format($balance, 2));
                }
            }
            
            $this->newLine();
            $this->warn("  Detailed Revenue Breakdown:");
            foreach ($revenueAccounts as $account) {
                $balance = $this->calculateAccountBalance($account, $endDate);
                if (abs($balance) > 0.01) {
                    $this->line("    {$account->account_number} {$account->account_name}: " . number_format($balance, 2));
                }
            }
            
            $this->newLine();
            $this->warn("  Detailed Expense Breakdown:");
            foreach ($expenseAccounts as $account) {
                $balance = $this->calculateAccountBalance($account, $endDate);
                if (abs($balance) > 0.01) {
                    $this->line("    {$account->account_number} {$account->account_name}: " . number_format($balance, 2));
                }
            }
        }
        
        $this->newLine();
    }
    
    private function calculateTotalBalance($accounts, $endDate)
    {
        $total = 0;
        
        foreach ($accounts as $account) {
            $total += $this->calculateAccountBalance($account, $endDate);
        }
        
        return $total;
    }
    
    private function calculateAccountBalance($account, $endDate)
    {
        // Get beginning balance from JSON
        $jsonPath = storage_path('app/beginning_balances/NEC-SJC-PIN-004.json');
        
        if (!file_exists($jsonPath)) {
            // If no beginning balances file, assume 0
            $beginningBalance = 0;
            $beginningBalanceDate = '1970-01-01';
        } else {
            $data = json_decode(file_get_contents($jsonPath), true);
            $dateKey = array_key_first($data);
            $balances = $data[$dateKey];
            $beginningBalance = floatval($balances[$account->account_number] ?? 0);
            $beginningBalanceDate = $dateKey; // e.g., '2023-12-31'
        }

        
        // Get transactions AFTER beginning balance date and up to end date
        // This matches Fis.php line 125: where('transaction_date', '>', $this->baseBalancesDate)
        $debits = Transactions::where('account_number', $account->account_number)
            ->where('transaction_side', 'DEBIT')
            ->where('transaction_date', '>', $beginningBalanceDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');
            
        $credits = Transactions::where('account_number', $account->account_number)
            ->where('transaction_side', 'CREDIT')
            ->where('transaction_date', '>', $beginningBalanceDate)
            ->where('transaction_date', '<=', $endDate)
            ->sum('amount');
        
        // Calculate balance based on account_type (matching Fis.php lines 132-144)
        // NOT based on normal_balance!
        if ($account->account_type == 'ASSET' || $account->account_type == 'EXPENSE') {
            // For Assets and Expenses: Debit increases, Credit decreases
            return $beginningBalance + $debits - $credits;
        } else {
            // For Liabilities, Equity, and Revenue: Credit increases, Debit decreases
            return $beginningBalance - $debits + $credits;
        }
    }


}
