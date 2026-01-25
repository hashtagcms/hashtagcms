<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MediaLang extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];
}
