<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE; chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' href='{{htcms_admin_asset("img/favicon.png")}}'>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('header_title', 'HashtagCMS : '.config('app.name')) </title>
    @php
        $resource_dir = htcms_admin_config('resource_dir');
    @endphp
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
    @stack('styles')
</head>

<body class="h-full overflow-hidden antialiased text-slate-900">

    <div id="app" class="flex flex-col h-screen overflow-hidden">
        
        <!-- Header / Topbar -->
        <header class="shrink-0 z-30">
            @include(htcms_admin_get_view_path('common.topbar'))
        </header>

        <div class="flex flex-1 overflow-hidden relative">
            
            <!-- Sidebar -->
            <aside class="h-full bg-[#0F172A] text-white shrink-0 transition-all duration-300 js_left_panel ease-in-out border-r border-slate-200">
                @include(htcms_admin_get_view_path('common.sidebar'))
            </aside>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-slate-50 relative custom-scrollbar">
                <div class="p-8 pb-24">
                    @yield('content')
                </div>
            </main>
        </div>

        @include(htcms_admin_get_view_path('common.footer'))
        @include(htcms_admin_get_view_path('common.components'))
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        {{ csrf_field() }}
    </form>
    
    <script src="{{htcms_admin_asset('js/app.js')}}"></script>
    @stack('scripts')
</body>

</html>