<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
//use Laravel\Socialite;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Illuminate\Http\JsonResponse;

class LoginController extends FrontendBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller - Modified Version of Original Auth\Login
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    private $route = '/login';

    // use RedirectsUsers;
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectPath = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //$this->redirectTo = (URL::previous() != URL::current()) ? URL::previous() : $this->redirectTo;
        //info("URL::previous() ======  ".URL::previous());

        $this->middleware('guest')->except('logout', 'socialcallback');

    }

    /**
     * @return array|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request, array $infoKeeper = [], array $mergeData = [])
    {

        if (Auth::id() > 0) {
            return redirect()->intended(URL::previous());
        } else {

            if ($request->method() == 'POST') {

                $this->redirectPath = ($request->input('redirect') == null || $request->input('redirect') == '') ? '/' : $request->input('redirect');

                $validator = Validator::make($request->all(), [
                    $this->username() => 'required|string',
                    'password' => 'required|string',
                ]);

                if ($validator->fails()) {

                    return redirect($this->route)
                        ->withErrors($validator)
                        ->withInput();
                }

                try {
                    return $this->login($request);

                } catch (\Exception $exception) {
                    return $exception->getMessage();
                }

            }

            //bind data for view
            if ($request->input('redirect')) {
                $this->bindDataForView('auth/login', ['redirect' => $request->input('redirect')]);
            }

            return parent::index($request);
        }

    }

    /**
     * @override
     *
     * @return string
     */
    protected function redirectTo()
    {

        return $this->redirectPath;
    }

    /*** From AuthencatesUsers Trait **/

    /**
     * Handle a login request to the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);
            try {

                return $this->sendLockoutResponse($request);

            } catch (\Exception $exception) {

                $seconds = $this->limiter()->availableIn(
                    $this->throttleKey($request)
                );

                return redirect($this->route)
                    ->withErrors([
                        $this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds])],
                    ])
                    ->status(429);

            }

        }

        if ($this->attemptLogin($request)) {

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        if (config('hashtagcms.enable_external_api')) {
            return $this->loginViaExternalApi($request);
        }
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($request->wantsJson() || $request->ajax()) {
            return new JsonResponse(['message' => 'Login successful', 'redirect' => $this->redirectPath()], 200);
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath()); //URL::previous()
    }

    /**
     * The user has been authenticated.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        info('We can use some of hacks here');

        /*if ($user->user_type == "Visitor") {
            return redirect("/");
        }*/
    }

    /**
     * Get the failed login response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return new JsonResponse(['message' => trans('auth.failed'), 'errors' => [$this->username() => [trans('auth.failed')]]], 422);
        }

        return redirect($this->route)
            ->withErrors([
                $this->username() => [trans('auth.failed')],
            ])
            ->withInput();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    private function username()
    {
        return 'email';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    //Social handling
    public function social($provider = 'facebook')
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function socialcallback(Request $request, $provider = 'facebook')
    {
        $error_code = $request->get('error_code');

        if ((int) $error_code > 0) {
            return parent::index($request);
            //return $this->viewMaster($theme, "index", $data);
        }

        $user = Socialite::driver($provider)->user();

        dd($user);
        //@todo: Implement Social login

        // $user->token;
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        if (config('hashtagcms.enable_external_api')) {
            $this->logoutViaExternalApi($request);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse(['message' => 'Logged out'], 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input($this->username())) . '|' . $request->ip());
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), 5);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit($this->throttleKey($request), 60);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            $this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])],
        ])->status(429);
    }

    protected function limiter()
    {
        return app(CacheRateLimiter::class);
    }

    /**
     * Login via external API
     * @param Request $request
     * @return bool
     */
    private function loginViaExternalApi(Request $request)
    {
        try {
            $loginUrl = config('hashtagcms.login_api');
            $apiSecret = config('hashtagcms.api_secret');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api_key' => $apiSecret
            ])->post($loginUrl, [
                        'email' => $request->input('email'),
                        'password' => $request->input('password')
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token']['access_token'] ?? null;
                $userData = $data['user'] ?? null;

                if ($token && $userData) {

                    //Store token
                    session(['hashtagcms_api_token' => $token]);
                    session(['hashtagcms_api_user' => $userData]);

                    // Login locally using the custom provider
                    // We need to retrieve the user instance that our custom provider creates from the session
                    // Since we just put it in the session, retrieveById should find it.

                    // However, Auth::loginUsingId() expects the user to be in the DB for the default provider.
                    // But if we are using our custom provider, it should work if configured.
                    // Alternatively, we can manually log them in if we get the user instance.

                    // Let's try to get the user instance via the provider logic (or manually hydrate)
                    // If we haven't switched the 'provider' in auth.php, Auth::loginUsingId might fail.
                    // But we can hydrate a User model and pass it to Auth::login().

                    $user = new User();
                    $user->forceFill($userData);
                    $user->id = $userData['id'] ?? 0; //Ensure ID is set
                    $user->exists = true; // Pretend existence

                    $this->guard()->login($user, $request->filled('remember'));
                    return true;
                }
            } else {
                $msg = $response->json()['message'] ?? 'Login failed';
                //We could flash this message but attemptLogin returns bool.
                //Log it?
                info("External Login Failed: " . $msg);
            }

        } catch (\Exception $e) {
            info("External Login Error: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Logout via external API
     * @param Request $request
     * @return void
     */
    private function logoutViaExternalApi(Request $request)
    {
        try {
            $token = session('hashtagcms_api_token');
            if ($token) {
                $logoutUrl = config('hashtagcms.logout_api');
                $apiSecret = config('hashtagcms.api_secret');

                Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'api_key' => $apiSecret
                ])->post($logoutUrl);
            }
        } catch (\Exception $e) {
            //Ignore logout errors
            info("External Logout Error: " . $e->getMessage());
        }
    }
}
