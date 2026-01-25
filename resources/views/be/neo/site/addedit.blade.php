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


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('name', 'Site Name') !!}
                        </div>


                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_title', 'Title') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'lang_title', $lang["title"], array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('under_maintenance', 'Under Maintenance') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('under_maintenance', $under_maintenance) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('domain', 'Domain') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'domain', $domain, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('context', 'Context') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'context', $context, array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('favicon', 'Fav Icon') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::file('favicon', $favicon, array('accept'=>'image/*'), TRUE, 100) !!}
                        </div>

                    </div>
                    @if($id > 0)
                    <fieldset class="fieldset">
                        <legend>Default Settings</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('theme_id', 'Theme') !!}
                            </div>

                            <div class="col-sm-10">
                                <div class="col-sm-10">
                                    {!! FormHelper::select('theme_id', $theme, array('class'=>'form-select select-sm'), $theme_id, array("label"=>"name","value"=>"id")) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('category_id', 'Category') !!}
                            </div>

                            <div class="col-sm-10">
                                <div class="col-sm-10">
                                    {!! FormHelper::select('category_id', $category, array('class'=>'form-select select-sm'), $category_id, array("label"=>"name","value"=>"category_id")) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('platform_id', 'Platform') !!}
                            </div>

                            <div class="col-sm-10">
                                <div class="col-sm-10">
                                    {!! FormHelper::select('platform_id', $platform, array('class'=>'form-select select-sm'), $platform_id) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('lang_id', 'Language') !!}
                            </div>

                            <div class="col-sm-10">
                                <div class="col-sm-10">
                                    {!! FormHelper::select('lang_id', $languages, array('class'=>'form-select select-sm'), $lang_id, array("label"=>"name","value"=>"id")) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('country_id', 'Country') !!}
                            </div>

                            <div class="col-sm-10">
                                <div class="col-sm-10">
                                    {!! FormHelper::select('country_id', $countries, array('class'=>'form-select select-sm'), $country_id, array("label"=>"name","value"=>"country_id")) !!}
                                </div>
                            </div>

                        </div>


                    </fieldset>
                    @endif

                    <div class="row">
                        <div class="form-group center-align">
                            <input type="submit" name="submit" value="Save" class="btn btn-success btn-from-submit" /> <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection
