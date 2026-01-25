@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
    ></title-bar>

    @php


        $id = 0;

        $name = old('name');
        $alias = old('alias');;
        $directory = old('directory');;
        $skeleton = old('skeleton');;
        $body_class = old('body_class');;
        $header_content = old('header_content');;
        $footer_content = old('footer_content');;
        $site_id = old('site_id', htcms_get_siteId_for_admin());;
        $img_preview = old('img_preview');

        //print_r($results);

        if(isset($results)) {
            extract($results);
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
                            {!!  FormHelper::label('directory', 'Directory') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'directory', $directory , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('body_class', 'Body Class') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'body_class',$body_class , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('skeleton', 'Skeleton') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('skeleton',$skeleton , array('class'=>'form-control','rows'=>'12','cols'=>'80','required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('header_content', 'Header Content') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('header_content',$header_content, array('class'=>'form-control','rows'=>'6','cols'=>'80')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('footer_content', 'Footer Content') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::textarea('footer_content',$footer_content , array('class'=>'form-control','rows'=>'6','cols'=>'80')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('img_preview', 'Preview') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::file('img_preview', $img_preview, array(), true, 200) !!}
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
