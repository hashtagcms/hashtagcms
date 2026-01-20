@extends(htcms_admin_config('theme').'.index')

@section('content')
<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
           data-back-url="{{$backURL ?? ''}}"
           data-show-copy="false"
           data-show-paste="false"
></title-bar>

<div class="card">
    <div class="card-header">
        {{$title ?? "Whoops!"}}
    </div>
    <div class="card-body">
        <p class="card-title text-danger"> {{$message ?? "There is some error. Please check the logs"}} </p>
    </div>
</div>

@endsection

