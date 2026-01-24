@extends(htcms_admin_config('theme').'.index')

@section('content')
<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>

<div class="card">
    @if(is_array($isSaved) && isset($isSaved['status']) && $isSaved['status']>200)

       <div class="card-header">
          Error!
        </div>
        <div class="card-body">
          <p class="card-title text-danger"> {{$isSaved['message']}} </p>
        </div>

    @else
        <div class="card-header">
                  Success!
                </div>
                <div class="card-body">
                  <p class="card-title text-success"> Great! Data has been saved successfully. <timer-button data-timeout="3" data-back-url="{{$backURL}}">Going Back in... </timer-button></p>
         </div>
    @endif

    <div class="card-footer">

        <a class="btn btn-primary" href="{{htcms_admin_path(request()->module_info->controller_name.'/create')}}">Add New</a> <a class="btn btn-outline-secondary" href="{{$backURL}}">Back</a>
    </div>

</div>

@endsection
