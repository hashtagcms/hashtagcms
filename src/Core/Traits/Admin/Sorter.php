<?php

namespace HashtagCms\Core\Traits\Admin;

trait Sorter
{
    public function sortNow($data = [])
    {

        $source = $data['dataSource'];

        $sourceWith = $data['dataWith'];

        $fields = $data['dataFields'];

        $actionFields = $data['actionFields'];
        // $allModules = $data['all'];
        //$supportedLangs = $data['supportedLangs'];

        if ($source != null) {

            $data['paginator'] = $source::getData($sourceWith);
            /*echo "<pre>";
            print_r($data["paginator"]);
            echo "</pre>";*/

            if (count($actionFields) > 0) {
                array_push($fields, htcms_admin_config('action_field_title'));
            }

            $data['fieldsName'] = $fields;
            $data['actionFields'] = $actionFields;
            $data['showAddMore'] = false;

        } else {

            $data['paginator'] = null;
            $data['fieldsName'] = null;

        }

        $controller_name = request()->module_info->controller_name;

        //check here if module has in own folder
        $viewName = [$controller_name.'.sorter', 'common.sorter'];

        return $this->viewNow($viewName, $data, false);

    }
}
