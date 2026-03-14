@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-show-copy="false" data-show-paste="false" data-show-back="false"></title-bar>
    <div class="max-w-full">
        <div class="flex flex-wrap gap-6 mb-12">

            <info-boxes
                data-modules="{{json_encode(request()->module_info)}}"
                data-modules-allowed="{{json_encode($allModules)}}"
                data-is-admin="{{ auth()->user()->isAdmin() ? 1 : 0 }}"
            >
            </info-boxes>

        </div>

    </div>


@endsection
