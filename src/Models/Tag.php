<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get all images with the tag
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gallery()
    {
        return $this->belongsToMany(Gallery::class);
    }
}
