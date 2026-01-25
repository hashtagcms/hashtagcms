@extends(htcms_admin_config('theme').'.index')

@section('content')
<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!} - Content Copier" data-back-url="{{$backURL}}" data-show-copy="false"  data-show-paste="false"></title-bar>
    @php


        $id = 0;
        $name = old('name', 'Create your form here...');

        //print_r($results);

        if(isset($results)) {
            extract($results);
        }

    @endphp
    <div class="row">
        <div class="admin-form">
            <language-copier
                    data-languages="{{json_encode($languages)}}"
                    data-language-tables="{{json_encode($langTables)}}"
            ></language-copier>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

