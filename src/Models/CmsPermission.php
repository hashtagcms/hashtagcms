<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\Model;

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
        $moduleInfo = CmsPermission::where('module_id', $module_id)->where('user_id', $user_id)->get()->first();

        return ($moduleInfo) ? $moduleInfo : false;
    }

    public static function detachOldModules($user_id)
    {
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
