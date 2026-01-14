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


    <div class="row">
        <div class="admin-form">

            <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
                  class="form-horizontal" role="form" enctype="multipart/form-data">
                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                {!! FormHelper::input('hidden', 'update_by', $insert_by) !!}

                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                <div class="form-group row hide">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('content_type', 'Content Type') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::select('content_type', $contentTypes, array('class'=>'form-select select-sm'), $content_type, "plain_array", "") !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('category_id', 'Content Category') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::select('category_id', $contentCategories, array('class'=>'form-select select-sm'), $category_id,  array("value"=>"id", "label"=>"lang.name"), "Select") !!}
                    </div>
                </div>

                <div id="parent_div" class="form-group row" style="display: none">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('parent_id', 'Parent Category') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::select('parent_id', $contentCategories, array('class'=>'form-select select-sm'), $parent_id,  array("value"=>"id", "label"=>"lang.name"), "Select") !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_name', 'Name') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_title', 'Title') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'lang_title', $lang["title"] , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>

                <div class="form-group row hide">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('alias', 'Alias') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'alias', $alias , array('class'=>'form-control')) !!}
                    </div>
                </div>

                <div class="form-group row hide">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_active_key', 'Active Key') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'lang_active_key', $lang["active_key"] , array('class'=>'form-control')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('platform_id', 'Platform') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::select('platform_id', $platforms, array('class'=>'form-select select-sm'), $platform_id, array("label"=>"name","value"=>"id")) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('link_rewrite', 'Url') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'link_rewrite', $link_rewrite , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>

                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('menu_placement', 'Menu Group') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::select('menu_placement', $menuPlacements, array('class'=>'form-select select-sm'), $menu_placement, "plain_array") !!}

                    </div>

                </div>


                <div class="form-group row hide">
                    <div class="col-sm-2">
                        {!!  FormHelper::label('link_navigation', 'Full Url [optional]') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'link_navigation', $link_navigation , array('class'=>'form-control')) !!}
                    </div>
                </div>


                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_description', 'Description') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('lang_description', htmlentities($lang["description"]), array('class'=>'form-control', 'rows'=>10)) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_page_content', 'Body') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('lang_page_content', htmlentities($lang["page_content"]), array('class'=>'form-control', 'rows'=>20)) !!}
                    </div>

                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('header_content', 'Header Content') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('header_content', $header_content, array('class'=>'form-control','rows'=>'5','cols'=>'80')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('footer_content', 'Footer Content') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('footer_content', $footer_content, array('class'=>'form-control','rows'=>'5','cols'=>'80')) !!}
                    </div>

                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('exclude_in_listing', 'Exclude in Listing?') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::checkbox('exclude_in_listing', $exclude_in_listing) !!}
                    </div>

                </div>

                <div class="form-group row">
                    <fieldset class="fieldset">
                        <legend>SEO Settings</legend>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_target', 'Target') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('lang_target', $targetTypes, array('class'=>'form-select select-sm'), $lang["target"], "plain_array") !!}
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_link_relation', 'Relation') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('lang_link_relation', $relationTypes, array('class'=>'form-select select-sm'), $lang["link_relation"],"plain_array") !!}
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_title', 'Page Title') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'lang_meta_title', $lang["meta_title"], array('class'=>'form-control')) !!}
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_meta_keywords', 'Meta Keywords') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'lang_meta_keywords', $lang["meta_keywords"], array('class'=>'form-control')) !!}
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
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('attachment', 'Attachment') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::file('attachment', $attachment, array('accept'=>'*'), TRUE, 100) !!}
                    </div>

                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('img', 'Image') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::file('img', $img, array('accept'=>'image/*'), TRUE, 100) !!}
                        <div class="text-left"> -- OR Image Path -- </div>
                        <div>
                            {!! FormHelper::input('text', 'image_path', '', array('class'=>'form-control')) !!}
                        </div>
                    </div>

                </div>

                <div class="form-group row">
                    <fieldset class="fieldset">
                        <legend>Publishing Options</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('author', 'Author') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'author', $author, array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('content_source', 'Content Source') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('text', 'content_source', $content_source, array('class'=>'form-control')) !!}
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

                        <div class="form-group row">
                            <div class="col-sm-2">
                                {!!  FormHelper::label('enable_comments', 'Enable Comments') !!}
                            </div>
                            <div class="col-sm-10">
                                {!! FormHelper::checkbox('enable_comments', $enable_comments) !!}
                            </div>
                        </div>
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
                                {!! FormHelper::checkbox('publish_status', $publish_status) !!}
                            </div>
                        </div>                        
                    </fieldset>
                </div>
                <div class="row">
                    <div class="form-group center-align">
                        <input type="submit" name="submit" value="Save" class="btn btn-success btn-from-submit"/> <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
            <image-gallery ref="imageGallery"></image-gallery>
        </div>
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
                PageManager.init("<?php echo $actionPerformed; ?>", "<?php echo $content_type ?>", "<?php echo $id ?>");
            } catch (e) {
                console.error(e.message, e.lineNumber);
            }

        })

    </script>

@endpush
