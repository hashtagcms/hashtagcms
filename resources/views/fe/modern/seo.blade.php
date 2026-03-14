<section class="py-24 bg-white relative overflow-hidden" id="faq">
    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-20">
            <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6 tracking-tight">{{ htcms_trans('hashtagcms::messages.faq_title') }}</h2>
            <p class="text-lg text-slate-500 font-normal leading-relaxed">{{ htcms_trans('hashtagcms::messages.faq_desc') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Free / License -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                    <i class="fa fa-gift text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-4 mt-5">{{ htcms_trans('hashtagcms::messages.faq_q1') }}</h4>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.faq_a1') }}
                </p>
            </div>

            <!-- Premium Support -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                    <i class="fa fa-bug text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-4 mt-5">{{ htcms_trans('hashtagcms::messages.support') }}</h4>
                <p class="text-slate-500 leading-relaxed text-sm">
                    {{ htcms_trans('hashtagcms::messages.faq_a2') }}
                </p>
            </div>

            <!-- Professional Help -->
            <div class="modern-card modern-card-hover p-10 flex flex-col h-full group bg-white border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm">
                    <i class="fa fa-question-circle text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-4 mt-5">{{ htcms_trans('hashtagcms::messages.faq_q3') }}</h4>
                <p class="text-slate-500 leading-relaxed text-sm mb-8">
                    {{ htcms_trans('hashtagcms::messages.faq_a3') }}
                </p>
                <div class="mt-auto">
                    <a href='{{htcms_get_path('contact')}}' class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-2 group">
                        {{ htcms_trans('hashtagcms::links.get_in_touch') }}
                        <i class="fa fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
