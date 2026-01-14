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
HashtagCMS Version: 1.6.0
```

---

### Code Generation

#### cms:module-controller

Generate a new module controller.

**Usage**:
```bash
php artisan cms:module-controller {ControllerName}
```

**Example**:
```bash
php artisan cms:module-controller ProductController
```

**Generated File**: `app/Http/Controllers/Admin/ProductController.php`

**Template**:
```php
<?php

namespace App\Http\Controllers\Admin;

use HashtagCms\Http\Controllers\Admin\BaseAdminController;

class ProductController extends BaseAdminController
{
    protected $model = 'Product';
    protected $viewPath = 'admin.products';
    
    public function index()
    {
        // Your code here
    }
}
```

---

#### cms:module-model

Generate a new module model.

**Usage**:
```bash
php artisan cms:module-model {ModelName}
```

**Example**:
```bash
php artisan cms:module-model Product
```

**Generated File**: `app/Models/Product.php`

**Template**:
```php
<?php

namespace App\Models;

use HashtagCms\Models\AdminBaseModel;

class Product extends AdminBaseModel
{
    protected $table = 'products';
    protected $guarded = [];
    
    // Add your relationships and methods here
}
```

---

#### cms:frontend-controller

Generate a frontend controller.

**Usage**:
```bash
php artisan cms:frontend-controller {ControllerName}
```

**Example**:
```bash
php artisan cms:frontend-controller ShopController
```

**Generated File**: `app/Http/Controllers/ShopController.php`

---

#### cms:validator

Generate a form validator.

**Usage**:
```bash
php artisan cms:validator {ValidatorName}
```

**Example**:
```bash
php artisan cms:validator ProductValidator
```

**Generated File**: `app/Http/Requests/ProductValidator.php`

---

### Setup

#### setup:standalone

Setup the application for standalone usage (usually when using as an API or separate frontend).

**Usage**:
```bash
php artisan cms:setup-standalone {--force}
```

**What it does**:
- Publishes configuration, assets, and views
- Asks for HashtagCMS API URL, Token, and Secret
- Updates `.env` file with provided credentials
- Sets `HASHTAG_CMS_LOAD_MODULE_FROM_DB=false`

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
php artisan vendor:publish --provider="HashtagCms\HashtagCmsServiceProvider"
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
php artisan db:seed --class=HashtagCms\\Database\\Seeds\\HashtagCmsDatabaseSeeder

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
