-- ============================================================================
-- FIX DUPLICATE JOURNAL ENTRY NUMBERS - SEPARATE UNRELATED PAYMENTS
-- ============================================================================
-- Purpose: Identify and separate transactions that incorrectly share the same
--          journal entry number but belong to different payments
--
-- Strategy:
-- 1. Find journal numbers with multiple payments (different member_ids or times)
-- 2. Group transactions by payment (same member_id, close created_at time)
-- 3. Assign new sequential journal numbers to each payment group
-- 4. Generate UPDATE statements for review
-- ============================================================================

USE powas_os_app;

-- ============================================================================
-- STEP 1: IDENTIFY DUPLICATE JOURNAL ENTRIES WITH MULTIPLE PAYMENTS
-- ============================================================================

-- Find journal entry numbers that have transactions from different payments
DROP TEMPORARY TABLE IF EXISTS duplicate_journal_numbers;

CREATE TEMPORARY TABLE duplicate_journal_numbers AS
SELECT 
    journal_entry_number,
    MIN(transaction_date) as transaction_date,
    COUNT(DISTINCT member_id) as unique_members,
    COUNT(DISTINCT DATE(created_at)) as unique_dates,
    COUNT(*) as total_transactions,
    MIN(created_at) as first_created,
    MAX(created_at) as last_created,
    TIMESTAMPDIFF(HOUR, MIN(created_at), MAX(created_at)) as time_span_hours
FROM transactions
WHERE journal_entry_number IS NOT NULL
  AND YEAR(transaction_date) = 2025
GROUP BY journal_entry_number
HAVING COUNT(DISTINCT member_id) > 1  -- Multiple members = different payments
    OR TIMESTAMPDIFF(HOUR, MIN(created_at), MAX(created_at)) > 24  -- Created far apart
ORDER BY total_transactions DESC;

-- Show summary
SELECT 
    'Duplicate Journal Numbers Found' as status,
    COUNT(*) as duplicate_count,
    SUM(total_transactions) as affected_transactions
FROM duplicate_journal_numbers;

-- Show details
SELECT 
    journal_entry_number,
    transaction_date,
    unique_members,
    total_transactions,
    time_span_hours,
    DATE_FORMAT(first_created, '%Y-%m-%d %H:%i') as first_time,
    DATE_FORMAT(last_created, '%Y-%m-%d %H:%i') as last_time
FROM duplicate_journal_numbers
ORDER BY total_transactions DESC
LIMIT 20;

-- ============================================================================
-- STEP 2: GROUP TRANSACTIONS BY ACTUAL PAYMENT
-- ============================================================================

-- For each duplicate journal number, group transactions into separate payments
DROP TEMPORARY TABLE IF EXISTS payment_groups;

CREATE TEMPORARY TABLE payment_groups AS
SELECT 
    t.trxn_id,
    t.journal_entry_number as old_journal_number,
    t.transaction_date,
    t.member_id,
    t.created_at,
    t.account_number,
    t.amount,
    t.transaction_side,
    -- Group transactions that belong together based on:
    -- 1. Same member_id
    -- 2. Created within 1 hour of each other
    CONCAT(
        t.journal_entry_number, 
        '-M', COALESCE(t.member_id, 'NULL'),
        '-T', DATE_FORMAT(t.created_at, '%Y%m%d%H%i')
    ) as payment_group_id
FROM transactions t
INNER JOIN duplicate_journal_numbers d 
    ON t.journal_entry_number = d.journal_entry_number
ORDER BY t.journal_entry_number, t.member_id, t.created_at;

-- Show how many payment groups we found
SELECT 
    old_journal_number,
    transaction_date,
    COUNT(DISTINCT payment_group_id) as payment_groups,
    COUNT(*) as total_transactions
FROM payment_groups
GROUP BY old_journal_number, transaction_date
HAVING COUNT(DISTINCT payment_group_id) > 1
ORDER BY payment_groups DESC;

-- ============================================================================
-- STEP 3: ASSIGN NEW JOURNAL ENTRY NUMBERS
-- ============================================================================

-- First, create helper table to find which group keeps the original number
DROP TEMPORARY TABLE IF EXISTS first_payment_groups;

CREATE TEMPORARY TABLE first_payment_groups AS
SELECT 
    old_journal_number,
    MIN(payment_group_id) as first_group_id
FROM payment_groups
GROUP BY old_journal_number;

-- For each payment group, assign a new sequential journal entry number
DROP TEMPORARY TABLE IF EXISTS renumbering_plan;

CREATE TEMPORARY TABLE renumbering_plan AS
SELECT 
    pg.payment_group_id,
    pg.old_journal_number,
    pg.transaction_date,
    DATE_FORMAT(pg.transaction_date, '%m') as month_part,
    COUNT(*) as trxn_count,
    -- Calculate new journal number
    -- Keep first group with original number, assign new numbers to others
    CASE
        WHEN pg.payment_group_id = fpg.first_group_id THEN pg.old_journal_number
        ELSE NULL  -- Will be assigned new number
    END as new_journal_number,
    MIN(pg.created_at) as group_created_at,
    GROUP_CONCAT(DISTINCT pg.member_id) as members,
    SUM(CASE WHEN pg.transaction_side = 'DEBIT' THEN pg.amount ELSE 0 END) as group_debits,
    SUM(CASE WHEN pg.transaction_side = 'CREDIT' THEN pg.amount ELSE 0 END) as group_credits,
    ABS(SUM(CASE WHEN pg.transaction_side = 'DEBIT' THEN pg.amount ELSE -pg.amount END)) as group_balance
FROM payment_groups pg
LEFT JOIN first_payment_groups fpg ON pg.old_journal_number = fpg.old_journal_number
GROUP BY pg.payment_group_id, pg.old_journal_number, pg.transaction_date, fpg.first_group_id
ORDER BY pg.transaction_date, pg.old_journal_number, group_created_at;

-- Assign new numbers to groups that need them
DROP TEMPORARY TABLE IF EXISTS new_journal_assignments;

CREATE TEMPORARY TABLE new_journal_assignments AS
SELECT 
    rp.*,
    -- Get max existing journal number for the month
    (SELECT MAX(
        CAST(SUBSTRING_INDEX(journal_entry_number, '-', -1) AS UNSIGNED)
    ) 
    FROM transactions 
    WHERE transaction_date BETWEEN 
        DATE_FORMAT(rp.transaction_date, '%Y-%m-01') AND 
        LAST_DAY(rp.transaction_date)
    ) as max_sequence_in_month,
    -- Assign sequential new numbers
    ROW_NUMBER() OVER (
        PARTITION BY rp.month_part 
        ORDER BY rp.transaction_date, rp.group_created_at
    ) as group_sequence
FROM renumbering_plan rp
WHERE rp.new_journal_number IS NULL;  -- Only groups needing new numbers

-- Update with calculated new journal numbers
UPDATE new_journal_assignments
SET new_journal_number = CONCAT(
    month_part, 
    '-',
    LPAD(max_sequence_in_month + group_sequence, 4, '0')
);

-- ============================================================================
-- STEP 4: GENERATE UPDATE STATEMENTS
-- ============================================================================

-- Show the renumbering plan
SELECT 
    'Renumbering Plan' as step,
    old_journal_number,
    COALESCE(new_journal_number, old_journal_number) as assigned_journal_number,
    transaction_date,
    trxn_count,
    members,
    group_balance as balance_check,
    CASE 
        WHEN group_balance < 0.01 THEN 'BALANCED ✓'
        ELSE CONCAT('IMBALANCED: ', group_balance)
    END as status
FROM (
    SELECT * FROM renumbering_plan WHERE new_journal_number IS NOT NULL
    UNION ALL
    SELECT 
        payment_group_id, old_journal_number, transaction_date, month_part,
        trxn_count, new_journal_number, group_created_at, members,
        group_debits, group_credits, group_balance
    FROM new_journal_assignments
) combined
ORDER BY transaction_date, old_journal_number;

-- Generate UPDATE SQL for each group that needs renumbering
SELECT CONCAT(
    '\n-- ========================================\n',
    '-- Renumber payment group\n',
    '-- Old: ', rp.old_journal_number, '\n',
    '-- New: ', COALESCE(nja.new_journal_number, rp.new_journal_number), '\n',
    '-- Date: ', rp.transaction_date, '\n',
    '-- Members: ', rp.members, '\n',
    '-- Transactions: ', rp.trxn_count, '\n',
    '-- Balance: ', rp.group_balance, '\n',
    '-- ========================================\n',
    'UPDATE transactions\n',
    'SET journal_entry_number = ''', COALESCE(nja.new_journal_number, rp.new_journal_number), ''',\n',
    '    updated_at = NOW()\n',
    'WHERE trxn_id IN (\n',
    '    SELECT trxn_id FROM (\n',
    '        SELECT trxn_id FROM payment_groups\n',
    '        WHERE payment_group_id = ''', rp.payment_group_id, '''\n',
    '    ) as temp\n',
    ');\n'
) as update_sql
FROM renumbering_plan rp
LEFT JOIN new_journal_assignments nja ON rp.payment_group_id = nja.payment_group_id
WHERE rp.new_journal_number IS NOT NULL OR nja.new_journal_number IS NOT NULL
ORDER BY rp.transaction_date, rp.group_created_at;

-- ============================================================================
-- STEP 5: VERIFICATION QUERIES (Run AFTER applying updates)
-- ============================================================================

-- Check if duplicates still exist
/*
SELECT 
    journal_entry_number,
    COUNT(DISTINCT member_id) as unique_members,
    COUNT(*) as total_transactions
FROM transactions
WHERE YEAR(transaction_date) = 2025
GROUP BY journal_entry_number
HAVING COUNT(DISTINCT member_id) > 1
ORDER BY total_transactions DESC;
*/

-- NOW check for truly unbalanced transactions
/*
SELECT 
    journal_entry_number,
    transaction_date,
    COUNT(*) as trxn_count,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE 0 END), 2) as debits,
    ROUND(SUM(CASE WHEN transaction_side = 'CREDIT' THEN amount ELSE 0 END), 2) as credits,
    ROUND(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END), 2) as imbalance
FROM transactions
WHERE YEAR(transaction_date) = 2025
GROUP BY journal_entry_number, transaction_date
HAVING ABS(SUM(CASE WHEN transaction_side = 'DEBIT' THEN amount ELSE -amount END)) > 0.01
ORDER BY ABS(imbalance) DESC;
*/

-- ============================================================================
-- HOW TO USE
-- ============================================================================
--
-- 1. RUN Steps 1-4 to analyze and generate UPDATE statements
--
-- 2. REVIEW the "Renumbering Plan" output:
--    - Check that payment groups make sense
--    - Verify balance_check shows if each group is balanced
--
-- 3. COPY the UPDATE SQL from Step 4 to a new file: apply_renumbering.sql
--
-- 4. BACKUP database:
--    mysqldump -u root -p powas_os_app > backup_before_renumber.sql
--
-- 5. APPLY the updates:
--    mysql -u root -p powas_os_app < apply_renumbering.sql
--
-- 6. RUN verification queries (uncomment Step 5)
--
-- 7. NOW you can accurately check for unbalanced transactions!
--
-- ============================================================================
