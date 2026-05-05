-- ============================================================
--  Seed: Previous-period payments (March 23 - April 21, 2026)
--  Fixes "$0.00 Previous Period / No prior data" in Revenue Report
--
--  payments schema: folio_id, amount, method, reference,
--                   processed_by, processed_at
-- ============================================================

-- Temporary table to hold folio IDs we'll attach payments to
DROP TEMPORARY TABLE IF EXISTS _tmp_folio_ids;
CREATE TEMPORARY TABLE _tmp_folio_ids AS
    SELECT id AS folio_id FROM folios ORDER BY id LIMIT 15;

-- ── March 23 - March 31, 2026 ──────────────────────────────
INSERT INTO payments (folio_id, amount, method, reference, processed_at)
SELECT folio_id,
       ROUND(350 + (RAND() * 900), 2),
       ELT(FLOOR(1 + RAND() * 4), 'cash', 'credit_card', 'debit_card', 'bank_transfer'),
       CONCAT('HIST-MAR-', FLOOR(RAND() * 90000 + 10000)),
       CONCAT('2026-03-', LPAD(FLOOR(23 + RAND() * 8), 2, '0'), ' ', LPAD(FLOOR(8 + RAND()*12), 2, '0'), ':', LPAD(FLOOR(RAND()*60), 2, '0'), ':00')
FROM _tmp_folio_ids LIMIT 6;

INSERT INTO payments (folio_id, amount, method, reference, processed_at)
SELECT folio_id,
       ROUND(600 + (RAND() * 1400), 2),
       ELT(FLOOR(1 + RAND() * 4), 'cash', 'credit_card', 'online', 'bank_transfer'),
       CONCAT('HIST-MAR-', FLOOR(RAND() * 90000 + 10000)),
       CONCAT('2026-03-', LPAD(FLOOR(24 + RAND() * 7), 2, '0'), ' ', LPAD(FLOOR(9 + RAND()*10), 2, '0'), ':', LPAD(FLOOR(RAND()*60), 2, '0'), ':00')
FROM _tmp_folio_ids LIMIT 5;

-- ── April 1 - April 7, 2026 ────────────────────────────────
INSERT INTO payments (folio_id, amount, method, reference, processed_at)
SELECT folio_id,
       ROUND(500 + (RAND() * 1800), 2),
       ELT(FLOOR(1 + RAND() * 5), 'cash', 'credit_card', 'debit_card', 'bank_transfer', 'online'),
       CONCAT('HIST-APR-', FLOOR(RAND() * 90000 + 10000)),
       CONCAT('2026-04-0', FLOOR(1 + RAND() * 6), ' ', LPAD(FLOOR(8 + RAND()*13), 2, '0'), ':', LPAD(FLOOR(RAND()*60), 2, '0'), ':00')
FROM _tmp_folio_ids;

-- ── April 8 - April 14, 2026 ───────────────────────────────
INSERT INTO payments (folio_id, amount, method, reference, processed_at)
SELECT folio_id,
       ROUND(700 + (RAND() * 2000), 2),
       ELT(FLOOR(1 + RAND() * 4), 'cash', 'credit_card', 'debit_card', 'bank_transfer'),
       CONCAT('HIST-APR-', FLOOR(RAND() * 90000 + 10000)),
       CONCAT('2026-04-', LPAD(FLOOR(8 + RAND() * 6), 2, '0'), ' ', LPAD(FLOOR(8 + RAND()*13), 2, '0'), ':', LPAD(FLOOR(RAND()*60), 2, '0'), ':00')
FROM _tmp_folio_ids;

-- ── April 15 - April 21, 2026 ──────────────────────────────
INSERT INTO payments (folio_id, amount, method, reference, processed_at)
SELECT folio_id,
       ROUND(800 + (RAND() * 2200), 2),
       ELT(FLOOR(1 + RAND() * 3), 'credit_card', 'debit_card', 'online'),
       CONCAT('HIST-APR-', FLOOR(RAND() * 90000 + 10000)),
       CONCAT('2026-04-', LPAD(FLOOR(15 + RAND() * 6), 2, '0'), ' ', LPAD(FLOOR(8 + RAND()*13), 2, '0'), ':', LPAD(FLOOR(RAND()*60), 2, '0'), ':00')
FROM _tmp_folio_ids LIMIT 10;

DROP TEMPORARY TABLE IF EXISTS _tmp_folio_ids;

-- ── Verify ─────────────────────────────────────────────────
SELECT
    DATE_FORMAT(processed_at, '%Y-%m') AS month,
    COUNT(*)                           AS tx_count,
    ROUND(SUM(amount), 2)              AS total_revenue
FROM payments
GROUP BY month
ORDER BY month;
