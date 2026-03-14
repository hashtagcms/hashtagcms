<div class="py-24 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="max-w-5xl mx-auto">
            @if (session('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center justify-between" id="success_msg">
                    <div class="flex items-center space-x-3">
                        <i class="fa fa-check-circle"></i>
                        <span class="font-medium">{{session('success')}}</span>
                    </div>
                    <button title="{{ htcms_trans('hashtagcms::messages.close') }}" class="hover:bg-emerald-100 p-1.5 rounded-lg transition-colors" onclick="document.getElementById('success_msg').style.display = 'none'">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            @endif

            @php
                $name = $user['name'] ?? '';
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Profile Sidebar Info -->
                <div class="lg:col-span-4 space-y-10">
                    <div>
                        <p class="uppercase tracking-[0.1em] text-blue-600 text-[11px] font-bold mb-4">{{ htcms_trans('hashtagcms::messages.user_account') }}</p>
                        <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6 tracking-tight">{{ htcms_trans('hashtagcms::messages.user_profile') }}</h1>
                        <p class="text-lg text-slate-500 leading-relaxed font-normal mb-8">
                            {{ htcms_trans('hashtagcms::messages.profile_manage_desc') }}
                        </p>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-start space-x-5">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center text-blue-600 border border-slate-100 flex-shrink-0">
                                <i class="fa fa-user text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ htcms_trans('hashtagcms::messages.account_status') }}</h4>
                                <p class="text-slate-500 text-sm mt-1">Verified User</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-5">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center text-blue-600 border border-slate-100 flex-shrink-0">
                                <i class="fa fa-envelope text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ htcms_trans('hashtagcms::messages.email_readonly') }}</h4>
                                <p class="text-slate-500 text-sm mt-1 truncate max-w-[200px]">{{$user['email'] ?? ''}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="lg:col-span-8">
                    @if($user != null)
                        <div class="modern-card bg-white border-slate-100 shadow-xl shadow-slate-200/50 p-8 lg:p-12">
                            <form method="POST" action="/profile/store" class="space-y-10">
                                @csrf
                                
                                <!-- Personal Info Section -->
                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="h-px flex-grow bg-slate-100"></div>
                                        <h2 class="text-xs font-bold text-slate-400 tracking-widest uppercase">{{ htcms_trans('hashtagcms::messages.personal_details') }}</h2>
                                        <div class="h-px flex-grow bg-slate-100"></div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                        <div class="space-y-2">
                                            <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.full_name') }}</label>
                                            <input type="text" required class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" id="name" name="name" placeholder="John Doe" value="{{$name}}">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label for="father_name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.father_name') }}</label>
                                            <input type="text" required class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" id="father_name" name="father_name" placeholder="{{ htcms_trans('hashtagcms::messages.father_name') }}" value="{{$profile['father_name'] ?? ''}}">
                                        </div>
                                        <div class="space-y-2">
                                            <label for="mother_name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.mother_name') }}</label>
                                            <input type="text" required class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" id="mother_name" name="mother_name" placeholder="{{ htcms_trans('hashtagcms::messages.mother_name') }}" value="{{$profile['mother_name'] ?? ''}}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact & Additional Info -->
                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="h-px flex-grow bg-slate-100"></div>
                                        <h2 class="text-xs font-bold text-slate-400 tracking-widest uppercase">{{ htcms_trans('hashtagcms::messages.additional_details') }}</h2>
                                        <div class="h-px flex-grow bg-slate-100"></div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label for="mobile" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.mobile_number') }}</label>
                                            <input type="text" required class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" id="mobile" name="mobile" placeholder="+1 234 567 890" value="{{$profile['mobile'] ?? ''}}">
                                        </div>
                                        <div class="space-y-2">
                                            <label for="date_of_birth" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.date_of_birth') }}</label>
                                            <input type="date" required class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" id="date_of_birth" name="date_of_birth" value="{{$profile['date_of_birth'] ?? ''}}">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="gender" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.gender') }}</label>
                                        <div class="relative">
                                            {!! FormHelper::select('gender', $genders, array('class'=>'w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all appearance-none bg-white text-sm', 'required'=>'required'), $profile['gender'] ?? '', 'plain_array') !!}
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                <i class="fa fa-chevron-down text-[10px]"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="modern-button w-full sm:w-auto">
                                        {{ htcms_trans('hashtagcms::messages.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="modern-card bg-white border-slate-100 shadow-sm p-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100 text-slate-300">
                                <i class="fa fa-lock text-2xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                                @php
                                    $loginText = htcms_trans('hashtagcms::messages.login_to_view_profile', 'You need to login to view your profile.');
                                    $loginText = str_replace(':login', '<a href="/login?redirect=/profile" class="text-blue-600 font-bold hover:underline">'.htcms_trans('hashtagcms::links.login').'</a>', $loginText);
                                @endphp
                                {!! $loginText !!}
                            </h2>
                            <p class="text-slate-500 mt-2 text-sm">{{ htcms_trans('hashtagcms::messages.access_restricted') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

