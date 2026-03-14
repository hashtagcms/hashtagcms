@foreach($tabs as $key=>$tab)
    @php
        $currentTab = str_replace(" ", "", strtolower($tab));
        $activeTab = str_replace(" ", "", strtolower($activeTab));
        $href = htcms_admin_path("site/settings/{$siteInfo->id}/$currentTab");
        $data[] = array("label"=>$tab, "href"=>$href);
    @endphp
@endforeach
<div class="overflow-auto">
<tab-view 
  data-tabs="{{json_encode($data)}}"
  data-active-tab="{{$activeTab}}"
></tab-view>
</div>
