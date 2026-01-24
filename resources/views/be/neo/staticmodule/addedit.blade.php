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


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'lang.id', $lang["lang_id"]) !!}

                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    {!! FormHelper::input('hidden', 'insert_by', $insert_by) !!}
                    {!! FormHelper::input('hidden', 'update_by', $update_by) !!}


                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('alias', 'Alias') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'alias', $alias , array('class'=>'form-control', 'required'=>'required')) !!}
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

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('lang_content', 'Content') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('lang_content', $lang["content"] , array('rows'=>14, 'class'=>'form-control', 'required'=>'required')) !!}
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

