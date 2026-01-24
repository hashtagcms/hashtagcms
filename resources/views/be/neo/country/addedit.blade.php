@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php


        $id = 0;
        $lang = array();
        $lang["name"] = old('lang_name');
        $iso_code = old('iso_code');

        $call_prefix = old('call_prefix');

        $contains_states = old('contains_states', 0);
        $need_identification_number = old('need_identification_number', 0);
        $need_zip_code = old('need_zip_code', 0);
        $zip_code_format = old('zip_code_format');
        $display_tax_label = old('display_tax_label', 0);
        $zone_id = old('zone_id', 0);
        $currency_id = old('currency_id', 0);

        //dd($results);


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

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}


                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lang_name', 'Country name') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('iso_code', 'ISO Code') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::input('text', 'iso_code', $iso_code, array('class'=>'form-control', 'required'=>'required')) !!}

                    </div>

                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('zone_id', 'Zone') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::select('zone_id', $zones, array("class"=>"form-select select-sm"), $zone_id) !!}

                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('call_prefix', 'Call prefix') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::input('text', 'call_prefix', $call_prefix, array('class'=>'form-control', 'required'=>'required')) !!}

                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('currency_id', 'Currency') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::select('currency_id', $currencies, array("class"=>"form-select select-sm"), $currency_id) !!}

                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('contains_states', 'Contains States?') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::checkbox('contains_states', $contains_states) !!}

                    </div>

                </div>

                <div class="form-group row">


                    <div class="col-sm-2">
                        {!!  FormHelper::label('need_identification_number', 'Need Identification Number?') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::checkbox('need_identification_number', $need_identification_number) !!}

                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('need_zip_code', 'Need Zip Code?') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::input('checkbox', 'need_zip_code', $need_zip_code) !!}

                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('zip_code_format', 'Zip code format') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::input('text', 'zip_code_format', $zip_code_format, array('class'=>'form-control', 'required'=>'required')) !!}

                    </div>

                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('display_tax_label', 'Display Tax Label?') !!}
                    </div>

                    <div class="col-sm-10">

                        {!! FormHelper::checkbox('display_tax_label', $display_tax_label) !!}

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
