<section class="mt-10 mb-10 p-10 bg-modern-bg">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card p-10">
            <h2 class="text-xl font-bold text-modern-blue bg-blue-50/50 px-5 py-2.5 rounded-xl mb-6 inline-block tracking-tight">{{$moduleInfo['dataType']}} {{ htcms_trans('hashtagcms::messages.module') }}</h2>
                {{ htcms_trans('hashtagcms::messages.query_service_desc') }} <br />
                {!! htcms_trans('hashtagcms::messages.query_service_props', ['data' => '<code class="bg-blue-50 text-modern-blue px-2 py-0.5 rounded">data</code>', 'queryData' => '<code class="bg-blue-50 text-modern-blue px-2 py-0.5 rounded">queryData</code>']) !!}
            
            <div class="space-y-4 mb-12">
                <div class="bg-modern-navy text-blue-400 font-mono p-5 rounded-2xl overflow-x-auto text-sm border border-white/5 shadow-inner">
                    <strong class="text-slate-400">{{ htcms_trans('hashtagcms::messages.service_url') }}</strong> <span class="ml-2">https://picsum.photos/v2/list?page=2&limit=4</span>
                </div>
                <div class="bg-modern-navy text-emerald-400 font-mono p-5 rounded-2xl overflow-x-auto text-sm border border-white/5 shadow-inner">
                    <strong class="text-slate-400">{{ htcms_trans('hashtagcms::messages.query_label') }}</strong> <span class="ml-2">select * from site_props where site_id=:site_id and group_name="SocialLinks" and is_public=1 and deleted_at is null;</span>
                </div>
            </div>

            <div class="mb-12">
                <h3 class="text-2xl font-bold text-modern-navy mb-8 flex items-center gap-3">
                    <span class="w-8 h-1 bg-modern-blue rounded-full"></span>
                    {{ htcms_trans('hashtagcms::messages.service_data') }}
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $serviceData = isset($serviceData) ? $serviceData : (isset($data['data']) ? $data['data'] : []);
                        $queryData = isset($queryData) ? $queryData : (isset($data['queryData']) ? $data['queryData'] : []);
                    @endphp
                    @if(sizeof($serviceData) == 0)
                        <div class="col-span-full bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 flex items-center gap-3">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span>{{ htcms_trans('hashtagcms::messages.service_error') }}</span>
                        </div>
                    @else
                        @foreach($serviceData as $pic)
                            <div class="group relative overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 bg-slate-100">
                                <img class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-700" src="{{$pic['download_url']}}" alt="Service Image">
                                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-modern-navy/90 to-transparent p-6 pt-12">
                                    <span class="text-white font-bold truncate block tracking-tight">{{$pic['author']}}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-2xl font-bold text-modern-navy mb-8 flex items-center gap-3">
                    <span class="w-8 h-1 bg-modern-blue rounded-full"></span>
                    {{ htcms_trans('hashtagcms::messages.query_data') }}
                </h3>
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100 overflow-x-auto shadow-inner">
                    <pre class="text-sm text-slate-700 font-mono">{{json_encode($queryData, JSON_PRETTY_PRINT)}}</pre>
                </div>
            </div>
        </div>
    </div>
</section>
