<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use HashtagCms\Core\Traits\RoleManager;
use HashtagCms\Http\Resources\UserResource;
use HashtagCms\User;

class AuthController extends ApiBaseController
{
    use HasApiTokens, RoleManager;

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|object
     */
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];

        $data = $request->all();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response($validator->getMessageBag())
                ->setStatusCode(422);

        }
        $data['user_type'] = 'Visitor';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
        ]);

        $token = $this->createAccessToken($user);

        return response(['user' => $user, 'token' => $token])
            ->setStatusCode(200);

    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|object
     */
    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];

        $data = $request->all();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response($validator->getMessageBag())
                ->setStatusCode(422);

        }
        $loginData['email'] = $data['email'];
        $loginData['password'] = $data['password'];

        if (! auth()->attempt($loginData)) {
            return response(['message' => 'Email or password is incorrect.'])
                ->setStatusCode(422);
        }

        $user = auth()->user();
        $token = $this->createAccessToken($user);

        return response(['user' => new UserResource($user), 'token' => $token])
            ->setStatusCode(200);
    }

    /**
     * Get current access token
     *
     * @return mixed
     */
    private function createAccessToken($user)
    {

        $tokenName = $this->getTokenName($user);

        $tokens = $user->createToken($tokenName);

        return ['access_token' => $tokens->plainTextToken,
            'scope' => $tokens->accessToken->abilities,
            'expires_at' => date(config('hashtagcmsapi.login_session_expiry_format'), strtotime($tokens->accessToken->created_at.' '.config('hashtagcmsapi.login_session')))];
    }

  /**
     * Get token name
     *
     * @return string
     */
    private function getTokenName($user)
    {
        return date('Y-m-d H:i:s').'_login_'.$user->name.'_'.$user->id;
    }

    /**
     * Get user info
     *
     * @return mixed
     */
    public function me(Request $request)
    {

        $user = $request->user();

        return new UserResource($user);
    }
}
