<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\LangScope;

class PageLang extends AdminBaseModel
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new LangScope);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
