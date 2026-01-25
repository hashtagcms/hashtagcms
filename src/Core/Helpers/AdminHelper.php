<?php

/**
 * Load view - This is just for admin
 */
use Illuminate\Support\Str;

if (! function_exists('htcms_admin_view')) {
    /**
     * Load View. Load a default view if view is not availble.
     *
     * @param  array  $data
     * @return mixed
     */
    function htcms_admin_view($name, $data = [], $isAjax = false)
    {

        if (is_array($name)) {
            $name = array_map(
                function ($arg) {
                    return htcms_admin_get_view_path($arg);
                }, $name);

        } else {

            //this is default message if view is not available.
            if (! htcms_is_view_exist($name)) {
                $data['title'] = 'Whooops!';
                $data['message'] = 'View not found! '.htcms_admin_get_view_path($name);
            }
            $name = [htcms_admin_get_view_path($name), htcms_admin_get_view_path('common.error')]; //if there is no view defined
        }

        if ($isAjax) {
            return response()->json($data, 400);
        }
        
        return view()->first($name, $data);
    }

}

if (! function_exists('htcms_admin_asset')) {
    /**
     * Check if url is secure
     *
     * @return bool
     */
    function htcms_admin_asset($url = '')
    {
        $preFix = Str::contains($url, '?') ? '&' : '?';
        $path = htcms_admin_config('theme_assets');
        $appUrl = env('APP_URL');

        return $appUrl.'/'.$path.'/'.$url.$preFix.'verions='.htcms_admin_config('version');
    }
}

if (! function_exists('htcms_admin_get_view_path')) {
    /**
     * Get View path
     *
     * @return string
     */
    function htcms_admin_get_view_path($name)
    {
        //If it contains namespace '::' we should respect that.
        //We also need to handle the leading dot added by traits if a namespace is present.
        if (Str::contains($name, '::')) {
            return ltrim($name, '.');
        }

        $theme = htcms_admin_theme();

        return $theme.'.'.ltrim($name, '.');
    }

}

if (! function_exists('htcms_admin_config')) {
    /**
     * Get Admin Config
     *
     * @param  null  $key
     * @param  null  $notCmsInfoObj
     * @return mixed|string
     */
    function htcms_admin_config($key = null, $notCmsInfoObj = null)
    {

        if ($notCmsInfoObj == null) {
            return ($key == null) ? json_encode(config('hashtagcmsadmin.cmsInfo')) : config('hashtagcmsadmin.cmsInfo.'.$key);
        }

        return ($key == null) ? json_encode(config('hashtagcmsadmin')) : config('hashtagcmsadmin.'.$key);

    }

}

if (! function_exists('htcms_admin_theme')) {

    /**
     * Get current admin theme name
     *
     * @return mixed
     */
    function htcms_admin_theme()
    {
        return config('hashtagcmsadmin.cmsInfo.theme');
    }

}

if (! function_exists('htcms_admin_base_resource')) {
    /**
     * Get Admin resource path
     *
     * @return mixed
     */
    function htcms_admin_base_resource()
    {
        return config('hashtagcmsadmin.cmsInfo.resource_dir');
    }

}

if (! function_exists('htcms_admin_path')) {
    /**
     * Get Admin path
     *
     * @param  string  $name
     * @return mixed|string
     */
    function htcms_admin_path($name = '', $queryParams = [])
    {
        $params = '';
        if (count($queryParams) > 0) {
            $params = [];
            foreach ($queryParams as $key => $val) {
                $params[] = "$key=$val";
            }
            $params = '?'.implode('&', $params);
        }

        return ($name != '') ? htcms_admin_config('base_path').'/'.$name.$params : htcms_admin_config('base_path');

    }

}

if (! function_exists('htcms_is_view_exist')) {
    /**
     * Check if view is exisit in current theme
     *
     * @return bool
     */
    function htcms_is_view_exist($name)
    {

        //Handle leading dots
        $cleanName = ltrim($name, '.');

        if (Str::contains($name, '::')) {
            return view()->exists($cleanName);
        }

        $theme = config('hashtagcmsadmin.cmsInfo.theme');
        if (view()->exists($theme.'.'.$cleanName)) {
            return true;
        }
        

        
        return false;
    }

}

if (! function_exists('htcms_get_media')) {
    /**
     * @param  string  $file
     * @return string
     */
    function htcms_get_media($file = '')
    {
        if ($file != '' && $file != null) {
            $media_path = config('hashtagcmsadmin.cmsInfo.media_path');

            return $media_path.'/'.$file;
        }

        return '';
    }

}

if (! function_exists('htcms_set_language_id_for_admin')) {

    /**
     * Set language id in session
     */
    function htcms_set_language_id_for_admin($id)
    {
        return session()->put('lang_id', $id);
    }
}

if (! function_exists('htcms_get_language_id_for_admin')) {

    /**
     * get current language id
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    function htcms_get_language_id_for_admin()
    {
        return session('lang_id', 1);
    }

}

if (! function_exists('htcms_set_siteId_for_admin')) {

    /**
     * Set language id in session
     */
    function htcms_set_siteId_for_admin($id)
    {
        return session()->put('site_id', $id);
    }

}

if (! function_exists('htcms_get_siteId_for_admin')) {

    /**
     * Get current site id
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    function htcms_get_siteId_for_admin()
    {
        return session('site_id', 1);
    }

}

if (! function_exists('htcms_get_save_path')) {

    /**
     * Get store path for view form
     *
     * @return mixed|string
     */
    function htcms_get_save_path($module_name)
    {
        return htcms_admin_path($module_name).'/store';
    }

}

if (! function_exists('htcms_get_module_name')) {

    /**
     * Get store path for view form
     *
     * @param  $module_info|Object
     * @return mixed|string
     */
    function htcms_get_module_name($module_info)
    {
        if ($module_info == null) {
            return '';
        }

        return Str::singular($module_info->name);
    }

}

if (! function_exists('htcms_get_current_date')) {

    /**
     * Get current date for db insert
     *
     * @return false|string
     */
    function htcms_get_current_date()
    {
        return date('Y-m-d H:i:s');
    }
}
