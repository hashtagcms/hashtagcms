# Installation & Setup

## Requirements
HashtagCMS requires the following:
- PHP >= 8.1
- Laravel >= 10.x
- Composer
- Database: MySQL 5.7+ / MariaDB or MongoDB (with Pro)

## Step-by-Step Installation

### 1. Install via Composer
If installing into an existing Laravel project:

```bash
composer require hashtagcms/core
```

### 2. Publish Assets & Config
Run the installer command to automatically publish migrations, assets, and configuration files.

```bash
php artisan cms:install
```
This wizard will guide you through:
-   Database configuration.
-   Creating the Super Admin account.
-   Seeding default data (Sites, Platforms, Zones).

### 3. Database Migration
If you skipped migration during install:
```bash
php artisan migrate
```

### 4. Configuration
The main configuration file is located at `config/hashtagcms.php`. Key settings include:

-   `info.site_id`: Default site ID (usually 1).
-   `namespace`: Namespace for your custom modules (e.g., `App\CMS`).

## Environment Variables

Ensure your `.env` is configured correctly:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hashtagcms
DB_USERNAME=root
DB_PASSWORD=
```

For MongoDB (Pro):
```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=hashtagcms
```

## Verify Installation

Start your server:
```bash
php artisan serve
```

-   **Admin Panel**: `http://localhost:8000/admin`
-   **Frontend**: `http://localhost:8000/`
