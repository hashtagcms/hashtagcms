<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get genders
     *
     * @return array
     */
    public static function genders()
    {
        return ['Male', 'Female', 'Others'];
    }
}
