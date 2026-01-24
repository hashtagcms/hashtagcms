@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-show-copy="false" data-show-paste="false" data-show-back="false"></title-bar>
    <div class="container-fluid">
        <div class="row v-space">

            <info-boxes
                data-modules="{{json_encode(request()->module_info)}}"
                data-modules-allowed="{{json_encode($moduleAllowed)}}"
                data-is-admin="{{$isAdmin}}"
            >
            </info-boxes>

        </div>

    </div>


@endsection
