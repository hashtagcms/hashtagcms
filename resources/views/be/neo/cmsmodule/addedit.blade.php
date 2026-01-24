@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>

    @php

        $id = 0;
        $name = old('name');
        $controller_name = old('controller_name');
        $sub_title = old('sub_title');
        $parent_id = old('parent_id');
        $icon_css = old('icon_css');
        $list_view_name = old('icon_css', 'listing');
        $edit_view_name = old('icon_css', 'addedit');

        //print_r($results);

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
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('sub_title', 'Sub Title') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'sub_title', $sub_title , array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('controller_name', 'Controller Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'controller_name', $controller_name , array('class'=>'form-control', 'required'=>'required')) !!}

                            <div class="alert alert-warning v-space">
                                Be careful while changing controller name. You need to rename lots of files manually.
                            </div>

                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('parent_id', 'Parent') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::select('parent_id', $cmsModules ,
                                                    array('class'=>'form-control'),
                                                    $parent_id
                                                    ) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('icon_css', 'Icon CSS') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'icon_css', $icon_css , array('class'=>'form-control')) !!}
                        </div>

                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('list_view_name', 'Listing View Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'list_view_name', $list_view_name, array('class'=>'form-control', 'placeholder'=>'Enter list view Name (Default list view is common/listing)')) !!}
                        </div>

                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('edit_view_name', 'Edit View Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'edit_view_name', $edit_view_name, array('class'=>'form-control', 'placeholder'=>'Edit list view Name (Default edit view is addedit)')) !!}
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

