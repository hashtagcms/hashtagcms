@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!} - Settings" data-back-url="{{htcms_admin_path('category')}}" data-show-copy="false"  data-show-paste="false"></title-bar>


    <category-settings
            data-site-id="{{htcms_get_siteId_for_admin()}}"
            data-platform-id="{{$platform_id}}"
            data-microsite-id="{{$microsite_id}}"
            data-site-microsites="{{json_encode($siteMicrosites)}}"
            data-site-categories="{{json_encode($siteCategories)}}"
            data-site-themes="{{json_encode($siteThemes)}}"
            data-site-platforms="{{json_encode($sitePlatforms)}}"
            data-categories="{{json_encode($categories)}}"
            data-user-rights="{{json_encode($userRights)}}"

    ></category-settings>
@endsection
