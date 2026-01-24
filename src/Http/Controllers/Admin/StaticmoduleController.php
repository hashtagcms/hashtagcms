<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Site;
use HashtagCms\Models\StaticModuleContent;

class StaticmoduleController extends BaseAdminController
{
    protected $dataFields = ['id', 'lang.title as title', 'alias', 'updated_at'];

    protected $dataSource = StaticModuleContent::class;

    protected $dataWith = ['lang'];

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $bindDataWithAddEdit = ['sites' => ['dataSource' => Site::class, 'method' => 'all']];

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        /*
         *  "alias" => [
                'required',
                'max:60',
                'string',
                Rule::unique('modules')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input("site_id"))
                        ->where('alias', $request->input("alias"));
                })
            ],
         */

        $rules = [
            'site_id' => 'required|numeric',
            'alias' => ['required',
                'max:60',
                'string',
                Rule::unique('static_module_contents')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('alias', $request->input('alias'));
                })],
            /*"alias" => "required|max:60|string",*/
            'update_by' => 'required|numeric',
            'lang_title' => 'required|max:255|string',
            'lang_content' => 'required',
        ];

        if ($request->input('id') > 0) {
            $rules['alias'] = ['required',
                'max:60',
                'string',
                Rule::unique('static_module_contents')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('alias', $request->input('alias'))
                        ->where('id', '<>', $request->input('id'));
                })];
        }

        if ($request->input('id') == 0) {

            $rules['insert_by'] = 'required|numeric';

        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['alias'] = strtoupper($data['alias']);
        $saveData['site_id'] = $data['site_id'];

        $saveData['update_by'] = $data['update_by'];
        $saveData['updated_at'] = htcms_get_current_date();

        if (! isset($data['id']) || $data['id'] == 0) {
            $saveData['insert_by'] = $data['insert_by'];
        }

        $langData['lang_id'] = $data['lang_id'] ?? 1;

        $langData['title'] = $data['lang_title'];
        $langData['content'] = $data['lang_content'];

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        $arrLangData = ['data' => $langData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }
}
