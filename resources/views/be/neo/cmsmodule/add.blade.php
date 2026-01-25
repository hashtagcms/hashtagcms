@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>
    @php

        if(isset($results)) {
            extract($results);
        }


    @endphp


        <div class="row">
            <div class="admin-form">

                <module-creator
                        data-cms-modules="{{json_encode($cmsModules)}}"
                        data-database-tables="{{json_encode($allTables)}}"
                        data-controller-name="{{request()->module_info->controller_name}}"
                        data-back-url="{{$backURL}}"
                >
                </module-creator>
            </div>
        </div>


@endsection
