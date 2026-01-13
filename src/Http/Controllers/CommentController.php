<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Models\Comment;
use HashtagCms\Models\Page;

class CommentController extends FrontendBaseController
{
    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return redirect()->intended('/');

    }

    /**
     * Save comments
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function saveComment(Request $request)
    {

        $rules = ['name' => 'required|max:255|string',
            'email' => 'required|max:255|email',
            'comment' => 'required|string'];
        //dd($rules);
        $validator = Validator::make($request->all(), $rules);
        //dd($validator);
        //If error
        if ($validator->fails()) {

            if ($request->ajax()) {
                $msg = ['success' => false, 'errors' => $validator->getMessageBag()->toArray()];

                return response()->json($msg, 400);
            } else {

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        $data = $request->all();
        unset($data['_token']);

        $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

        $user = auth()->user();
        if ($user != null) {
            $data['user_id'] = $user->id;
        }

        $message = 'Comment has been posted.';
        $success = true;
        try {
            Comment::create($data);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $success = false;
        }

        $count = Comment::count();

        $rData = ['success' => $success, 'message' => $message, 'data' => $data, 'count' => $count];

        if ($request->ajax()) {
            return response()->json($rData, 200);
        }

        return redirect()->back()->with('results', $rData);
    }

    /**
     * Fetch comments
     *
     * @param  int  $category_id
     * @param  int  $page_id
     */
    public function getComments(Request $request): array
    {

        $data = $request->all();

        $category_id = $data['category_id'] ?? 0;
        $page_id = $data['page_id'] ?? 0;
        $order = $data['order'] ?? 'desc';

        $comment = new Comment;

        if ($category_id > 0) {
            $comment = $comment->where('category_id', '=', $category_id);
        }

        if ($page_id > 0) {
            $comment = $comment->where('page_id', '=', $page_id);
        }

        $data = [];
        if ($page_id > 0 || $page_id > 0) {
            $comment = $comment->orderBy('id', $order);
            $data = $comment->get()->toArray();
        }

        return $data;

    }
}
