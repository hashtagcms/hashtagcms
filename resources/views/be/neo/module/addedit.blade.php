@extends(htcms_admin_config('theme').'.index')

@section('content')

<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}" data-show-copy="true" data-show-paste="false" data-copy-paste-auto-init="false"></title-bar>
    <div class="row">
        <div class="admin-form">
            <front-module-creator
                    data-form-action="{{htcms_get_save_path(request()->module_info->controller_name)}}"
                    data-results="{{json_encode($results)}}"
                    data-site="{{json_encode($sites)}}"
                    data-controller-name="{{request()->module_info->controller_name}}"
                    data-back-url="{{$backURL}}"
                    data-action-performed="{{$actionPerformed}}"
                    data-data-types="{{json_encode($dataTypes)}}"
                    data-data-types-info="{{json_encode($dataTypesInfo)}}"
                    data-site-id="{{htcms_get_siteId_for_admin()}}"

            >
          </front-module-creator>
        </div>
    </div>

@endsection
