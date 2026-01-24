@php
    $error_code = (int) request()->get("error_code");
@endphp

<div class="container" style="margin-top: 100px;margin-bottom: 100px">

    @if($error_code > 0)
        <div class="row justify-content-center">
            @php
                $error_message = request()->get("error_message");
            @endphp
            <div class="alert-danger alert">
                {!! $error_message !!}
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{htcms_trans('hashtagcms::auth.Login')}}</div>
                    <div class="card-body">
                        <form method="POST" action="/login">
                            @csrf

                            @if(isset($redirect))
                                <input id="redirect" type="hidden" name="redirect" value="{{ $redirect }}">
                            @endif
                            @if(old('redirect'))
                                <input id="redirect" type="hidden" name="redirect" value="{{ old('redirect') }}">
                            @endif

                            <div class="form-group row mt-2">
                                <label for="email" class="col-sm-4 col-form-label text-md-right">{{ htcms_trans('hashtagcms::auth.Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ htcms_trans('hashtagcms::auth.Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ htcms_trans('hashtagcms::auth.Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ htcms_trans('hashtagcms::auth.Login') }}
                                    </button>

                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ htcms_trans('hashtagcms::auth.Forgot Your Password?') }}
                                    </a>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
