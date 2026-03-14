<!-- slider_area_start -->
@php
    $img = htcms_get_image_resource("dashboard.png");
@endphp
<<section class="relative overflow-hidden bg-white py-24 lg:py-32" id="hero">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="js-parallax absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-blue-50 rounded-full blur-[120px] opacity-70" data-parallax-speed="0.05"></div>
    <div class="js-parallax absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-slate-100 rounded-full blur-[100px] opacity-50" data-parallax-speed="0.08"></div>

    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-6 pt-10 lg:pt-0">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-8 text-[11px] font-bold tracking-widest uppercase bg-slate-100 rounded-full border border-slate-200 text-slate-600 animate-fade-in-up">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    {{ htcms_trans('hashtagcms::messages.premium_template') }}
                </div>
                <h1 class="text-5xl lg:text-7xl font-bold leading-[1.1] mb-8 tracking-tight text-slate-900">
                    @php
                        $heroTitle = htcms_trans('hashtagcms::messages.hero_title', 'Design your perfect project.');
                        $heroTitle = str_replace(':perfect', '<span class="text-blue-600">perfect</span>', $heroTitle);
                    @endphp
                    {!! $heroTitle !!}
                </h1>
                <p class="text-lg lg:text-xl text-slate-500 leading-relaxed mb-10 max-w-xl">
                    {{ htcms_trans('hashtagcms::messages.hero_description') }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#features" class="modern-button">
                        {{ htcms_trans('hashtagcms::messages.get_started') }}
                    </a>
                    <a href="#" class="modern-button-outline">
                        {{ htcms_trans('hashtagcms::messages.view_demo') }}
                    </a>
                </div>
                
                <div class="mt-12 flex items-center gap-6 text-slate-400 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-check text-emerald-500"></i>
                        <span>{{ htcms_trans('hashtagcms::messages.no_setup_required') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa fa-check text-emerald-500"></i>
                        <span>{{ htcms_trans('hashtagcms::messages.high_performance') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-6 relative mt-10 lg:mt-0 hidden md:block">
                <div class="relative w-full max-w-2xl mx-auto">
                    <!-- Subtle shadow/glow -->
                    <div class="absolute inset-0 bg-blue-100/50 blur-[80px] rounded-full transform scale-90 translate-y-10 group-hover:scale-100 transition-transform duration-700"></div>
                    
                    <div class="js-parallax relative z-10 bg-white rounded-xl border border-slate-200 shadow-2xl p-1.5" data-parallax-speed="0.12">
                        <!-- Browser Header -->
                        <div class="w-full bg-slate-50 rounded-t-lg border-b border-slate-100 p-3 flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                            <div class="flex-1 text-center bg-white border border-slate-100 mx-6 rounded text-[10px] text-slate-400 py-1">{{ htcms_trans('hashtagcms::messages.preview_url') }}</div>
                        </div>
                        <img src="{{ $img }}" alt="{{ htcms_trans('hashtagcms::messages.preview') }}" class="w-full h-auto rounded-b-lg border border-slate-100/50" />
                    </div>
                    
                    <!-- Feature Card -->
                    <div class="js-parallax absolute -bottom-6 -right-6 bg-white border border-slate-200 p-5 rounded-xl shadow-xl z-20 hidden lg:block" data-parallax-speed="0.2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-900 text-white rounded-lg flex items-center justify-center">
                                <i class="fa fa-bolt"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ htcms_trans('hashtagcms::messages.fast_and_clean') }}</p>
                                <p class="text-slate-400 text-[11px]">{{ htcms_trans('hashtagcms::messages.lighthouse_score') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
