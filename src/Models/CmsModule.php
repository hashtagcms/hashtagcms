<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;

class CmsModule extends AdminBaseModel
{
    protected $guarded = [];

    /**
     * Get admin modules
     *
     * @param  null  $user_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAdminModules($user_id = null)
    {
        return static::with(['child'])->orderBy('position', 'asc')->get();
    }

    /**
     * Get with child
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child()
    {
        return $this->hasMany(CmsModule::class, 'parent_id')->orderBy('position', 'asc');
    }

    /**
     * Get info by name
     *
     * @param  string  $name
     * @return mixed
     */
    /**
     * Get info by name
     *
     * @param  string  $name
     * @return mixed
     */
    public static function getInfoByName($name = '')
    {

        return static::with('child')->where('controller_name', '=', $name)->get()->first();

    }

    /**
     * Get module info from URL path using Longest Prefix Match
     *
     * @param string $path
     * @return mixed
     */
    public static function getModuleFromUrl($path = '')
    {
        if (empty($path)) {
            return null;
        }

        $segments = explode('/', $path);
        $candidates = [];

        // Generate all possible paths from longest to shortest
        // e.g. pro/audit-logs/edit/1 -> pro/audit-logs/edit/1, pro/audit-logs/edit, pro/audit-logs, pro
        while(count($segments) > 0) {
            $candidates[] = implode('/', $segments);
            array_pop($segments);
        }

        // Fetch all potential matches
        $matches = static::with('child')
            ->whereIn('controller_name', $candidates)
            ->get();

        // Return the one with the longest controller_name (most specific match)
        return $matches->sortByDesc(function ($module) {
            return strlen($module->controller_name);
        })->first();
    }

    /**
     * Get children
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->child()->with('children');
    }

    /**
     * Get Parents only
     *
     * @return mixed
     */
    public static function parentOnly()
    {
        return self::where('parent_id', '=', 0)->get();
    }

    /************ Create Module **********************/

    /**
     * Get all tables
     */
    public function getAllTables(): array
    {
        $tables = parent::getTables(); // Uses the driver-agnostic helper from AdminBaseModel

        $arr = [];
        $index = 0;
        foreach ($tables as $value) {
            $arr[] = ['id' => $index++, 'name' => $value];
        }

        return $arr;
    }

    /**
     * Get Fields name
     *
     * @return mixed
     */
    public function getFieldsName($table)
    {
        return \Illuminate\Support\Facades\Schema::getColumnListing($table);
    }
}
