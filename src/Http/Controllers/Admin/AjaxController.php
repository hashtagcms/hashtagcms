<?php

namespace HashtagCms\Http\Controllers\Admin;

class AjaxController extends BaseAdminController
{
    public function __construct()
    {

    }

    /**
     * Set Language in session
     *
     * @param  int  $lang_id
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function setLanguage($lang_id = 1)
    {

        return htcms_set_language_id_for_admin($lang_id);
    }

    public function setSiteId($site_id = 1)
    {

        return htcms_set_siteId_for_admin($site_id);

    }

    public function getInfo($model = '', $id = 0)
    {

        if ($this->checkPolicy('read', $model)) {
            //$namespace = app()->getNamespace();
            $namespace = config('hashtagcms.namespace');

            return $controller = app($namespace."Http\Controllers\Admin\\".ucfirst($model).'Controller')->getById($id);
        }

        return ['error' => true, 'message' => "Sorry! You don't have permission to read"];

    }
}
