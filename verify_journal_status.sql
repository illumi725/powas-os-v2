-- ============================================================================
-- VERIFY JOURNAL ENTRY STATUS
-- ============================================================================

SELECT 'Checking for duplicate journal entries (Multiple Members/Payments per Entry)...' as Check_Type;

SELECT 
    COUNT(*) as duplicate_groups_found,
    SUM(total_transactions) as affected_transactions
FROM (
    SELECT 
        journal_entry_number,
        COUNT(DISTINCT member_id) as unique_members,
        COUNT(*) as total_transactions
    FROM transactions
    WHERE journal_entry_number IS NOT NULL
      AND YEAR(transaction_date) = 2025
    GROUP BY journal_entry_number
    HAVING COUNT(DISTINCT member_id) > 1
) as duplicates;

SELECT '--------------------------------------------------' as '---';

SELECT 'Top 5 remaining duplicates:' as 'Details';

SELECT 
    journal_entry_number,
    transaction_date,
    COUNT(DISTINCT member_id) as unique_members,
    COUNT(*) as trxn_count,
    MIN(created_at) as first_created,
    MAX(created_at) as last_created
FROM transactions
WHERE journal_entry_number IS NOT NULL
  AND YEAR(transaction_date) = 2025
GROUP BY journal_entry_number, transaction_date
HAVING COUNT(DISTINCT member_id) > 1
ORDER BY trxn_count DESC
LIMIT 5;
