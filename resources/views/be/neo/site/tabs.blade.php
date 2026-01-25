<ul class="nav nav-tabs mt-3" style="margin-left:20px">

    @foreach($tabs as $key=>$tab)
        @php
            $currentTab = str_replace(" ", "", strtolower($tab));
            $activeTab = str_replace(" ", "", strtolower($activeTab));
            $href = htcms_admin_path("site/settings/{$siteInfo->id}/$currentTab");
        @endphp
        @if($currentTab === $activeTab)
            @php
                $css = "nav-link active";
            @endphp
        @else
            @php
                $css = "nav-link";
            @endphp
        @endif

        <li role="presentation" class="nav-item"><a class="{{$css}}" href="{{$href}}">{{$tab}}</a></li>
    @endforeach
</ul>

