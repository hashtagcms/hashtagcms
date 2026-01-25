@extends(htcms_admin_config('theme').'.index')

@section('content')

    @php
        $title = htcms_get_module_name(request()->module_info) . " Copier";
    @endphp
    <title-bar data-title="{{$title}}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>

    <div class="row">
        <div class="col-md-12">
            <div class="margin-top-20">
                <site-cloner
                        data-all-sites="{{json_encode($siteInfo)}}"
                >

                </site-cloner>

            </div>

        </div>
    </div>


@endsection
