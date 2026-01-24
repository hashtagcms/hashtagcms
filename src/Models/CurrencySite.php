<?php

namespace HashtagCms\Models;

class CurrencySite extends AdminBaseModel
{
    protected $table = 'currency_site';

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
