<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * with lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {

        return $this->hasOne(CountryLang::class);

    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(CountryLang::class)->withoutGlobalScopes();
    }

    /**
     * with city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function city()
    {
        return $this->hasMany(City::class);
    }

    /**
     * with zone
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * with currencies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currency()
    {
        return $this->hasMany(Currency::class);
    }

    /**
     * with site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function site()
    {
        return $this->belongsToMany(Site::class);
    }
}
