<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
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

            return redirect()->intended('/login?redirect=/profile?'.http_build_query($reqParams, '', '&'));
        }

        $user = Auth::user();
        $user_id = $user->id;
        $user = User::with(['profile'])->where('id', '=', $user_id)->first()->toArray();
        $user['middle_name'] = (empty($user['middle_name'])) ? '' : ' '.$user['middle_name'];

        if ($user['profile'] == null) {
            $profile = ['father_name' => '', 'mother_name' => '', 'mobile' => '', 'date_of_birth' => '', 'gender' => ''];
        } else {
            $profile = $user['profile'];
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
}
