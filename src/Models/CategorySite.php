<?php

namespace HashtagCms\Models;

class CategorySite extends AdminBaseModel
{
    protected $table = 'category_site';

    protected $guarded = [];

    /**
     * With Lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {
        return $this->hasOne(CategoryLang::class, 'category_id', 'category_id');
    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(CategoryLang::class, 'category_id', 'category_id')->withoutGlobalScopes();
    }
}
