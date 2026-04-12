-- ============================================================================
-- FIX JOURNAL ENTRY NUMBER DUPLICATES & DAMAYAN TRANSACTIONS
-- ============================================================================
-- Purpose: 
--   1. Fix duplicate journal entry numbers in existing transactions
--   2. Remove erroneous Account 207 transactions created after damayan 
--      was abolished (June 2024)
--
-- Background:
--   - Journal entry number generation had a bug causing duplicates
--   - Damayan (death benefit assistance) was abolished around June 2024
--   - The system continued creating Account 207 transactions automatically
--
-- WARNING: This script will MODIFY transactions. BACKUP your database first!
-- ============================================================================

USE powas_os_app;

-- ============================================================================
-- PART 1: ANALYZE JOURNAL ENTRY NUMBER ISSUES
-- ============================================================================

-- Step 1A: Find duplicate journal entry numbers
SELECT 
    journal_entry_number,
    COUNT(DISTINCT trxn_id) as transaction_count,
    DATE_FORMAT(MIN(transaction_date), '%Y-%m-%d') as first_date,
    DATE_FORMAT(MAX(transaction_date), '%Y-%m-%d') as last_date,
    GROUP_CONCAT(DISTINCT powas_id) as powas_ids
FROM transactions
WHERE journal_entry_number IS NOT NULL
GROUP BY journal_entry_number
HAVING COUNT(DISTINCT trxn_id) > 10  -- Adjust threshold as needed
ORDER BY transaction_count DESC, journal_entry_number;

-- Step 1B: Analyze journal entry sequence by month
SELECT 
    powas_id,
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    COUNT(DISTINCT journal_entry_number) as unique_journal_numbers,
    COUNT(*) as total_transactions,
    MIN(journal_entry_number) as first_number,
    MAX(journal_entry_number) as last_number
FROM transactions
WHERE journal_entry_number IS NOT NULL
GROUP BY powas_id, DATE_FORMAT(transaction_date, '%Y-%m')
ORDER BY powas_id, month DESC;

-- Step 1C: Find transactions with NULL journal entry numbers
SELECT 
    COUNT(*) as null_journal_entries,
    MIN(transaction_date) as earliest_date,
    MAX(transaction_date) as latest_date
FROM transactions
WHERE journal_entry_number IS NULL;

-- ============================================================================
--PART 2: DAMAYAN TRANSACTION ANALYSIS (Account 207)
-- ============================================================================

-- Step 2A: Find Account 207 transactions by month
SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    COUNT(*) as transaction_count,
    SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END) as total_credits,
    SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END) as total_debits,
    SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE -amount END) as net_amount
FROM transactions
WHERE account_number = '207'
GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
ORDER BY month DESC;

-- Step 2B: Find recent Account 207 transactions (after 2024-06-01)
SELECT 
    trxn_id,
    transaction_date,
    description,
    amount,
    transaction_side,
    journal_entry_number,
    member_id,
    created_at
FROM transactions
WHERE account_number = '207'
  AND transaction_date >= '2024-06-01'
ORDER BY transaction_date DESC;

-- Step 2C: Find paired transactions (Account 207 + Account 401)
SELECT 
    t1.journal_entry_number,
    t1.transaction_date,
    t1.trxn_id as account_207_trxn_id,
    t1.description as account_207_desc,
    t1.amount as account_207_amount,
    t2.trxn_id as account_401_trxn_id,
    t2.description as account_401_desc,
    t2.amount as account_401_amount
FROM transactions t1
INNER JOIN transactions t2 
    ON t1.journal_entry_number = t2.journal_entry_number
    AND t1.transaction_date = t2.transaction_date
WHERE t1.account_number = '207'
  AND t2.account_number = '401'
  AND t1.transaction_date >= '2024-06-01'
  AND t1.description LIKE '%Central Fund%'
ORDER BY t1.transaction_date DESC;

-- ============================================================================
-- PART 3: OTHER DISCREPANCY CHECKS
-- ============================================================================

-- Check 3A: Zero amount transactions
SELECT 
    'Zero Amount Transactions' as issue_type,
    trxn_id,
    account_number,
    description,
    transaction_date,
    journal_entry_number
FROM transactions
WHERE amount = 0 OR amount IS NULL
ORDER BY transaction_date DESC;

-- Check 3B: Unbalanced journal entries
SELECT 
    'Unbalanced Journal Entries' as issue_type,
    journal_entry_number,
    transaction_date,
    COUNT(*) as trxn_count,
    SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END) as total_debits,
    SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END) as total_credits,
    ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) as imbalance
FROM transactions
GROUP BY journal_entry_number, transaction_date
HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 0.01
ORDER BY imbalance DESC
LIMIT 50;

-- Check 3C: Invalid account numbers
SELECT 
    'Invalid Account Number' as issue_type,
    t.trxn_id,
    t.account_number,
    t.description,
    t.transaction_date
FROM transactions t
LEFT JOIN chart_of_accounts c ON t.account_number = c.account_number
WHERE c.account_number IS NULL
ORDER BY t.transaction_date DESC;

-- Check 3D: Potential duplicates
SELECT 
    'Potential Duplicates' as issue_type,
    journal_entry_number,
    account_number,
    amount,
    transaction_side,
    COUNT(*) as duplicate_count,
    GROUP_CONCAT(trxn_id) as trxn_ids
FROM transactions
GROUP BY journal_entry_number, account_number, amount, transaction_side, description
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC
LIMIT 20;

-- ============================================================================
-- BACKUP TABLES
-- ============================================================================

-- Backup 1: Damayan transactions
CREATE TABLE IF NOT EXISTS transactions_damayan_backup_20241223 AS
SELECT * FROM transactions
WHERE (
    (account_number = '207' AND transaction_date >= '2024-06-01')
    OR
    (account_number = '401' 
     AND transaction_side = 'DEBIT' 
     AND transaction_date >= '2024-06-01'
     AND description LIKE '%Central Fund%'
     AND journal_entry_number IN (
         SELECT journal_entry_number 
         FROM transactions 
         WHERE account_number = '207' 
           AND transaction_date >= '2024-06-01'
     ))
);

SELECT 'Damayan backup created' as status, COUNT(*) as backed_up_transactions
FROM transactions_damayan_backup_20241223;

-- ============================================================================
-- FIX 1: DELETE DAMAYAN TRANSACTIONS (After reviewing backup!)
-- ============================================================================
-- Uncomment after reviewing backup table

-- DELETE Account 401 debits paired with damayan
/*
DELETE FROM transactions
WHERE account_number = '401'
  AND transaction_side = 'DEBIT'
  AND transaction_date >= '2024-06-01'
  AND description LIKE '%Central Fund%'
  AND journal_entry_number IN (
      SELECT journal_entry_number 
      FROM transactions_damayan_backup_20241223
      WHERE account_number = '207'
  );
*/

-- DELETE Account 207 transactions
/*
DELETE FROM transactions
WHERE account_number = '207'
  AND transaction_date >= '2024-06-01';
*/

-- ============================================================================
-- FIX 2: RENUMBER DUPLICATE JOURNAL ENTRIES (ADVANCED - USE WITH CAUTION!)
-- ============================================================================
-- NOTE: This is complex and should be done manually after analysis
-- The approach is to reassign new sequential journal entry numbers to duplicates

-- Step 2-1: Create a backup of ALL transactions before renumbering
/*
CREATE TABLE IF NOT EXISTS transactions_before_renumber_backup AS
SELECT * FROM transactions;
*/

-- Step 2-2: Example renumbering for one specific duplicate
-- This is just an example - you'll need to customize based on your analysis
/*
-- Example: If journal number "01-0001" has 50 transactions but should only have 2-3
-- You would need to identify which transactions belong together and which are duplicates
-- Then assign new journal numbers to the duplicates

-- Find all instances of the duplicate
SELECT trxn_id, transaction_date, description, created_at
FROM transactions
WHERE journal_entry_number = '01-0001'
ORDER BY created_at;

-- Manually identify groups and assign new numbers
-- Group 1 keeps '01-0001'
-- Group 2 gets '01-0002' 
-- Group 3 gets '01-0003', etc.

UPDATE transactions
SET journal_entry_number = '01-0XXX'  -- Replace XXX with new sequence
WHERE trxn_id IN ('id1', 'id2', 'id3');  -- IDs from analysis above
*/

-- ============================================================================
-- VERIFICATION AFTER FIXES
-- ============================================================================

-- Verify no damayan transactions after June 2024
SELECT 
    'Damayan check' as status,
    COUNT(*) as remaining_account_207_after_june
FROM transactions
WHERE account_number = '207'
  AND transaction_date >= '2024-06-01';

-- Verify last damayan transaction date
SELECT 
    'Last damayan date' as status,
    MAX(transaction_date) as last_account_207_transaction
FROM transactions
WHERE account_number = '207';

-- Check if duplicate journal numbers still exist
SELECT 
    'Duplicate check' as status,
    COUNT(*) as journal_numbers_with_duplicates
FROM (
    SELECT journal_entry_number
    FROM transactions
    WHERE journal_entry_number IS NOT NULL
    GROUP BY journal_entry_number
    HAVING COUNT(DISTINCT trxn_id) > 10
) as duplicates;

-- ============================================================================
-- RESTORE (if needed)
-- ============================================================================
/*
-- Restore damayan transactions
INSERT INTO transactions
SELECT * FROM transactions_damayan_backup_20241223;

-- Restore all transactions before renumbering
TRUNCATE TABLE transactions;
INSERT INTO transactions
SELECT * FROM transactions_before_renumber_backup;
*/

-- ============================================================================
-- INSTRUCTIONS
-- ============================================================================
-- 1. Run PART 1-3 analysis queries first
-- 2. Review results to determine cutoff date and identify duplicates
-- 3. Create backups
-- 4. Uncomment and run DELETE  statements for damayan fix
-- 5. For journal number duplicates:
--    - Requires manual analysis per duplicate
--    - Identify which transactions belong together
--    - Assign new sequential numbers to separated groups
-- 6. Run verification queries
-- ============================================================================
