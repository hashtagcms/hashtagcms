<div class="py-24 bg-modern-bg min-h-[90vh] flex items-center">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="max-w-md mx-auto">
            <div class="modern-card">
                <div class="bg-modern-card-header p-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-envelope-open text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight">{{htcms_trans("hashtagcms::auth.Verify Your Email Address")}}</h1>
                    <p class="text-white/80 mt-2 font-medium">{{ htcms_trans('hashtagcms::auth.one_last_step') }}</p>
                </div>

                <div class="p-8 lg:p-10 bg-white">
                    @if (session('resent'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center space-x-2 animate-fade-in-up">
                            <i class="fa fa-check-circle"></i>
                            <span>{{htcms_trans("hashtagcms::auth.A fresh verification link has been sent to your email address.")}}</span>
                        </div>
                    @endif

                    <div class="text-slate-600 space-y-4">
                        <p>{{htcms_trans("hashtagcms::auth.before_proceeding")}}</p>
                        <p>{{htcms_trans("hashtagcms::auth.if_not_received")}}: </p>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('verification.resend') }}" class="modern-button w-full">
                            {{htcms_trans("hashtagcms::auth.request_another")}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
