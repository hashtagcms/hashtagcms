<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Models\User;
use HashtagCms\Models\UserProfile;

class ProfileController extends FrontendBaseController
{
    public function __construct(Request $request)
    {

        parent::__construct($request);
    }

    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        if (Auth::id() == null) {
            $reqParams = $request->all();

            return redirect()->intended('/login?redirect=/profile?' . http_build_query($reqParams, '', '&'));
        }


        if (config('hashtagcms.enable_external_api')) {
            $user = $this->getUserFromExternalApi();

            $profile = $user['profile'] ?? ['fatherName' => '', 'motherName' => '', 'mobile' => '', 'dateOfBirth' => '', 'gender' => ''];
            //Normalize keys from camelCase (API) to snake_case (View) if needed, OR adjust view.
            //API UserResource returns: fatherName, motherName, dateOfBirth
            //View expects: father_name, mother_name, date_of_birth ?? Let's check view logic. 
            //The view likely uses snake_case keys if it was built for eloquent array.
            $profile = [
                'father_name' => $profile['fatherName'] ?? '',
                'mother_name' => $profile['motherName'] ?? '',
                'mobile' => $profile['mobile'] ?? '',
                'date_of_birth' => $profile['dateOfBirth'] ?? '',
                'gender' => $profile['gender'] ?? ''
            ];
            //User array normalization if needed
            if (!is_array($user)) {
                $user = (array) $user;
            }

        } else {
            $user = Auth::user();
            $user_id = $user->id;
            $user = User::with(['profile'])->where('id', '=', $user_id)->first()->toArray();
            $user['middle_name'] = (empty($user['middle_name'])) ? '' : ' ' . $user['middle_name'];

            if ($user['profile'] == null) {
                $profile = ['father_name' => '', 'mother_name' => '', 'mobile' => '', 'date_of_birth' => '', 'gender' => ''];
            } else {
                $profile = $user['profile'];
            }
        }

        $genders = UserProfile::genders();

        $data = ['user' => $user, 'profile' => $profile, 'genders' => $genders];

        $this->bindDataForView('profile', $data);

        return parent::index($request);
    }

    /**
     * Save personal info
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $user = Auth::user();

        if (Auth::id() == null) {

            return redirect()->intended('/login?redirect=/profile');
        }

        $rules = [
            'name' => 'required|string|max:130',
            'father_name' => 'nullable|max:255|string',
            'mother_name' => 'nullable|max:255|string',
            'mobile' => 'required|max:50|string',
            'date_of_birth' => 'nullable',
            'gender' => 'nullable|max:20|string',
        ];

        if (config('hashtagcms.enable_external_api')) {
            return $this->updateProfileViaExternalApi($request, $rules);
        }

        $user = User::with(['profile'])->where('id', '=', $user->id)->first();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        //create profile
        $profile['father_name'] = $data['father_name'];
        $profile['mother_name'] = $data['mother_name'];
        $profile['mobile'] = $data['mobile'];
        $profile['date_of_birth'] = date('Y-m-d', strtotime($data['date_of_birth']));
        $profile['gender'] = $data['gender'];

        if ($user->profile == null || $user->profile()->count() == 0) {
            $user->profile()->create($profile);
        } else {
            $user->profile()->update($profile);
        }
        $user->name = $data['name'];
        $user->save();

        return redirect('/profile')
            ->with('success', 'Your profile has been saved.');
    }


    /**
     * Get User from External API
     */
    /**
     * Get User from External API
     */
    private function getUserFromExternalApi()
    {
        $token = session('hashtagcms_api_token');
        $apiUrl = config('hashtagcms.user_me_api');

        try {
            $response = Http::withToken($token)->withHeaders(['Accept' => 'application/json'])->get($apiUrl);

            if ($response->successful()) {
                //If the API returns wrapped in 'data'
                $json = $response->json();
                return $json['data'] ?? $json;
                //UserResource usually returns direct array. But if wrapped, handle it.
                //Actually UserResource is used in AuthController::me -> return new UserResource($user).
                //JsonResource responses are often wrapped in 'data' by default in Laravel unless disabled.
                //Let's check UserResource usage... wait, "UserResource extends JsonResource".
                //So it IS wrapped in data.
            }
        } catch (\Exception $e) {
            logger()->error("Profile Load Error: " . $e->getMessage());
        }
        return [];
    }

    /**
     * Update Profile via External API
     */
    private function updateProfileViaExternalApi(Request $request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $token = session('hashtagcms_api_token');
        $apiUrl = config('hashtagcms.user_profile_update_api');

        try {
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($apiUrl, $request->all());

            if ($response->successful()) {
                return redirect('/profile')
                    ->with('success', 'Your profile has been saved.');
            } else {
                return redirect()->back()->withErrors(['message' => $response->json()['message'] ?? 'Update failed']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
