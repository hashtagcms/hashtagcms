# Installation Guide

This guide will walk you through installing HashtagCMS step by step.

## Prerequisites

Before installing HashtagCMS, ensure your system meets these requirements:

### System Requirements

- **PHP**: 8.2 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache or Nginx
- **Composer**: Latest version
- **Node.js**: 16+ (for asset compilation - optional)
- **NPM**: 8+ (for asset compilation - optional)

### PHP Extensions Required

- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo

## Installation Steps

### Step 1: Create a New Laravel Project

```bash
composer create-project laravel/laravel mysite
cd mysite
```

### Step 2: Install HashtagCMS Package

```bash
composer require hashtagcms/hashtagcms
```

### Step 3: Configure Environment

Open the `.env` file and configure your application:

```env
APP_NAME="My HashtagCMS Site"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

# Optional: Set context for multi-site
CONTEXT=hashtagcms

# Optional: API Secret for external API access
API_SECRET=your_random_secret_key
```

### Step 4: Update User Model

Open `app/Models/User.php` and make the following changes:

**Remove or comment out:**
```php
// use Illuminate\Foundation\Auth\User as Authenticatable;
```

**Add:**
```php
use HashtagCms\User as Authenticatable;
```

Your User model should look like this:

```php
<?php

namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
use HashtagCms\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### Step 5: Update Routes (Optional)

Open `routes/web.php` and comment out the default welcome route:

```php
/*
Route::get('/', function () {
    return view('welcome');
});
*/
```

### Step 6: Run Installation Command

```bash
php artisan cms:install
```

This command will:
- Run database migrations
- Seed initial data
- Publish assets
- Publish views

**Note**: If tables already exist, the installer will ask if you want to perform a fresh installation.

### Step 7: Configure Site via Browser

After the installation command completes, visit:

```
http://your-app-url/install
```

For example:
```
http://localhost/install
```

Follow the on-screen instructions to:
1. Set up your admin account
2. Configure site settings
3. Set up default language
4. Configure platform settings

### Step 8: Access Admin Panel

Once configuration is complete, access the admin panel at:

```
http://your-app-url/admin
```

There is no default credentials. You have fill the below information upon login. 
- **Email**: admin@example.com
- **Password**: password

## Post-Installation Steps

### 1. Publish Additional Assets (Optional)

If you need to customize views or assets:

```bash
# Publish frontend views
php artisan vendor:publish --tag=hashtagcms.views.frontend

# Publish admin views
php artisan vendor:publish --tag=hashtagcms.views.admincommon

# Publish all assets
php artisan vendor:publish --tag=hashtagcms.assets

# Publish configuration files
php artisan vendor:publish --tag=hashtagcms.config
```

### 2. Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for media files.

### 3. Set Permissions

Ensure these directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/media
```

### 4. Configure Web Server

#### Apache

Create or update `.htaccess` in the public directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx

Add this to your Nginx configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Compile Assets (Optional)

If you plan to modify frontend assets:

```bash
npm install
npm run dev
```

For production:

```bash
npm run build
```

## Premium Features Setup

### MongoDB Support (Starter Tier+)

If you have a Pro license or higher and want to use MongoDB:

1. Install MongoDB driver:
```bash
composer require mongodb/laravel-mongodb
```

2. Add license key to `.env`:
```env
HASHTAGCMS_LICENSE_KEY="your-license-key"

```

3. Configure MongoDB connection in `config/database.php`

See [MongoDB Support](19-mongodb.md) for detailed instructions.

### License Configuration

Add your license information to `.env`:

```env
# License Configuration
HASHTAGCMS_LICENSE_KEY="your-license-key-here"

# Optional: Cache duration in seconds (default: 7 days)
HASHTAGCMS_LICENSE_CACHE_DURATION=604800
```

## Verification

### Check Installation

1. **Frontend**: Visit `http://your-app-url/`
2. **Admin Panel**: Visit `http://your-app-url/admin`
3. **API Health**: Visit `http://your-app-url/api/hashtagcms/health-check`

### Test Database Connection

```bash
php artisan tinker
```

Then run:
```php
DB::connection()->getPdo();
\HashtagCms\Models\Site::first();
```

### Check Version

```bash
php artisan cms:version
```

## Troubleshooting

### Database Connection Error

**Error**: "Could not connect to database"

**Solution**:
1. Verify database credentials in `.env`
2. Ensure database exists
3. Check database server is running
4. Verify PHP PDO extension is installed

### Permission Denied

**Error**: "Permission denied" when accessing files

**Solution**:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 404 on All Routes

**Error**: All routes return 404

**Solution**:
1. Check `.htaccess` file exists in public directory
2. Enable `mod_rewrite` for Apache
3. Clear route cache: `php artisan route:clear`
4. Clear config cache: `php artisan config:clear`

### Assets Not Loading

**Error**: CSS/JS files not loading

**Solution**:
1. Run `php artisan storage:link`
2. Publish assets: `php artisan vendor:publish --tag=hashtagcms.assets`
3. Check `APP_URL` in `.env` matches your actual URL
4. Clear browser cache

### Installation Already Exists

**Error**: "Site is already configured"

**Solution**:
```bash
# Fresh installation (WARNING: This will delete all data)
php artisan migrate:fresh
php artisan db:seed --class=HashtagCms\\Database\\Seeds\\HashtagCmsDatabaseSeeder
```

## Next Steps

- [Quick Start Guide](03-quick-start.md) - Get started with basic operations
- [Configuration](26-configuration.md) - Learn about configuration options
- [Architecture Overview](04-architecture.md) - Understand the system architecture

## Additional Resources

- **Installation Video**: Coming soon
- **Docker Setup**: See Docker documentation
- **Cloud Deployment**: See [Deployment Guide](27-deployment.md)

## Getting Help

If you encounter issues during installation:

1. Check [Troubleshooting](29-troubleshooting.md)
2. Review [FAQ](33-faq.md)
3. Check GitHub issues: https://github.com/hashtagcms/hashtagcms/issues
4. Contact support: marghoobsuleman@gmail.com
