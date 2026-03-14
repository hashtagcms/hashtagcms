# Deployment Guide

Deploying HashtagCMS is similar to deploying a standard Laravel application.

## Server Requirements
-   PHP 8.2+
-   Extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.
-   MySQL 5.7+

## Steps

### 1. Code Deploy
Clone your repository to the server.

### 2. Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 3. Permissions
Ensure `storage` and `bootstrap/cache` are writable.
```bash
chmod -R 775 storage bootstrap/cache
```

### 4. Configuration
Create `.env` (copy from `.env.example`).
**Critical**: Set `APP_ENV=production` and `APP_DEBUG=false`.

### 5. Database
```bash
php artisan migrate --force
```
*Note: Do not run `cms:install` in production as it might overwrite data. Use migrations.*

### 6. Caching
Enable Laravel optimizations.
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## SSL configuration
HashtagCMS forces HTTPS if `APP_URL` starts with `https://`. Ensure your Nginx/Apache config handles the SSL header forwarding correctly.
