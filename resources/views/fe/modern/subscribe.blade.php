<section class="bg-slate-50 py-24 sm:py-32 border-t border-slate-100" id="subscribe">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card bg-white border-slate-100 shadow-sm p-12 lg:p-24 relative overflow-hidden">
            <!-- Subtle Background Pattern -->
            <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-50 rounded-full blur-[80px] opacity-60"></div>

            <div class="relative z-10 max-w-2xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 mb-8 text-[11px] font-bold tracking-widest uppercase bg-slate-50 rounded-full border border-slate-100 text-slate-500">
                    <i class="fa fa-envelope-o text-blue-500/70"></i>
                    {{ htcms_trans('hashtagcms::messages.newsletter') }}
                </div>
                
                <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6 tracking-tight leading-tight">
                    @php
                        $loopText = htcms_trans('hashtagcms::messages.stay_in_loop', 'Stay in the loop.');
                        $loopText = str_replace(':loop', '<span class="text-blue-600">loop</span>', $loopText);
                    @endphp
                    {!! $loopText !!}
                </h2>
                
                <p class="text-lg text-slate-500 mb-10 font-normal leading-relaxed">
                    {{htcms_trans("hashtagcms::modules.Please leave us your email address. We will update you.")}}
                </p>
                
                <form data-form="newsletter-form" class="flex flex-col sm:flex-row gap-3" action="/common/newsletter" method="post">
                    @csrf
                    <div class="flex-grow">
                        <input type="email" name="email" class="w-full h-14 px-6 rounded-lg bg-slate-50 border border-slate-100 text-slate-900 placeholder:text-slate-400 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm font-medium" placeholder="{{htcms_trans("hashtagcms::auth.E-Mail Address")}}" required>
                    </div>
                    <button class="modern-button h-14 px-8 min-w-[140px]" type="submit">
                        {{htcms_trans("hashtagcms::auth.Submit")}}
                    </button>
                </form>

                <div class="mt-6" data-message-holder="newsletter-message-holder" style="display: none;">
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-lg text-blue-900 text-sm">
                        <span class="message font-bold" data-class="newsletter-message"></span> 
                        <span data-class="newsletter-close" title="{{ htcms_trans('hashtagcms::messages.close') }}" class="w-8 h-8 flex items-center justify-center hover:bg-white/50 rounded-full cursor-pointer transition-colors shrink-0">
                            <i class="fa fa-times"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        // Initialize the standard Newsletter component from HashtagCms SDK
        if (typeof HashtagCms !== 'undefined' && HashtagCms.Newsletter) {
            const newsletter = new HashtagCms.Newsletter({
                form: 'form[data-form="newsletter-form"]',
                messageHolder: 'div[data-message-holder="newsletter-message-holder"]'
            });
        }
    </script>
@endpush
    