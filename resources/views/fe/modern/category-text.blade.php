<div class="py-24 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="max-w-3xl mx-auto">
            <article class="modern-card bg-white border-slate-100 shadow-sm mb-12 overflow-hidden">
                <div class="p-10 lg:p-16 border-b border-slate-50">
                    <p class="uppercase tracking-[0.2em] text-blue-600 text-[10px] font-bold mb-4">{{ htcms_get_category_info('name') }}</p>
                    <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 leading-tight mb-2 tracking-tight">
                        {{ htcms_get_category_info('title') }}
                    </h1>
                </div>
                
                <div class="p-10 lg:p-16">
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed italic-quote">
                        {!! htcms_get_category_info("content") !!}
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
