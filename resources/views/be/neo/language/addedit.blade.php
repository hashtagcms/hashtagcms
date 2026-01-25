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


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}


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
                            {!!  FormHelper::label('iso_code', 'ISO Code') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('language_code', 'Language Code') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'language_code', $language_code, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('date_format_lite', 'Date Format Lite') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'date_format_lite', $date_format_lite, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('date_format_full', 'Date Format Full') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'date_format_full', $date_format_full, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('is_rtl', 'Is right-to-left?') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('is_rtl', $is_rtl) !!}
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

