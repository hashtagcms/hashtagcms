<?php

namespace HashtagCms\Models;

class City extends AdminBaseModel
{
    protected $guarded = [];

    /**
     * Get Data with Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function country()
    {
        return $this->hasOne(CountryLang::class, 'country_id', 'country_id');
    }

    /**
     * Get Data with zone
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function zone()
    {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }
}
