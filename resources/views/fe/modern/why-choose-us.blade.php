<section class="py-24 bg-white relative overflow-hidden" id="about">
    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto">
            <div>
                <p class="uppercase tracking-[0.2em] text-blue-600 text-[11px] font-bold mb-4">{{ htcms_trans('hashtagcms::messages.why_choose_us') }}</p>
                <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 tracking-tight max-w-2xl leading-tight">
                    @php
                        $whyTitle = htcms_trans('hashtagcms::messages.why_choose_us_title', 'Designed for performance.');
                        $whyTitle = str_replace(':performance', '<span class="text-blue-600">performance</span>', $whyTitle);
                    @endphp
                    {!! $whyTitle !!}
                </h2>
            </div>
            <p class="text-lg text-slate-500 font-normal leading-relaxed max-w-md">
                {{ htcms_trans('hashtagcms::messages.why_choose_us_description') }}
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-bolt text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.innovation_first') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.innovation_first_desc') }}
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm lg:translate-y-6">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-layer-group text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.comp_driven') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.comp_driven_desc') }}
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-code-branch text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.framework_agnostic') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.framework_agnostic_desc') }}
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-shield-halved text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.trust_builders') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.trust_builders_desc') }}
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm lg:translate-y-6">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-cubes-stacked text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.modular_utility') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.modular_utility_desc') }}
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="modern-card modern-card-hover p-10 group border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mb-8 border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                    <i class="fa-solid fa-bolt text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 tracking-tight">{{ htcms_trans('hashtagcms::messages.rapid_deployment') }}</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.rapid_deployment_desc') }}
                </p>
            </div>
        </div>
    </div>
</section>
