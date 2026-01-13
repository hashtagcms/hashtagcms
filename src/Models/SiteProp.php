<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HashtagCms\Core\Scopes\SiteScope;

class SiteProp extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @override
     * boot
     */
    protected static function boot()
    {

        parent::boot();
        static::addGlobalScope(new SiteScope);
    }

    /**
     * Get Site Group
     *
     * @return array
     */
    public static function getSiteGroup()
    {
        $siteGroup = SiteProp::all('group_name')->where('group_name', '!=', '')->groupBy('group_name')->toArray();
        $all = [];
        foreach ($siteGroup as $key => $menu) {
            $all[] = $key;
        }

        return $all;
    }

    /**
     * with site
     *
     * @return mixed
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * with platform
     *
     * @return mixed
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
