<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\Model;

class LangSite extends Model
{
    /**
     * with lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }
}
