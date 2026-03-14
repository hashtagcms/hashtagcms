# Testing HashtagCMS

HashtagCMS ships with a built-in test runner that handles all setup and prerequisite checks for you.

## Quick Start

```bash
# Run Unit + Feature tests (SQLite — safe, MySQL never touched)
php artisan cms:test-adminpanel

# Run Browser tests with screenshots (MySQL + Chrome)
php artisan cms:test-adminpanel --dusk
```

---

## Test Types

| Test Type | Database | Runner | What it tests |
|-----------|----------|--------|---------------|
| Unit / Feature | **SQLite** (auto-created) | `cms:test-adminpanel` | Helpers, controllers, RBAC, API, installation |
| Browser (Dusk) | **MySQL** (your .env DB) | `cms:test-adminpanel --dusk` | Admin panel UI, screenshots, visual regression |

> **MySQL is never touched** by Unit/Feature tests. The TestCase forces SQLite before the app boots, so `DatabaseMigrations` always runs `migrate:fresh` on SQLite — never on your real database.

---

## Setup (one-time)

### 1. Add test suites to `phpunit.xml`

```xml
<testsuites>
    <!-- your existing suites -->
    <testsuite name="HashtagCms-Unit">
        <directory suffix=".php">vendor/hashtagcms/hashtagcms/tests/Unit</directory>
    </testsuite>
    <testsuite name="HashtagCms-Feature">
        <directory suffix=".php">vendor/hashtagcms/hashtagcms/tests/Feature</directory>
    </testsuite>
</testsuites>
```

### 2. Add the test namespace to `composer.json`

```json
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/",
        "HashtagCms\\Tests\\": "vendor/hashtagcms/hashtagcms/tests/"
    }
}
```

Then run `composer dump-autoload`.

### 3. Force SQLite in `phpunit.xml`

This is **critical** — without `force="true"`, PHPUnit won't override your `.env` MySQL settings:

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite" force="true"/>
    <env name="DB_DATABASE" value="database/testing.sqlite" force="true"/>
    <env name="SESSION_DRIVER" value="array" force="true"/>
</php>
```

### 4. Install Dusk (for browser tests only)

```bash
composer require --dev laravel/dusk
php artisan dusk:install
php artisan dusk:chrome-driver
```

---

## Running Unit & Feature Tests

```bash
# Run all CMS tests
php artisan cms:test-adminpanel

# Filter by test name
php artisan cms:test-adminpanel --filter=test_admin_role

# Show as checklist
php artisan cms:test-adminpanel --testdox
```

The command checks prerequisites before running:

```
  ┌──────────────────────────────────────────────────────────┐
  │          #CMS Admin Panel Test Runner                    │
  │                                                          │
  │  Mode: Unit / Feature — SQLite                           │
  └──────────────────────────────────────────────────────────┘

  Prerequisite Check
  ──────────────────
  ✓ Package tests found
  ✓ phpunit.xml configured
  ✓ Autoload namespace configured

  All prerequisites met. Running tests on SQLite...
  (MySQL is NEVER touched by these tests)
```

If anything is missing, you get clear instructions on how to fix it.

---

## Running Browser (Dusk) Tests

Browser tests use **your MySQL database** from `.env`. They open a real Chrome browser, log in, navigate every admin page, and take screenshots.

### Prerequisites

The `--dusk` flag checks everything for you:

```bash
php artisan cms:test-adminpanel --dusk
```

```
  Prerequisite Check
  ──────────────────
  ✓ Server running at http://localhost:8000
  ✓ Database: mysql (my_database)
  ✓ CMS tables exist
  ✓ CMS is installed
  ✓ Staff user exists
  ✓ Laravel Dusk installed
  ✓ Browser tests found

  All prerequisites met. Starting Dusk browser tests...
```

If any check fails, you get clear instructions:

```
  ✘ Server is NOT running at http://localhost:8000
    Start it with: php artisan serve --host=localhost --port=8000

  ✘ CMS is not installed (site_installed != 1)
    Install first: php artisan cms:install
    Or visit /install in your browser.

  ✘ Prerequisites not met. Please fix the issues above and try again.
```

### What to do before running browser tests

1. **MySQL** must be running with CMS installed:
   ```bash
   php artisan migrate
   php artisan db:seed --class="HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder"
   php artisan cms:install
   ```

2. **Web server** must be running:
   ```bash
   php artisan serve
   ```

3. Then run:
   ```bash
   php artisan cms:test-adminpanel --dusk
   ```

### Screenshot Report

After browser tests complete, an HTML report is generated at:
```
tests/Browser/screenshots/report.html
```

Open it in your browser to see side-by-side screenshots of every admin controller in both grid and table layouts.

---

## Test Architecture

```
tests/
├── Unit/
│   └── HelperTest.php              # Helper function tests (SQLite)
├── Feature/
│   ├── AdminPanelBrowserTest.php   # Admin HTTP tests (SQLite)
│   ├── AdminRbacTest.php           # RBAC permission tests (SQLite)
│   ├── CommandTest.php             # Artisan command tests (SQLite)
│   ├── ExtensiveTest.php           # Full installation flow (SQLite)
│   ├── FrontendTest.php            # Frontend route tests (SQLite)
│   └── InstallationTest.php        # Installer tests (SQLite)
└── Browser/
    └── AdminPanelDuskTest.php      # Visual admin tests (MySQL + Chrome)
```

### Base classes

| Class | Extends | Database | Purpose |
|-------|---------|----------|---------|
| `HashtagCms\Testing\TestCase` | `Tests\TestCase` | SQLite (forced) | Unit/Feature tests |
| `HashtagCms\Testing\DuskTestCase` | `Laravel\Dusk\TestCase` | MySQL (from .env) | Browser tests |

### How MySQL is protected

1. `TestCase::ensureSqliteConnection()` runs **before** `parent::setUp()`, setting `DB_CONNECTION=sqlite` via `putenv()`, `$_ENV`, and `$_SERVER`
2. This happens before Laravel boots, so `DatabaseMigrations` never sees MySQL
3. If `phpunit.xml` doesn't set SQLite, the TestCase auto-creates a temp SQLite file as a safety net
4. `DuskTestCase` skips all of this — it uses your `.env` database directly (read-only, no migrations)
5. `DuskTestCase::assertDuskPrerequisites()` verifies the database is ready before running any test
