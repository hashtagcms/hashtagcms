<?php

namespace {{namespace}}Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use {{namespace}}Models\{{dataSource}};
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Http\Controllers\Admin\BaseAdminController;

class {{controller_name}} extends BaseAdminController
{
    protected $dataFields = {{dataFields}};

    protected $dataSource = {{dataSource}}::class;

    protected $dataWith = {{dataWith}};

    protected $actionFields = array("edit", "delete"); //This is last column of the row


    // protected $minResults = 1; //this will disable delete when record count is one
    /*
    //This will be added after add/edit etc action fields
    protected $moreActionFields = array(
            array("label"=>"Show all info",
                "css"=>"js_ajax",
                "icon_css"=>"fa fa-info-circle",
                "hrefAttributes"=>["data-info"=>"cmslog", "data-editable"=>false, "data-excludefields"=>["user", "module"]],
                "action"=>"showinfo",
                "action_append_field"=>"id"
                ),
                 array("label"=>"Site Settings",
                            "icon_css"=>"fa fa-cogs",
                            "action"=>"settings",
                            "action_append_field"=>"id")
        );
    */

    /*
    //This is action bar items. (Add/Search bar)
    protected $moreActionBarItems = array(
            array("label"=>"Clone Site",
                "as"=>"icon",
                "icon_css"=>"fa fa-copy", "action"=> "site/copysite"
            )
        );
    */

    //Get data for editing.
    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));
                                        */



    public function store(Request $request) {

            //$module_name = $request->module_info->controller_name;

             if(!$this->checkPolicy('edit')) {
                  return htcms_admin_view("common.error", Message::getWriteError());
             }
             $rules = [{{validationFields}}];

             $validator = Validator::make($request->all(), $rules);

             if ($validator->fails()) {

                 return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
             }

            $data = $request->all();

            $saveData["name"] = $data["name"];

            $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

            //This is how you create lang data
            //$langData["name"] = $data["lang_name"];
            //$arrLangData = array("data"=>$langData);

            if($data["actionPerformed"]=="edit") {

                $where = $data["id"];
                //This is in base controller
                $savedData = $this->saveData($arrSaveData, $where);
                //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

            } else {
                //This is in base controller
                $savedData = $this->saveData($arrSaveData);
                //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
            }

            $viewData["id"] = $savedData["id"];;
            $viewData["saveData"] = $data;
            $viewData["backURL"] = $data["backURL"];
            $viewData["isSaved"] = $savedData["isSaved"];

            return htcms_admin_view("common.saveinfo", $viewData);
        }
}
