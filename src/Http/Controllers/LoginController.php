<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
//use Laravel\Socialite;
use Laravel\Socialite\Facades\Socialite;

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

    use RedirectsUsers;
    use ThrottlesLogins;

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
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
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
}
