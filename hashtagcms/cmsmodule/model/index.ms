<?php

namespace {{namespace}}Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HashtagCms\Models\AdminBaseModel;

{{useModels}}
{{useLangScope}}

class {{model}} extends AdminBaseModel
{
    use SoftDeletes;
    protected $guarded = array();
    {{relationMethods}}

    {{langScopeBoot}}

}
