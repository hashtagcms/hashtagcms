@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
    ></title-bar>

    @php

        $id = 0;
        $lang = array();
        $lang["title"] = old("lang_title");


        $name = old("name");;
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $under_maintenance = old("under_maintenance", 0);
        $domain = old("domain");;
        $context = old("context");;
        $favicon = old("favicon");;
        $lang_count = old("lang_count");
        $theme_id = old("theme_id", 0);
        $category_id = old("category_id", 0);
        $platform_id = old("platform_id", 0);
        $country_id = old("country_id", 0);


        if(isset($results)) {
            extract($results);
        }

       //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["title"] = "";
        }


    @endphp


<div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Site Configuration</h3>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Global Identity</span>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-10">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Primary Identity Section -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3">
                        <i class="fa fa-globe text-blue-500"></i>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800">Primary Identity</h4>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('name', 'Site Branding Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('lang_title', 'Title', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'lang_title', $lang["title"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Title')) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('domain', 'Primary Domain', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'domain', $domain, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Domain')) !!}
                            <p class="text-[10px] text-slate-400 font-medium">
                                The primary domain for this site. A single site can be served on <span class="font-black text-slate-500">multiple domains</span> — configure all of them via <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-600">config/hashtagcms.php</code> → <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-600">domains</code>.
                            </p>
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('context', 'Application Context', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'context', $context, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Context')) !!}
                            <div class="mt-2 rounded-xl border border-blue-100 bg-blue-50/50 overflow-hidden">
                                <div class="px-4 py-2.5 flex items-start gap-2.5">
                                    <i class="fa fa-info-circle text-blue-400 mt-0.5 shrink-0"></i>
                                    <div class="space-y-1.5">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">How Multi-Domain Works</p>
                                        <p class="text-[10px] text-blue-700/80 leading-relaxed">
                                            A single site can run on multiple domains. Each domain must map to this site's <strong>Context</strong> value inside
                                            <code class="bg-blue-100 px-1 py-0.5 rounded">config/hashtagcms.php</code>:
                                        </p>
                                        <pre class="mt-1.5 bg-blue-900/10 text-blue-800 text-[10px] font-mono rounded-lg px-3 py-2 leading-relaxed overflow-x-auto">'domains' => [
    'dev.hashtagcms.com'     => env('CONTEXT', '{{ $context ?? 'htcms' }}'),
    'www.myothersite.com'    => '{{ $context ?? 'htcms' }}',
]</pre>
                                        <p class="text-[10px] text-blue-600/70">The value on the right of each domain entry must match the Context you enter above.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('favicon', 'Site Favicon (Icon)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                {!! FormHelper::file('favicon', $favicon, array('accept'=>'image/*'), TRUE, 100) !!}
                            </div>
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('under_maintenance', 'Operational Status', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="flex items-center gap-4 bg-red-50/50 p-5 rounded-xl border border-red-100">
                                {!! FormHelper::checkbox('under_maintenance', $under_maintenance, array('class' => 'w-6 h-6 rounded border-red-300 text-red-600 focus:ring-red-500')) !!}
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-red-700">Under Maintenance Mode</span>
                                    <span class="text-[10px] text-red-600/70 font-medium tracking-tight">Public access will be restricted to a maintenance message</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($id > 0)
                <!-- Default Settings Section -->
                <section class="pt-10 border-t border-slate-50 space-y-8">
                    <div class="flex items-center gap-3">
                        <i class="fa fa-sliders text-emerald-500"></i>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800">Localization & Defaults</h4>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('theme_id', 'Default Visual Theme', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('theme_id', $theme, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $theme_id, array("label"=>"name","value"=>"id")) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('category_id', 'Default Home Category', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('category_id', $category, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $category_id, array("label"=>"name","value"=>"category_id")) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('platform_id', 'Primary Platform', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('platform_id', $platform, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $platform_id) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('lang_id', 'System Language', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('lang_id', $languages, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $lang_id, array("label"=>"name","value"=>"id")) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('country_id', 'Regional Country', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::select('country_id', $countries, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $country_id, array("label"=>"name","value"=>"country_id")) !!}
                        </div>
                    </div>
                </section>
                @endif
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
