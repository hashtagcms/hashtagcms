# Caching in HashtagCMS

HashtagCMS includes a robust caching layer powered by Redis to ensure high performance and low latency for API responses. This document outlines how caching works, how to configure it, and how to manage the cache via API.

## Overview

The system uses a **Cache-Aside** pattern where API responses for content (Site Configs, Page Data, Blog Lists) are cached in Redis. Subsequent requests with the same parameters and headers are served directly from the cache.

The caching system is implemented via the `RedisCacheManager` class and integrated directly into the `ServiceController`.

## Configuration

### Cache Headers
Dynamic caching keys can include specific request headers. This is configured in `config/hashtagcmsapi.php`.

```php
// config/hashtagcmsapi.php
return [
    // ...
    'cache_header_include' => ['x-api-secret', 'x-site', 'x-lang', 'x-platform', 'x-category', 'x-microsite'],
];
```

Any header defined in this array will be appended to the cache key if present in the request. This allows you to cache different versions of content based on user context (e.g., different API keys or microsites).

## Cache Keys implementation

Cache keys are automatically generated using the following pattern:

```
{prefix}_{context}_{lang}_{platform}_{category}_{microsite}_{header_value_1}_{header_value_2}...
```

*   **Prefix**: E.g., `load_data`, `site_configs`, `blog_latests`
*   **Context**: Site identifier (e.g., `web`, `ios`)
*   **Standard Params**: Language and Platform
*   **Headers**: Values of headers specified in `cache_header_include`

## API Endpoints (Cached)

The following public API endpoints are automatically cached:

*   `GET /api/hashtagcms/public/configs/v1/site-configs`
*   `GET /api/hashtagcms/public/sites/v1/load-data`
*   `GET /api/hashtagcms/public/sites/v1/load-data-mobile`
*   `GET /api/hashtagcms/public/sites/v1/blog/latests`

**TTL (Time To Live)**: Defaults to 300 seconds (5 minutes) unless overridden.

## Cache Management API

HashtagCMS provides specific endpoints to manage the cache programmatically. behavior is controlled via the `CacheController`.

### Authentication
All cache management endpoints require authentication using the Site Context and API Secret defined in your `config/hashtagcms.php` (`api_secrets` array).

**Required Headers (or Query Params):**
*   `x-site` (or `site` param)
*   `x-api-secret` (or `api_secret` param)

### 1. List Cache Keys
Get a list of all keys currently in the Redis cache.

*   **Endpoint:** `GET /api/hashtagcms/public/cache/v1/keys`
*   **Parameters:**
    *   `pattern` (optional): Redis key pattern (default: `*`)
*   **Response:**
    ```json
    {
        "success": true,
        "count": 5,
        "data": [
            "hashtagcms_database_load_data_web_en_web_...",
            "hashtagcms_database_site_configs_web_en_web_..."
        ]
    }
    ```

### 2. Clear All Cache
Flush all cache entries. Use with caution.

*   **Endpoint:** `GET /api/hashtagcms/public/cache/v1/clear-all`
*   **Response:**
    ```json
    {
        "message": "Cache cleared successfully",
        "status": 200
    }
    ```

### 3. Clear Specific Key
Remove a single item from the cache.

*   **Endpoint:** `GET /api/hashtagcms/public/cache/v1/clear-key`
*   **Parameters:**
    *   `key`: The exact cache key to remove
*   **Response:**
    ```json
    {
        "message": "Cache key '...' cleared successfully",
        "status": 200
    }
    ```

## Requirements

*   **Redis**: Identify and configure your Redis connection in `.env`.
    ```env
    CACHE_DRIVER=redis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```
