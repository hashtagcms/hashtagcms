@if($withCss)
    <link rel="stylesheet" href="{{app()->HashtagCms->layoutManager()->parseStringForPath('%{css_path}%/app.css')}}" />
@endif
@if($withJs)
    <script>
        let _siteProps_ = <?php echo htcms_get_site_props(true); ?>;
    </script>
    <script src="{{app()->HashtagCms->layoutManager()->parseStringForPath('%{js_path}%/app.js')}}"></script>
@endif
{!! $data !!}