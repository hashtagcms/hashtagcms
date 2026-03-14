@extends(htcms_admin_config('theme').'.index')

@section('content')
<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!} - Content Copier" data-back-url="{{$backUrl ?? ''}}" data-show-copy="false"  data-show-paste="false"></title-bar>
    @php


        $id = 0;
        //print_r($results);

        if(isset($results)) {
            extract($results);
        }

    @endphp
    <div class="flex flex-wrap -mx-4">
        <div class="w-full max-w-5xl mx-auto bg-white rounded-md p-12 shadow-lg border border-gray-100">
            <language-copier
                    data-languages="{{json_encode($languages)}}"
                    data-language-tables="{{json_encode($langTables)}}"
            ></language-copier>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

