<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HashtagCms\Core\Scopes\SiteScope;

class Festival extends AdminBaseModel
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
     * Get modules
     *
     * @param  null  $user_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllFestivals($user_id = null)
    {
        return static::where('publish_status', 1)->orderBy('position', 'asc')->get();
    }
}
