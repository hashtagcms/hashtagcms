<?php

namespace HashtagCms\Models;

class CountrySite extends AdminBaseModel
{
    protected $table = 'country_site';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
