# Deep Dive: Caching Architecture in HashtagCMS

This document provides a comprehensive technical overview of the caching mechanisms implemented in HashtagCMS. It is intended for developers who need to debug, extend, or optimize the caching layer.

## Overview

HashtagCMS utilizes a **hybrid caching strategy**:
1.  **API Response Caching**: Used heavily by the `ServiceController` to cache entire JSON payloads for headers-based requests (React/Mobile apps). This uses a custom `RedisCacheManager`.
2.  **Internal Object Caching**: Standard Laravel caching used for site resolution, permissions, and background job tracking.

## Configuration

To enable Redis caching, you must configure both the standard Laravel environment and the HashtagCMS specific configuration.

### 1. Environment (.env)
Ensure your cache driver is set to Redis.
```dotenv
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. HashtagCMS API Config
File: `config/hashtagcmsapi.php`
This file controls the behavioral toggles and TTLs (Time To Live) for the API layer.

```php
return [
    'api_cache_enabled' => true,      // Master switch for API caching
    'cache_load_data_ttl' => 14400,     // 4 Hours (Seconds)
    'cache_load_config_ttl' => 14400,   // 4 Hours
    
    // Headers to include in the cache key hash
    // (Ensures users with different secrets/contexts get unique cache entries)
    'cache_header_include' => [
        'x-api-secret', 
        'x-site', 
        'x-lang', 
        'x-platform', 
        'x-category', 
        'x-microsite'
    ],
];
```

### 3. Core Config
File: `config/hashtagcms.php`
some internal caching TTLs are defined here.
```php
'external_api_cache_prefix' => env('HASHTAGCMS_EXTERNAL_API_CACHE_PREFIX', 'api_'), // Prefix for public API keys
'enable_cache' => env('HASHTAGCMS_ENABLE_CACHE', true), // Master switch for Internal caching
'internal_cache_prefix' => env('HASHTAGCMS_INTERNAL_CACHE_PREFIX', 'internal_'), // Prefix for internal cache keys
'cache_site_config_ttl' => env('HASHTAGCMS_CACHE_SITE_CONFIG_TTL', 30), // In Minutes
```

## Cache Keys & Storage Structure

HashtagCMS constructs composite keys to ensure data isolation between different sites, languages, and platforms running on the same instance.

| Logic / Use Case | Cache Key Pattern | TTL (Default) | Source Location |
| :--- | :--- | :--- | :--- |
| **API: Load Data** | `[api_]load_data_{context}_{lang}_{platform}_{cat}_{micro}_{hash}` | 4 Hours | `ServiceController.php` |
| **API: Site Configs** | `[api_]site_configs_{context}_{lang}_{platform}_{hash}` | 4 Hours | `ServiceController.php` |
| **API: Blog List** | `[api_]blog_latests_{context}_{lang}_{plat}_{cat}_{lim}_{hash}` | 4 Hours | `ServiceController.php` |
| **Internal: Site Info** | `[internal_]site_config_{context}` | 30 Mins | `SiteConfigResolver.php` |
| **Internal: Permissions**| `[internal_]cms_permissions_boot` | 1 Hour | `AdminServiceProvider.php` |
| **Internal: External API** | `[internal_]external_data_{...}` | 30 Mins | `DataLoader.php` |
| **Internal: Cloning Job** | `[internal_]clone_job_{jobId}` | 24 Hours | `HandleSiteCloningProgress.php` |

> **Note**: `[api_]` and `[internal_]` denote the prefixes defined in `config/hashtagcms.php`. The default values are `api_` and `internal_` respectively.
> **Note**: `{hash}` is an MD5 hash of the request headers defined in `cache_header_include`.

## Core Files & Classes

If you need to debug *how* data is being cached, check these files:

### 1. `src/Core/Utils/RedisCacheManager.php`
This is a wrapper around `Illuminate\Support\Facades\Cache`. It adds safety checks (try-catch blocks) to ensure the application doesn't crash if Redis goes down. It also ensures the `api_cache_enabled` config is respected.

**Methods:**
*   `get($key)`
*   `set($key, $val, $ttl)`
*   `remember($key, $callback, $ttl)`
*   `generateKey($prefix, $params)`: Standardizes key generation (replaces spaces with underscores).

### 2. `src/Http/Controllers/Api/ServiceController.php`
This controller handles the primary content APIs (`loadData`, `siteConfigs`). It manually constructs cache keys and uses `RedisCacheManager::remember` to wrap the `ServiceLoader` calls.

### 3. `src/Core/Context/Resolvers/SiteConfigResolver.php`
This middleware/resolver runs early in the request lifecycle. It caches the mapping between a domain (e.g., `example.com`) and a context (e.g., `web_en`) to reduce database hits on the `sites` table.

## Debugging & Management API

HashtagCMS provides a dedicated `CacheController` to manage keys via API. This is useful for building admin dashboards or clearing cache during deployments.

**Controller**: `src/Http/Controllers/Api/CacheController.php`

### Endpoints

1.  **List Keys**
    *   `GET /api/hashtagcms/public/cache/v1/keys`
    *   Query Param: `?pattern=*[api_]load_data*`

2.  **Clear All**
    *   `POST /api/hashtagcms/public/cache/v1/clear-all`

3.  **Clear Specific Key**
    *   `POST /api/hashtagcms/public/cache/v1/clear-key`
    *   Body: `{ "key": "[api_]load_data_..." }`

4.  **Smart Clear (By Site)**
    *   `POST /api/hashtagcms/public/cache/v1/clear-site-config`
    *   `POST /api/hashtagcms/public/cache/v1/clear-load-data`
    *   *These endpoints automatically find and delete all keys matching the current site context.*

## Common Issues & Debugging

**Issue: Changes in Admin Panel not reflecting on Frontend.**
*   **Cause**: The API cache (TTL 4 hours) is serving old data.
*   **Fix**: Call the `clear-load-data` endpoint or simply pass `?clearCache=true` (if implemented in your frontend data fetcher) or manually clear Redis.

**Issue: "Redis connection refused" logs.**
*   **Cause**: Redis service is down, but `api_cache_enabled` is true.
*   **Behavior**: `RedisCacheManager` catches the exception and returns `null` or executes the callback directly. Use `tail -f storage/logs/laravel.log` to confirm.

**Issue: Cache keys colliding.**
*   **Cause**: Two sites sharing the same Redis instance without unique contexts.
*   **Fix**: Ensure every site has a unique `context` string in the `sites` table.
