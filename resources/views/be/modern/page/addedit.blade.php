@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php

        $id = 0;
        $alias = old("alias");
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $category_id = old("category_id", $defaultCategory ?? "");
        $platform_id = old("platform_id");
        $parent_id = old("parent_id");
        $link_navigation = old("link_navigation");
        $link_rewrite = old("link_rewrite");
        $target = old("target");
        $header_content = old("header_content");
        $footer_content = old("footer_content");
        $exclude_in_listing = old("exclude_in_listing");
        $position = old("position");
        $content_type = old("content_type", $defaultContentType ?? "");
        $publish_status = old("publish_status");
        $enable_comments = old("enable_comments");
        $publish_at = old("publish_at");
        $expire_at = old("expire_at");


        $attachment = old("attachment");
        $img = old("img");
        $author = old("author", auth()->user()->name);
        $content_source = old("content_source");


        $lang = array();
        $lang["name"] = old("lang_name");
        $lang["title"] = old("lang_title");
        $lang["active_key"] = old("lang_active_key");
        $lang["target"] = old("lang_target");
        $lang["link_relation"] = old("lang_link_relation");
        $lang["page_content"] = old("lang_page_content");
        $lang["description"] = old("lang_description");
        $lang["meta_title"] = old("lang_meta_title");;
        $lang["meta_keywords"] = old("lang_meta_keywords");
        $lang["meta_description"] = old("lang_meta_description");
        $lang["meta_robots"] = old("lang_meta_robots");
        $lang["meta_canonical"] = old("lang_meta_canonical");


        $insert_by = Auth()->user()->id;
        $menu_placement = old("menu_placement");
        $required_login = old("required_login");

        if(isset($results)) {
            extract($results);
        }


        //dd($contentCategories);

        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["name"] = "";
        }

    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-5xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Page Content Management</h3>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full">{{ $content_type }} Page</span>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-12 space-y-16">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'update_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'content_type', $content_type) !!}
                {!! FormHelper::input('hidden', 'alias', $alias) !!}
                {!! FormHelper::input('hidden', 'lang_active_key', $lang["active_key"]) !!}
                {!! FormHelper::input('hidden', 'link_navigation', $link_navigation) !!}

                <!-- Section 1: Core Identity -->
                <section data-collapsible="page-core-identity" data-collapsed="false" class="rounded-2xl border border-slate-100 overflow-hidden">
                    <!-- Trigger -->
                    <div data-collapsible-trigger
                         class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-tag text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Core Identity</h4>
                        <i data-collapsible-chevron class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <!-- Body -->
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">                                
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_name', 'Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_title', 'Title', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_title', $lang["title"] , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Title')) !!}
                            </div>
    
                            <div class="space-y-2">
                                {!! FormHelper::label('link_rewrite', 'Link Rewrite (page url)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'link_rewrite', $link_rewrite , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Rewrite')) !!}
                            </div>                            
                            <div class="space-y-2">
                                {!! FormHelper::label('category_id', 'Content Category', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('category_id', $contentCategories, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required'), $category_id,  array("value"=>"id", "label"=>"lang.name"), "Select") !!}
                                <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                                    <i class="fa fa-info-circle mr-1 text-blue-400"></i>
                                    Only categories with <code class="font-mono bg-slate-100 text-pink-500 px-1.5 py-0.5 rounded text-[9px]">{link_rewrite}</code> or <code class="font-mono bg-slate-100 text-pink-500 px-1.5 py-0.5 rounded text-[9px]">{link_rewrite?}</code> patterns are available here. Your page URL will be structured as: <code class="font-mono bg-slate-100 text-blue-500 px-1.5 py-0.5 rounded text-[9px]">{category_link_rewrite}/{this_page_link_rewrite}</code>.
                                </p>
                            </div>
                            <div id="parent_div" class="space-y-2" style="display: none">
                                {!! FormHelper::label('parent_id', 'Parent Page/Category', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('parent_id', $contentCategories, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $parent_id,  array("value"=>"id", "label"=>"lang.name"), "Select") !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('platform_id', 'Target Platform', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('platform_id', $platforms, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $platform_id, array("label"=>"name","value"=>"id")) !!}
                            </div>
                            <div class="space-y-2">
                                 {!! FormHelper::label('menu_placement', 'Menu Group Assignment', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                 {!! FormHelper::select('menu_placement', $menuPlacements, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $menu_placement, "plain_array") !!}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Rich Content & Media -->
                <section data-collapsible="page-rich-content" data-collapsed="false" class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                         class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-file-text text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Rich Content &amp; Media</h4>
                        <i data-collapsible-chevron class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-8">
                             <div class="space-y-2">
                                {!! FormHelper::label('lang_description', 'Teaser / Abstract (Short Summary)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('lang_description', htmlentities($lang["description"]), array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'id' => 'lang_description', 'rows' => 10)) !!}
                            </div>
        
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_page_content', 'Full Page Body Content', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                    {!! FormHelper::textarea('lang_page_content', htmlentities($lang["page_content"]), array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'id' => 'lang_page_content', 'rows' => 20)) !!}
                                </div>
                            </div>                            

                            <div class="pt-6 border-t border-slate-100 space-y-6">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Media Assets</span>
                                <div class="space-y-6">
                                    <div class="space-y-4">
                                        {!! FormHelper::label('attachment', 'Main Download Attachment', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                                            {!! FormHelper::file('attachment', $attachment, array('accept'=>'*'), TRUE, 100) !!}
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        {!! FormHelper::label('img', 'Feature Hero Image', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4 overflow-hidden">
                                            {!! FormHelper::file('img', $img, array('accept'=>'image/*'), TRUE, 100) !!}
                                            <div class="relative py-2 text-center text-[10px] text-slate-400 uppercase font-black tracking-[0.2em]">-- OR RELATIVE PATH --</div>
                                            {!! FormHelper::input('text', 'image_path', '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Image Path')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: SEO Optimization -->
                <section data-collapsible="page-seo" data-collapsed="true" class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                         class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-google text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">SEO & Configuration</h4>
                        <i data-collapsible-chevron class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_target', 'Hyperlink Target', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('lang_target', $targetTypes, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $lang["target"], "plain_array") !!}
                            </div>
                             <div class="space-y-2">
                                {!! FormHelper::label('lang_link_relation', 'Link Relationship (Rel)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('lang_link_relation', $relationTypes, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $lang["link_relation"],"plain_array") !!}
                            </div>
    
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_meta_title', 'Meta Title', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_meta_title', $lang["meta_title"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Title')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_meta_keywords', 'Meta Keywords', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_meta_keywords', $lang["meta_keywords"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Keywords')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_meta_description', 'Meta Description', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('lang_meta_description', $lang["meta_description"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows'=>3)) !!}
                            </div>
    
                             <div class="space-y-2">
                                {!! FormHelper::label('lang_meta_robots', 'Meta Robots', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_meta_robots', $lang["meta_robots"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Robots')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_meta_canonical', 'Canonical URL', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_meta_canonical', $lang["meta_canonical"], array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Canonical')) !!}
                            </div>
                            <div class="pt-6 border-t border-slate-100 space-y-6">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Code Injections</span>
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        {!! FormHelper::label('header_content', 'Custom Header (scripts/tags)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::textarea('header_content', $header_content, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows'=>4)) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('footer_content', 'Custom Footer (scripts/tags)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::textarea('footer_content', $footer_content, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows'=>4)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Section 4: Governance & Advanced -->
                <section data-collapsible="page-governance" data-collapsed="true" class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                         class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-sliders text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Governance & Publishing</h4>
                        <i data-collapsible-chevron class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">
                             <div class="space-y-2">
                                {!! FormHelper::label('author', 'Attributed Author', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'author', $author, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Author')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('content_source', 'Information Source / URL', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'content_source', $content_source, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Content Source')) !!}
                            </div>
    
                             <div class="space-y-2">
                                {!! FormHelper::label('publish_at', 'Scheduled Launch Time', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('datetime-local', 'publish_at', $publish_at, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('expire_at', 'Scheduled Expiration Time', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('datetime-local', 'expire_at', $expire_at, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900')) !!}
                            </div>
    
                            <div class="space-y-4 pt-4">
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::checkbox('enable_comments', $enable_comments) !!}
                                    {!! FormHelper::label('enable_comments', 'Enable Engagement (Comments)', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::checkbox('required_login', $required_login) !!}
                                    {!! FormHelper::label('required_login', 'Restrict Accessibility (Requires Login)', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>                                
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::checkbox('exclude_in_listing', $exclude_in_listing) !!}
                                    {!! FormHelper::label('exclude_in_listing', 'Hide from Link Listings/Archives', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>                                
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('publish_status', 'Launch Status', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    {!! FormHelper::input('checkbox', 'publish_status', $publish_status) !!}
                                    <span class="text-xs font-black uppercase text-slate-600">Published</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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
        <image-gallery ref="imageGallery"></image-gallery>
    </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection


@push('scripts')
    <script src="{{htcms_admin_asset('js/vendors/tinymce/tinymce.min.js')}}"></script>
    <script src="{{htcms_admin_asset('js/editor.js')}}"></script>
    <script>
        window.addEventListener("load", function () {
            try {
                EditorHelper.makeRichEditor("#lang_page_content");
                EditorHelper.makeRichEditor("#lang_description", {height:300});
                PageManager.init("<?php echo $actionPerformed; ?>", "<?php echo $content_type ?>", "<?php echo $id ?>", {
                    dependencies: [
                        {
                            onChangeId: "lang_name",
                            shouldUpdate: [
                                { element: "lang_title", formatter: 'capitalize' },
                                { element: "lang_active_key", formatter: 'lowercase_clean' },
                                { element: "link_rewrite", formatter: 'lowercase_clean' }
                            ]
                        }
                    ]
                });
            } catch (e) {
                console.error(e.message, e.lineNumber);
            }

        })

    </script>

@endpush
