@extends(htcms_admin_config('theme') . '.index')


@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php
        $id = 0;
        $parent_id = old('parent_id');
        $link_rewrite = old('link_rewrite');
        $link_rewrite_pattern = old('link_rewrite_pattern');
        $link_navigation = old('link_navigation');
        $is_new = old('is_new');
        $platform_wise["icon"] = old('icon');
        $icon_css = old('icon_css');
        $exclude_in_listing = old('exclude_in_listing');
        $cache_category = old('cache_category');
        $is_root_category = old('is_root_category');
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $has_wap = old('has_wap');
        $wap_url = old('wap_url');
        $has_some_special_module = old('has_some_special_module');
        $special_module_alias = old('special_module_alias');
        $required_login = old('required_login');
        $controller_name = old('controller_name');

        $header_content = old('header_content');
        $footer_content = old('footer_content');

        $lang = array();

        $lang["name"] = old('lang_name');
        $lang["title"] = old('lang_title');
        ;
        $lang["content"] = old('lang_content');
        $lang["meta_title"] = old('lang_meta_title');
        $lang["meta_keywords"] = old('lang_meta_keywords');
        $lang["meta_description"] = old('lang_meta_description');
        $lang["meta_robots"] = old('lang_meta_robots');
        $lang["meta_canonical"] = old('lang_meta_canonical');
        $lang["excerpt"] = old('lang_excerpt');

        $publish_status = old('publish_status');
        $publish_at = old('publish_at');
        $expire_at = old('expire_at');

        $lang["target"] = old('lang_target');
        $lang["active_key"] = old('lang_active_key');
        $lang["b2b_mapping"] = old('lang_b2b_mapping');
        $lang["is_external"] = old('lang_is_external');
        $lang["link_relation"] = old('lang_link_relation');
        $lang["third_party_mapping_key"] = old('lang_third_party_mapping_key');

        $platform_wise["platform_id"] = old("platform_id", 1);
        $platform_wise["icon_css"] = old("icon_css");
        $platform_wise["header_content"] = old("header_content");
        $platform_wise["footer_content"] = old("footer_content");
        $platform_wise["exclude_in_listing"] = old("exclude_in_listing");
        $platform_wise["cache_category"] = old("cache_category");
        $platform_wise["theme_id"] = old("theme_id", $siteDefaults['theme_id']);

        $insert_by = Auth()->user()->id;
        $platform_id = 1;
        //echo "<pre>";
        //print_r($results);


        if (isset($results)) {
            extract($results);
        }
        if (count($platform_wise) == 0) {
            $platform_wise["platform_id"] = old("platform_id", $platform_id);
            $platform_wise["icon_css"] = old("icon_css");
            $platform_wise["icon"] = old("icon");
            $platform_wise["header_content"] = old("header_content");
            $platform_wise["footer_content"] = old("footer_content");
            $platform_wise["exclude_in_listing"] = old("exclude_in_listing");
            $platform_wise["cache_category"] = old("cache_category");
            $platform_wise["theme_id"] = old("theme_id");
        }

        //if platform_wise not found

        //dd($categories);



    @endphp


    <div
        class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <!-- Dashboard Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Category Configuration</h3>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Primary
                Identity</span>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
            enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-12 space-y-16">
                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'update_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'platform_id', $platform_wise["platform_id"]) !!}

                <!-- Section 1: Core Identity -->
                <section data-collapsible="cat-core-identity" data-collapsed="false"
                    class="rounded-2xl border border-slate-100 overflow-hidden">
                    <!-- Trigger -->
                    <div data-collapsible-trigger
                        class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-tag text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Core Identity
                        </h4>
                        <i data-collapsible-chevron
                            class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <!-- Body -->
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_name', 'Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_name', $lang["name"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Electronics, Shoes...')) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('lang_title', 'Title', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_title', $lang["title"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Full Descriptive Title')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('link_rewrite', 'Link Rewrite (category url)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'link_rewrite', $link_rewrite, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'custom-url')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('link_rewrite_pattern', 'Dynamic Pattern', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'link_rewrite_pattern', $link_rewrite_pattern, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'for example {link_rewrite} or {link_rewrite?} for optional')) !!}
                                <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                                    <i class="fa fa-info-circle mr-1 text-blue-400"></i>
                                    Use <code
                                        class="font-mono bg-slate-100 text-pink-500 px-1.5 py-0.5 rounded text-[9px]">{link_rewrite}</code>
                                    or <code
                                        class="font-mono bg-slate-100 text-pink-500 px-1.5 py-0.5 rounded text-[9px]">{link_rewrite?}</code>
                                    (optional) to allow pages to dynamically route under this category's URL Rewrite prefix.
                                </p>
                            </div>

                            <div class="space-y-2 hidden">
                                {!! FormHelper::label('lang_active_key', 'Active Key', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'lang_active_key', $lang["active_key"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required', 'placeholder' => 'unique-slug')) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('parent_id', 'Parent Category', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('parent_id', $categories, array("class" => "form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $parent_id, array("label" => "lang.name", "value" => "id")) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('link_navigation', 'Link Navigation', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'link_navigation', $link_navigation, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'custom-url')) !!}
                                <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                                    <i class="fa fa-info-circle mr-1 text-blue-400"></i>
                                    Instead of link rewrite, this link_navigation will be used for navigation menu.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Rich Content -->
                <section data-collapsible="cat-rich-content" data-collapsed="true"
                    class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                        class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div
                            class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-file-text text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Rich Description
                            &amp; Layout</h4>
                        <i data-collapsible-chevron
                            class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                {!! FormHelper::label('lang_excerpt', 'Short Synopsis', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('lang_excerpt', htmlentities($lang["excerpt"]), array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 3)) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('lang_content', 'Full Body Content', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('lang_content', htmlentities($lang["content"]), array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 20)) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('header_content', 'Dynamic Header Injection', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('header_content', $platform_wise["header_content"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 4)) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('footer_content', 'Dynamic Footer Injection', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::textarea('footer_content', $platform_wise["footer_content"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 4)) !!}
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('theme_id', 'Visual Theme', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('theme_id', $themes, array("class" => "form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $platform_wise["theme_id"], array("label" => "name", "value" => "id")) !!}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Specialized Settings -->
                <section data-collapsible="cat-specialized" data-collapsed="true"
                    class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                        class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div
                            class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-puzzle-piece text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">SEO & Specialized
                            Settings</h4>
                        <i data-collapsible-chevron
                            class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-6">

                            <!-- Mobile / WAP -->
                            <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                                <div class="flex items-center gap-3">
                                    <i class="fa fa-mobile text-slate-400"></i>
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-600">Mobile &amp;
                                        WAP</h5>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex items-center gap-3">
                                        {!! FormHelper::input('checkbox', 'has_wap', $has_wap, array('class' => 'w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500')) !!}
                                        {!! FormHelper::label('has_wap', 'Enable WAP Support', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('wap_url', 'Alternate WAP URL', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'wap_url', $wap_url, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Wap Url')) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Visual Assets -->
                            <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                                <div class="flex items-center gap-3">
                                    <i class="fa fa-magic text-slate-400"></i>
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-600">Visual
                                        Assets</h5>
                                </div>
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        {!! FormHelper::label('icon', 'Feature Graphic / Icon', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                                            {!! FormHelper::file('icon', $platform_wise["icon"], array('accept' => 'image/*', 'class' => 'text-xs'), TRUE, 100) !!}
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('icon_css', 'Custom CSS Class (Icon)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'icon_css', $platform_wise["icon_css"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'fa fa-stars')) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Special Modules -->
                            <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                                <div class="flex items-center gap-3">
                                    <i class="fa fa-plug text-slate-400"></i>
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-600">Special
                                        Integration</h5>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex items-center gap-3">
                                        {!! FormHelper::input('checkbox', 'has_some_special_module', $has_some_special_module) !!}
                                        {!! FormHelper::label('has_some_special_module', 'Has Special Module?', array('class' => 'text-sm font-medium text-slate-700')) !!}                                        
                                    </div>
                                    <div class="space-y-2">                                        
                                        <hc-auto-suggest value="{{ $special_module_alias }}" name="special_module_alias" label="Linked Module Alias"
                                            placeholder="Module Alias"
                                            endpoint="{{ htcms_admin_path('module/getModuleAlias') }}"
                                            display-field="alias" :min-chars="2">
                                            <template #icon-left>
                                                <i class="fa fa-link"></i>
                                            </template>
                                        </hc-auto-suggest>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-6">
                                <div class="flex items-center gap-3">
                                    <i class="fa fa-google text-slate-400"></i>
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-600">SEO Meta
                                        Strategy</h5>
                                </div>
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_meta_title', 'Meta Title', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'lang_meta_title', $lang["meta_title"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Title')) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_meta_keywords', 'Meta Keywords', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::textarea('lang_meta_keywords', $lang["meta_keywords"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 2)) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_meta_description', 'Meta Description', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::textarea('lang_meta_description', $lang["meta_description"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 3)) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_meta_robots', 'Meta Robots', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'lang_meta_robots', $lang["meta_robots"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Robots')) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_meta_canonical', 'Meta Canonical', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'lang_meta_canonical', $lang["meta_canonical"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Meta Canonical')) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Section 4: Governance & Advanced -->
                <section data-collapsible="cat-governance" data-collapsed="true"
                    class="rounded-2xl border border-slate-100 overflow-hidden">
                    <div data-collapsible-trigger
                        class="flex items-center gap-4 px-6 py-4 bg-slate-50/60 hover:bg-slate-100/60 cursor-pointer select-none transition-colors">
                        <div
                            class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-sliders text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex-1">Governance &amp;
                            Publishing Options</h4>
                        <i data-collapsible-chevron
                            class="fa fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div data-collapsible-body style="transition: max-height 0.35s ease, overflow 0s 0.35s;">
                        <div class="p-6 space-y-8">
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    {!! FormHelper::label('controller_name', 'Controller Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    {!! FormHelper::input('text', 'controller_name', $controller_name, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Controller Name')) !!}
                                </div>
                                <div class="space-y-2">
                                    {!! FormHelper::label('lang_target', 'Hyperlink Target', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    {!! FormHelper::select('lang_target', $target_types, array("class" => "form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $lang['target'], "plain_array") !!}
                                </div>
                                <div class="space-y-2">
                                    {!! FormHelper::label('lang_link_relation', 'Link Relation (Rel)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    {!! FormHelper::select('lang_link_relation', $relation_types, array("class" => "form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900"), $lang["link_relation"], "plain_array") !!}
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    {!! FormHelper::label('is_root_category', 'Structural Priority', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        {!! FormHelper::input('checkbox', 'is_root_category', $is_root_category) !!}
                                        <span class="text-xs font-black uppercase text-slate-600">Root Node</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    {!! FormHelper::label('publish_at', 'Activation Date', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    {!! FormHelper::input('datetime-local', 'publish_at', $publish_at, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900')) !!}
                                </div>
                                <div class="space-y-2">
                                    {!! FormHelper::label('expire_at', 'Expiration Date', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                    {!! FormHelper::input('datetime-local', 'expire_at', $expire_at, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900')) !!}
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-100 space-y-6">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Extended
                                    Attributes</span>
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_b2b_mapping', 'B2B System Mapping', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'lang_b2b_mapping', $lang["b2b_mapping"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Internal B2B ID')) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('cache_category', 'Cache Strategy/Key', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'cache_category', $platform_wise["cache_category"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Custom cache ttl or key')) !!}
                                    </div>
                                    <div class="space-y-2">
                                        {!! FormHelper::label('lang_third_party_mapping_key', 'External System ID', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                        {!! FormHelper::input('text', 'lang_third_party_mapping_key', $lang["third_party_mapping_key"], array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Hubspot, Salesforce ID etc.')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::input('checkbox', 'exclude_in_listing', $platform_wise["exclude_in_listing"]) !!}
                                    {!! FormHelper::label('exclude_in_listing', 'Hide from Listing', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::input('checkbox', 'is_new', $is_new) !!}
                                    {!! FormHelper::label('is_new', 'Mark as New', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::checkbox('required_login', $required_login) !!}
                                    {!! FormHelper::label('required_login', 'Restrict Access (Login Required)', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-3">
                                    {!! FormHelper::input('checkbox', 'lang_is_external', $lang["is_external"]) !!}
                                    {!! FormHelper::label('lang_is_external', 'Mark as External Link', array('class' => 'text-sm font-medium text-slate-700')) !!}
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
            <div
                class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}"
                    class="w-full sm:w-auto text-center px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit"
                    class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>
    <image-gallery ref="imageGallery"></image-gallery>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection

@push('scripts')
    <script src="{{htcms_admin_asset('js/vendors/tinymce/tinymce.min.js')}}"></script>
    <script src="{{htcms_admin_asset('js/editor.js')}}"></script>
    <script>
        //EditorHelper.makeRichEditor("#lang_content");
        //EditorHelper.makeRichEditor("#lang_excerpt", {height:300});

        window.addEventListener("load", function () {
            try {
                EditorHelper.makeRichEditor("#lang_content");
                EditorHelper.makeRichEditor("#lang_excerpt", { height: 300 });
                PageManager.init("<?php echo $actionPerformed; ?>", "", "<?php echo $id ?>", {
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