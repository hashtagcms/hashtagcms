<?php

namespace HashtagCms\Core\Traits\Admin;

trait Searcher
{
    public function searchData($data)
    {

        if (! $this->checkPolicy('read')) {

            //@todo: Get this message from common place
            $errorData['errorTitle'] = 'Access Denied';
            $errorData['errorMessage'] = "Sorry! You don't have permission to view this page.";

            return htcms_admin_view('common.error', $errorData);

        }

        $source = $data['dataSource'];

        $sourceWith = $data['dataWith'];

        $where = $data['dataWhere'];

        $fields = $data['dataFields'];

        $actionFields = $data['actionFields'];

        if ($source != null) {

            $all = request()->all();
            $segments = request()->segments();

            // /admin/controller/search/id == 4 segements
            if (count($segments) == 4 && is_numeric($segments[3])) {

                $q = $segments[3];
                //get primary key here
                $paginator = $source::searchData($sourceWith, 'id', '=', $q, $where);
                $data['searchParams'] = ['q' => $q, 'o' => '=', 'f' => 'id'];

            } elseif (isset($all['q'])) {

                $arr = json_decode(urldecode($all['q']));
                $data['searchParams'] = json_decode(urldecode($all['q']));

                $paginator = $source::searchData($sourceWith, $arr->f, $arr->o, $arr->q, $where);

            } else {

                $paginator = $source::getData($sourceWith, [], $where);

            }

            $data['paginator'] = $paginator;

            if (count($actionFields) > 0) {
                array_push($fields, htcms_admin_config('action_field_title'));
            }

            $data['fieldsName'] = $fields;
            $data['actionFields'] = $actionFields;

        } else {

            $data['paginator'] = null;
            $data['fieldsName'] = null;

        }

        $controller_name = request()->module_info->controller_name;
        $controller_view = request()->module_info->list_view_name;
        $listingView = ($controller_view == null || empty($controller_view)) ? $controller_name.'.listing' : '.'.$controller_view;
        $listingView = str_replace('/', '.', $listingView);
        //check here if module has in own folder
        $viewName = [$listingView, 'common.listing']; //use view or default

        if (request()->get('asJson') == 'true') {
            return $data;
        }

        return $this->viewNow($viewName, $data, false);

    }
}
