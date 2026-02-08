# HashtagCms Quick Reference Card

Quick reference for the most commonly used features, commands, and functions.

## 🚀 Installation

```bash
composer create-project laravel/laravel mysite
cd mysite
composer require hashtagcms/hashtagcms
php artisan cms:install
```

Visit: `http://your-domain/install`

## 📁 Directory Structure

```
hashtagcms/
├── config/
│   ├── hashtagcms.php          # Main config
│   ├── hashtagcmsadmin.php     # Admin config
│   └── hashtagcmscommon.php    # Common config
├── src/
│   ├── Models/                 # Eloquent models
│   ├── Http/Controllers/       # Controllers
│   ├── Core/                   # Core functionality
│   └── routes/                 # Routes
├── resources/views/vendor/hashtagcms/
│   ├── fe/                     # Frontend themes
│   └── be/                     # Backend themes
└── public/
    ├── assets/hashtagcms/      # Assets
    └── help/                   # This documentation
```

## 🎯 Console Commands

```bash
# Installation
php artisan cms:install

# Version
php artisan cms:version

# Generate code
php artisan cms:module-controller MyController
php artisan cms:module-model MyModel
php artisan cms:frontend-controller MyController
php artisan cms:validator MyValidator

# Data management
php artisan cms:exportdata
php artisan cms:importdata

# Publishing
php artisan vendor:publish --tag=hashtagcms.assets
php artisan vendor:publish --tag=hashtagcms.views.frontend
php artisan vendor:publish --tag=hashtagcms.views.admincommon
php artisan vendor:publish --tag=hashtagcms.config

# Cache management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🔧 Helper Functions

### Frontend Helpers
```php
// Menu
htcms_get_header_menu($active)
htcms_get_header_menu_html($maxLimit, $css)

// Content
htcms_get_body_content()
htcms_get_header_content($reverse)
htcms_get_footer_content($reverse)
htcms_get_header_title()
htcms_get_all_meta_tags()

// Info
htcms_get_site_info($key)
htcms_get_lang_info($key)
htcms_get_platform_info($key)
```

### Admin Helpers
```php
htcms_admin_config($key, $default)
htcms_admin_url($path)
htcms_admin_asset($path)
htcms_admin_user()
htcms_can($permission)
```

### Utility Helpers
```php
htcms_config($key, $default)
htcms_asset($path, $type)
htcms_media_url($path)
htcms_route($name, $parameters)
htcms_trans($key, $replace, $locale)
htcms_sanitize($content)
htcms_slug($text)
htcms_excerpt($text, $length)
```

### Feature Helpers
```php
htcms_has_feature($feature)
htcms_feature_tier()
htcms_feature_info()
```

## 🌐 API Endpoints

### Public Endpoints
```bash
# Health check
GET /api/hashtagcms/health-check

# Site config
GET /api/hashtagcms/public/configs/v1/site-configs
    ?site=htcms&api_secret=your_secret

# Load data
GET /api/hashtagcms/public/sites/v1/load-data
    ?site=htcms&link_rewrite=page&api_secret=your_secret

# Mobile data
GET /api/hashtagcms/public/sites/v1/load-data-mobile
    ?site=htcms&link_rewrite=page&api_secret=your_secret

# Register
POST /api/hashtagcms/public/user/v1/register
    {"name":"John","email":"john@example.com","password":"pass"}

# Login
POST /api/hashtagcms/public/user/v1/login
    {"email":"john@example.com","password":"pass"}
```

### Protected Endpoints
```bash
# User profile
GET /api/hashtagcms/user/v1/me
    Authorization: Bearer YOUR_TOKEN
```

## 📦 Module Types

| Type | Purpose | Example Use Case |
|------|---------|------------------|
| **Static** | CMS content | About page, Terms |
| **Query** | Database query | Blog list, Products |
| **Service** | External API | Weather, Stock prices |
| **Custom** | Custom logic | Interactive widgets |
| **QueryService** | Query + API | Enriched product data |
| **UrlService** | Dynamic API | User-specific data |

## 🎨 Blade Helpers

```blade
{{-- Menu --}}
{!! HashtagCms::getHeaderMenuHTML(10, 'nav-class') !!}

{{-- Content --}}
{!! HashtagCms::getBodyContent() !!}
{!! HashtagCms::getHeaderContent() !!}
{!! HashtagCms::getFooterContent() !!}

{{-- Meta --}}
{!! HashtagCms::getAllMetaTags() !!}
<title>{{ HashtagCms::getHeaderTitle() }}</title>

{{-- Assets --}}
<link href="{{ htcms_asset('style.css', 'css') }}">
<script src="{{ htcms_asset('app.js', 'js') }}"></script>

{{-- Media --}}
<img src="{{ htcms_media_url('image.jpg') }}">

{{-- Features --}}
@if(htcms_has_feature('sso'))
    <a href="/sso/login">SSO Login</a>
@endif

{{-- Layout Manager --}}
{!! app()->HashtagCms->layoutManager()->renderStack('scripts') !!}
```

## 🔐 Configuration

### .env File
```env
APP_URL=http://localhost
CONTEXT=hashtagcms
API_SECRET=your_random_secret

DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Feature Activation (Advanced)
HASHTAGCMS_FEATURE_TOKEN=your_token

```

### config/hashtagcms.php
```php
'context' => env('CONTEXT', 'hashtagcms'),
'domains' => [
    'site1.com' => 'site1',
    'site2.com' => 'site2',
],
'api_secrets' => [
    'site1' => env('API_SECRET', 'secret1'),
],
```

## 📊 Database Tables

### Core Tables
- `sites` - Sites
- `categories` - Categories
- `pages` - Pages/Blog posts
- `modules` - Modules
- `static_module_contents` - Static content
- `langs` - Languages
- `themes` - Themes
- `platforms` - Platforms
- `users` - Users
- `roles` - Roles
- `permissions` - Permissions

### Relationship Tables
- `category_site` - Category-Site
- `module_site` - Module assignments
- `site_user` - Site-User
- `category_gallery` - Category-Gallery
- `gallery_page` - Gallery-Page

## 🎯 Common Tasks

### Create Category
1. Admin → Category → Add New
2. Fill name, link_rewrite
3. Assign theme in Platform tab
4. Save

### Create Module
1. Admin → Module → Add New
2. Set name, alias, data_type
3. Configure type-specific settings
4. Save

### Assign Module
1. Admin → Homepage
2. Select Site, Platform, Category
3. Add Module
4. Set position
5. Save

### Create Page
1. Admin → Page → Add New
2. Fill title, content, link_rewrite
3. Select category
4. Set publish status
5. Save

## 🚨 Troubleshooting

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
composer dump-autoload
```

### Reset Database
```bash
# WARNING: Deletes all data
php artisan migrate:fresh
php artisan db:seed --class=HashtagCms\\Database\\Seeds\\HashtagCmsDatabaseSeeder
```

### Fix Permissions
```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Storage Link
```bash
php artisan storage:link
```

## 📱 Quick API Example

### JavaScript/Fetch
```javascript
// Get page data
fetch('http://api.example.com/api/hashtagcms/public/sites/v1/load-data?site=htcms&link_rewrite=about&api_secret=secret')
    .then(r => r.json())
    .then(data => console.log(data));

// Login
fetch('http://api.example.com/api/hashtagcms/public/user/v1/login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({email: 'user@example.com', password: 'pass'})
})
    .then(r => r.json())
    .then(data => localStorage.setItem('token', data.token));
```

## 🎓 Feature Tiers

| Tier | Features | Max Users | Support |
|------|----------|-----------|-------|
| Free | Core CMS | 5 | Community |
| Extended | + MongoDB | 25 | Community |
| Advanced | + SSO | 100 | Community |
| Scale | + Figma | Unlimited | Community |

## 📚 Documentation Links
- **Full Docs**: `00-index.md`
- **Installation**: `02-installation.md`
- **Quick Start**: `03-quick-start.md`
- **Modules**: `10-modules.md`
- **API**: `13-api-headless.md`
- **Commands**: `22-console-commands.md`
- **Helpers**: `31-helper-functions.md`
- **FAQ**: `33-faq.md`
- **Features**: `FEATURES.md`

## 🆘 Getting Help

- **Email**: hashtagcms.org@gmail.com
- **GitHub**: https://github.com/hashtagcms/hashtagcms
- **Docs**: `public/help/`

---

**Version**: 1.6.0 | **PHP**: 8.2+ | **Laravel**: 10+
