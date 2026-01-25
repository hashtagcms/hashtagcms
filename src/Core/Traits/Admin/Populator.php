<?php

namespace HashtagCms\Core\Traits\Admin;

trait Populator
{
    /*
     * Popluate paginator data
     *
     * @param:
        $data["dataSource"] = $dataSource;
        $data["dataFields"] = $dataFields;
        $data["lang_id"] = 1;
        $data["actionFields"] = $actionFields;
     */
    public function pouplate($data = [])
    {

        $source = $data['dataSource'];

        $sourceWith = $data['dataWith'];

        $where = $data['dataWhere'];

        $fields = $data['dataFields'];

        $actionFields = $data['actionFields'];

        $extraData = $data['extraData'];

        if ($source != null) {

            $data['paginator'] = $source::getData($sourceWith, [], $where);

            /*echo "<pre>";
            print_r($data["paginator"]);
            echo "</pre>";*/

            if (count($actionFields) > 0 || count($data['moreActionFields']) > 0) {
                array_push($fields, htcms_admin_config('action_field_title'));
            }

            $data['fieldsName'] = $fields;
            $data['actionFields'] = $actionFields;
            $data['extraData'] = $this->fetchExtraData($extraData);

        } else {

            $data['paginator'] = null;
            $data['fieldsName'] = null;
            $data['extraData'] = null;

        }

        $viewName = $this->getViewNames(request()->module_info, 'listing');       

        if (request()->get('asJson') == 'true') {
            return $data;
        }

        return $this->viewNow($viewName, $data, false);

    }

    private function fetchExtraData($extra = null)
    {

        if ($extra == null) {
            return null;
        }

        $extras = [];

        foreach ($extra as $key => $extraData) {

            $source = $extraData['dataSource'];
            $method = $extraData['method'];
            $params = isset($extraData['params']) ?
                ((is_string($extraData['params']) ? [$extraData['params']] : $extraData['params']))
                : [];

            $checker = new \ReflectionMethod($source, $method);
            if ($checker->isStatic()) {
                $extras[$key] = call_user_func_array([$source, $method], $params);
            } else {
                $source_obj = new $source;
                $extras[$key] = $source_obj->{$method}($params);
            }

        }

        return $extras;
    }
}
