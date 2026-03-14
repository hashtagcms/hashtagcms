<section class="py-24 bg-slate-50 min-h-[80vh]">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Contact Info Sidebar -->
                <div class="lg:col-span-4 space-y-10">
                    <div>
                        <p class="uppercase tracking-[0.1em] text-blue-600 text-[11px] font-bold mb-4">{{ htcms_trans('hashtagcms::links.contact_us') }}</p>
                        <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6 tracking-tight">{{ htcms_trans('hashtagcms::messages.get_in_touch') }}</h1>
                        <p class="text-lg text-slate-500 leading-relaxed font-normal mb-8">
                            {{ htcms_trans('hashtagcms::messages.contact_description') }}
                        </p>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-start space-x-5">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center text-blue-600 border border-slate-100 flex-shrink-0">
                                <i class="fa fa-map-marker text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ htcms_trans('hashtagcms::messages.our_studio') }}</h4>
                                <p class="text-slate-500 text-sm mt-1">{{ htcms_trans('hashtagcms::messages.studio_address') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-5">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center text-blue-600 border border-slate-100 flex-shrink-0">
                                <i class="fa fa-envelope text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ htcms_trans('hashtagcms::messages.email_us') }}</h4>
                                <p class="text-slate-500 text-sm mt-1">hello@oceantheme.example</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-8">
                    <div class="modern-card bg-white border-slate-100 shadow-xl shadow-slate-200/50 p-8 lg:p-12">
                        <form data-form="contact-form" action="/common/contact" method="post" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{htcms_trans("hashtagcms::modules.Name")}}</label>
                                    <input type="text" required placeholder="{{htcms_trans("hashtagcms::modules.Please enter your full name")}}" id="name" name="name" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label for="email" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{htcms_trans("hashtagcms::modules.Email")}}</label>
                                    <input type="email" required placeholder="{{htcms_trans("hashtagcms::modules.Please enter your email")}}" id="email" name="email" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="phone" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{htcms_trans("hashtagcms::modules.Phone")}} ({{ htcms_trans('hashtagcms::messages.optional') }})</label>
                                <input type="text" placeholder="{{htcms_trans("hashtagcms::modules.Please enter your phone number")}}" id="phone" name="phone" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label for="comment" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{htcms_trans("hashtagcms::modules.Comment")}}</label>
                                <textarea required placeholder="{{htcms_trans("hashtagcms::modules.Please tell us your query")}}" id="comment" name="comment" rows="5" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all resize-none text-sm"></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="modern-button w-full sm:w-auto">
                                    {{ htcms_trans('hashtagcms::modules.Submit') }}
                                </button>
                            </div>

                            <div class="mt-8" data-message-holder="contact-message-holder" style="display: none;">
                                <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-lg text-slate-900 text-sm">
                                    <span class="message font-medium" data-class="contact-message"></span> 
                                    <span data-class="contact-close" title="Close" class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-100 rounded-full cursor-pointer transition-colors border border-slate-100 shadow-sm">
                                        <i class="fa fa-times text-[10px]"></i>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        // Initialize the standard Contact component from HashtagCms SDK
        // This handles validation, AJAX submission, and message display automatically
        if (typeof HashtagCms !== 'undefined' && HashtagCms.Contact) {
            const contactForm = new HashtagCms.Contact({
                form: 'form[data-form="contact-form"]',
                messageHolder: 'div[data-message-holder="contact-message-holder"]'
            });
        }
    </script>
@endpush
