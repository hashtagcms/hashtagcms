<section class="mt-10 mb-10 p-10 bg-modern-bg">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card p-10">
            <h2 class="text-xl font-bold text-modern-blue bg-blue-50/50 px-5 py-2.5 rounded-xl mb-6 inline-block tracking-tight">{{$moduleInfo['dataType']}} {{ htcms_trans('hashtagcms::messages.module') }}</h2>
            <p class="text-lg text-slate-600 mb-6 font-medium leading-relaxed">
                {{ htcms_trans('hashtagcms::messages.service_module_desc') }}
            </p>
            <div class="bg-modern-navy text-blue-400 font-mono p-5 rounded-2xl mb-12 overflow-x-auto text-sm border border-white/5 shadow-inner">
                <strong class="text-slate-400">{{ htcms_trans('hashtagcms::messages.service_url') }}</strong> <span class="ml-2">https://picsum.photos/v2/list?limit=4</span>
            </div>

            @if(sizeof($data) == 0)
                <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 flex items-center gap-3">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>{{ htcms_trans('hashtagcms::messages.service_error') }}</span>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($data as $pic)
                        <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-slate-100">
                            <div class="aspect-square overflow-hidden bg-slate-50">
                                <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="{{$pic['download_url']}}" alt="{{ htcms_trans('hashtagcms::messages.photo_by') }} {{$pic['author']}}">
                            </div>
                            <div class="p-6 text-center">
                                <p class="text-sm font-bold text-modern-navy truncate tracking-tight group-hover:text-modern-blue transition-colors">{{$pic['author']}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
