<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\SiteScope;

class StaticModuleContent extends AdminBaseModel
{
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
     * with lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {
        return $this->hasOne(StaticModuleContentLang::class);
    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(StaticModuleContentLang::class)->withoutGlobalScopes();
    }
}
