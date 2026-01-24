@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php

        $id = 0;
        $name = old('name');
        $country_id = old('country_id');
        $iso_code = old('iso_code');
        $tax_behavior = old('tax_behavior');
        $airport_name = old('airport_name');
        $airport_code = old('airport_code');
        $latitude = old('latitude');
        $longitude = old('longitude');

        if(isset($results)) {
            extract($results);
        }

        //dd($countries[0]);


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
                            {!!  FormHelper::label('name', 'City name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('country_id', 'Country') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::select('country_id', $countries,
                                                    array('required'=>'required', 'class'=>'form-select select-sm'),
                                                    $country_id,
                                                    array("value"=>"id", "label"=>"lang.name")) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('iso_code', 'ISO Code') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('airport_code', 'Airport Code') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'airport_code', $airport_code, array('class'=>'form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('airport_name', 'Airport Name') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'airport_name', $airport_name, array('class'=>'form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('tax_behavior', 'Tax Behavior') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('tax_behavior', $tax_behavior) !!}
                        </div>


                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('latitude', 'Latitude') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'latitude', $latitude, array('class'=>'form-control')) !!}

                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('longitude', 'Longitude') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'longitude', $longitude, array('class'=>'form-control')) !!}

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
