<section class="py-24 bg-slate-50 relative overflow-hidden">
    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
            <!-- Text Content -->
            <div class="lg:w-5/12">
                <div class="space-y-6">
                    <p class="uppercase tracking-[0.1em] text-blue-600 text-[11px] font-bold mb-4">{{ htcms_trans('hashtagcms::messages.architecture') }}</p>
                    <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 leading-[1.2] tracking-tight">
                        @php
                            $philTitle = htcms_trans('hashtagcms::messages.philosophy_title', 'Developer First philosophy.');
                            $philTitle = str_replace(':philosophy', '<span class="text-slate-400">Philosophy.</span>', $philTitle);
                        @endphp
                        {!! $philTitle !!}
                    </h2>
                    <p class="text-lg text-slate-500 leading-relaxed font-normal">
                        {{ htcms_trans('hashtagcms::messages.philosophy_description') }}
                    </p>
                    <div class="pt-4">
                        <a href="#" class="modern-button">
                            {{ htcms_trans('hashtagcms::messages.read_documentation') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Code Comparison Block -->
            <div class="lg:w-7/12 w-full">
                <div class="bg-slate-900 rounded-2xl p-6 lg:p-8 shadow-2xl relative group transform hover:-translate-y-1 transition-transform duration-500">
                    
                    <!-- Window Controls -->
                    <div class="flex items-center justify-between mb-8 border-b border-white/5 pb-5">
                        <div class="flex gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                        </div>
                        <div class="text-slate-500 text-[10px] font-bold tracking-widest uppercase">
                            {{ htcms_trans('hashtagcms::messages.hybrid_engine') }}
                        </div>
                    </div>

                    <!-- Code Grids -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Headless API JSON -->
                        <div class="relative">
                            <div class="bg-slate-800/50 rounded-xl p-5 font-mono text-[13px] overflow-x-auto border border-white/5 h-full">
                                <div class="text-slate-500 mb-4 text-[11px] uppercase tracking-wider font-bold">{{ htcms_trans('hashtagcms::messages.api_response') }}</div>
<pre><code class="text-blue-400">{
  <span class="text-slate-300">"data"</span>: {
    <span class="text-slate-300">"title"</span>: <span class="text-emerald-400">"Ocean"</span>,
    <span class="text-slate-300">"nodes"</span>: [
      {
        <span class="text-slate-300">"id"</span>: <span class="text-emerald-400">"hero"</span>
      }
    ]
  }
}</code></pre>
                            </div>
                        </div>

                        <!-- Blade Template -->
                        <div class="relative">
                            <div class="bg-slate-800/50 rounded-xl p-5 font-mono text-[13px] overflow-x-auto border border-white/5 h-full">
                                <div class="text-slate-500 mb-4 text-[11px] uppercase tracking-wider font-bold">{{ htcms_trans('hashtagcms::messages.native_view') }}</div>
<pre><code class="text-purple-400">&#64;extends<span class="text-slate-300">(</span><span class="text-emerald-400">'theme'</span><span class="text-slate-300">)</span>

<span class="text-purple-400">&#64;section</span><span class="text-slate-300">(</span><span class="text-emerald-400">'ui'</span><span class="text-slate-300">)</span>
  &lt;<span class="text-blue-400">h1</span>&gt;<span class="text-slate-300">&#123;</span>$title<span class="text-slate-300">&#125;</span>&lt;/<span class="text-blue-400">h1</span>&gt;
  
  <span class="text-purple-400">&#64;include</span><span class="text-slate-300">(</span><span class="text-emerald-400">'hero'</span><span class="text-slate-300">)</span>
<span class="text-purple-400">&#64;endsection</span></code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
