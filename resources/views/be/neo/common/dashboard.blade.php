@extends(htcms_admin_config('theme').'.index')

@section('content')
<h1>{{request()->module_info->name}}</h1>
<div class="row">

    @foreach($widgets as $widget)
    <div class="col-xs-6 col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">{{$widget["title"]}}</div>
            <div class="panel-body alert-info">{{$widget["data"]}}</div>
        </div>
    </div>
    @endforeach

</div>

@endsection
