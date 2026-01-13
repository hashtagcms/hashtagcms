<?php

namespace HashtagCms\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LangScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $lang_id = htcms_get_language_id_for_admin();
        $builder->where('lang_id', '=', $lang_id);
    }
}
