# MLM Income System — SQL-Only Deploy Notes

**No Laravel migration is required.**

Use only:
`account/application/database/sql/2026_07_21_mlm_income_reward_system.sql`

## Execute order (phpMyAdmin)

1. **FIRST** — SECTION A (existence checks). Skip any ALTER whose column already exists.
2. **SECOND** — SECTION B (`users` ALTER columns).
3. **THIRD** — SECTION C (`turnover_reward_masters` + `turnover_reward_achievers` ALTER columns).
4. **FOURTH** — SECTION D (index).
5. **LAST** — SECTION E (UPDATE + INSERT Rewards Ratio ranks 1–9: BEGINNER…EXCELLENT).
   Or run standalone: `database/sql/2026_07_25_rewards_ratio_ladder.sql`

## Already exists (do not recreate)

From prior production schema / earlier work:

- Tables: `users`, `ewallet_logs`, `turnover_reward_masters`, `turnover_reward_achievers`
- Master columns already on reward master: `milestone_order`, `turnover_amount`, `cash_reward`
- Achiever base columns: `member_id`, `reward_id`, `leg1/2/3_business`, `cash_reward`

## New columns (SQL only)

- `users`: registration_fee, reward_id, locked_reward_*, sponsor_unlock_done
- `turnover_reward_masters`: title, required_*, weekly_salary
- `turnover_reward_achievers`: weekly salary payout tracking columns

## Confirm

- No `php artisan migrate` for this feature
- No seeders
- No new duplicate tables
