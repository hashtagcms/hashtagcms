<?php

namespace HashtagCms\Core\Traits\Admin;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadManager
{
    /**
     * @param  array  $files  // request()->file("key") or request()->allFiles()
     * @param  null  $path
     * @param  bool  $asJson
     * @return array|mixed|null
     */
    public function upload(string $module = '', $files = [], $path = null, $asJson = false)
    {

        $imageSaved = null;
        $allFiles = $files;

        $hasFiles = ((isset($allFiles) && is_array($allFiles) && count($allFiles) > 0) || ! empty($allFiles)) ? true : false;

        //Save all - when passed data as request()->allFiles()
        if (is_array($files) && $hasFiles) {

            $imageSaved = [];

            //save multiple
            foreach ($allFiles as $key => $images) {

                //Save if multiple
                if (is_array($images)) {

                    foreach ($images as $imgKey => $imgFile) {
                        $imageSaved[$key][] = $this->saveNow($module, $imgFile, $path, $asJson);
                    }

                } else {
                    //Save single image
                    $imageSaved[$key] = $this->saveNow($module, request()->file($key), $path, $asJson);
                }
            }

        } elseif ($hasFiles) {

            //save one - when pass data as file object request()->file('html_input_name')
            $imageSaved = $this->saveNow($module, $files, $path, $asJson);
        }

        return $imageSaved;

    }

    /**
     * @param  null  $path
     * @param  bool  $asJson
     * @return mixed
     */
    private function saveNow(string $module = '', ?UploadedFile $file = null, $path = null, $asJson = false)
    {
        $storedName = '';
        if ($module != '' && $file != null && $module != null) {
            $upload_path = $this->getFolder($module);
            //dd($upload_path);
            if ($asJson) {
                $storedName = Str::uuid().'.json';
                Storage::putFileAs($upload_path, new File($file), $storedName);
                $storedName = $upload_path.'/'.$storedName;
            } else {
                $storedName = Storage::putFile($upload_path, $file);
            }

        } elseif ($path != null && $file != null) {
            $storedName = Storage::putFile($path, $file);
        }

        //remove basepath and save only folder and filename
        return str_replace(config('hashtagcmsadmin.media.upload_path').'/', '', $storedName);
    }

    /**
     * Get folder for upload
     *
     * @return string
     */
    private function getFolder(string $path, bool $withMonthYear = true, bool $makeIfNotExist = false)
    {
        $month = '';
        $year = '';
        $upload_path = config('hashtagcmsadmin.media.upload_path');
        if ($withMonthYear == true) {
            $time = mktime(0, 0, 0);
            $year = '/'.date('Y', $time).'/';
            $month = strtolower(date('F', $time));
        }
        $path = strtolower($path);
        $folderPath = $upload_path.'/'.$path.$year.$month;

        return $folderPath;
    }
}
