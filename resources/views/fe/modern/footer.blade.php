<footer class="bg-white text-slate-600 pt-20 pb-10 border-t border-slate-100 relative overflow-hidden">
    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <!-- Branding/About -->
            <div class="md:col-span-2">
                <a class="text-2xl font-bold text-slate-900 mb-6 inline-flex items-center gap-2.5 group tracking-tight"
                    href="{{htcms_get_path('/')}}">
                    <div
                        class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-white transition-transform duration-500 group-hover:scale-105">
                        <span class="italic text-lg font-bold">
                            <img src="{{ htcms_get_image_resource('logo.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </span>
                    </div>
                    <span>{{ htcms_get_site_info('name') }}</span>
                </a>
                <p class="text-slate-500 max-w-sm text-base leading-relaxed mt-4">
                    {{ htcms_trans('hashtagcms::messages.theme_description', 'A premium UI template for modern web applications. Built with elegance and designed for extreme performance and aesthetics.') }}
                </p>
            </div>

            <!-- Links -->
            <div>
                <h5 class="text-xs font-bold mb-6 text-slate-900 uppercase tracking-widest">{{ htcms_trans('hashtagcms::links.resources') }}</h5>
                <ul class="space-y-3">
                    <li><a class="text-slate-500 hover:text-slate-900 transition-colors text-sm"
                            href="#">{{ htcms_trans('hashtagcms::links.documentation') }}</a></li>
                    <li><a class="text-slate-500 hover:text-slate-900 transition-colors text-sm" href="#">{{ htcms_trans('hashtagcms::links.components') }}</a>
                    </li>
                    <li><a class="text-slate-500 hover:text-slate-900 transition-colors text-sm" href="#">{{ htcms_trans('hashtagcms::links.theme_setup') }}</a></li>
                </ul>
            </div>

            <div>
                <h5 class="text-xs font-bold mb-6 text-slate-900 uppercase tracking-widest">{{ htcms_trans('hashtagcms::links.connect') }}</h5>
                <div class="flex space-x-3">
                    <a href="#"
                        class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300">
                        <i class="fa-brands fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300">
                        <i class="fa-brands fa-twitter text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300">
                        <i class="fa-brands fa-github text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-slate-400 text-xs">
                &copy; {{date("Y")}} {{ htcms_trans('hashtagcms::links.all_rights_reserved') }}.
            </p>
            <div class="flex items-center space-x-6 text-xs text-slate-400 font-medium">
                <a href="{{ htcms_get_path('support/privacy-policy') }}"
                    class="hover:text-slate-900 transition-colors">{{ htcms_trans('hashtagcms::links.privacy_policy') }}</a>
                <span class="text-slate-200">•</span>
                <a href="{{ htcms_get_path('support/tnc') }}" class="hover:text-slate-900 transition-colors">{{ htcms_trans('hashtagcms::links.terms_of_service') }}</a>
            </div>            
        </div>
    </div>
</footer>
</div>
</footer>