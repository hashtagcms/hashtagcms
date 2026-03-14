@extends(htcms_admin_config('theme').'.index')

@section('content')

<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}" data-show-copy="true" data-show-paste="false" data-copy-paste-auto-init="false"></title-bar>
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Frontend Module Configuration</h3>
        </div>
        <front-module-creator
                data-form-action="{{htcms_get_save_path(request()->module_info->controller_name)}}"
                data-results="{{json_encode($results)}}"
                data-site="{{json_encode($sites)}}"
                data-controller-name="{{request()->module_info->controller_name}}"
                data-back-url="{{$backURL}}"
                data-action-performed="{{$actionPerformed}}"
                data-module-types="{{json_encode($moduleTypes)}}"
                data-method-types="{{json_encode($methodTypes)}}"
                data-site-id="{{htcms_get_siteId_for_admin()}}"
        >
        </front-module-creator>
    </div>

@endsection
