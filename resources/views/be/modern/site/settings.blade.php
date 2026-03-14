@extends(htcms_admin_config('theme').'.index')

@section('content')
     @php
     $title = htcms_get_module_name(request()->module_info) . ' - <small>Settings for <span class="ml-1 font-black text-green-600 tracking-widest underline">'.$siteInfo->name.'</span></small>';
     @endphp
    <title-bar data-title="{{$title}}"
    data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"

    ></title-bar>
    <div class="space-y-6">
        @include(htcms_admin_get_view_path('site.tabs'))

        <div class="mt-8">
           <site-wise
            data-site-data="{{json_encode($selectedData)}}"
            data-all-data="{{json_encode($allData)}}"
            data-message="{{isset($allData['message']) ? $allData['message'] : ''}}"
            data-current-key="{{$activeTab}}"
            data-site-id="{{$siteInfo->id}}"
           >
           </site-wise>
        </div>
    </div>


@endsection
