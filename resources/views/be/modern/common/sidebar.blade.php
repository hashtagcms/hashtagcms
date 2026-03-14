<admin-modules class="sidebar" data-is-admin="{{ auth()->user()->isAdmin() ? 1 : 0 }}"
    data-list="{{(isset($allModules) ? json_encode($allModules) : json_encode(array()))}}"
    data-controller-name="{{request()->module_info->controller_name ?? ''}}"
    data-hashtagcms-version="{{config('hashtagcmscommon.version')}}">
</admin-modules>