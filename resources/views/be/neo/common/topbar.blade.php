@php
$user = auth()->user();
$userName = $user->name;
@endphp

<top-nav data-username="{{$userName}}"
         data-site-name="{{htcms_admin_config('site_label')}}"
         data-current-site="{{htcms_get_siteId_for_admin()}}"
         data-is-admin="{{$isAdmin}}"
         data-site-combo="true"
         data-logo-height="35">
</top-nav>
