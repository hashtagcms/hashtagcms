<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! app()->HashtagCms->layoutManager()->getHeaderContent(); !!}
    {!! app()->HashtagCms->layoutManager()->getMetaContent(); !!}
    <title>{!! app()->HashtagCms->layoutManager()->getTitle(); !!}</title>
    @php
        $lottieFile = app()->HashtagCms->layoutManager()->getViewThemeFolder()."._layout_.lottie";
        $bodyCss = app()->HashtagCms->layoutManager()->getFestivalCss();
        $bodyBackground = "";
        if (!empty(app()->HashtagCms->layoutManager()->getBodyBackgroundImage())) {
            $bodyBackground = app()->HashtagCms->layoutManager()->getBodyBackgroundImage(). ";background-repeat: no-repeat;background-attachment: fixed;background-position: center;";
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
</head>
<body class="{!!$bodyCss!!}"
      style="{!!$bodyBackground!!}">
@include($lottieFile)
{!! app()->HashtagCms->layoutManager()->getBodyContent(); !!}
<form id="logout-form" action="/login/doLogout" method="POST" style="display: none;">
    @csrf
    <input type="submit" value="Logout">
</form>
<div class="small" style="padding:10px; text-align: center;">
    <a title="#CMS" href="https://www.hashtagcms.org/">Powered by HashtagCms.org</a>
</div>

{!! app()->HashtagCms->layoutManager()->getFooterContent(); !!}


@if(env('GOOGLE_TAG_MANAGER_KEY') != '')
    <script async
            src="https://www.googletagmanager.com/gtag/js?id=<?php echo env('GOOGLE_TAG_MANAGER_KEY'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', "<?php echo env('GOOGLE_TAG_MANAGER_KEY'); ?>");

        gtag('event', 'pageView', {
            'pageName': (_siteProps_.pageId === -1) ? _siteProps_.categoryName : _siteProps_.pageName
        });
    </script>
@endif
<script>
    try {
        HashtagCms.Analytics.init(_siteProps_);
        HashtagCms.AppConfig.setConfigData(window.Laravel.configData);
        //HashtagCms.Analytics.trackPageView(_siteProps_.categoryName + ""+_siteProps_.pageName)
    } catch (e) {
        console.error(e.message, "@", e.fileName);
    }
</script>
{!! app()->HashtagCms->layoutManager()->renderStack('scripts') !!}
</body>
</html>
