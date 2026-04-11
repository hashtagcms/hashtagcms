<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hashtag CMS</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.amplitude.com/script/f161277b936cf071b24095fe5299094e.js"></script>
    <script>window.amplitude.add(window.sessionReplay.plugin({ sampleRate: 1 })); window.amplitude.init('f161277b936cf071b24095fe5299094e', { "fetchRemoteConfig": true, "autocapture": { "attribution": true, "fileDownloads": true, "formInteractions": true, "pageViews": true, "sessions": true, "elementInteractions": true, "networkTracking": true, "webVitals": true, "frustrationInteractions": { "thrashedCursor": true, "errorClicks": true, "deadClicks": true, "rageClicks": true } } });</script>
    <style>
        code {
            border: 1px solid #e6e6e6;
            display: inline;
            background-color: #f2f2f2;
            text-align: center;
        }

        body {
            background: #fff;
        }

        /* Screensaver Animation */
        @keyframes drift {
            from {
                transform: translateY(0) translateX(0) rotate(0);
            }

            to {
                transform: translateY(-100vh) translateX(20vw) rotate(360deg);
            }
        }

        .star {
            position: fixed;
            background: linear-gradient(to bottom, #3b82f6, #6366f1);
            border-radius: 50%;
            opacity: 0.3;
            pointer-events: none;
            z-index: -10;
            filter: blur(1px);
        }

        /* Different sizes and speeds for parallax */
        .star-1 {
            width: 4px;
            height: 4px;
            animation: drift 25s linear infinite;
        }

        .star-2 {
            width: 8px;
            height: 8px;
            animation: drift 40s linear infinite;
            opacity: 0.2;
        }

        .star-3 {
            width: 12px;
            height: 12px;
            animation: drift 60s linear infinite;
            opacity: 0.1;
        }
    </style>

</head>

<body class="min-h-screen relative overflow-x-hidden">
    <!-- Screensaver Background Particles -->
    @for ($i = 0; $i < 50; $i++)
        <div class="star star-1 shadow-blue-400"
            style="top: {{ rand(0, 100) }}vh; left: {{ rand(0, 100) }}vw; animation-delay: -{{ rand(0, 25) }}s;"></div>
        <div class="star star-2 shadow-indigo-400"
            style="top: {{ rand(0, 100) }}vh; left: {{ rand(0, 100) }}vw; animation-delay: -{{ rand(0, 40) }}s;"></div>
        <div class="star star-3 shadow-blue-400"
            style="top: {{ rand(0, 100) }}vh; left: {{ rand(0, 100) }}vw; animation-delay: -{{ rand(0, 60) }}s;"></div>
    @endfor

    <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8 relative z-10" id="app">
        @php
            $siteInfo->domain = request()->getSchemeAndHttpHost();
        @endphp
        <site-installer data-site-info="{{json_encode($siteInfo)}}" data-is-installed="{{$isInstalled}}"
            data-needs-db-setup="{{isset($needsDbSetup) && $needsDbSetup ? '1' : '0'}}"></site-installer>
    </div>
    <script async src="/assets/hashtagcms/installer/js/installer.js"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CQNZRL6S2G"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-CQNZRL6S2G');

        gtag('event', 'site_installed', {
            'site_name': window.location.href
        });

        amplitude.track('Site Installed', {
            'site_name': window.location.href
        });
    </script>

</body>

</html>