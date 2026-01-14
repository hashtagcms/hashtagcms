<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use HashtagCms\Core\Traits\RoleManager;
use HashtagCms\Http\Resources\UserResource;
use HashtagCms\User;

use HashtagCms\Models\UserProfile;

use HashtagCms\Http\Traits\AuthLogic;

class AuthController extends ApiBaseController
{
    use HasApiTokens, RoleManager, AuthLogic;

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|object
     */
    public function register(Request $request)
    {
        $rules = $this->getApiRegisterRules();

        $data = $request->all();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response($validator->getMessageBag())
                ->setStatusCode(422);

        }
        $data['user_type'] = 'Visitor';

        $user = $this->createUser($data);

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

        if (!auth()->attempt($loginData)) {
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

        return [
            'access_token' => $tokens->plainTextToken,
            'scope' => $tokens->accessToken->abilities,
            'expires_at' => date(config('hashtagcmsapi.login_session_expiry_format'), strtotime($tokens->accessToken->created_at . ' ' . config('hashtagcmsapi.login_session')))
        ];
    }

    /**
     * Get token name
     *
     * @return string
     */
    private function getTokenName($user)
    {
        return date('Y-m-d H:i:s') . '_login_' . $user->name . '_' . $user->id;
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

    /**
     * Logout
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        return response(['message' => 'Logged out successfully'])->setStatusCode(200);
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:130',
            'father_name' => 'nullable|max:255|string',
            'mother_name' => 'nullable|max:255|string',
            'mobile' => 'required|max:50|string',
            'date_of_birth' => 'nullable',
            'gender' => 'nullable|max:20|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response($validator->getMessageBag())->setStatusCode(422);
        }

        $data = $request->all();

        //Update user
        $user->name = $data['name'];
        $user->save();

        //Update Profile
        $profileData = [
            'father_name' => $data['father_name'],
            'mother_name' => $data['mother_name'],
            'mobile' => $data['mobile'],
            'date_of_birth' => (!empty($data['date_of_birth'])) ? date('Y-m-d', strtotime($data['date_of_birth'])) : null,
            'gender' => $data['gender']
        ];

        if ($user->profile == null) {
            $user->profile()->create($profileData);
        } else {
            $user->profile()->update($profileData);
        }

        return response(['message' => 'Profile updated successfully', 'user' => new UserResource($user)])->setStatusCode(200);
    }
}
