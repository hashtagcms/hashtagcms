@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php


        $id = 0;
        $group_name = old('group_name', '');
        $name = old('name');
        $value = old('value');
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $platform_id = old('platform_id', []);
        $is_public = old('is_public', 0);
        $convert_camelcase = old('convert_camelcase', 1);

        $platform_select_name = "platform_id[]";


        //print_r($results);

        if(isset($results)) {
            extract($results);
            if ($id > 0) {
               $platform_select_name = "platform_id";
               $convert_camelcase = old('convert_camelcase', 0);
            }
            
        }


        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["name"] = "";
        }

    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Site Property & Configuration</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm" enctype="multipart/form-data">
            <div class="p-8 lg:p-10 space-y-10">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                <!-- Group & Identity -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('group_name', 'Group Namespace', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        <hc-auto-suggest value="{{ $group_name }}" name="group_name"
                                            placeholder="Enter or search Group Name..."
                                            endpoint="{{ htcms_admin_path('siteprop/getSiteGroup') }}"
                                            display-field="group_name" :min-chars="2">
                                            <template #icon-left>
                                                <i class="fa fa-layer-group"></i>
                                            </template>
                                        </hc-auto-suggest>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('name', 'Property Unique Key', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 overflow-hidden">
                                <div class="px-4 pt-4 pb-3">
                                    {!! FormHelper::input('text', 'name', $name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder'=>'e.g. siteTitle or my_key')) !!}
                                </div>
                                <div class="px-4 py-3 border-t border-slate-100 flex items-center gap-3">   
                                    
                                    {!! FormHelper::checkbox('convert_camelcase', $convert_camelcase, array('id' => 'convert_camelcase', 'class' => 'w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500', 'value' => '1')) !!}
                                    <div class="flex flex-col">
                                        {!! FormHelper::label('convert_camelcase', 'Always convert Key to camelCase', array('class' => 'text-xs font-semibold text-slate-700 cursor-pointer leading-tight')) !!}
                                        <span class="text-[10px] text-slate-400 font-medium">e.g. "my key" &rarr; "myKey"</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('platform_id', 'Applicable Platform', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select($platform_select_name, $platforms, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $platform_id) !!}
                        </div>
                    </div>
                </div>

                <!-- Content & Value -->
                <div class="space-y-6 border-t border-slate-50 pt-10">
                    <div class="space-y-2">
                        {!! FormHelper::label('value', 'Property Value', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('value', $value, array('rows'=>8, 'class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required')) !!}
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            {!! FormHelper::input('checkbox', 'is_public', $is_public, array('class' => 'w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500')) !!}
                            <div class="flex flex-col">
                                {!! FormHelper::label('is_public', 'Expose to Public API', array('class' => 'text-sm font-bold text-slate-800')) !!}
                                <span class="text-[10px] text-slate-400 font-medium">Allows fetching this property via public endpoints</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}" class="w-full sm:w-auto text-center px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit" class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>

@include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

