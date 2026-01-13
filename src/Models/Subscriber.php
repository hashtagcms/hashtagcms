<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use HashtagCms\Core\Scopes\SiteScope;

class Subscriber extends AdminBaseModel
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
     * Get today's contacts
     *
     * @return mixed
     */
    public static function today()
    {
        return self::whereDate('created_at', Carbon::today())->get();
    }
}
