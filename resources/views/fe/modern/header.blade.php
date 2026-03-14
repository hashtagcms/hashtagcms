<!-- header-start -->
<header class="fixed top-0 left-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="flex items-center justify-between h-20">
            <!-- Brand -->
            <div class="flex-shrink-0">
                <a class="text-2xl font-bold text-slate-900 flex items-center gap-2.5 group tracking-tight" href="{{htcms_get_path('/')}}">
                    <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center text-white transition-transform duration-500 group-hover:scale-105">
                        <span class="italic text-lg font-bold"><img src="{{htcms_get_image_resource('logo.png')}}" alt="Logo" class="w-6 h-6"></span>
                    </div>
                    <span>{{htcms_get_site_info('name')}}</span>                                        
                </a>
            </div>

            <!-- Main Nav (Desktop) -->
            <nav class="hidden lg:flex items-center space-x-8 text-slate-600 text-sm font-medium">            
                @foreach ($data as $item)
                    <a href="{{htcms_get_path($item['link_rewrite'])}}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center space-x-5">                
                <!-- User Actions -->
                 @if (htcms_get_site_info('langCount') > 1)
                    <!-- language switcher -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-slate-600 hover:text-slate-900 transition-all duration-300 px-3 py-1.5 rounded-lg hover:bg-slate-50">
                            <i class="fa fa-globe text-slate-400"></i>
                            <span class="text-sm font-medium">{{ htcms_get_lang_info('name') }}</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl shadow-slate-200/50 py-2 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right group-hover:translate-y-0 translate-y-2 z-50">
                            <div class="px-3 py-1 mb-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ htcms_trans('hashtagcms::links.select_language', 'Select Language') }}</span>
                            </div>
                            @foreach (htcms_get_config_data('langs') as $lang)
                                @php
                                    $isActive = htcms_get_lang_info('id') == $lang['id'];
                                @endphp
                                <a href="{{ htcms_get_language_url($lang['isoCode']) }}" 
                                   class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $isActive ? 'text-slate-900 bg-slate-50 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span>{{ $lang['name'] }}</span>
                                    @if($isActive)
                                        <i class="fa fa-check text-[10px] text-green-500"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                 @endif
                @auth
                    <div class="flex items-center space-x-2 border-l border-slate-100 pl-5">
                        @if(strtolower(auth()->user()->user_type) == 'staff')
                            <a href="{{htcms_admin_path('dashboard')}}" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-full transition-all" title="{{__('hashtagcms::links.dashboard')}}">
                                <i aria-hidden="true" class="fa fa-dashboard text-base"></i>
                            </a>
                        @endif
                        <a href="{{htcms_get_path('profile')}}" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-full transition-all" title="{{__('hashtagcms::links.profile')}}">
                            <i aria-hidden="true" class="fa fa-user text-base"></i>
                        </a>
                        <a href="{{htcms_get_path('logout')}}" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all" title="{{__('hashtagcms::links.logout')}}">
                            <i aria-hidden="true" class="fa fa-sign-out text-base"></i>
                        </a>
                    </div>
                @endauth

                @guest
                    <div class="hidden sm:flex items-center space-x-4">
                        <a href="{{htcms_get_path('login')}}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            {{__('hashtagcms::links.login')}}
                        </a>
                        <a href="{{htcms_get_path('register')}}" class="modern-button">
                            {{__('hashtagcms::links.register')}}
                        </a>
                    </div>
                @endguest

                <!-- Mobile Menu Button -->
                <button class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 transition-all" type="button" aria-label="Toggle navigation">
                    <i class="fa fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>
</header>

