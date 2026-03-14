<section class="py-32 bg-slate-50 overflow-hidden relative" id="products">
    <!-- Decorative -->
    <div
        class="absolute top-0 right-0 w-[800px] h-[800px] bg-gradient-to-b from-[#E9F2F9]/40 to-transparent rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3">
    </div>

    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <!-- Video Embed Container -->
            <div class="lg:w-1/2 w-full">
                <div class="relative group transform hover:scale-[1.02] transition-transform duration-500">
                    <div class="aspect-video relative">
                        <img src="{{htcms_get_image_resource('cms-box-2.png')}}" alt="{{ htcms_trans('hashtagcms::messages.cms_box') }}" class="object-cover">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="lg:w-1/2 space-y-8">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-full border border-blue-100 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#3A89C9] animate-pulse shadow-[0_0_10px_#3A89C9]"></span>
                    <span class="text-[#3A89C9] text-sm font-bold uppercase tracking-widest">{{ htcms_trans('hashtagcms::messages.bundled_with_docs') }}</span>
                </div>
                <h2 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-[#1B325F] leading-[1.1] tracking-tight">
                    {{ htcms_trans('hashtagcms::messages.browse_docs') }}
                </h2>
                <div class="h-1.5 w-24 bg-gradient-to-r from-[#9CC4E4] to-[#9CC4E4] rounded-full"></div>
                <p class="text-xl text-slate-500 leading-relaxed font-light">
                    {{ htcms_trans('hashtagcms::messages.docs_description') }}
                </p>
                <div class="pt-6">
                    <a target="_blank" href="https://github.com/hashtagcms/hashtagcms/blob/master/readme.md"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-white text-[#1B325F] font-bold rounded-2xl shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] hover:shadow-[0_20px_40px_-10px_rgba(0,119,182,0.15)] hover:-translate-y-1 border border-slate-100 transition-all duration-300 text-lg group">
                        <span>{{ htcms_trans('hashtagcms::messages.see_documentation') }}</span>
                        <i
                            class="fa fa-external-link text-sm transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>