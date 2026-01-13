<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HashtagCms\Core\Scopes\SiteScope;

class ModuleProp extends AdminBaseModel
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
     * Get with lang
     */
    public function lang(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ModulePropLang::class);
    }

    /**
     * Get with lang
     */
    public function langs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ModulePropLang::class)->withoutGlobalScopes();
    }

    /**
     * with module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
