@extends(htcms_admin_config('theme').'.index')


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
        $lang["title"] = old('lang_title');;
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
        $platform_wise["cache_category"] =  old("cache_category");
        $platform_wise["theme_id"] =  old("theme_id", $siteDefaults['theme_id']);

        $insert_by = Auth()->user()->id;
        $platform_id = 1;
//echo "<pre>";
//print_r($results);


        if(isset($results)) {
            extract($results);
        }
        if(count($platform_wise) == 0) {
                $platform_wise["platform_id"] = old("platform_id", $platform_id);
                $platform_wise["icon_css"] = old("icon_css");
                $platform_wise["icon"] = old("icon");
                $platform_wise["header_content"] = old("header_content");
                $platform_wise["footer_content"] = old("footer_content");
                $platform_wise["exclude_in_listing"] = old("exclude_in_listing");
                $platform_wise["cache_category"] =  old("cache_category");
                $platform_wise["theme_id"] =  old("theme_id");
        }

        //if platform_wise not found

//dd($categories);



    @endphp


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" id="addEditForm">

                    {{csrf_field()}}


                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                    {!! FormHelper::input('hidden', 'update_by', $insert_by) !!}


                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                    {!! FormHelper::input('hidden', 'platform_id', $platform_wise["platform_id"]) !!}

                    <div class="form-group row">
                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_name', 'Name') !!}
                        </div>
                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_title', 'Title') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'lang_title', $lang["title"] , array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_active_key', 'Active Key') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'lang_active_key', $lang["active_key"] , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('parent_id', 'Parent Category') !!}
                        </div>

                        <div class="col-sm-10">
                          {!! FormHelper::select('parent_id', $categories, array("class"=>"form-select select-sm"), $parent_id, array("label"=>"lang.name","value"=>"id")) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('link_rewrite', 'Link Rewrite') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'link_rewrite', $link_rewrite , array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('link_rewrite_pattern', 'Dynamic Link Pattern') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'link_rewrite_pattern', $link_rewrite_pattern , array('class'=>'form-control', 'placeholder'=>'for example {link_rewrite} or {link_rewrite?} for optional')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_excerpt', 'Excerpt') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('lang_excerpt', htmlentities($lang["excerpt"]), array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_content', 'Content') !!}
                        </div>
                        <div class="col-sm-10">
                            {!! FormHelper::textarea('lang_content', htmlentities($lang["content"]), array('class'=>'form-control', 'rows'=>20)) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('header_content', 'Header Content') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('header_content', $platform_wise["header_content"], array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('footer_content', 'Footer Content') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('footer_content', $platform_wise["footer_content"], array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('theme_id', 'Theme') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::select('theme_id', $themes, array("class"=>"form-select select-sm"), $platform_wise["theme_id"], array("label"=>"name","value"=>"id")) !!}
                        </div>

                    </div>


                    <fieldset class="fieldset">
                        <legend>Mobile Specific</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('has_wap', 'Has Wap Site?') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('checkbox', 'has_wap', $has_wap) !!}
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('wap_url', 'Wap Url') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'wap_url', $wap_url , array('class'=>'form-control')) !!}
                            </div>

                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Beautify</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('icon', 'Icon') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::file('icon', $platform_wise["icon"], array('accept'=>'image/*'), TRUE, 100) !!}
                            </div>

                        </div>


                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('icon_css', 'Icon css') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'icon_css', $platform_wise["icon_css"] , array('class'=>'form-control')) !!}
                            </div>
                        </div>

                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Special Modules?</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('has_some_special_module', 'Has Special Module') !!}
                            </div>

                            <div class="col-sm-10">
                              {!! FormHelper::input('checkbox', 'has_some_special_module', $has_some_special_module) !!}
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('special_module_alias', 'Special Module Alias') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'special_module_alias', $special_module_alias , array('class'=>'form-control')) !!}
                            </div>
                        </div>

                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>SEO Info</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_title', 'Meta Title') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'lang_meta_title', $lang["meta_title"] , array('class'=>'form-control')) !!}
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_keywords', 'Meta Keywords') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::textarea('lang_meta_keywords', $lang["meta_keywords"], array('class'=>'form-control')) !!}
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_description', 'Meta Description') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::textarea('lang_meta_description', $lang["meta_description"], array('class'=>'form-control')) !!}
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_robots', 'Meta Robots') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'lang_meta_robots', $lang["meta_robots"], array('class'=>'form-control')) !!}
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_canonical', 'Meta Canonical') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'lang_meta_canonical', $lang["meta_canonical"], array('class'=>'form-control')) !!}
                            </div>

                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Advanced Options <i style="font-size: 10px"  class="hide js_icon_expand hand fa fa-plus"></i></legend>
                        <div class="js_fieldset">
                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('exclude_in_listing', 'Exclude from Listing') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('checkbox', 'exclude_in_listing', $platform_wise["exclude_in_listing"]) !!}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2">
                                    {!!  FormHelper::label('controller_name', 'Controller Name') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('text', 'controller_name', $controller_name, array('class'=>'form-control')) !!}
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('lang_target', 'Target') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::select('lang_target', $target_types, array("class"=>"form-select select-sm"), $lang['target'], "plain_array") !!}
                                </div>
                            </div>

                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('lang_link_relation', 'Link Relation') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::select('lang_link_relation', $relation_types, array("class"=>"form-select select-sm"), $lang["link_relation"], "plain_array") !!}
                                </div>
                            </div>


                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('link_navigation', 'Link Navigation') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('text', 'link_navigation', $link_navigation , array('class'=>'form-control')) !!}
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('is_new', 'Is New?') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('checkbox', 'is_new', $is_new) !!}
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('lang_is_external', 'Is External?') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('checkbox', 'lang_is_external', $lang["is_external"]) !!}
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('is_root_category', 'Is Root Category?') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('checkbox', 'is_root_category', $is_root_category) !!}
                                </div>

                            </div>
                            <div class="form-group row">

                                <span class="text-black-50">
                                    You might not need it but these are extra fields; in case you need them.
                                </span>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    {!!  FormHelper::label('lang_b2b_mapping', 'B2B Mapping') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('text', 'lang_b2b_mapping', $lang["b2b_mapping"], array('class'=>'form-control')) !!}
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('cache_category', 'Cache Category') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('text', 'cache_category', $platform_wise["cache_category"], array('class'=>'form-control')) !!}
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="col-sm-2">
                                    {!!  FormHelper::label('lang_third_party_mapping_key', 'Third Party Mapping key') !!}
                                </div>

                                <div class="col-sm-10">
                                    {!! FormHelper::input('text', 'lang_third_party_mapping_key', $lang["third_party_mapping_key"] , array('class'=>'form-control')) !!}
                                </div>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Publishing Options</legend>

                        <div class="form-group row">
                            <div class="col-sm-2">
                                {!!  FormHelper::label('required_login', 'Required Login?') !!}
                            </div>
                            <div class="col-sm-10">
                                {!! FormHelper::checkbox('required_login', $required_login) !!}
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('publish_status', 'Published') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('checkbox', 'publish_status', $publish_status) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">
                                {!!  FormHelper::label('publish_at', 'Publish Date') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('datetime-local', 'publish_at', $publish_at, array('class'=>'form-control')) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">
                                {!!  FormHelper::label('expire_at', 'Expire Date') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('datetime-local', 'expire_at', $expire_at, array('class'=>'form-control')) !!}
                            </div>
                        </div>

                    </fieldset>

                    <div class="row">
                        <div class="form-group center-align">
                            <input type="submit" name="submit" value="Save" class="btn btn-success btn-from-submit" /> <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    <image-gallery ref="imageGallery"></image-gallery>
@include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection

@push('scripts')
    <script src="{{htcms_admin_asset('js/vendors/tinymce/tinymce.min.js')}}"></script>
    <script src="{{htcms_admin_asset('js/editor.js')}}"></script>
    <script>
        EditorHelper.makeRichEditor("#lang_content");
        EditorHelper.makeRichEditor("#lang_excerpt", {height:300});
    </script>

@endpush
