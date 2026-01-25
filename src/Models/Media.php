<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends AdminBaseModel
{
    use SoftDeletes;

    protected $table = 'medias';

    protected $guarded = [];

    public function lang()
    {
        return $this->hasMany(MediaLang::class);
    }
}
