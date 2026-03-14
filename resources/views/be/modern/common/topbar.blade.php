@php
$user = auth()->user();
$userName = $user->name;
$siteName = config('hashtagcms.info.site_name');
@endphp

<top-nav data-username="{{$userName}}"
         data-site-name="{{$siteName}}"
         data-current-site="{{htcms_get_siteId_for_admin()}}"
         data-is-admin="{{ auth()->user()->isAdmin() ? 1 : 0 }}"
         data-site-combo="true"
         data-logo="{{ htcms_admin_asset('img/logo.png') }}"
         data-logo-height="35">
</top-nav>
