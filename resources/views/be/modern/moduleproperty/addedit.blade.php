@extends(htcms_admin_config('theme') . '.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php


        $id = 0;
        $module_id = old('module_id', );
        $name = old('name');
        $group = old('group');
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $convert_camelcase = old('convert_camelcase', 1);

        $update_in_all_language = old('update_in_all_language', 1);

        $lang = array();

        $lang["value"] = old('value');


        $moduleComboName = "module_id";
        $platformComboName = "platform_id";
        if ($actionPerformed === 'add') {
            $moduleComboName = "module_id[]";
            $platformComboName = "platform_id[]";
        }

        $module_id = old($moduleComboName, array());
        $platform_id = old($platformComboName, array());

        //dd($module_id, $moduleComboName, $actionPerformed);


        //print_r($results);
        //dd($modules);
        if (isset($results)) {
            extract($results);
        }

        //work around if no lang
        if (empty($lang)) {
            $lang = array();
        }

    @endphp


    <div
        class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">Module Property Definition</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
            enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-8">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                <!-- Module & Platform Assignment -->
                <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Contextual Assignment</h4>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('module_id', 'Assigned Modules', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="mt-1">
                                {!! FormHelper::select($moduleComboName, $modules, array('id' => 'module_id', 'class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'size' => 10), $module_id, array("value" => "id", "label" => "alias")) !!}
                            </div>
                            <p class="text-[10px] text-slate-400">Select one or more modules</p>
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('platform_id', 'Assigned Platforms', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="mt-1">
                                {!! FormHelper::select($platformComboName, $platforms, array('id' => 'platform_id', 'class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'size' => 10), $platform_id, array("value" => "id", "label" => "name")) !!}
                            </div>
                            <p class="text-[10px] text-slate-400">Choose target platforms</p>
                        </div>
                    </div>
                </div>

                <!-- Property Details -->
                <div class="space-y-6">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('group', 'Property Group', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <hc-auto-suggest value="{{ $group }}" name="group"
                                            placeholder="Enter or search Group Name..."
                                            endpoint="{{ htcms_admin_path('moduleproperty/getModuleGroup') }}"
                                            display-field="group_name" :min-chars="2">
                                            <template #icon-left>
                                                <i class="fa fa-layer-group"></i>
                                            </template>
                                        </hc-auto-suggest>
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('name', 'Property Name (Key)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 overflow-hidden">
                                <div class="px-4 pt-4 pb-3">
                                    {!! FormHelper::input('text', 'name', $name, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required', 'placeholder' => 'e.g. siteTitle or my_key')) !!}
                                </div>
                                <div class="px-4 py-3 border-t border-slate-100 flex items-center gap-3">
                                    {!! FormHelper::input('hidden', 'convert_camelcase', '0') !!}
                                    {!! FormHelper::input('checkbox', 'convert_camelcase', $convert_camelcase, array('id' => 'convert_camelcase', 'class' => 'w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500', 'value' => '1')) !!}
                                    <div class="flex flex-col">
                                        {!! FormHelper::label('convert_camelcase', 'Always convert Key to camelCase', array('class' => 'text-sm font-semibold text-slate-700 cursor-pointer leading-tight')) !!}
                                        <span class="text-[10px] text-slate-400 font-medium">e.g. "my key" &rarr;
                                            "myKey"</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('value', 'Property Value', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('value', $lang["value"], array('rows' => 8, 'class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required')) !!}
                    </div>

                    @if($actionPerformed === "edit")
                        <div class="flex items-center gap-3 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                            {!! FormHelper::checkbox('update_in_all_language', $update_in_all_language, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            <div class="space-y-0.5">
                                {!! FormHelper::label('update_in_all_language', 'Propagate to all languages', array('class' => 'text-sm font-bold text-blue-900')) !!}
                                <p class="text-[10px] text-blue-600/80">Synchronize this value across all enabled language
                                    versions</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Footer -->
            <div
                class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}"
                    class="w-full sm:w-auto text-center px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit"
                    class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection