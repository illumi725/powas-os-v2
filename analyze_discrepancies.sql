-- ============================================================================
-- ANALYZE REMAINING FINANCIAL STATEMENT DISCREPANCIES
-- ============================================================================
-- Issue: After deleting damayan transactions, still have discrepancies:
--   - January 2025: BALANCED ✓
--   - February-July 2025: 50 discrepancy
--   - August 2025-Present: -1110 discrepancy
--
-- Accounting Equation: ASSETS = LIABILITIES + EQUITY
-- Or checking: ASSETS - LIABILITIES - EQUITY = 0
-- ============================================================================

USE powas_os_app;

-- ============================================================================
-- STEP 1: MONTHLY TRANSACTION BALANCE CHECK
-- ============================================================================

-- Check if debits = credits for each month in 2025
SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    COUNT(*) as total_transactions,
    SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END) as total_debits,
    SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END) as total_credits,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END), 2) as imbalance
FROM transactions
WHERE YEAR(transaction_date) = 2025
GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
ORDER BY month;

-- ============================================================================
-- STEP 2: FIND UNBALANCED JOURNAL ENTRIES (Feb-July 2025)
-- ============================================================================

-- Find journal entries that don't balance in Feb-July period
SELECT 
    journal_entry_number,
    transaction_date,
    COUNT(*) as trxn_count,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END), 2) as debits,
    ROUND(SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END), 2) as credits,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END), 2) as imbalance,
    GROUP_CONCAT(DISTINCT account_number ORDER BY account_number) as accounts_used
FROM transactions
WHERE transaction_date BETWEEN '2025-02-01' AND '2025-07-31'
  AND journal_entry_number IS NOT NULL
GROUP BY journal_entry_number, transaction_date
HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 0.01
ORDER BY ABS(imbalance) DESC;

-- ============================================================================
-- STEP 3: FIND UNBALANCED JOURNAL ENTRIES (Aug 2025-Present)
-- ============================================================================

SELECT 
    journal_entry_number,
    transaction_date,
    COUNT(*) as trxn_count,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END), 2) as debits,
    ROUND(SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END), 2) as credits,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END), 2) as imbalance,
    GROUP_CONCAT(DISTINCT account_number ORDER BY account_number) as accounts_used
FROM transactions
WHERE transaction_date >= '2025-08-01'
  AND journal_entry_number IS NOT NULL
GROUP BY journal_entry_number, transaction_date
HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 0.01
ORDER BY ABS(imbalance) DESC;

-- ============================================================================
-- STEP 4: CHECK FOR ORPHAN TRANSACTIONS (No Journal Entry Number)
-- ============================================================================

SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    COUNT(*) as orphan_count,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END), 2) as orphan_debits,
    ROUND(SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END), 2) as orphan_credits,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END), 2) as orphan_imbalance
FROM transactions
WHERE journal_entry_number IS NULL
  AND YEAR(transaction_date) = 2025
GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
ORDER BY month;

-- ============================================================================
-- STEP 5: DETAILED VIEW OF PROBLEM TRANSACTIONS
-- ============================================================================

-- Show all transactions for journal entries with 50 imbalance (Feb-July)
SELECT 
    t.journal_entry_number,
    t.transaction_date,
    t.trxn_id,
    t.account_number,
    c.account_name,
    t.description,
    t.amount,
    t.transaction_side,
    t.member_id
FROM transactions t
LEFT JOIN chart_of_accounts c ON t.account_number = c.account_number
WHERE t.journal_entry_number IN (
    SELECT journal_entry_number
    FROM transactions
    WHERE transaction_date BETWEEN '2025-02-01' AND '2025-07-31'
      AND journal_entry_number IS NOT NULL
    GROUP BY journal_entry_number, transaction_date
    HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 49
       AND ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) < 51
)
ORDER BY t.journal_entry_number, t.transaction_side DESC, t.account_number;

-- Show all transactions for journal entries around -1110 imbalance (Aug+)
SELECT 
    t.journal_entry_number,
    t.transaction_date,
    t.trxn_id,
    t.account_number,
    c.account_name,
    t.description,
    t.amount,
    t.transaction_side,
    t.member_id
FROM transactions t
LEFT JOIN chart_of_accounts c ON t.account_number = c.account_number
WHERE t.journal_entry_number IN (
    SELECT journal_entry_number
    FROM transactions
    WHERE transaction_date >= '2025-08-01'
      AND journal_entry_number IS NOT NULL
    GROUP BY journal_entry_number, transaction_date
    HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 1100
)
ORDER BY t.journal_entry_number, t.transaction_side DESC, t.account_number;

-- ============================================================================
-- STEP 6: CHECK FOR REMAINING DAMAYAN TRACES
-- ============================================================================

-- Check if any Account 207 or damayan-related transactions remain
SELECT 
    'Account 207 Check' as check_type,
    COUNT(*) as count,
    MIN(transaction_date) as earliest,
    MAX(transaction_date) as latest,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END), 2) as total_debits,
    ROUND(SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END), 2) as total_credits
FROM transactions
WHERE account_number = '207'
  AND YEAR(transaction_date) = 2025;

-- Check for any "Central Fund" descriptions remaining
SELECT 
    'Central Fund Descriptions' as check_type,
    COUNT(*) as count,
    MIN(transaction_date) as earliest,
    MAX(transaction_date) as latest
FROM transactions
WHERE description LIKE '%Central Fund%'
  AND YEAR(transaction_date) = 2025;

-- ============================================================================
-- STEP 7: ACCOUNT BALANCE SUMMARY (for Feb-July period)
-- ============================================================================

-- Show net effect by account for the problematic period
SELECT 
    c.account_number,
    c.account_name,
    c.account_type,
    c.normal_balance,
    COUNT(t.trxn_id) as trxn_count,
    ROUND(SUM(CASE WHEN t.transaction_side = 'DEBIT' THEN t.amount ELSE 0 END), 2) as total_debits,
    ROUND(SUM(CASE WHEN t.transaction_side = 'CREDIT' THEN t.amount ELSE 0 END), 2) as total_credits,
    ROUND(SUM(CASE WHEN t.transaction_side = c.normal_balance THEN t.amount ELSE -t.amount END), 2) as net_balance
FROM chart_of_accounts c
LEFT JOIN transactions t ON c.account_number = t.account_number
    AND t.transaction_date BETWEEN '2025-02-01' AND '2025-07-31'
GROUP BY c.account_number
HAVING COUNT(t.trxn_id) > 0
ORDER BY c.account_type, c.account_number;

-- ============================================================================
-- STEP 8: SEARCH FOR SPECIFIC AMOUNT PATTERNS
-- ============================================================================

-- Look for transactions with amount = 50 (the Feb-July discrepancy)
SELECT 
    transaction_date,
    journal_entry_number,
    account_number,
    description,
    amount,
    transaction_side,
    member_id
FROM transactions
WHERE amount = 50
  AND transaction_date BETWEEN '2025-02-01' AND '2025-07-31'
ORDER BY transaction_date;

-- Look for transactions with amount = 1110 or 555 (half of -1110April discrepancy)
SELECT 
    transaction_date,
    journal_entry_number,
    account_number,
    description,
    amount,
    transaction_side,
    member_id
FROM transactions
WHERE amount IN (1110, 555, 370, 185)
  AND transaction_date >= '2025-08-01'
ORDER BY transaction_date, amount DESC;

-- ============================================================================
-- INSTRUCTIONS
-- ============================================================================
-- 1. Run Step 1 to see monthly imbalance
-- 2. Run Step 2 & 3 to find specific unbalanced journal entries
-- 3. Run Step 4 to check for orphan transactions
-- 4. Run Step 5 to see details of problematic transactions
-- 5. Run Step 6 to verify damayan is fully removed
-- 6. Run Step 7 & 8 to look for patterns
-- 
-- Once you identify the specific problematic transactions, you can:
-- - Delete them if they're erroneous
-- - Add missing offsetting transactions
-- - Correct amounts
-- ============================================================================
