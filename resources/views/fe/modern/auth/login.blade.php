@php
    $error_code = (int) request()->get("error_code");
@endphp

<div class="py-24 bg-modern-bg min-h-[90vh] flex items-center">
    <div class="container mx-auto px-4 lg:px-8">
        @if($error_code > 0)
            <div class="max-w-md mx-auto mb-8">
                @php
                    $error_message = request()->get("error_message");
                @endphp
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center space-x-3">
                    <i class="fa fa-exclamation-circle text-xl"></i>
                    <span>{!! $error_message !!}</span>
                </div>
            </div>
        @endif
        
        <div class="max-w-md mx-auto">
            <div class="modern-card">
                <div class="bg-modern-card-header p-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-sign-in text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight">{{htcms_trans('hashtagcms::auth.Login')}}</h1>
                    <p class="text-white/80 mt-2 font-medium">{{htcms_trans('hashtagcms::auth.welcome_back')}}</p>
                </div>
                
                <div class="p-8 lg:p-10 bg-white">
                    <form method="POST" action="/login" class="space-y-6">
                        @csrf

                        @if(isset($redirect))
                            <input id="redirect" type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif
                        @if(old('redirect'))
                            <input id="redirect" type="hidden" name="redirect" value="{{ old('redirect') }}">
                        @endif

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Email Address') }}</label>
                            <input id="email" type="email" class="modern-input{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ htcms_trans('hashtagcms::auth.your_email') }}">
                            @if ($errors->has('email'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('email') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-bold text-modern-navy/70">{{ htcms_trans('hashtagcms::auth.Password') }}</label>
                            <input id="password" type="password" class="modern-input{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password" required placeholder="{{ htcms_trans('hashtagcms::auth.enter_password') }}">
                            @if ($errors->has('password'))
                                <p class="text-red-600 text-sm mt-1 flex items-center space-x-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span>{{ $errors->first('password') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-modern-blue rounded focus:ring-modern-blue/20" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="ml-2 text-sm text-modern-navy/70">
                                {{ htcms_trans('hashtagcms::auth.Remember Me') }}
                            </label>
                        </div>

                        <div class="space-y-4 pt-2">
                            <button type="submit" class="modern-button w-full">
                                {{ htcms_trans('hashtagcms::auth.Login') }}
                            </button>
                            
                            <div class="text-center">
                                <a class="text-modern-blue hover:text-modern-navy font-semibold text-sm transition-colors" href="{{ route('password.request') }}">
                                    {{ htcms_trans('hashtagcms::auth.Forgot Your Password?') }}
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
                
                <div class="px-8 pb-8 bg-white text-center border-t border-slate-100">
                    <p class="text-sm text-slate-500 mt-6">
                        {{ htcms_trans('hashtagcms::auth.dont_have_account') }} 
                        <a href="{{ route('register') }}" class="text-modern-blue hover:text-modern-navy font-bold transition-colors">{{ htcms_trans('hashtagcms::auth.register_here') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
