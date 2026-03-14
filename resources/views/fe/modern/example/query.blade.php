<section class="mt-10 mb-10 p-10 bg-modern-bg">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card p-10">
            <h2 class="text-xl font-bold text-modern-blue bg-blue-50/50 px-5 py-2.5 rounded-xl mb-6 inline-block tracking-tight">{{$moduleInfo['dataType']}} {{ htcms_trans('hashtagcms::messages.module') }}</h2>
            <p class="text-lg text-slate-600 mb-6 font-medium">
                {{ htcms_trans('hashtagcms::messages.desc_query_module') }}
            </p>
            <div class="bg-modern-navy text-blue-400 font-mono p-5 rounded-2xl mb-10 overflow-x-auto text-sm border border-white/5 shadow-inner">
                <strong class="text-slate-400">{{ htcms_trans('hashtagcms::messages.query_label') }}</strong> <span class="ml-2">{{ htcms_trans('hashtagcms::messages.example_query') }}</span>
            </div>
            
            @if(isset($data))
                <div class="space-y-6">
                @foreach($data as $comment)
                <div class="flex flex-col md:flex-row gap-6 items-start bg-slate-50/50 p-6 rounded-3xl border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-md group">
                    <div class="md:w-48 shrink-0">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white shadow-sm border border-slate-100 text-modern-navy font-bold text-xl mb-3 group-hover:bg-modern-blue group-hover:text-white group-hover:border-modern-blue transition-all duration-300">
                            {{ substr($comment['name'], 0, 1) }}
                        </span>
                        <div class="font-bold text-modern-navy text-lg">{{$comment['name']}}</div>
                    </div>
                    <div class="flex-grow w-full">
                        <div class="text-slate-600 leading-relaxed bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative">
                            <!-- Arrow -->
                            <div class="hidden md:block absolute -left-2 top-8 w-4 h-4 bg-white border-l border-b border-slate-100 transform rotate-45"></div>
                            {{$comment['comment']}}
                        </div>
                    </div>
                </div>
                @endforeach
                </div>
            @else
                <div class="text-slate-500 italic p-10 bg-slate-50 rounded-2xl text-center border border-dashed border-slate-200">{{ htcms_trans('hashtagcms::messages.no_data') }}</div>
            @endif
        </div>
    </div>
</section>
