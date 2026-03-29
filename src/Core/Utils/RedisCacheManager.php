<?php

namespace HashtagCms\Core\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * RedisCacheManager — central cache utility for HashtagCMS.
 *
 * Key structure
 * -------------
 * Every cache key is composed of three layers:
 *
 *   {namespace}:{source_type}:{entity}_{discriminators}
 *
 * Layer 1 — namespace (project isolation on shared Redis)
 *   Comes from HASHTAGCMS_CACHE_NAMESPACE env var, defaulting to APP_NAME.
 *   Slugified automatically (lowercase, special chars → "_", stripped of leading/trailing "_").
 *   Example: APP_NAME="My Project"  →  namespace = "my_project"
 *
 * Layer 2 — source_type prefix (returned by the get*Prefix() helpers)
 *   With namespace:    "{namespace}:{type}:"    e.g. "my_project:cms:"
 *   Without namespace: "{type}_"               e.g. "cms_"  (legacy, backwards-compatible)
 *   cms  — internal / admin / permissions / boot cache
 *   db   — data loaded from the local database
 *   ex   — data fetched from an external CMS API source
 *   api  — raw external API responses
 *
 * Layer 3 — entity constant + discriminators (defined in CacheKeys)
 *   e.g.  site_config_{context}
 *         external_data_{context}_{lang}_{platform}_{category}_{microsite}_{paramHash}
 *         clone_job_{jobId}
 *
 * Full key examples
 * -----------------
 *   my_project:cms:permissions_boot
 *   my_project:db:site_config_main_site
 *   my_project:ex:external_data_main_site_en_web_news__a1b2c3
 *   my_project:cms:admin_modules_master_list
 *
 * Redis debugging
 * ---------------
 *   Use getAllKeys() / clearByPattern() from this class — they handle
 *   Laravel's store prefix automatically.
 *
 *   Or via redis-cli (manual, must include Laravel's store prefix):
 *     redis-cli KEYS "my_project_my_project:*"   ← laravel_prefix + namespace
 *
 * Known limitations
 * -----------------
 *   - get(), set(), remember(), forget(), flush() are all gated by isEnabled()
 *     (config: hashtagcmsapi.api_cache_enabled). Direct Cache:: calls elsewhere in
 *     the codebase are gated by hashtagcms.enable_cache instead. These are two
 *     separate flags — ensure both are set consistently.
 *   - forget() and flush() also respect isEnabled(), so stale cache entries cannot
 *     be cleared programmatically while caching is disabled.
 *   - generateKey() silently skips params where the value is falsy (0, '', false,
 *     null) due to PHP's empty(). Pass string discriminators to avoid missed segments.
 *   - getAllKeys() / clearByPattern() use Redis KEYS which is O(N) and blocks the
 *     server. Use only for debugging or low-traffic maintenance tasks, never on the
 *     hot request path.
 */
class RedisCacheManager
{
    /**
     * Default TTL in seconds (5 minutes).
     */
    const DEFAULT_TTL = 300;

    /**
     * Returns true when API caching is active (hashtagcmsapi.api_cache_enabled).
     * Note: direct Cache:: calls in the codebase are controlled by hashtagcms.enable_cache.
     */
    private static function isEnabled(): bool
    {
        return config('hashtagcmsapi.api_cache_enabled', false);
    }

    /**
     * Get a value from cache. Returns null when caching is disabled or on error.
     *
     * @param string $key Full cache key including prefix (use get*Prefix() helpers)
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        if (!self::isEnabled()) return null;
        try {
            return Cache::get($key);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::get failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store a value in cache.
     *
     * @param string   $key  Full cache key including prefix
     * @param mixed    $value
     * @param int|null $ttl  TTL in seconds. Defaults to DEFAULT_TTL (300 s).
     * @return bool False when caching is disabled or on error.
     */
    public static function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        if (!self::isEnabled()) return false;
        $ttl = $ttl ?? self::DEFAULT_TTL;
        try {
            return Cache::put($key, $value, $ttl);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::set failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Return a cached value or compute and store it via $callback.
     * If caching is disabled the callback is always executed directly.
     * If the cache store throws, the callback is executed as fallback so the
     * application continues to work even with a degraded cache backend.
     *
     * @param string   $key      Full cache key including prefix
     * @param \Closure $callback Produces the value to cache on a miss
     * @param int|null $ttl      TTL in seconds. Defaults to DEFAULT_TTL (300 s).
     * @return mixed
     */
    public static function remember(string $key, \Closure $callback, ?int $ttl = null): mixed
    {
        if (!self::isEnabled()) return $callback();
        $ttl = $ttl ?? self::DEFAULT_TTL;
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::remember failed: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Remove a single key from cache.
     * Returns false when caching is disabled (note: stale keys cannot be cleared
     * while the cache is disabled — call Cache::forget() directly in that case).
     *
     * @param string $key Full cache key including prefix
     * @return bool
     */
    public static function forget(string $key): bool
    {
        if (!self::isEnabled()) return false;
        try {
            return Cache::forget($key);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::forget failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Flush all cache entries, or only entries matching the given tag(s).
     * Tag-based flush requires a cache driver that supports tagging (Redis, Memcached).
     * Returns false when caching is disabled.
     *
     * @param array|string|null $tags Optional tag or array of tags to flush selectively.
     * @return bool
     */
    public static function flush(array|string|null $tags = null): bool
    {
        if (!self::isEnabled()) return false;
        try {
            if ($tags) {
                return Cache::tags($tags)->flush();
            }
            return Cache::flush();
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::flush failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build a cache key from a prefix and an ordered list of discriminator params.
     *
     * Each non-empty param value is appended as "_{value}". Array values are
     * joined with "_". Spaces, slashes, and backslashes in values are replaced
     * with "_".
     *
     * CAUTION: falsy values (0, false, '', null) are silently skipped due to
     * PHP's empty() check. Use string discriminators to avoid unintended key
     * collisions when a segment is legitimately zero.
     *
     * @param string $prefix Source-type prefix from get*Prefix() helpers
     * @param array  $params Ordered discriminator values (e.g. context, lang, platform)
     * @return string
     */
    public static function generateKey(string $prefix, array $params = []): string
    {
        $key = $prefix;
        foreach ($params as $v) {
            if (!empty($v)) {
                $v = is_array($v) ? implode('_', $v) : $v;
                $key .= "_{$v}";
            }
        }
        return str_replace([' ', '/', '\\'], '_', $key);
    }

    /**
     * Get all keys matching pattern (Redis only).
     *
     * The pattern is matched against the logical cache key (without Laravel's store
     * prefix). Internally the store prefix is prepended automatically so the raw
     * Redis KEYS command finds the right entries.
     *
     * Example usage:
     *   RedisCacheManager::getAllKeys('myapp:*')        // all keys for project "myapp"
     *   RedisCacheManager::getAllKeys('myapp:ex:*')     // all external-source cache
     *   RedisCacheManager::getAllKeys('myapp:cms:site_config_*')
     *
     * NOTE: avoid KEYS on large production Redis instances; use SCAN instead for
     * high-traffic environments. This helper is intended for debugging / manual cache
     * inspection, not hot-path code.
     *
     * @param string $pattern Glob pattern matched against logical (un-prefixed) keys
     * @return array Raw Redis keys (including store prefix) ready for del()
     */
    public static function getAllKeys(string $pattern = '*'): array
    {
        try {
            if (!self::isEnabled() || Cache::getDefaultDriver() !== 'redis') {
                return [];
            }

            // Laravel's RedisStore silently prepends a store prefix (e.g. "myproject_")
            // to every key it writes. The raw Redis connection does NOT add this prefix,
            // so we must include it in the KEYS pattern ourselves, otherwise no keys
            // will ever be matched.
            $storePrefix = '';
            $store = Cache::getStore();
            if (method_exists($store, 'getPrefix')) {
                $storePrefix = $store->getPrefix();
            }

            return Redis::connection('cache')->keys($storePrefix . $pattern);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::getAllKeys failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear all keys matching a glob pattern (Redis only).
     *
     * Uses the same store-prefix-aware pattern matching as getAllKeys().
     * Returns the number of keys deleted, or 0 on failure / non-Redis driver.
     *
     * @param string $pattern Glob pattern matched against logical (un-prefixed) keys
     * @return int Number of keys removed
     */
    public static function clearByPattern(string $pattern): int
    {
        try {
            if (!self::isEnabled() || Cache::getDefaultDriver() !== 'redis') {
                return 0;
            }

            $keys = self::getAllKeys($pattern);
            if (empty($keys)) {
                return 0;
            }

            // Redis::del expects an array of raw keys (with store prefix included)
            return Redis::connection('cache')->del($keys);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::clearByPattern failed: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Return the project namespace used to isolate cache keys on a shared Redis server.
     *
     * Resolution order:
     *   1. HASHTAGCMS_CACHE_NAMESPACE env var  (explicit, highest priority)
     *   2. APP_NAME env var                    (automatic, per-project default)
     *   3. Empty string                        (legacy mode — no namespace)
     *
     * The raw value is slugified: lowercased, any run of non-alphanumeric characters
     * collapsed to a single underscore, leading/trailing underscores stripped.
     *   "My Project"  →  "my_project"
     *   "-MyApp-"     →  "myapp"
     *
     * When the result is non-empty, get*Prefix() helpers return "{namespace}:{type}:"
     * instead of the legacy "{type}_" format.
     *
     * @return string Slugified namespace, or "" for legacy (no-namespace) mode.
     */
    public static function getNamespace(): string
    {
        $ns = (string) config('hashtagcms.cache_namespace', '');
        // Slugify: lowercase, collapse non-alphanumeric runs to "_", strip leading/trailing "_"
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($ns)));
        return trim($slug, '_');
    }

    /**
     * Compose the source-type prefix with optional namespace.
     *
     * With namespace:    "{namespace}:{type}:"   e.g. "my_project:cms:"
     * Without namespace: config value or legacy default,  e.g. "cms_"
     *
     * @param string $type          Short source-type label (cms, db, ex, api)
     * @param string $configKey     Dotted config path for the legacy prefix value
     * @param string $legacyDefault Fallback when config is absent and no namespace
     * @return string
     */
    private static function buildPrefix(string $type, string $configKey, string $legacyDefault): string
    {
        $namespace = self::getNamespace();
        if ($namespace !== '') {
            return "{$namespace}:{$type}:";
        }
        return config($configKey, $legacyDefault);
    }

    /**
     * Prefix for raw external API response cache keys.
     * Type label: "api"  →  e.g. "my_project:api:" or legacy "api_"
     * Config: hashtagcms.externals.external_api_cache_prefix / HASHTAGCMS_EXTERNAL_API_CACHE_PREFIX
     */
    public static function getApiPrefix(): string
    {
        return self::buildPrefix('api', 'hashtagcms.externals.external_api_cache_prefix', 'api_');
    }

    /**
     * Prefix for internal / admin / permissions cache keys.
     * Type label: "cms"  →  e.g. "my_project:cms:" or legacy "cms_"
     * Config: hashtagcms.internal_cache_prefix / HASHTAGCMS_INTERNAL_CACHE_PREFIX
     */
    public static function getInternalPrefix(): string
    {
        return self::buildPrefix('cms', 'hashtagcms.internal_cache_prefix', 'cms_');
    }

    /**
     * Prefix for data loaded from the local database.
     * Type label: "db"   →  e.g. "my_project:db:" or legacy "db_"
     * Config: hashtagcms.database_cache_prefix / HASHTAGCMS_DATABASE_CACHE_PREFIX
     */
    public static function getDatabasePrefix(): string
    {
        return self::buildPrefix('db', 'hashtagcms.database_cache_prefix', 'db_');
    }

    /**
     * Prefix for data fetched from an external CMS API source.
     * Type label: "ex"   →  e.g. "my_project:ex:" or legacy "ex_"
     * Config: hashtagcms.external_source_cache_prefix / HASHTAGCMS_EXTERNAL_SOURCE_CACHE_PREFIX
     */
    public static function getExternalSourcePrefix(): string
    {
        return self::buildPrefix('ex', 'hashtagcms.external_source_cache_prefix', 'ex_');
    }
}
