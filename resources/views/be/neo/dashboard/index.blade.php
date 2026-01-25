@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-show-copy="false" data-show-paste="false" data-show-back="false"></title-bar>
    <div class="container">
        <div class="row m-2">
            @foreach($data as $row)
                <div class="col-auto">
                    <info-box data-title="{{$row['label']}}"
                              data-sub-title="{{$row['total'] === 0 ? "" : $row['total']}}"
                              data-icon-css="{{$row['icon']}}"
                              data-link="{{htcms_admin_path($row['link'])}}"
                    ></info-box>
                </div>
            @endforeach
        </div>
        <div class="row" style="padding: 10px;">
            <div class="col-lg-5">
                <h2>Top Categories</h2>
                <canvas id="topCatgories" width="100%" height="100">Loading...</canvas>
            </div>
            <div class="col-lg-5">
                <h2>Top Contents</h2>
                <canvas id="topContents" width="100%" height="100">Loading...</canvas>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{htcms_admin_asset('js/dashboard.js')}}"></script>
    <script>
        window.addEventListener("load", ()=> {
            if(window.Dashboard) {
                Dashboard.init(<?php echo $graphData; ?>);
            } else {
                console.log("Unable to find dashboard")
            }
        });
    </script>
@endpush
