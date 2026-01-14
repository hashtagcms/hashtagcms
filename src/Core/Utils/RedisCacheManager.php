<?php

namespace HashtagCms\Core\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RedisCacheManager
{
    /**
     * Default TTL in seconds (5 minutes)
     */
    const DEFAULT_TTL = 300;

    /**
     * Check if caching is enabled
     * @return bool
     */
    private static function isEnabled(): bool 
    {
        return config('hashtagcmsapi.redis_cache_enabled', false);
    }

    /**
     * Get cached value
     *
     * @param string $key
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
     * Set cache value
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl Seconds
     * @return bool
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
     * Get item from cache or store default value
     *
     * @param string $key
     * @param \Closure $callback
     * @param int|null $ttl Seconds
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
            // If cache fails, execute callback directly so application doesn't break
            return $callback();
        }
    }

    /**
     * Remove item from cache
     *
     * @param string $key
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
     * Flush all cache or by tags
     *
     * @param array|string|null $tags
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
     * Generate a standardized cache key
     *
     * @param string $prefix
     * @param array $params
     * @return string
     */
    public static function generateKey(string $prefix, array $params = []): string
    {
        $key = $prefix;
        foreach ($params as $k => $v) {
            if (!empty($v)) {
                $v = is_array($v) ? implode('_', $v) : $v;
                $key .= "_{$v}";
            }
        }
        // Sanitize key: replace standard restricted chars if needed, although specific Redis implementations handle most.
        // But to be safe let's replace spaces with underscores.
        return str_replace([' ', '/', '\\'], '_', $key);
    }

    /**
     * Get all keys matching pattern (Redis only)
     *
     * @param string $pattern
     * @return array
     */
    public static function getAllKeys(string $pattern = '*'): array
    {
        try {
            if (!self::isEnabled() || Cache::getDefaultDriver() !== 'redis') {
                 return [];
            }
            // Use the default redis connection
            $keys = Redis::connection('cache')->keys($pattern);
            
            return $keys;
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::getAllKeys failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear keys matching pattern
     *
     * @param string $pattern
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

            // Redis::del expects array of keys
            return Redis::connection('cache')->del($keys);
        } catch (\Throwable $e) {
            Log::error("RedisCacheManager::clearByPattern failed: " . $e->getMessage());
            return 0;
        }
    }
}
