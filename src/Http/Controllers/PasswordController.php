<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordController extends FrontendBaseController
{
    /*
   |--------------------------------------------------------------------------
   | Password Reset Controller
   |--------------------------------------------------------------------------
   |
   | This controller is responsible for handling password reset requests
   | and uses a simple trait to include this behavior. You're free to
   | explore this trait and override any methods you wish to tweak.
   |
   */
    use RedirectsUsers;
    use SendsPasswordResetEmails;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Hint: password.request | HashtagCms\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm
     *
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        return parent::index($request);

    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */

    //Hint: password.email   | HashtagCms\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail
    public function email(Request $request)
    {
        if ($request->method() == 'GET') {
            return $this->reset($request);
        }
        try {
            //info("Sending email");
            return $this->sendResetLinkEmail($request);

        } catch (\Exception $exception) {
            return redirect('/password/reset')
                ->with('errorMessage', trans($exception->getMessage()));
        }

    }

    /**
     * @param  null  $token
     * @return array
     *
     * * If no token is present, display the link request form.
     */

    //Hint: password.reset   | HashtagCms\Http\Controllers\Auth\ResetPasswordController@showResetForm
    public function reset(Request $request, $token = null)
    {

        if ($token != null) {
            $this->bindDataForView('auth/passwords/email', ['token' => $token, 'email' => $request->email]);
        }

        return parent::index($request);

    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */

    //Hint: password.update  | HashtagCms\Http\Controllers\Auth\ResetPasswordController@reset
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        // return $response. " :: ".Password::PASSWORD_RESET;

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /*** From ResetPassword Trait   ***/

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('__hashtagcms_message__', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
