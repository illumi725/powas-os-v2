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



-- GENERATED FIX SCRIPT
-- Based on renumbering_plan.txt


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0384
-- Date: 2024-07-05
-- Members: NEC-SJC-PIN-004-6113
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0384',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-6113-T202407060851'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 08-0234
-- New: 08-0361
-- Date: 2024-08-03
-- Members: NEC-SJC-PIN-004-3315
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '08-0361',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '08-0234-MNEC-SJC-PIN-004-3315-T202408301157'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 09-0184
-- New: 09-0384
-- Date: 2024-09-03
-- Members: NEC-SJC-PIN-004-3276
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '09-0384',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '09-0184-MNEC-SJC-PIN-004-3276-T202409231249'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0383
-- Date: 2025-04-03
-- Members: NEC-SJC-PIN-004-7921
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0383',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7921-T202505110527'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0384
-- Date: 2025-04-04
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0384',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7993-T202505110529'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0385
-- Date: 2025-04-04
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0385',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7993-T202505110552'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0386
-- Date: 2025-04-05
-- Members: NEC-SJC-PIN-004-7179
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0386',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7179-T202505110556'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0387
-- Date: 2025-04-05
-- Members: NEC-SJC-PIN-004-7179
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0387',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7179-T202505110559'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0388
-- Date: 2025-04-06
-- Members: NEC-SJC-PIN-004-7179
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0388',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7179-T202505182328'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0390
-- Date: 2025-04-15
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0390',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7993-T202505110603'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0370
-- Date: 2025-04-17
-- Members: NEC-SJC-PIN-004-6213
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0370',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-6213-T202505110608'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 04-0370
-- New: 04-0392
-- Date: 2025-04-19
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '04-0392',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '04-0370-MNEC-SJC-PIN-004-7993-T202505110610'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0367
-- Date: 2025-07-03
-- Members: NEC-SJC-PIN-004-7921
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0367',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7921-T202508111151'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0368
-- Date: 2025-07-03
-- Members: NEC-SJC-PIN-004-7921
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0368',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7921-T202508111154'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0369
-- Date: 2025-07-04
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0369',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7993-T202508111156'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0370
-- Date: 2025-07-04
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0370',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7993-T202508111158'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0373
-- Date: 2025-07-14
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0373',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7993-T202508111203'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0374
-- Date: 2025-07-15
-- Members: NEC-SJC-PIN-004-6213
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0374',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-6213-T202508031414'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0375
-- Date: 2025-07-21
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0375',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7993-T202508111211'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0376
-- Date: 2025-07-27
-- Members: NEC-SJC-PIN-004-7993
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0376',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-7993-T202508111212'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 07-0257
-- New: 07-0257
-- Date: 2025-07-28
-- Members: NEC-SJC-PIN-004-1011
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '07-0257',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '07-0257-MNEC-SJC-PIN-004-1011-T202507282242'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 08-0234
-- New: 08-0335
-- Date: 2025-08-21
-- Members: NEC-SJC-PIN-004-2398
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '08-0335',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '08-0234-MNEC-SJC-PIN-004-2398-T202508220252'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 08-0234
-- New: 08-0234
-- Date: 2025-08-28
-- Members: NEC-SJC-PIN-004-1011
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '08-0234',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '08-0234-MNEC-SJC-PIN-004-1011-T202508282324'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 09-0184
-- New: 09-0280
-- Date: 2025-09-08
-- Members: NEC-SJC-PIN-004-7781
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '09-0280',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '09-0184-MNEC-SJC-PIN-004-7781-T202509282356'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 09-0184
-- New: 09-0184
-- Date: 2025-09-28
-- Members: NEC-SJC-PIN-004-1011
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '09-0184',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '09-0184-MNEC-SJC-PIN-004-1011-T202509290425'
    ) as temp
);


-- ========================================
-- Renumber payment group
-- Old: 09-0184
-- New: 09-0281
-- Date: 2025-09-29
-- Members: NEC-SJC-PIN-004-9761
-- Transactions: 2
-- Balance: 0.00
-- ========================================
UPDATE transactions
SET journal_entry_number = '09-0281',
    updated_at = NOW()
WHERE trxn_id IN (
    SELECT trxn_id FROM (
        SELECT trxn_id FROM payment_groups
        WHERE payment_group_id = '09-0184-MNEC-SJC-PIN-004-9761-T202509292336'
    ) as temp
);

