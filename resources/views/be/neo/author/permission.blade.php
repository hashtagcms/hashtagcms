@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
        data-back-url="{{$backURL}}"
        ></title-bar>

    @php


        $id = 0;

        if(isset($results)) {
            extract($results);
        }


    @endphp
        <div class="row">
            <div class="col-auto">
                <module-permission

                        data-back-url="{{$backURL}}"
                        data-cms-modules="{{json_encode($allModules)}}"
                        data-user-modules="{{json_encode($userModules)}}"
                        data-controller-name="{{request()->module_info->controller_name}}"
                        data-is-super-admin="{{$isSuperAdmin}}"
                ></module-permission>

            </div>
        </div>
@endsection
