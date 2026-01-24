<?php

namespace HashtagCms\Http\Controllers\Installer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HashtagCms\Http\Controllers\Controller;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;
use HashtagCms\User;

class InstallController extends Controller
{
    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->getInfo();

        if ($data['isInstalled'] === true) {
            return redirect('/')->with('__hashtagcms_message__', 'Site is already configured');
            exit;
        }
        $data['siteInfo']->context = Str::uuid();

        return view('hashtagcms::installer/index', $data);

    }

    private function getInfo()
    {
        $site = Site::with('lang')->find(1);
        $data['siteInfo'] = $site;
        $data['isInstalled'] = $this->isInstalled();

        return $data;
    }

    /**
     * Found a user
     *
     * @return bool
     */
    private function isInstalled()
    {
        $siteInstalled = SiteProp::where('name', '=', 'site_installed')->first();

        return ($siteInstalled != null && (string) $siteInstalled->value == '0') ? false : true;
    }

    /**
     * Save data
     *
     * @return bool|int
     */
    public function save(Request $request)
    {

        //return 1;

        $rules = [
            'site_name' => 'required|max:100|string',
            'site_title' => 'required|max:255|string',
            'site_context' => 'required|max:40|string',
            'site_domain' => 'required|max:255|string',
            'name' => 'required|max:255|string',
            'user_email' => 'required|max:255|email',
            'user_password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            if ($request->ajax()) {
                $msg['errors'] = $validator->getMessageBag()->toArray();

                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        $data = $request->all();

        if (! $this->isInstalled()) {

            $data = $request->all();

            $site_name = $data['site_name'];
            $site_title = $data['site_title'];
            $site_context = $data['site_context'];
            $site_domain = $data['site_domain'];

            $name = $data['name'];
            $email = $data['user_email'];
            $password = $data['user_password'];

            $user = User::find(1);
            $user->email = $email;
            $user->name = $name;
            $user->password = Hash::make($password);
            $user->save();

            //Site Info
            $site = Site::with('lang')->find(1);

            $site->name = $site_name;
            $site->context = $site_context;
            $site->domain = $site_domain;
            $site->save();

            $site->lang()->update(['title' => $site_title]);

            $info = $this->getInfo();

            //update Install prop
            $siteInstalled = SiteProp::where('name', '=', 'site_installed')->first();
            $siteInstalled->value = 1;
            $siteInstalled->save();

            $info['isInstalled'] = 1;

        } else {
            $info['error'] = 'Unable to make the changes. Please login to change the info.';
            $info['title'] = 'Error!';
        }

        return $info;

    }
}
