<div class="py-24 bg-modern-bg min-h-[90vh] flex items-center">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="max-w-md mx-auto">
            <div class="modern-card">
                <div class="bg-modern-card-header p-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-user-plus text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ htcms_trans('hashtagcms::auth.Register') }}</h1>
                    <p class="text-white/80 mt-2 font-medium">{{ htcms_trans('hashtagcms::auth.one_last_step') }}</p>
                </div>

                <div class="p-8 lg:p-10 bg-white">
                    <form method="POST" action="/register" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Name') }}</label>
                            <input id="name" type="text" class="modern-input{{ $errors->has('name') ? ' border-red-500' : '' }}" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ htcms_trans('hashtagcms::auth.your_full_name') }}">
                            @if ($errors->has('name'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('name') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.E-Mail Address') }}</label>
                            <input id="email" type="email" class="modern-input{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ old('email') }}" required placeholder="{{ htcms_trans('hashtagcms::auth.your_email') }}">
                            @if ($errors->has('email'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('email') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Password') }}</label>
                            <input id="password" type="password" class="modern-input{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password" required placeholder="{{ htcms_trans('hashtagcms::auth.choose_password') }}">
                            @if ($errors->has('password'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('password') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="password-confirm" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="modern-input" name="password_confirmation" required placeholder="{{ htcms_trans('hashtagcms::auth.confirm_password') }}">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="modern-button w-full">
                                {{ htcms_trans('hashtagcms::auth.Register') }}
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="px-8 pb-8 bg-white text-center border-t border-slate-100">
                    <p class="text-sm text-slate-500 mt-6">
                        {{ htcms_trans('hashtagcms::auth.already_have_account') }} 
                        <a href="{{ route('login') }}" class="text-modern-blue hover:text-modern-navy font-bold transition-colors">{{ htcms_trans('hashtagcms::auth.login_here') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
