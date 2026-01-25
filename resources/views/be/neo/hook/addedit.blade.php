@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php


        $id = 0;
        $alias = old("alias");
        $description= old("description");
        $site_id = old("site_id", 1);
        $name = old("name");
        $direction = old("direction", "Horizontal");

        if(isset($results)) {
            extract($results);
        }

    @endphp


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form">

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
                            {!!  FormHelper::label('alias', 'Alias') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'alias', $alias , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('description', 'Description') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('description', $description , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('direction', 'Direction') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::select('direction', $directions, array("class"=>"form-select select-sm"), $direction, "plain_array","") !!}
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

