<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\Model;
use HashtagCms\Core\Utils\CacheKeys;

class CmsPermission extends Model
{
    protected $guarded = [];

    /**
     * Check module permission
     *
     * @return bool
     */
    public static function has($module_id, $user_id)
    {
        $ttl = (int)config('hashtagcmsadmin.permissions.cache_ttl', 0);

        // Check if caching is enabled (ttl > 0)
        if ($ttl <= 0) {
            $moduleInfo = CmsPermission::where('module_id', $module_id)->where('user_id', $user_id)->first();
            return ($moduleInfo) ? $moduleInfo : false;
        }

        $cachePrefix = config('hashtagcmsadmin.permissions.cache_key_prefix', CacheKeys::CMS_PERMISSIONS_CACHE_KEY_PREFIX);
        $cacheKey = $cachePrefix . $user_id . '_' . $module_id;

        if (session()->has($cacheKey)) {
            return session()->get($cacheKey);
        }

        $moduleInfo = CmsPermission::where('module_id', $module_id)->where('user_id', $user_id)->first();

        session()->put($cacheKey, ($moduleInfo) ? $moduleInfo : false);

        return ($moduleInfo) ? $moduleInfo : false;
    }

    public static function detachOldModules($user_id)
    {
        // We shouldn't clear the active editor's session (which might be admin),
        // we essentially just wipe DB. We'll add a helper or rely on the user 
        // next login or manual flush for proper persistence, but let's clear what we can.
        $keysData = session()->all();
        $cachePrefix = config('hashtagcmsadmin.permissions.cache_key_prefix', CacheKeys::CMS_PERMISSIONS_CACHE_KEY_PREFIX);

        foreach ($keysData as $key => $value) {
            if (str_starts_with($key, $cachePrefix . $user_id . '_')) {
                session()->forget($key);
            }
        }
        
        // Let's use the identical cache prefix style for menu allowed list
        session()->forget($cachePrefix . CacheKeys::CMS_PERMISSIONS_MENU_ALLOWED . $user_id);
        
        return CmsPermission::where('user_id', $user_id)->delete();
    }

    /**
     * Check module permission
     * Alias of has
     *
     * @return bool
     */
    public static function isReadyOnly($module_id, $user_id)
    {
        if (self::has($module_id, $user_id) == false) {
            return 0; //might be admin
        } else {
            return self::has($module_id, $user_id)->readonly == 1;
        }
    }
}
