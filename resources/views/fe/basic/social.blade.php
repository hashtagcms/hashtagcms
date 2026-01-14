<div class="col-6 col-md-4">
    <h5>{{__("hashtagcms::links.follow_us")}}</h5>
    <div class="socials">
        @foreach($data as $social)
            @php
                $sLabel = ucfirst($social['name']);
                $sCss = strtolower($social['name']);
                $sHref = trim($social['value']);
            @endphp
            <a class="social social-{{$sCss}}" href="{{$sHref}}" title="{{$sLabel}}" target="_blank" rel="noopener nofollow"><i class="fa fa-{{$sCss}}"></i></a>
        @endforeach
    </div>
</div>