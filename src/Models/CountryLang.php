<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\LangScope;

class CountryLang extends AdminBaseModel
{
    protected $guarded = [];

    protected static function boot()
    {

        parent::boot();
        static::addGlobalScope(new LangScope);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
