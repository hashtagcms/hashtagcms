<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\LangScope;

class CategoryLang extends AdminBaseModel
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new LangScope);
    }

    public function theme()
    {
        return $this->hasOne(Theme::class, 'id', 'theme_id');
    }

    public function site()
    {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }

    public function prefered()
    {

        return $this->belongsTo(Category::class)->select(['id', 'category_id']);
    }
}
