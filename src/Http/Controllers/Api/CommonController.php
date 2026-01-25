<?php

namespace HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use HashtagCms\Models\Site;
use HashtagCms\Http\Traits\CommonLogic;
use Symfony\Component\HttpFoundation\Response;

class CommonController extends ApiBaseController
{
    use CommonLogic;

    /**
     * Submit Contact
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contact(Request $request)
    {
        $data = $request->all();
        $validator = $this->validateData($data, $this->getContactRules());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->getMessageBag()->toArray()], Response::HTTP_BAD_REQUEST);
        }

        //Resolve Site ID if not provided
        if (!isset($data['site_id'])) {
            $context = $request->input('site') ?? $request->header('x-site');
            if ($context) {
                $site = Site::where('context', $context)->first();
                $data['site_id'] = $site->id ?? null;
            }
        }

        if (empty($data['site_id'])) {
            return response()->json(['success' => false, 'message' => 'Site ID or Context is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->saveContact($data);

        return response()->json(['success' => true, 'message' => 'Information have been saved successfully.'], Response::HTTP_OK);
    }

    /**
     * Subscribe
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $data = $request->all();
        $validator = $this->validateData($data, $this->getSubscriberRules());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->getMessageBag()->toArray()], Response::HTTP_BAD_REQUEST);
        }

        //Resolve Site ID if not provided
        if (!isset($data['site_id'])) {
            $context = $request->input('site') ?? $request->header('x-site');
            if ($context) {
                $site = Site::where('context', $context)->first();
                $data['site_id'] = $site->id ?? null;
            }
        }

        if (empty($data['site_id'])) {
            return response()->json(['success' => false, 'message' => 'Site ID or Context is required'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->saveSubscriber($data);

        return response()->json($result, Response::HTTP_OK);
    }
}
