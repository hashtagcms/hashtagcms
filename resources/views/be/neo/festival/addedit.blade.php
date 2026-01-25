@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"></title-bar>
    @php


        $id = 0;
        $site_id = htcms_get_siteId_for_admin();
        $name = old('name', '');
        $image = old('image', '');
        $lottie = old('lottie', '');
        $body_css = old('body_css', '');
        $extra = old('extra', '');
        $start_date = old('start_date', date('Y-m-d'));
        $end_date = old('end_date', date('Y-m-d', strtotime("next month")));
        $publish_status = old('publish_status', 1);

        $width = old('width', '100%');
        $height = old('height', '100%');
        $background = old('background', 'transparent');
        $hide_on_complete = old('hide_on_complete', 1);

        $top = old('top', 0);
        $left = old('left', 0);
        $position_css = old('position_css', 'absolute');
        $z_index = old('z_index', 99999);

        $zIndex = old('zIndex', 99999);
        $play_mode = old('play_mode', 'normal'); //bounce
        $direction = old('direction', '1'); //backward

        $autoplay = old('autoplay', 1);
        $loop = old('loop', 1);
        $hover = old('hover', 0);
        $controls = old('controls', 0);

        //print_r($results);

        if(isset($results)) {
            extract($results);
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
        }

    @endphp


    <div class="row">
        <div class="admin-form" id="adminForm">
            <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
                  class="form-horizontal" role="form" enctype="multipart/form-data">

                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('name', 'Name') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'name', $name, array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('body_css', 'Body Css') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'body_css', $body_css , array('class'=>'form-control')) !!}
                    </div>
                </div>

                <!-- create all fields here -->
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('image', 'Image') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::file('image', $image, array('accept'=>'image/*'), TRUE, 100) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lottie', 'Lottie') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::file('lottie', $lottie, array(), TRUE, 100) !!}
                    </div>
                </div>

                <fieldset class="fieldset">
                    <legend>Lottie Props</legend>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            {!!  FormHelper::label('width', 'Width (px/%)') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'width', $width, array('class'=>'form-control')) !!}
                        </div>
                        <div class="col-sm-2 text-end">
                            {!!  FormHelper::label('height', 'Height (px/%)') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'height', $height, array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            {!!  FormHelper::label('top', 'Top') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'top', $top, array('class'=>'form-control')) !!}
                        </div>

                        <div class="col-sm-2 text-end">
                            {!!  FormHelper::label('left', 'Left') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'left', $left, array('class'=>'form-control')) !!}
                        </div>

                    </div>
                    <div class="form-group row">


                        <div class="col-sm-2">
                            {!!  FormHelper::label('position_css', 'Position Css') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'position_css', $position_css, array('class'=>'form-control')) !!}
                        </div>

                        <div class="col-sm-2 text-end">
                            {!!  FormHelper::label('background', 'Background color') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'background', $background, array('class'=>'form-control', 'style'=>'width:80%; position;relative; display:inline')) !!}
                            {!! FormHelper::input('color', 'color', null, array('oninput'=>'updateColor(this)', 'style'=>'width:10%;position:relative;display:inline;margin-left:10px;top:5px')) !!}
                        </div>
                    </div>

                    <div class="form-group row">


                        <div class="col-sm-2">
                            {!!  FormHelper::label('z_index', 'zIndex') !!}
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::input('text', 'z_index', $z_index, array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2">
                            {!!  FormHelper::label('play_mode', 'Play Mode') !!}
                        </div>
                        <div class="col-sm-3">
                            {!!  FormHelper::label('play_mode_normal', 'Normal') !!}
                            {!! FormHelper::input('radio', 'play_mode', 'normal', array("id"=>"play_mode_normal", 'class'=>'me-3 ms-1'), $play_mode) !!}

                            {!!  FormHelper::label('play_mode_bounce', 'Bounce') !!}
                            {!! FormHelper::input('radio', 'play_mode', 'bounce', array("id"=>"play_mode_bounce", 'class'=>'me-3 ms-1'), $play_mode) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2">
                            {!!  FormHelper::label('direction', 'Direction') !!}
                        </div>
                        <div class="col-sm-3">
                            {!!  FormHelper::label('direction_forward', 'Forward') !!}
                            {!! FormHelper::input('radio', 'direction', '1', array("id"=>"direction_forward", 'class'=>'me-3 ms-1'), $direction) !!}

                            {!!  FormHelper::label('direction_backward', 'Backward') !!}
                            {!! FormHelper::input('radio', 'direction', '-1', array("id"=>"direction_backward", 'class'=>'me-3 ms-1'), $direction) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::checkbox('autoplay', $autoplay, array()) !!}
                            &nbsp;{!!  FormHelper::label('autoplay', 'Autoplay') !!}&nbsp;
                            <br /><span class="small">Play animation on load</span>
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::checkbox('loop', $loop, array()) !!}
                            &nbsp;{!!  FormHelper::label('loop', 'Loop') !!}&nbsp;
                            <br /><span class="small">Set to repeat animation</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::checkbox('hover', $hover, array()) !!}
                            &nbsp;{!!  FormHelper::label('hover', 'Hover') !!}&nbsp;
                            <br /><span class="small">Play animation on hover</span>
                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::checkbox('controls', $controls, array()) !!}
                            &nbsp;{!!  FormHelper::label('controls', 'Controls') !!}&nbsp;
                            <br /><span class="small">Display animation controls: Play, Pause & Slider</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-3">
                            {!! FormHelper::checkbox('hide_on_complete', $hide_on_complete, array()) !!}
                            &nbsp;{!!  FormHelper::label('hide_on_complete', 'Hide on complete') !!}&nbsp;
                            <br /><span class="small">Hide the player after complete</span>
                        </div>

                    </div>

                </fieldset>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('extra', 'Extra') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('extra', $extra, array('class'=>'form-control', 'placeholder'=>'Any other value')) !!}
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('start_date', 'Start Date') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('date', 'start_date', $start_date , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('end_date', 'End Date') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('date', 'end_date', $end_date, array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                <fieldset class="fieldset">
                    <legend>Publishing Options</legend>

                    <div class="form-group row">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('publish_status', 'Published') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('checkbox', 'publish_status', $publish_status) !!}
                        </div>
                    </div>

                </fieldset>

                <div class="row">
                    <div class="form-group center-align">
                        <input type="submit" name="submit" value="Save" class="btn btn-success"/> <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

@push('scripts')
    <script>
        let backgroundInput;
        function updateColor(color) {
            if(!backgroundInput) {
                backgroundInput = document.getElementById("background");
            }
            backgroundInput.value = color.value;

        }

    </script>
@endpush
