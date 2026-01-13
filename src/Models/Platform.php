<?php

namespace HashtagCms\Models;

class Platform extends AdminBaseModel
{
    protected $guarded = [];

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
