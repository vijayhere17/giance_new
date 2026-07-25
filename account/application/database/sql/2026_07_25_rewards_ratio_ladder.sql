-- =============================================================================
-- REWARDS RATIO LADDER — RAW MySQL ONLY (phpMyAdmin)
-- Run this on existing DB to sync master ranks to the Rewards Ratio plan.
-- No Laravel migration. Reuses turnover_reward_masters only.
-- =============================================================================
-- Qualifies by: Direct Referrals + Team Size + Self Investment + Total Business
-- Salary: weekly for 12 weeks (enforced in app config income.reward_salary_weeks)
-- =============================================================================

UPDATE `turnover_reward_masters`
SET `title`='BEGINNER', `required_directs`=5, `required_team`=20, `required_self_business`=100, `required_team_business`=5000, `turnover_amount`=5000, `cash_reward`=10, `weekly_salary`=10, `updated_at`=NOW()
WHERE `milestone_order`=1;

UPDATE `turnover_reward_masters`
SET `title`='LEARNER', `required_directs`=6, `required_team`=50, `required_self_business`=200, `required_team_business`=12000, `turnover_amount`=12000, `cash_reward`=20, `weekly_salary`=20, `updated_at`=NOW()
WHERE `milestone_order`=2;

UPDATE `turnover_reward_masters`
SET `title`='ACHIVER', `required_directs`=7, `required_team`=100, `required_self_business`=300, `required_team_business`=20000, `turnover_amount`=20000, `cash_reward`=50, `weekly_salary`=50, `updated_at`=NOW()
WHERE `milestone_order`=3;

UPDATE `turnover_reward_masters`
SET `title`='ADVISOR', `required_directs`=8, `required_team`=200, `required_self_business`=500, `required_team_business`=50000, `turnover_amount`=50000, `cash_reward`=100, `weekly_salary`=100, `updated_at`=NOW()
WHERE `milestone_order`=4;

UPDATE `turnover_reward_masters`
SET `title`='MASTER', `required_directs`=9, `required_team`=500, `required_self_business`=700, `required_team_business`=100000, `turnover_amount`=100000, `cash_reward`=200, `weekly_salary`=200, `updated_at`=NOW()
WHERE `milestone_order`=5;

UPDATE `turnover_reward_masters`
SET `title`='SUPERIOR', `required_directs`=10, `required_team`=1000, `required_self_business`=1000, `required_team_business`=250000, `turnover_amount`=250000, `cash_reward`=300, `weekly_salary`=300, `updated_at`=NOW()
WHERE `milestone_order`=6;

UPDATE `turnover_reward_masters`
SET `title`='SUPREME', `required_directs`=11, `required_team`=2000, `required_self_business`=1500, `required_team_business`=500000, `turnover_amount`=500000, `cash_reward`=500, `weekly_salary`=500, `updated_at`=NOW()
WHERE `milestone_order`=7;

UPDATE `turnover_reward_masters`
SET `title`='MARVELOUS', `required_directs`=15, `required_team`=5000, `required_self_business`=2000, `required_team_business`=1000000, `turnover_amount`=1000000, `cash_reward`=1000, `weekly_salary`=1000, `updated_at`=NOW()
WHERE `milestone_order`=8;

UPDATE `turnover_reward_masters`
SET `title`='EXCELLENT', `required_directs`=20, `required_team`=10000, `required_self_business`=5000, `required_team_business`=5000000, `turnover_amount`=5000000, `cash_reward`=2000, `weekly_salary`=2000, `updated_at`=NOW()
WHERE `milestone_order`=9;

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 1, 'BEGINNER', 5, 20, 100, 5000, 5000, 10, 10, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 1);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 2, 'LEARNER', 6, 50, 200, 12000, 12000, 20, 20, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 2);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 3, 'ACHIVER', 7, 100, 300, 20000, 20000, 50, 50, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 3);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 4, 'ADVISOR', 8, 200, 500, 50000, 50000, 100, 100, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 4);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 5, 'MASTER', 9, 500, 700, 100000, 100000, 200, 200, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 5);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 6, 'SUPERIOR', 10, 1000, 1000, 250000, 250000, 300, 300, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 6);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 7, 'SUPREME', 11, 2000, 1500, 500000, 500000, 500, 500, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 7);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 8, 'MARVELOUS', 15, 5000, 2000, 1000000, 1000000, 1000, 1000, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 8);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 9, 'EXCELLENT', 20, 10000, 5000, 5000000, 5000000, 2000, 2000, NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 9);

DELETE FROM `turnover_reward_masters` WHERE `milestone_order` > 9;

-- Verify
SELECT milestone_order, title, required_directs, required_team, required_self_business, required_team_business, weekly_salary
FROM turnover_reward_masters
ORDER BY milestone_order ASC;
