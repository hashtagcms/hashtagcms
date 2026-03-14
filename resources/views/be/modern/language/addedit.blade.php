@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
    ></title-bar>

    @php


        $id = 0;
        $name = old('name');
        $iso_code = old('iso_code');
        $language_code = old('language_code');
        $date_format_lite = old('date_format_lite');
        $date_format_full = old('date_format_full');
        $is_rtl = old('is_rtl');

        //print_r($results);

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
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Language System Configuration</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-8">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Identification -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('name', 'Language Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'name', $name , array('class'=>'w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('iso_code', 'ISO Code (e.g. en)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Iso Code')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('language_code', 'Language Code (e.g. en-us)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'language_code', $language_code, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Language Code')) !!}
                        </div>
                    </div>
                </div>

                <!-- Formatting -->
                <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Localization Defaults</h4>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('date_format_lite', 'Lite Date Format', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'date_format_lite', $date_format_lite, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Date Format Lite')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('date_format_full', 'Full Date Format', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'date_format_full', $date_format_full, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Date Format Full')) !!}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        {!! FormHelper::checkbox('is_rtl', $is_rtl, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                        {!! FormHelper::label('is_rtl', 'This is a right-to-left (RTL) language', array('class' => 'text-sm font-medium text-slate-700')) !!}
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

