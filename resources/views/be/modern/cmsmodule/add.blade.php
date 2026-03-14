@extends(htcms_admin_config('theme').'.index')

@section('content')

    <module-creator
            data-cms-modules="{{json_encode($cmsModules)}}"
            data-database-tables="{{json_encode($allTables)}}"
            data-controller-name="{{request()->module_info->controller_name}}"
            data-back-url="{{$backURL}}"
    >
    </module-creator>


@endsection
