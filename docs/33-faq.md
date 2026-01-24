# Frequently Asked Questions (FAQ)

## General Questions

### What is HashtagCMS?

HashtagCMS is a powerful, flexible Content Management System built on Laravel that can function as both a traditional CMS and a headless CMS. It supports multi-site, multi-platform, and multi-language architectures out of the box.

### Is HashtagCMS free?

Yes, the core HashtagCMS is free and open-source under the MIT license. Premium features like MongoDB support, SSO login, and Figma integration require a paid license.

### What are the system requirements?

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Laravel 10+
- Node.js 16+ (for asset compilation)

### Can I use HashtagCMS for commercial projects?

Yes, HashtagCMS is released under the MIT license, which allows commercial use.

---

## Installation & Setup

### How do I install HashtagCMS?

```bash
composer create-project laravel/laravel mysite
cd mysite
composer require hashtagcms/hashtagcms
php artisan cms:install
```

See the [Installation Guide](02-installation.md) for detailed instructions.

### The installation fails with "database connection error"

Check your `.env` file and ensure:
1. Database credentials are correct
2. Database exists
3. Database server is running
4. PHP PDO extension is installed

### Installation fails with "Table 'cache' doesn't exist" or similar table error
During installation or first run, if you see an error like `Table 'packagestesting.cache' doesn't exist`, this is often due to a circular dependency where the application tries to use the database cache before the tables are created.

**Solution:**
Temporarily change the cache driver to `file` in your `.env` file:
```env
CACHE_STORE=file
```
After running migrations (`php artisan migrate`), you can switch it back to `database` if desired.

### How do I reset the admin password?

Use Laravel Tinker:
```bash
php artisan tinker
```

Then run:
```php
$user = \App\Models\User::where('email', 'admin@example.com')->first();
$user->password = bcrypt('new_password');
$user->save();
```

### Can I install HashtagCMS on shared hosting?

Yes, but ensure your hosting meets the system requirements. You may need to configure the web server to point to the `public` directory.

---

## Features & Functionality

### What is the difference between categories and pages?

- **Categories**: Organize content and define URL structure. They can contain multiple modules and pages.
- **Pages**: Individual content items (blog posts, articles, etc.) that belong to categories.

### How many module types are available?

Six module types:
1. **Static**: Content from CMS database
2. **Query**: Custom database queries
3. **Service**: External API calls
4. **Custom**: Custom logic
5. **QueryService**: Combination of query and service
6. **UrlService**: Dynamic service calls
7. **ServiceLater**: Similar to Custom module but it return service url to the view

See [Modules Guide](10-modules.md) for details.

### Can I create custom module types?

Yes, you can extend the module system by creating custom module loaders. See [Custom Modules](14-custom-modules.md).

### How do I add a new language?

1. Go to **Admin → Language**
2. Click **Add New Language**
3. Fill in language details (name, code, locale)
4. Assign the language to your site

### How do I create a multi-site setup?

1. Create a new site in **Admin → Site**
2. Configure domain mapping in `config/hashtagcms.php`
3. Set up API secrets for each site
4. Create site-specific content

See [Multi-Site Guide](05-multisite.md).

---

## API & Headless CMS

### Can I use HashtagCMS as a headless CMS?

Yes! HashtagCMS has a complete RESTful API. See [API & Headless CMS Guide](13-api-headless.md).

### How do I get an API secret?

API secrets are configured in `config/hashtagcms.php`:

```php
'api_secrets' => [
    'htcms' => env('API_SECRET', 'your_secret_key'),
],
```

### What authentication method does the API use?

Laravel Sanctum for token-based authentication.

### Can I use HashtagCMS with React/Vue/Angular?

Yes! The API is framework-agnostic. See examples in the [API Guide](13-api-headless.md).

### How do I handle CORS for API requests?

Configure CORS in `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['https://your-frontend.com'],
```

---

## Themes & Templates

### Where are theme files located?

```
resources/views/vendor/hashtagcms/fe/{theme-name}/
```

### How do I create a custom theme?

1. Publish views: `php artisan vendor:publish --tag=hashtagcms.views.frontend`
2. Copy the default theme folder
3. Customize the templates
4. Register the theme in the database

See [Themes Guide](11-themes.md).

### Can I use my own CSS framework?

Yes, you can use any CSS framework (Bootstrap, Tailwind, etc.) in your theme templates.

### How do I override a specific view?

Publish the views and edit the files in:
```
resources/views/vendor/hashtagcms/
```

---

## Content Management

### How do I upload images?

1. Go to **Admin → Gallery**
2. Click **Upload**
3. Select images
4. Add tags and descriptions

### What image formats are supported?

APNG, AVIF, GIF, JPG, JPEG, PNG, SVG, WebP, BMP, ICO, TIFF

### How do I create a blog?

1. Create a "Blog" category
2. Assign a theme to the category
3. Create a blog list module
4. Create blog posts in **Admin → Page**

### Can I schedule posts for future publication?

Yes, set the `publish_date` field when creating a page.

### How do I add a contact form?

1. Create a custom module
2. Use the Contact model
3. Create a form view
4. Process submissions in a controller

---

## Users & Permissions

### How do I create user roles?

1. Go to **Admin → Role**
2. Click **Add New Role**
3. Set role name and description
4. Assign permissions in **Admin → Rolesright**

### Can users have different permissions per site?

Yes, permissions can be site-specific through the role system.

### How do I restrict access to specific content?

Use middleware and permissions in your routes and controllers.

---

## Performance & Optimization

### How do I enable caching?

1. Set `cache_module` to 1 in module assignments
2. Use Laravel's cache system
3. Enable route caching: `php artisan route:cache`
4. Enable config caching: `php artisan config:cache`

### My site is slow. How can I optimize it?

1. Enable caching
2. Optimize database queries
3. Use a CDN for assets
4. Enable OPcache
5. Use queue workers for heavy tasks
6. Optimize images

See [Performance Optimization](28-performance.md).

### How do I clear all caches?

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Database & Data

### Can I use PostgreSQL instead of MySQL?

The core system is designed for MySQL/MariaDB. PostgreSQL support would require modifications.

### How do I backup my data?

```bash
php artisan cms:exportdata
```

This exports all data to **PHP Seeder files** in `database/seeders/`.

### How do I restore from backup?

```bash
php artisan cms:importdata
```

This acts as a wrapper for `db:seed` to populate your database from the generated seeders.

### Can I use MongoDB?

Yes, with a **Pro** license or higher. See [MongoDB Support](19-mongodb.md).

---

## Premium Features

### How do I get a license?

Contact sales at the official HashtagCMS website or email marghoobsuleman@gmail.com.

### What's included in each tier?

| Tier | Features | Max Users |
|------|----------|-----------|
| Free | Core CMS | 5 |
| Pro | + MongoDB Support, Analytics, Logging | 100 |
| Enterprise | + Figma Integration, Priority Support | Unlimited |

### How do I activate my license?

Add your license key to `.env`:
```env
HASHTAGCMS_LICENSE_KEY="your-license-key"

```

### Can I use one license for multiple sites?

Check your license agreement. Some licenses are per-domain.

### How do I check which features are available?

```php
use HashtagCms\Core\Utils\License;

$tier = License::getTier();
$features = License::getFeatures();
$hasMongoDb = License::hasFeature('mongo_support');
```

---

## Troubleshooting

### I get a 404 error on all pages

1. Check `.htaccess` file exists in public directory
2. Enable `mod_rewrite` for Apache
3. Clear route cache: `php artisan route:clear`
4. Check web server configuration

### Assets (CSS/JS) are not loading

1. Run `php artisan storage:link`
2. Publish assets: `php artisan vendor:publish --tag=hashtagcms.assets`
3. Check `APP_URL` in `.env`
4. Clear browser cache

### I can't login to admin panel

1. Clear browser cookies
2. Check database connection
3. Verify user exists in database
4. Reset password using Tinker

### Module data is not displaying

1. Check module is published
2. Verify module assignment to category
3. Check view file exists
4. Clear view cache: `php artisan view:clear`

### API returns 401 Unauthorized

1. Check API secret is correct
2. For protected endpoints, include Bearer token
3. Verify token hasn't expired
4. Check CORS configuration

---

## Development

### How do I create a custom controller?

```bash
php artisan cms:module-controller MyController
```

### How do I create a custom model?

```bash
php artisan cms:module-model MyModel
```

### Can I extend the admin panel?

Yes, create controllers in `app/Http/Controllers/Admin/` and they'll be automatically loaded.

### How do I add custom routes?

Add routes to `routes/web.php` or `routes/api.php` as usual in Laravel.

### Can I use HashtagCMS with Livewire?

Yes, HashtagCMS is compatible with Livewire.

---

## Deployment

### How do I deploy to production?

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Run `npm run build`
7. Set proper file permissions

See [Deployment Guide](27-deployment.md).

### What are the recommended server settings?

- PHP memory_limit: 256M or higher
- PHP max_execution_time: 60 or higher
- PHP upload_max_filesize: 20M or higher
- Enable OPcache
- Use PHP-FPM for better performance

### Can I use HashtagCMS with Docker?

Yes, you can containerize HashtagCMS. Create a Dockerfile based on Laravel requirements.

---

## Migration & Updates

### How do I update HashtagCMS?

```bash
# Backup data first
php artisan cms:exportdata

# Update package
composer update hashtagcms/hashtagcms

# Run migrations
php artisan migrate

# Publish new assets
php artisan vendor:publish --tag=hashtagcms.assets --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Will updates break my customizations?

Published views and configs won't be overwritten unless you use `--force`. Always backup before updating.

### How do I migrate from another CMS?

Create custom import scripts using the `cms:importdata` command as a reference.

---

## Security

### Is HashtagCMS secure?

HashtagCMS follows Laravel security best practices including:
- CSRF protection
- XSS prevention
- SQL injection protection
- Password hashing
- Secure authentication

### How do I secure the admin panel?

1. Use strong passwords
2. Enable 2FA (custom implementation)
3. Restrict admin access by IP
4. Use HTTPS
5. Keep software updated

### How do I report security vulnerabilities?

Email security issues to marghoobsuleman@gmail.com instead of using the issue tracker.

---

## Support

### Where can I get help?

1. Check this FAQ
2. Review [Troubleshooting Guide](29-troubleshooting.md)
3. Check GitHub issues
4. Contact support: marghoobsuleman@gmail.com

### Is there a community forum?

Check the GitHub repository for discussions and community support.

### Do you offer commercial support?

Yes, contact marghoobsuleman@gmail.com for commercial support options.

---

## Contributing

### Can I contribute to HashtagCMS?

Yes! Contributions are welcome. See [contributing.md](../../contributing.md) for guidelines.

### How do I report bugs?

Open an issue on GitHub: https://github.com/hashtagcms/hashtagcms/issues

### How do I suggest new features?

Open a feature request on GitHub or email marghoobsuleman@gmail.com.

---

## Additional Resources

- [Installation Guide](02-installation.md)
- [Quick Start](03-quick-start.md)
- [API Documentation](13-api-headless.md)
- [Troubleshooting](29-troubleshooting.md)
- [GitHub Repository](https://github.com/hashtagcms/hashtagcms)
