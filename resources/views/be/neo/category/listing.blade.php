@extends(htcms_admin_config('theme').'.index')

@section('content')

    <action-bar
            data-controller-title="{{request()->module_info->name}}"
            data-controller-name="{{request()->module_info->controller_name}}"
            data-selected-params="{{json_encode(isset($searchParams) ? $searchParams : [])}}"
            data-fields="{{json_encode($fieldsName)}}"
            data-action-fields="{{json_encode($actionFields)}}"
            data-languages="{{json_encode($supportedLangs->toArray())}}"
            data-selected-language="{{session('lang_id') ?? 1}}"
            data-more-actions="{{json_encode(isset($moreActionBarItems) ? $moreActionBarItems : [])}}"
            data-has-lang-method="{{$hasLangMethod}}"
            data-cms-modules="{{json_encode(request()->module_info)}}"
            data-layout-type="{{ Session::get('layout') }}"
            data-show-search="true"
    >

    </action-bar>

    @if($paginator)
        <category-platform
        data-platforms="{{json_encode($extraData["platforms"])}}"

        >

        </category-platform>

        <table-view
                data-list="{{json_encode($paginator->items())}}"
                data-headers="{{json_encode($fieldsName)}}"
                data-action-fields="{{json_encode($actionFields)}}"
                data-controller-name="{{request()->module_info->controller_name}}"
                data-action-as-ajax="{{json_encode(htcms_admin_config('action_as_ajax'))}}"
                data-action-css="{{json_encode(htcms_admin_config('action_icon_css'))}}"
                data-more-action-fields="{{json_encode($moreActionFields)}}"
                data-user-rights="{{json_encode($user_rights)}}"
                data-make-field-as-link="{{json_encode(htcms_admin_config('make_field_as_link'))}}"
                data-show-delete-popup="{{((bool)htcms_admin_config('show_delete_popup')) ? 'true' : 'false'}}"
                data-edit-handled-by-other-component="true"
                data-min-results-needed="{{(isset($minResults) ? $minResults : -1)}}"
                data-layout-type="{{ Session::get('layout') }}"
        >
        </table-view>

        @include(htcms_admin_get_view_path('common.pagination'))

    @else
        <div class="panel panel-default" role="alert">
            <div class="panel-heading">Error!</div>
            <div class="panel-body alert-danger"><p>Data source may be missing.</p></div>
        </div>
    @endif

@endsection
