<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use HashtagCms\Http\Traits\CommonLogic;
use HashtagCms\Models\Contact;
use HashtagCms\Models\Subscriber;

class CommonController extends FrontendBaseController
{
    use CommonLogic;

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
        //return app()->HashtagCmsInfoLoader->getInfoKeeper();
        abort(404);
    }

    /**
     * Save contacts
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function contact(Request $request)
    {
        try {
            // Handle External API
            if (config('hashtagcms.enable_external_api')) {
                $feResponse = $this->postToExternalApi('contact');
                if ($request->ajax()) {
                    return response()->json($feResponse, $feResponse['status']);
                }

                if ($feResponse['success']) {
                    return redirect()->back()->with('success', $feResponse['message']);
                } else {
                    return redirect()->back()->withErrors(['error' => $feResponse['message']])->withInput();
                }
            }

            $validator = $this->validateData($request->all(), $this->getContactRules());

            if ($validator->fails()) {
                if ($request->ajax()) {
                    $msg = ['success' => false, 'message' => $validator->getMessageBag()->toArray()];
                    return response()->json($msg, 400);
                } else {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            }

            $data = $request->all();
            $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

            $this->saveContact($data);

            if ($request->ajax()) {
                $msg = ['success' => true, 'message' => 'Information have been saved successfully.'];
                return response()->json($msg, 200);
            }

            return redirect()->back()->with('success', 'Information have been saved successfully.');
        } catch (\Exception $e) {
            $msg = ['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
            if ($request->ajax()) {
                return response()->json($msg, 500);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Save subscriber
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request)
    {
        try {
            // Handle External API
            if (config('hashtagcms.enable_external_api')) {
                $feResponse = $this->postToExternalApi('subscribe');
                if ($request->ajax()) {
                    return response()->json($feResponse, $feResponse['status']);
                }
                // Redirect back
                if ($feResponse['success']) {
                    return redirect()->back()->with('success', $feResponse['message']);
                } else {
                    return redirect()->back()->withErrors(['error' => $feResponse['message']])->withInput();
                }
            }

            $validator = $this->validateData($request->all(), $this->getSubscriberRules());

            if ($validator->fails()) {
                if ($request->ajax()) {
                    $msg = ['success' => false, 'message' => $validator->getMessageBag()->toArray()];
                    return response()->json($msg, 400);
                } else {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            }

            $data = $request->all();
            $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

            $result = $this->saveSubscriber($data);
            $message = $result['message'];

            if ($request->ajax()) {
                $msg = ['success' => true, 'message' => $message];
                return response()->json($msg, 200);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            $msg = ['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
            if ($request->ajax()) {
                return response()->json($msg, 500);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Calculate endpoint and post data to external API
     * @param string $type
     * @return array
     */
    private function postToExternalApi($type)
    {
        $context = config('hashtagcms.context');
        $apiSecret = config('hashtagcms.api_secrets.' . $context);

        // Fetch specific API URL from config based on type (contact or subscribe)
        $apiUrl = config("hashtagcms.{$type}_api");

        // Fallback if specific config is missing but base URL is available
        if (empty($apiUrl) && $baseUrl = config('hashtagcms.external_api_base_url')) {
            $apiUrl = $baseUrl . '/api/hashtagcms/public/common/v1/' . $type;
        }

        if (empty($apiUrl) || empty($apiSecret)) {
            $missing = [];
            if (empty($apiUrl))
                $missing[] = "API URL ($type)";
            if (empty($apiSecret))
                $missing[] = "API Secret ($context)";

            $msg = "CommonController: Missing configuration: " . implode(', ', $missing);
            logger()->error($msg);

            return ['success' => false, 'message' => 'Configuration Error: ' . implode(', ', $missing), 'status' => 500];
        }

        try {
            //logger()->info("Posting to External API: $apiUrl using Site: $context");

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api_key' => $apiSecret,
            ])->post($apiUrl, array_merge(request()->all(), ['site' => $context]));

            if ($response->successful()) {
                $json = $response->json();
                return ['success' => true, 'message' => $json['message'] ?? 'Success', 'status' => $response->status(), 'data' => $json];
            } else {
                logger()->error("External API Failed ($apiUrl): " . $response->body());
                return ['success' => false, 'message' => 'Remote Error: ' . $response->status(), 'status' => $response->status()];
            }
        } catch (\Exception $e) {
            logger()->error("CommonController External API Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Connection Error: ' . $e->getMessage(), 'status' => 500];
        }
    }

    /**
     * just for testing
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function test()
    {
        $isError = request()->get('error');
        $message = request()->get('message');
        //return redirect("/")->with('__hashtagcms_message__', array('message'=>'This is coming from common/test', 'type'=>'success'));
        if ($isError) {
            return redirect('/')->with('__hashtagcms_message_error__', $message ?? "This is error message coming from common/test");
        }

        return redirect('/')->with('__hashtagcms_message__', $message ?? "This is success message coming from common/test");
    }
}
