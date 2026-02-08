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
        return $this->processAction($request, 'contact', $this->getContactRules(), function ($data) {
            $this->saveContact($data);
            return ['success' => true, 'message' => 'Information have been saved successfully.'];
        });
    }

    /**
     * Save newsletter (subscriber)
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function newsletter(Request $request)
    {
        // For external API backwards compatibility
        $type = 'newsletter';
        if (config('hashtagcms.enable_external_api')) {
            if (!config('hashtagcms.newsletter_api') && config('hashtagcms.configure_api')) {
                $type = 'configure';
            }
        }

        return $this->processAction($request, $type, $this->getSubscriberRules(), function ($data) {
            $res = $this->saveSubscriber($data);
            if ($res['success']) {
                 $res['message'] = htcms_trans("hashtagcms::messages.subscriber_add_success", "Thank you! You have been subscribed to our newsletter.");
            }
            return $res;
        });
    }

    /**
     * Legacy method for newsletter
     * @deprecated Use newsletter()
     */
    public function configure(Request $request)
    {
        return $this->newsletter($request);
    }

    /**
     * Legacy method for subscribe
     * @deprecated Use newsletter()
     */
    public function subscribe(Request $request)
    {
        return $this->newsletter($request);
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

        // Fetch specific API URL from config based on type (contact or newsletter)
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
     * Unified processor for common actions
     * @param Request $request
     * @param string $type
     * @param array $rules
     * @param callable $saveCallback
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function processAction(Request $request, string $type, array $rules, callable $saveCallback)
    {
        try {
            // Handle External API
            if (config('hashtagcms.enable_external_api')) {
                $feResponse = $this->postToExternalApi($type);
                return $this->actionResponse($request, $feResponse);
            }

            $validator = $this->validateData($request->all(), $rules);

            if ($validator->fails()) {
                $msg = ['success' => false, 'message' => $validator->getMessageBag(), 'status' => 422];
                return $this->actionResponse($request, $msg);
            }

            $data = $request->all();
            $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

            $res = $saveCallback($data);
            $res['status'] = $res['success'] === true ? 200 : 400;

            return $this->actionResponse($request, $res);

        } catch (\Exception $e) {
            $msg = ['success' => false, 'message' => $e->getMessage(), 'status' => 500];
            return $this->actionResponse($request, $msg);
        }
    }

    /**
     * Handle consistent response based on request type
     * @param Request $request
     * @param array $result
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function actionResponse(Request $request, array $result)
    {
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json($result, $result['status'] ?? 200);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        // Handle errors
        $errors = is_array($result['message']) ? $result['message'] : ['error' => $result['message']];
        if ($result['message'] instanceof \Illuminate\Support\MessageBag) {
            return redirect()->back()->withErrors($result['message'])->withInput();
        }
        
        return redirect()->back()->withErrors($errors)->withInput();
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
