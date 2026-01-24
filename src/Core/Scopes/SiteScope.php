<?php

namespace HashtagCms\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SiteScope implements Scope
{
    /**
     * @param  Model  $model\
     */
    public function apply(Builder $builder, Model $model)
    {

        $site_id = htcms_get_siteId_for_admin();
        $builder->where('site_id', '=', $site_id);

    }
}
