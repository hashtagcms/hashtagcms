<admin-modules class="sidebar" data-is-admin="{{$isAdmin}}"
    data-list="{{(isset($allModules) ? json_encode($allModules) : json_encode(array()))}}"
    data-controller-name="{{request()->module_info->controller_name ?? ''}}"
    data-modules-allowed="{{(isset($moduleAllowed) ? json_encode($moduleAllowed) : json_encode(array()))}}"
    data-hashtagcms-version="{{config('hashtagcmscommon.version')}}">

</admin-modules>