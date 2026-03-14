@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-show-copy="false" data-show-paste="false" data-show-back="false"></title-bar>
    <div class="max-w-full">
        <div class="flex flex-wrap gap-6 mb-12">
            @php $i = 1; @endphp
            @foreach($data as $row)
                <div class="flex-1 min-w-[280px]">
                    <info-box data-title="{{$row['label']}}"
                              data-sub-title="{{$row['total'] === 0 ? "" : $row['total']}}"
                              data-icon-css="{{$row['icon']}}"
                              data-link="{{htcms_admin_path($row['link'])}}"
                              data-color-index="{{$i % 5}}"
                    ></info-box>
                </div>
                @php $i++; @endphp
            @endforeach
        </div>

        
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <div class="bg-white p-10 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-4 bg-blue-600 rounded-full"></div>
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Top Categories</h2>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="topCatgories"></canvas>
                </div>
            </div>
            
            <div class="bg-white p-10 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Top Contents</h2>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="topContents"></canvas>
                </div>
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
