# Console Commands Reference

HashtagCMS provides several artisan commands to help you manage your CMS installation.

## Available Commands

### Installation & Setup

#### cms:install

Install HashtagCMS with database migrations and seeders.

**Usage**:
```bash
php artisan cms:install
```

**What it does**:
- Runs database migrations
- Seeds initial data
- Publishes assets
- Publishes frontend views
- Publishes admin views
- Creates default site configuration

**Options**: None

**Interactive**: Yes (asks for confirmation if tables exist)

---

#### cms:version

Display the current HashtagCMS version.

**Usage**:
```bash
php artisan cms:version
```

**Output**:
```
HashtagCMS Version: 3.0.1
```

---

## Code Generation

### cms:module _(Interactive Wizard)_

The recommended way to create a new admin panel module end-to-end.

**Usage**:
```bash
php artisan cms:module
```

**What it does (interactive prompts)**:

| Prompt | Example answer |
|---|---|
| Generate scaffolding files? | Yes |
| Module name | `BlogPost` |
| Sub-title / description | `Manage Blog Posts` |
| Admin URL path (controller mapping) | `blog-posts` |
| Parent module | Root Level |
| Sidebar icon | `fa fa-pencil` |
| Listing template | `common/listing` |
| Editor template | `addedit` |
| Main database table | `blog_posts` |
| Relations (repeat until done) | `lang → hasMany → BlogPostLang` [LangScope?] |

**What gets generated automatically**:

- **Controller** — `app/Http/Controllers/Admin/BlogPostController.php`
  - `$dataFields` auto-derived from table (dot-notation for relations: `lang.name`, `zone.name`)
  - `$dataWith` auto-derived from `_id` columns + lang relation
  - `$bindDataWithAddEdit` for all `_id` foreign key dropdowns
  - `store()` with validation rules, `$saveData` assignments, lang block, correct save method
  - Related model `use` statements resolved (app namespace first, then `HashtagCMS\Models` fallback)

- **Model** — `app/Models/BlogPost.php`
  - Relationship methods (`lang()`, `zone()`, etc.)
  - `LangScope` boot method if the relation has `isLanguage = true`
  - Related models created recursively if they don't exist yet

- **Validator** — `app/Http/Requests/BlogPostControllerRequest.php`
  - Rules auto-derived from DB column types

- **View** — `resources/views/be/modern/blog-post/addedit.blade.php`
  - Tailwind card layout
  - Form fields generated from DB schema (text, textarea, select, checkbox, date, number)
  - Dropdowns for `_id` FK columns
  - Lang fields section if lang table exists

- **Database record** — module entry saved to `cms_modules`

**Summary table shown before saving**:
```
+--------------------+-------------------------------+
| Field              | Value                         |
+--------------------+-------------------------------+
| Module Name        | BlogPost                      |
| Sub-Title          | Manage Blog Posts             |
| Controller Mapping | blog-posts                    |
| Parent             | Root Level                    |
| Icon CSS           | fa fa-pencil                  |
| Listing Template   | common/listing                |
| Editor Template    | addedit                       |
| Main Data Source   | BlogPost                      |
| Relations          | lang (hasMany) [LangScope ✓]  |
| Generate Files     | Yes                           |
+--------------------+-------------------------------+
```

---

### cms:controller

Generate a module admin controller.

**Usage**:
```bash
php artisan cms:controller {name} {dataSource} {dataWith?} {dataFields?}
```

**Example**:
```bash
php artisan cms:controller Country Country null '*'
```

When `dataFields` is `*`, all field declarations are auto-derived from the live DB table schema.

**Generated File**: `app/Http/Controllers/Admin/CountryController.php`

---

### cms:model

Generate a module Eloquent model with optional relationship methods.

**Usage**:
```bash
php artisan cms:model {name} {methods?}
```

The `methods` argument is a `~`-separated list of comma-separated tuples:
```
alias,relationType,ModelName,langScopeFlag~alias2,relationType2,ModelName2,0
```

**Example**:
```bash
# Country model with lang (hasMany, LangScope) and zone (belongsTo, no LangScope)
php artisan cms:model Country "lang,hasMany,CountryLang,1~zone,belongsTo,Zone,0"
```

**Generated File**: `app/Models/Country.php`

If the related model (e.g. `CountryLang`) doesn't already exist, it is auto-created recursively. If `langScopeFlag = 1`, the related model gets a `LangScope` boot method.

---

### cms:validator

Generate a `FormRequest` validator class with rules auto-derived from the DB table schema.

**Usage**:
```bash
php artisan cms:validator {name} {validatorName?}
```

**Example**:
```bash
php artisan cms:validator Country CountryControllerRequest
```

**Generated File**: `app/Http/Requests/CountryControllerRequest.php`

Rules are derived from column types (e.g. `varchar(65)` → `max:65|string`, `tinyint(1)` → `boolean`, `_id` → `numeric`).

---

### cms:validation

Discover and output validation rules for a specific table without generating a file. Implemented in `CmsValidatorCommand.php`.

**Usage**:
```bash
php artisan cms:validation {name?}
```

**Example**:
```bash
php artisan cms:validation blog_posts
```

**Output**:
Outputs a formatted string of validation rules derived from the live DB table schema (and its lang table, if it exists), ready to copy into a controller or `FormRequest`.

```
["name" => "required|max:255|string", "is_active" => "nullable|integer", ...]
```

> **Note**: If no table name is provided, the command will interactively ask for one.

---

### cms:register-modules-from-view-folder

Scan a theme directory and register all Blade template files as Frontend Modules in the database. Useful when adding a new theme with many views.

**Usage**:
```bash
php artisan cms:register-modules-from-view-folder {--path=} {--site_id=1}
```

**Interactive Prompts**:
- Theme folder name (under `views/vendor/hashtagcms/fe/`) — default: `sapphire`
- Site ID — default: `1`

**What it does**:
- Recursively scans all `.blade.php` files in the specified theme folder
- Skips folders: `auth`, `_layout_`, `_services_`, `example`
- For each file, creates a `Module` database record with alias, name, and view name
- Skips modules that already exist (idempotent — safe to re-run)

**Example**:
```bash
php artisan cms:register-modules-from-view-folder
# Prompts: Theme folder? sapphire
# Prompts: Site Id? 1
```

---

### cms:language-install

Install additional language translation data into a HashtagCMS installation by running the relevant seeders for the selected languages.

**Usage**:
```bash
php artisan cms:language-install {langs?} {--langs=}
```

**Arguments**:
- `langs` — Comma-separated ISO language codes (e.g. `fr`, `de`, `ar`)

**Example**:
```bash
php artisan cms:language-install fr,de
```

**What it does**:
- Validates the provided language codes against available translation seeds in `Database/Seeds/Translations/`
- Warns about unsupported codes and skips them
- Runs the following seeders for valid languages:
  - `LangsTableSeeder`
  - `CountryLangsTableSeeder`
  - `SitesTableSeeder`
  - `CategoriesTableSeeder`
  - `PagesTableSeeder`
  - `StaticModuleContentsTableSeeder`

---

### cms:module-controller _(legacy alias)_

Lower-level command — prefer `cms:module` for full scaffolding.

**Usage**:
```bash
php artisan cms:module-controller {ControllerName}
```

---

### cms:module-model _(legacy alias)_

Lower-level command — prefer `cms:module` for full scaffolding.

**Usage**:
```bash
php artisan cms:module-model {ModelName}
```

---

### cms:frontend-controller

Generate a frontend controller.

**Usage**:
```bash
php artisan cms:frontend-controller {ControllerName}
```

**Generated File**: `app/Http/Controllers/ShopController.php`

---

### Setup

#### cms:setup-standalone

Setup the application for standalone usage (usually when using as an API or separate frontend).

**Usage**:
```bash
php artisan cms:setup-standalone {--force}
```

**What it does**:
- Publishes configuration, assets, and views
- Asks for HashtagCMS API URL, Token, and Secret
- Updates `.env` file with provided credentials
- Sets `HASHTAGCMS_LOAD_MODULE_FROM_DB=false`

---

### Data Management

#### cms:exportdata

Export database data to PHP Seeder files.

**Usage**:
```bash
php artisan cms:exportdata
```

**What it does**:
- Generates PHP Seeder classes for specified tables
- Creates `BaseSeeder.php` helper
- Creates `DatabaseSeeder.php` to run all seeders
- Useful for migrating data between environments

**Export Location**: `database/seeders/` (default)

**File Structure**:
```
database/seeders/
├── SiteTableSeeder.php
├── LangTableSeeder.php
├── PageTableSeeder.php
├── DatabaseSeeder.php
└── ...
```

**Options**:
```bash
# Export specific tables
php artisan cms:exportdata --tables=sites,categories

# Exclude specific tables
php artisan cms:exportdata --exclude=sessions,jobs

# Limit records per table
php artisan cms:exportdata --limit=100

# Custom output directory
php artisan cms:exportdata --output=database/my_seeds
```

---

#### cms:importdata

Import data by running the generated seeders.

**Usage**:
```bash
php artisan cms:importdata
```

**What it does**:
- Wrapper around `php artisan db:seed`
- Optionally updates site domain matching APP_URL
- Can check for existing records to avoid duplicates

**Options**:
```bash
# Run specific seeder class
php artisan cms:importdata --class=DatabaseSeeder

# Force run in production
php artisan cms:importdata --force

# Update site domain to match .env APP_URL
php artisan cms:importdata --update-domain

# Check existing records (idempotent seed)
php artisan cms:importdata --check-existing
```

---

### Publishing Assets

#### Publish Configuration

```bash
php artisan vendor:publish --tag=hashtagcms.config
```

**Publishes**:
- `config/hashtagcms.php`
- `config/hashtagcmsadmin.php`
- `config/hashtagcmsapi.php`
- `config/hashtagcmscommon.php`

---

#### Publish Frontend Views

```bash
php artisan vendor:publish --tag=hashtagcms.views.frontend
```

**Publishes**: `resources/views/vendor/hashtagcms/fe/`

**Contents**:
- Theme templates
- Layout files
- Module views
- Partial views

---

#### Publish Admin Views

```bash
php artisan vendor:publish --tag=hashtagcms.views.admincommon
```

**Publishes**: `resources/views/vendor/hashtagcms/be/`

**Contents**:
- Admin panel views
- Form templates
- List views
- Dashboard components

---

#### Publish Assets

```bash
php artisan vendor:publish --tag=hashtagcms.assets
```

**Publishes**: `public/assets/hashtagcms/`

**Contents**:
- CSS files
- JavaScript files
- Images
- Fonts

---



#### Publish All Views
 
 ```bash
 php artisan vendor:publish --tag=hashtagcms.views.all
 ```
 
 **Publishes**:
 - `resources/views/vendor/hashtagcms/fe`
 - `resources/views/vendor/hashtagcms/be`
 - `resources/lang/vendor/hashtagcms`
 - `resources/assets/hashtagcms`
 
 ---
 
 #### Publish All

```bash
php artisan vendor:publish --provider="HashtagCMS\HashtagCMSServiceProvider"
```

Publishes everything at once.

---

## Common Command Workflows

### Fresh Installation

```bash
# 1. Install HashtagCMS
php artisan cms:install

# 2. Publish assets
php artisan vendor:publish --tag=hashtagcms.assets

# 3. Create storage link
php artisan storage:link

# 4. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

### Development Setup

```bash
# 1. Publish views for customization
php artisan vendor:publish --tag=hashtagcms.views.frontend
php artisan vendor:publish --tag=hashtagcms.views.admincommon

# 2. Publish config for customization
php artisan vendor:publish --tag=hashtagcms.config

# 3. Generate custom controller
php artisan cms:module-controller MyController

# 4. Generate custom model
php artisan cms:module-model MyModel
```

---

### Migration & Backup

```bash
# Export current data
php artisan cms:exportdata

# Fresh install
php artisan migrate:fresh

# Seed data
php artisan db:seed --class=HashtagCMS\\Database\\Seeds\\HashtagCMSDatabaseSeeder

# Import backed up data
php artisan cms:importdata
```

---

### Update Workflow

```bash
# 1. Backup data
php artisan cms:exportdata

# 2. Update package
composer update hashtagcms/hashtagcms

# 3. Run migrations
php artisan migrate

# 4. Publish new assets
php artisan vendor:publish --tag=hashtagcms.assets --force

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Next Steps

- [Configuration](26-configuration.md) - Configure your CMS
- [Deployment](27-deployment.md) - Deploy to production
- [Troubleshooting](29-troubleshooting.md) - Solve common issues
