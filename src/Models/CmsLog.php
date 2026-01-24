<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\SiteScope;

class CmsLog extends AdminBaseModel
{
    protected $table = 'logs';

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
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return void
     */
    public function module()
    {
        return $this->belongsTo(CmsModule::class);
    }
}
