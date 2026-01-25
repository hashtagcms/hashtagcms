<?php

namespace HashtagCms\Models;

class Hook extends AdminBaseModel
{
    protected $guarded = [];

    public function site()
    {
        return $this->belongsToMany(Site::class);
    }

    /**
     * Get content type
     *
     * @return array
     */
    public static function getDirections()
    {
        return static::getEnumValues('hooks', 'direction');
    }
}
