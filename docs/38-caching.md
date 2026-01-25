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

HashtagCMS provides specific endpoints to manage the cache programmatically via the `CacheController`.

> **Note**: As of the latest version, all cache management endpoints are **protected** and require authentication using a valid Sanctum Bearer Token.

### Authentication
**1. Sanctum Token (Middleware Protection)**
*   `Authorization: Bearer <your-access-token>`

**2. Context Validation (Controller Logic)**
*   `x-site` (or `site` param): The site context key (e.g., `web`).
*   `x-api-secret` (or `api_secret` param): The API secret for the site context.

### 1. List Cache Keys
Get a list of all keys currently in the Redis cache.

*   **Endpoint:** `GET /api/hashtagcms/private/cache/v1/keys`
*   **Headers:**
    *   `Authorization`: Bearer `<token>`
    *   `x-site`: `<site-context>`
    *   `x-api-secret`: `<api-secret>`
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

### 2. Clear Site Config
*   **Endpoint:** `GET /api/hashtagcms/private/cache/v1/clear-site-config`
*   **Headers:** `Authorization`, `x-site`, `x-api-secret`
*   **Response:** `{"message": "Site config cache cleared"}`

### 3. Clear Load Data
*   **Endpoint:** `GET /api/hashtagcms/private/cache/v1/clear-load-data`
*   **Headers:** `Authorization`, `x-site`, `x-api-secret`
*   **Response:** `{"message": "Load data cache cleared"}`

### 4. Clear All Cache
Flush all cache entries. Use with caution.

*   **Endpoint:** `GET /api/hashtagcms/private/cache/v1/clear-all`
*   **Headers:** `Authorization`, `x-site`, `x-api-secret`
*   **Response:**
    ```json
    {
        "message": "Cache cleared successfully",
        "status": 200
    }
    ```

### 5. Clear Specific Key
Remove a single item from the cache.

*   **Endpoint:** `GET /api/hashtagcms/private/cache/v1/clear-key`
*   **Headers:** `Authorization`, `x-site`, `x-api-secret`
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
