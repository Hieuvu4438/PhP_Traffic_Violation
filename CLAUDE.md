# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.

---

# PROJECT: Website Tra cứu Phương tiện Vi phạm Giao thông

## Identity

- University capstone project (đồ án bài tập lớn PHP)
- Vietnamese-language UI, comments, and documentation
- Reference: https://vnetraffic.org/
- Full specs: `docs/01-tong-quan-du-an.md` through `docs/06-ke-hoach-phat-trien.md`

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.x (vanilla — NO framework), custom MVC |
| Database | MySQL/MariaDB via XAMPP, database name `traffic_violation_db`, charset `utf8mb4_unicode_ci`, engine InnoDB |
| Frontend | HTML5, CSS3, Vanilla JS, Bootstrap 5 |
| Charts | Chart.js |
| Maps | Google Maps JS API (Leaflet+OpenStreetMap fallback if no API key) |
| Icons | Font Awesome 6 or Bootstrap Icons |
| Editor | CKEditor 5 (CDN) for admin news |
| Server | Apache (XAMPP), `public/` is DocumentRoot |

## Architecture: Custom MVC

```
Request → .htaccess → public/index.php → Router → Controller → Model (PDO) → View
```

```
htdocs/
├── public/              # DocumentRoot — ONLY this is web-accessible
│   ├── index.php        # Single entry point
│   ├── .htaccess        # URL rewriting
│   └── assets/          # css/, js/, images/, uploads/
├── app/
│   ├── core/            # Router.php, Controller.php, Model.php, Database.php, Session.php, Validator.php, Helper.php
│   ├── controllers/     # client/ (guest+user) and admin/ (admin only)
│   ├── models/          # One PHP class per DB table, NAMED AFTER TABLE (e.g., User.php → table `users`)
│   └── views/           # layouts/ (client.php, admin.php), partials/, client/, admin/
├── config/              # config.php (DB constants), routes.php
├── database/            # schema.sql, seed.sql
└── docs/                # 6 detailed design documents
```

## Database (14 tables, InnoDB, utf8mb4_unicode_ci)

Core: `users`, `vehicles`, `violations`, `offenses`, `offense_categories`, `locations`, `news`, `news_categories`, `traffic_signs`, `traffic_sign_groups`, `faqs`, `search_history`, `traffic_alerts`, `contact_messages`

Full schema → `docs/03-thiet-ke-co-so-du-lieu.md` and `database/schema.sql`

## Mandatory Coding Rules

1. **PDO prepared statements** for ALL queries — never string-interpolate SQL. Use `Database::getInstance()->getConnection()` (Singleton).
2. **`password_hash(PASSWORD_BCRYPT)`** / `password_verify()` for passwords.
3. **`htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`** on all user-origin output in views.
4. **CSRF tokens** on every POST form — hidden input + session check in controller.
5. **Server-side validation** via `Validator` class — never trust client-only validation.
6. **Friendly URLs only** — `/tra-cuu` not `/tra-cuu.php`. Defined in `config/routes.php`.
7. **PSR-12** — 4 spaces, `<?php` tag, no closing `?>`, `namespace App\...`.
8. **No direct file access** — every request goes through `public/index.php` → Router.

## Patterns

**Controller** (extends `app/core/Controller.php`):
- `$this->view('folder/file', ['key' => $data]);` — renders with client layout
- `$this->view('admin/folder/file', $data, 'admin');` — renders with admin layout
- `$this->requireLogin();` / `$this->requireAdmin();` — auth guards
- `$this->redirect('/url');` — redirect and exit

**Model** (extends `app/core/Model.php`, set `protected string $table = 'table_name';`):
- Inherits: `all()`, `find($id)`, `findBy($col, $val)`, `create($data)`, `update($id, $data)`, `delete($id)`, `count()`, `paginate($page, $perPage)`

**Routing** (`config/routes.php`):
```php
$router->get('/tra-cuu', 'client/TraCuuController@index');
$router->post('/tra-cuu', 'client/TraCuuController@search');
```

## User Roles

| Role | Scope |
|------|-------|
| Guest | Search violations, view news, signs, stats, map, FAQ |
| User (`role='user'`) | Guest + manage own vehicles, search history, profile |
| Admin (`role='admin'`) | All + admin dashboard at `/admin`, CRUD all entities |

Default admin: `admin@traffic.vn` / `admin123` (bcrypt)

## Vietnamese Data Rules

- **License plates:** regex `\d{2}[A-Z]\d?[-\s]?\d{4,5}` — handles 30A-12345, 59F1-12345
- **Vehicle types enum:** `car`, `motorcycle`, `electric_motorcycle`
- **Violation status enum:** `pending`, `processed`, `paid`
- **Location types enum:** `camera`, `csgt`, `toll`, `inspection`

## When Adding a Feature

1. Check `docs/` for the relevant spec
2. DB change? → add to `database/schema.sql`
3. Create Model in `app/models/`
4. Create Controller in `app/controllers/client/` or `admin/`
5. Add routes in `config/routes.php`
6. Create view(s) in `app/views/`
7. Test at `http://localhost/route`

## Priority Order

**P0 (build first):** Violation lookup, Auth (login/register), Admin CRUD (users, violations, news, signs), Client views (home, news list, sign list, search results), Admin dashboard
**P1:** Search history, News filter by category, Vehicle CRUD (user), FAQ, Google Maps, Chart.js stats
**P2:** PDF export, plate autocomplete, forgot password, regional pie chart, traffic alerts, contact form

## Key Constraints

- NO Composer — autoload via `spl_autoload_register` in core
- NO React/SPA — server-rendered HTML with vanilla JS
- Vietnamese URLs — `/tra-cuu`, `/tin-tuc`, `/bien-bao`
- Seed data is mock — no real CSGT API available
