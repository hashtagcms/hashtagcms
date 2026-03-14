@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php


        $id = 0;

        $lang = array();
        $lang["lang_id"] = "";
        $lang["title"] = old('lang_title');
        $lang["content"] = old('lang_title');
        $insert_by = old('insert_by', Auth()->user()->id);
        $update_by = old('insert_by', $insert_by);

        $alias = old('alias');

        $site_id = old('site_id', session("site_id", 1));

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
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Static Content Module</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-6">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'lang.id', $lang["lang_id"]) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'update_by', $update_by) !!}

                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('alias', 'Identified Alias', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'alias', $alias , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'eg: CONTENT_ABOUT_US')) !!}
                    </div>
                    <div class="space-y-2">
                         {!! FormHelper::label('lang_title', 'Module Heading', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                         {!! FormHelper::input('text', 'lang_title', $lang["title"] , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'eg: About Us')) !!}
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('lang_content', 'Static Content (HTML/JSON)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('lang_content', $lang["content"] , array('rows'=>14, 'class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'eg: { "title": "About Us", "content": "About Us" } or <div><h1>About Us</h1><p>About Us</p></div>')) !!}
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
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

