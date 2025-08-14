# VGK History Stats (PHP + MySQL + Bootstrap/jQuery)

A tiny app that surfaces Vegas Golden Knights history: playoff appearances, Stanley Cup Final (SCF) runs, and the championship season.

## Stack
- PHP 8+ (works on PHP 7.4+), PDO
- MySQL or MariaDB (use the `sql/seed.sql` file)
- Bootstrap 5, jQuery (via CDN)

## Quick Start (Local with XAMPP/MAMP/LAMP or shared hosting)
1) Create a MySQL database and import seed data:
   - Create DB: `CREATE DATABASE vgk_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
   - Import: `mysql -u <user> -p vgk_stats < sql/seed.sql`

2) Configure DB credentials:
   - Copy `api/config.example.php` to `api/config.php`
   - Update host, db, user, pass

3) Deploy files to your web root (e.g. `public/` goes under `public_html` or `htdocs`).
   - Make sure the `api/` folder is also uploaded (keep it outside web root if your host allows; update paths accordingly).

4) Visit `http(s)://<your-host>/index.php`

## Endpoints
- `GET /api/summary.php` — high-level stats summary
- `GET /api/seasons.php` — all seasons (with playoff, SCF, Cup flags)
- `GET /api/scf.php` — seasons they reached SCF + whether they won
- `GET /api/playoffs.php` — seasons they made playoffs

## Notes
- Data current through the end of the 2024–25 season per Wikipedia/NHL. Update `sql/seed.sql` as needed.
- You can add more detail by creating a `series` table for round-by-round matchups (schema included).

