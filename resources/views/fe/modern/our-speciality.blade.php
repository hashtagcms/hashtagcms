<section class="py-24 bg-slate-50 relative overflow-hidden" id="features">
    <!-- Subtle Background Decor -->
    <div class="js-parallax absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-100/30 rounded-full blur-[100px]" data-parallax-speed="0.03"></div>
    <div class="js-parallax absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-[400px] h-[400px] bg-slate-200/40 rounded-full blur-[80px]" data-parallax-speed="0.06"></div>

    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="mb-16 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div>
                <p class="uppercase tracking-[0.2em] text-blue-600 text-[11px] font-bold mb-4">{{ htcms_trans('hashtagcms::messages.core_principles') }}</p>
                <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 tracking-tight max-w-2xl leading-tight">
                    @php
                        $featTitle = htcms_trans('hashtagcms::messages.features_title', 'Clean code, extraordinary results.');
                        $featTitle = str_replace(':extraordinary', '<span class="text-slate-400">extraordinary</span>', $featTitle);
                    @endphp
                    {!! $featTitle !!}
                </h2>
            </div>
            <p class="text-lg text-slate-500 font-normal leading-relaxed max-w-md">
                {{ htcms_trans('hashtagcms::messages.features_description') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">01</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-desktop text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_responsive') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_responsive_desc') }}
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">02</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-paint-brush text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_customizable') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_customizable_desc') }}
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">03</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-font text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_typography') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_typography_desc') }}
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">04</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-moon text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_aesthetics') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_aesthetics_desc') }}
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">05</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-bolt text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_lightweight') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_lightweight_desc') }}
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-4xl font-bold text-slate-100 group-hover:text-slate-200 transition-colors duration-500">06</span>
                    <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="fa fa-code text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.feature_clean_code') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm mt-auto">
                    {{ htcms_trans('hashtagcms::messages.feature_clean_code_desc') }}
                </p>
            </div>
        </div>
    </div>
</section>
