@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>
    <div class="row">
        <div class="col-md-4">
            <menu-sorter
                    data-all-data="{{json_encode($data)}}"
                    data-fields="{{json_encode($fields)}}"
                    data-controller-name="{!! htcms_get_module_name(request()->module_info) !!}"
                    data-show-groups="false"
            >
            </menu-sorter>
        </div>
    </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection

