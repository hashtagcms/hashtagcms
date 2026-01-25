@extends(htcms_admin_config('theme').'.index')

@section('content')

    @php
        //default settings
        //dd($categoryModules);
        $site_id = (isset($site_id)) ? $site_id : $siteInfo->id;
        $microsite_id = (isset($microsite_id)) ? $microsite_id : 0;
        $category_id = (isset($category_id)) ? $category_id : $siteInfo->category_id;
        $platform_id = (isset($platform_id)) ? $platform_id : 1;

        $categories = isset($siteInfo->category) ? $siteInfo->category : [];
        $microsites = isset($siteInfo->microsite) ? $siteInfo->microsite : [];
        $platforms = isset($siteInfo->platform) ? $siteInfo->platform : [];

    @endphp
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-show-copy="false" data-show-paste="false" data-show-back="false" data-show-expand="true"></title-bar>
    <page-manager
            data-site-id="{{$site_id}}"
            data-microsite-id="{{$microsite_id}}"
            data-platform-id="{{$platform_id}}"
            data-category-id="{{$category_id}}"

            data-categories="{{$categories}}"
            data-microsites="{{$microsites}}"
            data-platforms="{{$platforms}}"

            data-site-info="{{json_encode($siteInfo)}}"
            data-hook-info="{{json_encode($siteInfo->hook)}}"
            data-all-modules="{{json_encode($allModules)}}"
            data-category-modules="{{json_encode($categoryModules)}}"
            data-category-info="{{json_encode($categoryInfo)}}"
            data-theme-info="{{json_encode($themeInfo)}}"
            data-user-rights="{{json_encode($user_rights)}}"
            data-is-module-readonly="{{$isModuleReadonly}}"
            data-all-sites="{{json_encode($allSites)}}"
    >
    </page-manager>
@endsection

