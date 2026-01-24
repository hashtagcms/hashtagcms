<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Hashtag CMS</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
            code{border: 1px solid #e6e6e6; display: block; padding:50px 5px; margin:30px 0 5px 0; background-color: #f2f2f2; text-align: center;}
        </style>

    </head>
    <body>


    <div class="container" style="width: 50%; margin-top: 50px" id="app">
        @php
            $siteInfo->domain = request()->getSchemeAndHttpHost();
        @endphp
        <site-installer
            data-site-info="{{json_encode($siteInfo)}}"
            data-is-installed="{{$isInstalled}}"
        ></site-installer>
    </div>
    <script async src="/assets/hashtagcms/installer/js/installer.js"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CQNZRL6S2G"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-CQNZRL6S2G');

        gtag('event', 'site_installed', {
            'site_name': window.location.href
        });
    </script>

</body>
</html>
