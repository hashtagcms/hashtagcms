# Troubleshooting

## Installation & Setup
### "Class not found"
-   Run `composer dump-autoload`.
-   If usage involves a new package, ensure it is installed.
-   **JWT Error**: If you see `Class "Firebase\JWT\JWT" not found`, run `composer require firebase/php-jwt`.

### "Key too long" (Migration Error)
-   Edit `app/Providers/AppServiceProvider.php` and add `Schema::defaultStringLength(191);` in the `boot` method.

## Assets & UI
### CSS/JS Returning 404
-   **Publish Assets**: Run `php artisan vendor:publish --tag=hashtagcms.assets --force`.
-   **Check Config**: verify `info.assets_path` in `config/hashtagcms.php`.
-   **Mixed Content**: Ensure `ASSET_URL` in `.env` matches your protocol (http vs https).

### Missing Images
-   Ensure the storage link exists: `php artisan storage:link`.
-   Check `media.http_path` configuration.

## Runtime Issues
### "Module not found" or Blank Space
-   **Database**: Check if the module exists in the `modules` table and is assigned to the current Category/Hook.
-   **View File**: Ensure the view file (e.g., `featured.blade.php`) exists in your **active theme** folder (`resources/views/fe/{theme}/`).

### "404 on API" or "Site Not Found"
-   **Domain Mapping**: Check `config/hashtagcms.php`. Your request domain (e.g., `localhost`) must map to a `context` (e.g., `web`).
-   **Headers**: APIs often require `x-site` or `site` parameter if domain detection fails.
-   **Secrets**: Public APIs need `api_secret` or `x-api-secret`.

## Permissions & Server
### "Permission Denied" (Logs/Storage)
The web server needs write access to `storage` and `bootstrap/cache`.
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### "Unable to create module" (Backend)
When using the CMS Module Builder, the server writes files.
Ensure write access to `app/Http/Controllers/Admin` and `app/Models`.

## The "Nuclear Option"
If nothing makes sense, clear all caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
composer dump-autoload
```
