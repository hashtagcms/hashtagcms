<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app('HashtagCmsLayoutManager')->isRtl() }}">

<head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        /* Get layout manager instance (LM) for shortcut */
        $layoutManager = app('HashtagCmsLayoutManager');
    @endphp
    {!! $layoutManager->getHeaderContent() !!}
    {!! $layoutManager->getMetaContent() !!}
    <title>{!! $layoutManager->getTitle() !!}</title>
    @php
        $lottieFile = $layoutManager->getViewThemeFolder() . "._layout_.lottie";
        $bodyCss = $layoutManager->getFestivalCss();
        $bodyBackground = "";
        if (!empty($layoutManager->getBodyBackgroundImage())) {
            $bodyBackground = $layoutManager->getBodyBackgroundImage() . ";background-repeat: no-repeat;background-attachment: fixed;background-position: center;";
        }
    @endphp

    <script>
        window.Laravel = {!! json_encode([
    'csrfToken' => csrf_token()
]) !!};
        let _siteProps_ = <?php echo htcms_get_site_props(true); ?>;
        window.Laravel.configData = {};
        window.Laravel.configData.media = <?php echo json_encode(config('hashtagcms.media')) ?>;
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {!! $layoutManager->renderStack('styles') !!}
</head>

<body class="{!!$bodyCss!!}" style="{!!$bodyBackground!!}">

    @include($lottieFile)
    {!! $layoutManager->getBodyContent() !!}
    <form id="logout-form" action="/login/doLogout" method="POST" style="display: none;">
        @csrf
        <input type="submit" value="Logout">
    </form>
    <div class="small" style="padding:10px; text-align: center;">
        <a title="#CMS" href="https://www.hashtagcms.org/">{{ htcms_trans('hashtagcms::links.powered_by') }} HashtagCMS</a>
    </div>

    {!! $layoutManager->getFooterContent() !!}
    <div class="text-[10px] text-slate-400 py-5 text-center">        
        <span class="hidden">{{ htcms_trans('hashtagcms::links.config_loader') }}: {{ htcms_get_performance('configLoadTime') }}ms |</span>
        <span class="hidden">{{ htcms_trans('hashtagcms::links.data_loader') }}: {{ htcms_get_performance('loadDataTime') }}ms |</span>
        <span>{{ htcms_trans('hashtagcms::links.page_render') }}: {{ htcms_get_performance('pageRenderTime') }}ms</span>
    </div>

    @if(env('GOOGLE_TAG_MANAGER_KEY') != '')
        <script async
            src="https://www.googletagmanager.com/gtag/js?id=<?php    echo env('GOOGLE_TAG_MANAGER_KEY'); ?>"></script>
        <script>
            try {
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }

                gtag('js', new Date());
                gtag('config', "<?php    echo env('GOOGLE_TAG_MANAGER_KEY'); ?>");
                let pageUrl = window.location.pathname.replace("/", "") == "" ? "homepage" : window.location.pathname.replace("/", "");
                gtag('event', 'pageView', {
                    'pageName': (_siteProps_.pageId === -1) ? _siteProps_.categoryName : _siteProps_.pageName,
                    'pageUrl': pageUrl
                });
            } catch (e) {
                console.error(e.message, "@", e.fileName);
            }
        </script>
    @endif
    <script>
        try {
            HashtagCms.Analytics.trackCmsPage({ categoryId: _siteProps_.categoryId, pageId: _siteProps_.pageId })
        } catch (e) {
            console.error(e.message, "@", e.fileName);
        }
    </script>
    {!! $layoutManager->renderStack('scripts') !!}
</body>

</html>