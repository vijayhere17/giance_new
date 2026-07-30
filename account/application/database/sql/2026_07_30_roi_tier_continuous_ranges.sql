-- =============================================================================
-- Permanent ROI package ranges (no gaps) — run in phpMyAdmin
-- Table: roi_tier_masters (+ align stake_masters display packages)
-- =============================================================================
-- Covers $10 to unlimited continuously:
--   10     – 500
--   501    – 1000
--   1001   – 5000
--   5001   – 10000
--   10001  – unlimited (max_amount NULL)
-- =============================================================================

-- 1) Fix ROI tier ranges
UPDATE `roi_tier_masters`
SET
  `min_amount` = 10.00,
  `max_amount` = 500.00,
  `is_active` = 1,
  `updated_at` = NOW()
WHERE `id` = 1;

UPDATE `roi_tier_masters`
SET
  `min_amount` = 501.00,
  `max_amount` = 1000.00,
  `is_active` = 1,
  `updated_at` = NOW()
WHERE `id` = 2;

UPDATE `roi_tier_masters`
SET
  `min_amount` = 1001.00,
  `max_amount` = 5000.00,
  `is_active` = 1,
  `updated_at` = NOW()
WHERE `id` = 3;

UPDATE `roi_tier_masters`
SET
  `min_amount` = 5001.00,
  `max_amount` = 10000.00,
  `is_active` = 1,
  `updated_at` = NOW()
WHERE `id` = 4;

UPDATE `roi_tier_masters`
SET
  `min_amount` = 10001.00,
  `max_amount` = NULL,
  `is_active` = 1,
  `updated_at` = NOW()
WHERE `id` = 5;

-- 2) Align Buy Robo package cards (stake_masters ptype=2) to same mins
--    UI range is built from amount → next amount - 1
UPDATE `stake_masters`
SET `amount` = 10.00, `updated_at` = NOW()
WHERE `ptype` = 2 AND ROUND(`percantage`, 3) = 0.300;

UPDATE `stake_masters`
SET `amount` = 501.00, `updated_at` = NOW()
WHERE `ptype` = 2 AND ROUND(`percantage`, 3) = 0.350;

UPDATE `stake_masters`
SET `amount` = 1001.00, `updated_at` = NOW()
WHERE `ptype` = 2 AND ROUND(`percantage`, 3) = 0.400;

UPDATE `stake_masters`
SET `amount` = 5001.00, `updated_at` = NOW()
WHERE `ptype` = 2 AND ROUND(`percantage`, 3) = 0.450;

UPDATE `stake_masters`
SET `amount` = 10001.00, `updated_at` = NOW()
WHERE `ptype` = 2 AND ROUND(`percantage`, 3) = 0.500;

-- 3) Verify
SELECT id, min_amount, max_amount, daily_percent, is_active
FROM `roi_tier_masters`
ORDER BY min_amount ASC;

SELECT id, name, amount, percantage, ptype
FROM `stake_masters`
WHERE ptype = 2
ORDER BY amount ASC;
