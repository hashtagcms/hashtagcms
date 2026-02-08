<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE; chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' href='{{htcms_admin_asset("img/favicon.png")}}'>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('header_title', 'HashtagCms') </title>
    @php
        $resource_dir = htcms_admin_config('resource_dir');
    @endphp
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ htcms_admin_asset('css/app.css') }}" />
    
    <script>
        (function() {
            const htcmsAdminConfig = (() => {
                try {
                    const config = {!! htcms_admin_config() ?: '{}' !!};
                    const parsed = typeof config === 'object' ? config : JSON.parse(config);
                    return (parsed && typeof parsed === 'object') ? parsed : {};
                } catch (e) {
                    console.error("HashtagCms: Failed to parse adminConfig", e);
                    return {};
                }
            })();

            window.HashtagCms = {
                ...(window.HashtagCms || {}),
                csrfToken: "{{ csrf_token() }}",
                adminConfig: htcmsAdminConfig
            };            

            /** deprecated */
            window.Laravel = {
                ...(window.Laravel || {}),
                csrfToken: "{{ csrf_token() }}",
                adminConfig: htcmsAdminConfig,
                htcmsAdminConfig: function (key) {
                    return (window.Laravel.adminConfig && window.Laravel.adminConfig[key]) ? window.Laravel.adminConfig[key] : null;
                }
            };
        })();
    </script>
    @stack('links')
</head>

<body>

    <div id="app" class="d-flex flex-column min-vh-100">
        @include(htcms_admin_get_view_path('common.topbar'))
        <div class="container-fluid flex-grow-1">
            <div class="row mb-5">
                <div class="col col-lg-2 js_left_panel">
                    @include(htcms_admin_get_view_path('common.sidebar'))
                </div>
                <div class="col pt-3 js_right_panel">
                    @yield('content')
                </div>
            </div>
        </div>
        @include(htcms_admin_get_view_path('common.footer'))
        @include(htcms_admin_get_view_path('common.components'))
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script src="{{htcms_admin_asset('js/app.js')}}"></script>
    @stack('scripts')
</body>

</html>