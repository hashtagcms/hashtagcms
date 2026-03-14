@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-8">
        <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                {!! FormHelper::select('typeGroups', $typeGroups, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $mediaType, "plain_array", "Select a media group") !!}
            </div>
            <div class="flex-1 min-w-[200px]">
                {!! FormHelper::select('groups', $imageGroups, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $groupName, "plain_array", "Select a media type") !!}
            </div>
            <button type="button" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all active:scale-95 shadow-lg shadow-blue-600/20" onclick="navigateToSort()">
                Go
            </button>
        </div>
    </div>

    <div class="space-y-6">
        @if(sizeof($data) > 0)
            <div class="max-w-xl">
                <menu-sorter
                        data-all-data="{{json_encode($data)}}"
                        data-fields="{{json_encode($fields)}}"
                        data-controller-name="{!! htcms_get_module_name(request()->module_info) !!}"
                        data-show-groups="false"
                >
                </menu-sorter>
            </div>
        @else
            <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-8 text-center max-w-2xl">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa fa-info"></i>
                </div>
                <p class="text-blue-800 font-medium">There is no data to sort for the selected group.</p>
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
