<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\Lang;
use HashtagCms\Models\Site;
use PHPUnit\Exception;

class LanguageController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'iso_code', 'language_code', 'created_at', 'updated_at'];

    protected $dataSource = Lang::class;

    protected $dataWith = '';

    protected $minResults = 1;

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $moreActionBarItems = [['label' => 'Translator',
        'as' => 'icon', 'icon_css' => 'fa fa-language',
        'action' => 'language/translator']];

    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));*/

    /**
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {

        if (! $this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = ['name' => 'required|max:40|string',
            'iso_code' => 'required|max:2',
            'language_code' => 'required|max:5|string',
            'date_format_lite' => 'nullable|max:32|string',
            'date_format_full' => 'nullable|max:32|string',
            'is_rtl' => 'nullable|integer'];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData['name'] = $data['name'];
        $saveData['iso_code'] = $data['iso_code'];
        $saveData['language_code'] = $data['language_code'];
        $saveData['date_format_lite'] = $data['date_format_lite'];
        $saveData['date_format_full'] = $data['date_format_full'];
        $saveData['is_rtl'] = $data['is_rtl'] ?? 0;

        //date
        $saveData['updated_at'] = htcms_get_current_date();
        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);

            //Add default language in all tables based on new id
            if ($savedData['id']) {
                Lang::insertLangInAllTables($savedData['id']);
            }
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);

            //update language count in site table;
            try {
                Site::updateLangCount(htcms_get_siteId_for_admin());
            } catch (Exception $exception) {
                //
            }

        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Translate UI
     *
     * @param  int  $id
     * @return mixed
     */
    public function translator($id = 0)
    {
        $viewDatap['sites'] = Site::all();
        $viewData['languages'] = Lang::all();
        $viewData['backURL'] = $this->getBackURL();
        $viewData['langTables'] = Lang::getAllLangTables();
        $viewData['actionPerformed'] = 'translation';
        $viewData['id'] = $id;

        return $this->viewNow('language.translator', $viewData);
    }

    /**
     * Translate now
     *
     * @param  int|null  $fromLang
     * @param  int|null  $fromSite
     * @param  int|null  $toLang
     * @param  int|null  $toSite
     * @param  array  $tables
     * @return array
     */
    public function translateNow(Request $request)
    {

        $rules = [
            'sourceLang' => 'required|numeric',
            'targetLang' => 'required|numeric',
            'tables' => 'required',
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

        $data = request()->all();

        /*$fromLang = ($fromLang == NULL) ?? $input["fromLang"];
        $fromSite = ($fromSite == NULL) ?? $input["fromSite"];
        $toLang = ($toLang == NULL) ?? $input["toLang"];
        $toSite = ($toSite == NULL) ?? $input["toSite"];
        $tables = (sizeof($tables) == 0) ?? $input["tables"];*/

        $fromLang = $data['sourceLang'];
        $toLang = $data['targetLang'];
        $tables = $data['tables'];

        if (is_string($tables)) {
            $tables = explode(',', $tables);
        }

        /*  if($fromLang == NULL || $fromSite==NULL || $toLang==NULL || $toSite == NULL || sizeof($tables) == 0) {
              return array("error"=>true, "message"=>"Parameter is missing or mismatch");
          }*/

        $mgsArr = [];
        foreach ($tables as $table) {
            $rows = DB::select("select * from $table where lang_id=:lang_id", ['lang_id' => $fromLang]);
            $targetRows = DB::select("select * from $table where lang_id=:lang_id", ['lang_id' => $toLang]);
            if (count($targetRows) == 0) {
                //manipulate data
                foreach ($rows as $row) {
                    $row->lang_id = $toLang;
                    $row->created_at = htcms_get_current_date();
                    $row->updated_at = htcms_get_current_date();
                }

                $status = $this->rawInsert($table, $rows);
                $msg = ($status == 0) ? 'There is some error while copying content.' : "$table has been copied.";
            } else {
                $status = 0;
                $msg = "Language Id $toLang is already there in $table. Unable to copy";
            }
            $mgsArr[] = ['status' => $status, 'table' => $table, 'message' => $msg];
        }

        $data = ['isSaved' => 1, 'message' => $mgsArr];

        return $data;

    }
}
