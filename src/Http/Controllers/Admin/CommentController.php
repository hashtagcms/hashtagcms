<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Comment;

class CommentController extends BaseAdminController
{
    protected $dataFields = ['id', 'category.link_rewrite', 'content.link_rewrite', 'comment', 'created_at'];

    protected $dataSource = Comment::class;

    protected $dataWith = ['category', 'content'];

    protected $actionFields = ['delete']; //This is last column of the row

    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));*/

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        //$module_name = $request->module_info->controller_name;

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        $rules = ['parent_id' => 'nullable|numeric',
            'category_id' => 'required|numeric',
            'page_id' => 'nullable|numeric',
            'user_id' => 'nullable|numeric',
            'comment' => 'required'];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        //This is how you create lang data
        //$langData["name"] = $data["lang_name"];
        //$arrLangData = array("data"=>$langData);

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
