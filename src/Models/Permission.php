<?php

namespace HashtagCms\Models;

class Permission extends AdminBaseModel
{
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
