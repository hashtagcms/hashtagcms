<?php

namespace HashtagCms\Core\Traits\Admin;

use Illuminate\Support\Facades\Auth;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\User;

trait Downloader
{
    public function download($checkPolicy = true)
    {

        $user = User::find(Auth::user()->id);
        $isAdmin = $user->isSuperAdmin();
        $allowed = true;

        if (! $isAdmin) {

            $allowed = false;

            //get module info by controller
            $cmsModule = CmsModule::where('controller_name', '=', request()->module_info->controller_name)->first('id');

            //if found
            if ($cmsModule != null) {

                $allowed = $user->cmsmodules()->where('module_id', '=', $cmsModule->id)->count();

            } else {

                return ['message' => 'Unknown module.'];
            }
        }

        if ($checkPolicy == true || $checkPolicy == '') {

            if (! $this->checkPolicy('read') || ! $allowed) {

                return response(['error' => true, 'message' => "Sorry! You don't have permission"]);

            }
        }

        if (! isset($this->dataSource)) {
            return response(['error' => true, 'message' => "I don't know. What to download."]);
        }

        //if everything is okay

        $csvExporter = new \Laracsv\Export();

        $dataWith = (isset($this->dataWith) && $this->dataWith != '') ? $this->dataWith : '';

        $source = $this->dataSource;

        $obj = ($dataWith != '') ? $source::with($dataWith) : new $source;
        $data = $obj->get();

        //dd($data->toArray());
        if ($data->count() > 0) {

            if (is_string($dataWith) && $dataWith !== '') {
                $dataWith = [$dataWith]; //convert it to array
            }

            //Make fields
            $firstRec = $data->first()->toArray();

            $fields = [];

            foreach ($firstRec as $key => $val) {
                if ($key != 'deleted_at' && $key != 'created_at' && $key != 'updated_at') {
                    if ($dataWith == '') {
                        array_push($fields, $key);
                    } elseif (is_array($dataWith) && ! in_array($key, $dataWith)) {
                        array_push($fields, $key);
                    }

                }
            }

            //if with
            if ($dataWith != '') {
                //Children keys
                foreach ($dataWith as $dKey => $dVal) {
                    $childKey = $dVal;
                    $currentRec = $firstRec[$dVal];
                    foreach ($currentRec as $key => $val) {
                        if ($key != 'deleted_at' && $key != 'created_at' && $key != 'updated_at' && $key != 'id') {
                            array_push($fields, $childKey.'.'.$key);
                        }
                    }
                }
            }

            if (isset($firstRec['created_at'])) {
                array_push($fields, 'created_at');
            }
            if (isset($firstRec['updated_at'])) {
                array_push($fields, 'updated_at');
            }

        }
        $csvExporter->build($data, $fields)->download();

    }
}
