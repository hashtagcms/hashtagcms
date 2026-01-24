@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php


        $id = 0;
        $group_name = old('group_name', '');
        $name = old('name');
        $value = old('value');
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $platform_id = old('platform_id', []);
        $is_public = old('is_public', 0);

        $platform_select_name = "platform_id[]";


        //print_r($results);

        if(isset($results)) {
            extract($results);
            if ($id > 0) {
               $platform_select_name = "platform_id";
            }
        }


        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["name"] = "";
        }

    @endphp


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}


                    @if(sizeof($siteGroups) > 0)
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('siteGroups', 'Choose Existing Group') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('siteGroups', $siteGroups, array('class'=>'form-select select-sm', 'onChange'=>'group_name.value = this.value'), '', "plain_array") !!}

                            </div>

                        </div>
                    @endif

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('group_name', 'Site Group') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'group_name', $group_name , array('class'=>'form-control', 'required'=>'required')) !!}

                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('name', 'Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('value', 'Value') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('value', $value, array('rows'=>8, 'class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('platform_id', 'Platform') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::select($platform_select_name, $platforms, array('class'=>'form-select select-sm'), $platform_id) !!}
                        </div>
                    </div>

                    <fieldset class="fieldset">
                        <legend>Visibility</legend>
                        <div class="form-group row">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('is_public', 'Is Public?') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('checkbox', 'is_public', $is_public) !!}
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

@include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

