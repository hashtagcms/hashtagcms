@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>
    <div class="row">
        <div class="col d-flex">
            {!! FormHelper::select('typeGroups', $typeGroups, array('class'=>'form-select select-sm'), $mediaType, "plain_array", "Select a media group") !!}
            &nbsp;{!! FormHelper::select('groups', $imageGroups, array('class'=>'form-select select-sm'), $groupName, "plain_array", "Select a media type") !!}
            &nbsp;<input type="button" class="btn btn-outline-secondary btn-sm" value="Go" onclick="navigateToSort()" />
        </div>
    </div>
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
    <div class="row">
        @if(sizeof($data) === 0)
            <div class="col-md-8 mt-4">
                <div class="alert alert-info">
                    <p>There is no data to sort.</p>
                </div>
            </div>
        @endif
    </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection
@push('scripts')
    <script>
        function navigateToSort() {
            let typeGroups = document.getElementById('typeGroups').value;
            let groups = document.getElementById('groups').value;
            if(typeGroups.length === 0 || groups.length === 0){
                ToastGloabl.show(Vue, "Please select a media group and a media type", 2000);
                return false;
            }
            window.location.href = AdminConfig.admin_path(`gallery/sort/${typeGroups}/${groups}`);
        }
    </script>
@endpush
