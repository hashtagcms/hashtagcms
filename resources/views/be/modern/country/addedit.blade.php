@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php


        $id = 0;
        $lang = array();
        $lang["name"] = old('lang_name');
        $iso_code = old('iso_code');

        $call_prefix = old('call_prefix');

        $contains_states = old('contains_states', 0);
        $need_identification_number = old('need_identification_number', 0);
        $need_zip_code = old('need_zip_code', 0);
        $zip_code_format = old('zip_code_format');
        $display_tax_label = old('display_tax_label', 0);
        $zone_id = old('zone_id', 0);
        $currency_id = old('currency_id', 0);

        //dd($results);


        if(isset($results)) {
            extract($results);
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
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Country Configuration</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-6">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Primary Info -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('lang_name', 'Country Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('iso_code', 'ISO Code', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'iso_code', $iso_code, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Iso Code')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('call_prefix', 'Call Prefix', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'call_prefix', $call_prefix, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Call Prefix')) !!}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('zone_id', 'Zone', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('zone_id', $zones, array("class"=>"form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $zone_id) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('currency_id', 'Default Currency', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('currency_id', $currencies, array("class"=>"form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $currency_id) !!}
                        </div>
                    </div>
                </div>

                <!-- Zip & Identification Group -->
                <div class="pt-6 border-t border-slate-50 space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Regional Requirements</h4>
                    
                    <div class="space-y-4 bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            {!! FormHelper::input('checkbox', 'need_zip_code', $need_zip_code, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('need_zip_code', 'Requires Zip Code', array('class' => 'text-sm font-medium text-slate-700')) !!}
                        </div>
                        
                        <div class="space-y-2 pl-8">
                            {!! FormHelper::label('zip_code_format', 'Zip Code Format (e.g. LNNNNLL)', array('class' => 'text-xs font-medium text-slate-500 block')) !!}
                            {!! FormHelper::input('text', 'zip_code_format', $zip_code_format, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Zip Code Format')) !!}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            {!! FormHelper::checkbox('contains_states', $contains_states, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('contains_states', 'This country contains states/provinces', array('class' => 'text-sm font-medium text-slate-700')) !!}
                        </div>
                        <div class="flex items-center gap-3">
                            {!! FormHelper::checkbox('need_identification_number', $need_identification_number, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('need_identification_number', 'Requires National Identification Number', array('class' => 'text-sm font-medium text-slate-700')) !!}
                        </div>
                        <div class="flex items-center gap-3">
                            {!! FormHelper::checkbox('display_tax_label', $display_tax_label, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('display_tax_label', 'Display Tax Label for this country', array('class' => 'text-sm font-medium text-slate-700')) !!}
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
