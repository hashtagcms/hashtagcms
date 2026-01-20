<?php

namespace HashtagCms\Core\Traits\Admin\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate as GateFacade;
use Illuminate\Support\Str;
use HashtagCms\Models\Permission;

/**
 * Trait HasCrudHelpers
 *
 * Provides utility methods and getters for CRUD operations
 *
 * @package HashtagCms\Core\Traits\Admin\Crud
 */
trait HasCrudHelpers
{
    /**
     * Get All vars for listing etc
     *
     * @return array
     */
    private function getSegregatedData()
    {
        $data['actionFields'] = $this->getFilteredActions();
        $data['moreActionFields'] = $this->getMoreActionFields();
        $data['dataSource'] = $this->getDataSource();
        $data['dataWith'] = $this->getDataWith();
        $data['dataFields'] = $this->getDataFields();
        $data['dataWhere'] = $this->getDataWhere();
        $data['supportedLangs'] = $this->getSupportedSiteLang(htcms_get_siteId_for_admin());
        $data['hasLangMethod'] = 'false'; //string because used in javascript
        if($this->getDataSource() != null){
            $data['hasLangMethod'] = (method_exists($data['dataSource'], 'lang')) ? 'true' : 'false';
        }
        $data['user_rights'] = $this->getUserRights();
        $data['extraData'] = $this->getExtraDataForListing();
        $data['moreActionBarItems'] = $this->getMoreActionBarItems();
        $data['minResults'] = (isset($this->minResults)) ? $this->minResults : -1;

        return $data;
    }

    /**
     * Get Filtered Actions based on user permissions
     *
     * @return array
     */
    private function getFilteredActions()
    {
        $action = [];

        if (isset($this->actionFields)) {
            //Check in gate
            foreach ($this->actionFields as $field) {
                if (GateFacade::allows($field)) {
                    $action[] = $field;
                }
            }
        }

        return $action;
    }

    /**
     * Get Data Fields for display
     *
     * @return array
     */
    private function getDataFields()
    {
        $arr = isset($this->dataFields) ? $this->dataFields : [];

        if (isset($this->dataFields)) {
            if (is_array($this->dataFields) && Str::contains(implode('', Arr::flatten($this->dataFields)), ' as ') == 1) {
                $arr = [];

                foreach ($this->dataFields as $field) {
                    if (is_string($field) && Str::contains($field, ' as ')) {
                        $c = explode(' as ', $field);
                        $arr[] = ['key' => $c[0], 'label' => $c[1]];
                    } else {
                        $arr[] = $field;
                    }
                }
            } elseif ($this->dataFields == trim('*')) {
                $dataSource = $this->getDataSource();

                if ($dataSource != null) {
                    $model = new $dataSource;
                    $this->dataFields = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
                    $arr = $this->dataFields;
                }
            }
        }

        return $arr;
    }

    /**
     * Get more action fields
     *
     * @return array
     */
    private function getMoreActionFields()
    {
        return $this->moreActionFields ?? [];
    }

    /**
     * Get Data "with" relationships
     *
     * @return string
     */
    private function getDataWith()
    {
        return (isset($this->dataWith)) ? $this->dataWith : '';
    }

    /**
     * Get Data "where" conditions
     *
     * @return array
     */
    private function getDataWhere()
    {
        return (isset($this->dataWhere)) ? $this->dataWhere : [];
    }

    /**
     * Get extra data for listing
     *
     * @return mixed|null
     */
    private function getExtraDataForListing()
    {
        return (isset($this->bindDataWithListing)) ? $this->bindDataWithListing : null;
    }

    /**
     * Get more action bar items
     *
     * @return array
     */
    private function getMoreActionBarItems()
    {
        return (isset($this->moreActionBarItems)) ? $this->moreActionBarItems : [];
    }

    /**
     * Get Data Source model class
     *
     * @return string|null
     */
    private function getDataSource()
    {
        return (isset($this->dataSource)) ? $this->dataSource : null;
    }

    /**
     * Get Extra Data for add/edit forms
     *
     * @param array|null $bindData
     * @param bool $useBoth
     * @return array|null
     * @throws \ReflectionException
     */
    protected function getExtraDataForEdit($bindData = null, $useBoth = false)
    {
        if (isset($this->bindDataWithAddEdit) || $bindData != null) {
            $extras = [];
            $useData = ($bindData == null) ? $this->bindDataWithAddEdit : $bindData;

            if ($useBoth == true && isset($this->bindDataWithAddEdit)) {
                $useData = array_merge($this->bindDataWithAddEdit, $bindData);
            }

            foreach ($useData as $key => $extraData) {
                if (isset($extraData['dataSource'])) {
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
                } else {
                    $extras[$key] = $extraData;
                }
            }

            return $extras;
        }

        return null;
    }

    /**
     * Get back URL for navigation
     *
     * @param bool $isEdit
     * @param int $id
     * @return string
     */
    protected function getBackURL($isEdit = false, $id = 0): string
    {
        if ($id == 0) {
            $backURL = htcms_admin_path(request()->module_info->controller_name);
        } else {
            $backURL = url()->previous();
            $backURL_arr = explode('?', $backURL);
            $backURL_Base = $backURL_arr[0];

            parse_str(parse_url(html_entity_decode($backURL), PHP_URL_QUERY), $queryParams_arr);
            $queryParams_arr['id'] = $id;
            $params = http_build_query($queryParams_arr);

            $separator = '?';
            $backURL = $backURL_Base . $separator . $params;
        }

        //if editing directory
        if (url()->current() == url()->previous()) {
            $backURL = htcms_admin_path(request()->module_info->controller_name);
        }

        return $backURL;
    }

    /**
     * Get User Rights/Permissions
     *
     * @return array
     */
    protected function getUserRights()
    {
        return (request()->user()->isSuperAdmin() == 1) ? Arr::flatten(Permission::all('name')->toArray()) : request()->user()->rights();
    }

    /**
     * Get View Name
     *
     * @param mixed $moduleInfo
     * @param mixed $type
     * @return string|null
     */
    public function getViewNames($moduleInfo, $type='listing')
    {

        $defaults = array(
                        'listing'=>array('default'=>'common.listing', 'custom'=>'listing'), 
                        'add'=>array('default'=>'common.addedit', 'custom'=>'addedit'), 
                        'edit'=>array('default'=>'common.addedit', 'custom'=>'addedit'),
                        'show'=>array('default'=>'common.show', 'custom'=>'show'),
                        'sorter'=>array('default'=>'common.sorter', 'custom'=>'sorter')
                        );

        //Default fallback  
        $fallback = $defaults[$type]['default'] ?? null;

        $controller_name = $moduleInfo->controller_name;
        
        $targetView = '';

        // Default convention: controller_name.custom_suffix
        $customSuffix = $defaults[$type]['custom'] ?? 'common.error';
        $targetView = $controller_name . '.' . $customSuffix;

        // Check for DB override (only supported for listing and edit)
        if ($type === 'listing' || $type === 'edit') {
            $viewKeyMap = [
                'listing' => 'list_view_name',
                'edit' => 'edit_view_name'
            ];
            $viewProp = $viewKeyMap[$type];
            $controller_view = $moduleInfo->$viewProp ?? null;

            if (!empty($controller_view)) {
                $targetView = $controller_view;
            }
        }

        $targetView = ltrim($targetView, '.');

        // Handle Package Prefix
        if($moduleInfo->package != null && !str_contains($targetView, '::')) {
            $targetView = $moduleInfo->package.'::'.$targetView;
        }

        // Normalize path
        // Ensure we don't have leading dots if it's just a path, unless expected by helper
        // But previously we added leading dot for list_view_name? 
        // AdminHelper::htcms_admin_get_view_path handles ltrim('.', $name).
        // So we can be clean here.
        
        $targetView = str_replace('/', '.', $targetView);
        
        // If explicitly defined view starts with dot, preserve it? 
        // Actually, cleaner to rely on AdminHelper to prepend Theme if no :: exists.
        // example for below line 
        // [0] => "hashtagcms-pro::admin.users.listing"
        // [1] => "common.listing"
        return [$targetView, $fallback];
    }
    
}
