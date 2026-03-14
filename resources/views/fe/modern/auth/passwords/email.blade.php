@if (session('errorMessage'))
    <div class="fixed top-4 right-4 z-50 max-w-md">
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center justify-between shadow-lg" id="errorFlashMessage">
            <div class="flex items-center space-x-2">
                <i class="fa fa-exclamation-circle"></i>
                <span>{{session('errorMessage')}}</span>
            </div>
            <button title="Close" class="hover:bg-red-100 p-1 rounded" onclick="document.getElementById('errorFlashMessage').style.display = 'none'">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(isset($token))
<div class="py-24 bg-modern-bg min-h-[90vh] flex items-center">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="max-w-md mx-auto">
            <div class="modern-card">
                <div class="bg-modern-card-header p-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-key text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ htcms_trans('hashtagcms::auth.Reset Password') }}</h1>
                    <p class="text-white/80 mt-2 font-medium">{{ htcms_trans('hashtagcms::auth.create_new_password') }}</p>
                </div>

                <div class="p-8 lg:p-10 bg-white">
                    <form method="POST" action="/password/update" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.E-Mail Address') }}</label>
                            <input id="email" type="email" class="modern-input{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="{{ htcms_trans('hashtagcms::auth.your_email') }}">
                            @if ($errors->has('email'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('email') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Password') }}</label>
                            <input id="password" type="password" class="modern-input{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password" required placeholder="{{ htcms_trans('hashtagcms::auth.enter_new_password') }}">
                            @if ($errors->has('password'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('password') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="password-confirm" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="modern-input" name="password_confirmation" required placeholder="{{ htcms_trans('hashtagcms::auth.confirm_new_password') }}">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="modern-button w-full">
                                {{ htcms_trans('hashtagcms::auth.Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="px-8 pb-8 bg-white text-center border-t border-slate-100">
                    <p class="text-sm text-slate-500 mt-6">
                        {{ htcms_trans('hashtagcms::auth.remember_password') }} 
                        <a href="{{ route('login') }}" class="text-modern-blue hover:text-modern-navy font-bold transition-colors">{{ htcms_trans('hashtagcms::auth.login_here') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@else

    <div class="py-24 bg-modern-bg min-h-[90vh] flex items-center">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-md mx-auto">
                <div class="modern-card">
                    <div class="bg-modern-card-header p-8 text-white text-center">
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa fa-envelope text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ htcms_trans('hashtagcms::auth.Reset Password') }}</h1>
                        <p class="text-white/80 mt-2 font-medium">{{ htcms_trans('hashtagcms::auth.enter_email_reset') }}</p>
                    </div>

                    <div class="p-8 lg:p-10 bg-white">
                        @if (session('status'))
                            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center space-x-2 animate-fade-in-up">
                                <i class="fa fa-check-circle"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="/password/email" class="space-y-6">
                            @csrf

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

                            <div class="pt-2">
                                <button type="submit" class="modern-button w-full">
                                    {{ htcms_trans('hashtagcms::auth.Send Password Reset Link') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="px-8 pb-8 bg-white text-center border-t border-slate-100">
                        <p class="text-sm text-slate-500 mt-6">
                            {{ htcms_trans('hashtagcms::auth.remember_password') }} 
                            <a href="{{ route('login') }}" class="text-modern-blue hover:text-modern-navy font-bold transition-colors">{{ htcms_trans('hashtagcms::auth.login_here') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif
