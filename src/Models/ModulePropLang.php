<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HashtagCms\Core\Scopes\LangScope;

class ModulePropLang extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new LangScope);
    }
}
