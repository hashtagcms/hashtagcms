<section class="mt-10 mb-10 p-10 bg-modern-bg">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card p-10">
            <h2 class="text-xl font-bold text-modern-blue bg-blue-50/50 px-5 py-2.5 rounded-xl mb-6 inline-block tracking-tight">{{$moduleInfo['dataType']}} {{ htcms_trans('hashtagcms::messages.module') }}</h2>
            <p class="text-lg text-slate-600 mb-8 leading-relaxed font-medium">
                {!! htcms_trans('hashtagcms::messages.url_service_desc', ['limit' => '<code class="text-modern-blue font-bold">limit</code>']) !!}
            </p>
            <div class="bg-modern-navy text-blue-400 font-mono p-5 rounded-2xl mb-10 overflow-x-auto text-sm border border-white/5 shadow-inner">
                <strong class="text-slate-400">{{ htcms_trans('hashtagcms::messages.service_url') }}</strong> <span class="ml-2">https://picsum.photos/v2/list?page=2&limit=:limit</span>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between bg-blue-50/50 p-8 rounded-3xl border border-blue-100/50 mb-12 gap-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-modern-blue shadow-sm border border-blue-100">
                        <i class="fa fa-list-ol text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-modern-navy">{{ htcms_trans('hashtagcms::messages.total_results') }}</h3>
                        <p class="text-sm text-slate-500 font-medium">{{ htcms_trans('hashtagcms::messages.displaying') }} <span class="text-modern-blue font-bold">{{sizeof($data)}}</span> {{ htcms_trans('hashtagcms::messages.items') }}</p>
                    </div>
                </div>
                <div class="text-slate-600 text-sm bg-white/80 py-3 px-5 rounded-2xl border border-blue-100/50 backdrop-blur-sm">
                    <span class="font-bold text-modern-navy">{{ htcms_trans('hashtagcms::messages.try_changing_url') }}</span> 
                    <code class="bg-slate-100 px-3 py-1 rounded-lg ml-2 text-modern-blue font-mono border border-slate-200">{{htcms_get_domain_path()}}/example?limit=10</code>
                </div>
            </div>
 
            @if(sizeof($data) == 0)
                <div class="bg-red-50 text-red-600 p-8 rounded-3xl border border-red-100 flex items-center gap-3">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>{{ htcms_trans('hashtagcms::messages.service_error') }}</span>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($data as $pic)
                        <div class="group relative overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 bg-slate-100 border border-slate-100">
                            <div class="aspect-square overflow-hidden">
                                <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="{{$pic['download_url']}}" alt="{{ htcms_trans('hashtagcms::messages.photo_by') }} {{$pic['author']}}">
                            </div>
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-modern-navy/90 to-transparent p-4 pt-10">
                                <p class="text-[11px] text-white font-bold truncate tracking-tight">{{$pic['author']}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
