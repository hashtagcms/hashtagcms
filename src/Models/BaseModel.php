<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class BaseModel
 * @package HashtagCms\Models
 */

// IDE Helper for the dynamic parent
if (false) {
    class BaseModelParent extends EloquentModel
    {
    }
}

// Dynamic Parent Resolution
// Check if the Pro package is installed and has a FeatureLoader
if (class_exists('HashtagCmsPro\FeatureLoader')) {
    \HashtagCmsPro\FeatureLoader::boot();
}

// Default Fallback: Use standard Eloquent Model if the alias hasn't been created yet
if (!class_exists('HashtagCms\Models\BaseModelParent')) {
    class_alias(EloquentModel::class, 'HashtagCms\Models\BaseModelParent');
}

class BaseModel extends BaseModelParent
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
