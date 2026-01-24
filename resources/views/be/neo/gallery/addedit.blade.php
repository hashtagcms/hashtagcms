@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php

        $id = 0;
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $path = old('path', '');
        $media_type = old('media_type', 'image');
        $group_name = old('group_name');
        $media_key = old('media_key');
        $tags = old('tags', "");

        $isMultiple = array('class'=>'form-control', 'multiple'=>'multiple');
        $fileField = 'image[]';
        $tag = [];

        if(isset($results)) {
            extract($results);
        }
        foreach ($tag as $row) {
            $tags .= $row['name'].",";
        }

        $tags = rtrim($tags, ",");

        if ($id > 0) {
            $isMultiple = array('class'=>'form-control');
            $fileField = 'image';
        }



    @endphp

        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}
                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    <fieldset class="fieldset">
                        <legend>Type</legend>
                    @if(sizeof($typeGroups) > 0)
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('typeGroups', 'Choose from existing type') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('typeGroups', $typeGroups, array('class'=>'form-select select-sm', 'onChange'=>'document.getElementById("media_type").value = this.value'), '', "plain_array") !!}

                            </div>

                        </div>
                    @endif
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('media_type', 'Media Type') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'media_type', $media_type , array('class'=>'form-control', 'required'=>'required', 'placeholder'=>'Kind of file you are uploading or choose from the above dropdown')) !!}
                        </div>
                    </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend>Group</legend>
                    @if(sizeof($imageGroups) > 0)
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('groups', 'Choose from existing group') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('groups', $imageGroups, array('class'=>'form-select select-sm', 'onChange'=>'document.getElementById("group_name").value  = this.value'), '', "plain_array") !!}
                            </div>

                        </div>
                    @endif
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('group_name', 'Group') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'group_name', $group_name , array('class'=>'form-control', 'placeholder'=>'Type any group name or choose from the above dropdown if there is any')) !!}
                        </div>

                    </div>
                    </fieldset>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('tags', 'Tags (for search)') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'tags', $tags, array('class'=>'form-control', 'placeholder'=>'Please enter keyword to search later. User comma for multiple keywords')) !!}
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('media_key', 'Media Key') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'media_key', $media_key, array('class'=>'form-control', 'placeholder'=>'Any key for the media? (optional)')) !!}
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('image', 'Select file(s)') !!}
                            @if($id === 0)
                            <br /> <span class="small text-info">Will be uploaded as a separate record.</span>
                            @endif
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::file($fileField, $path, $isMultiple, TRUE, 100, NULL, "", FALSE) !!}
                        </div>

                    </div>

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
