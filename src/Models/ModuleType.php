<?php

namespace HashtagCms\Models;

use HashtagCms\Models\Module;

class ModuleType extends AdminBaseModel
{
    protected $guarded = [];

    /**
     * Get all active module types merged with existing module data types
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveTypes()
    {
        // Get defined published types
        $definedTypes = self::where('publish_status', 1)->get();
        $definedNames = $definedTypes->pluck('name')->toArray();

        // Get unique data_type from modules table using Module model
        $existingDataTypes = Module::select('data_type')
            ->distinct()
            ->pluck('data_type')
            ->toArray();

        // Find types that exist in modules but not in module_types
        $missingTypes = array_diff($existingDataTypes, $definedNames);

        foreach ($missingTypes as $typeName) {
            if (!empty($typeName)) {
                $definedTypes->push((object)[
                    'name' => $typeName,
                    'label' => $typeName,
                    'icon' => 'fa fa-question-circle',
                    'description' => "This data type is currently in use but not formally defined in the module types registry.",
                    'field_hint' => 'Check module configuration',
                    'placeholder' => 'Enter handler value...',
                    'publish_status' => 1
                ]);
            }
        }

        return $definedTypes;
    }
}
