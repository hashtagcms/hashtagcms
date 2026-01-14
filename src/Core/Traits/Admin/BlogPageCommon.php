<?php

namespace HashtagCms\Core\Traits\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

trait BlogPageCommon
{
    protected function getRulesArray(Request $request): array
    {

        $rules = [
            'category_id' => 'nullable|numeric',
            'parent_id' => 'nullable|numeric',
            'site_id' => 'required|numeric',
            'platform_id' => 'nullable|numeric',
            'alias' => 'nullable|max:60|string',
            'microsite_id' => 'nullable|numeric',
            'exclude_in_listing' => 'nullable|integer',
            'content_type' => 'required',
            'position' => 'nullable|integer',
            /*"link_rewrite" => "required|max:255|string|unique:pages,link_rewrite",*/
            'link_rewrite' => [
                'required',
                'max:255',
                'string',
                Rule::unique('pages')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('platform_id', $request->input('platform_id'))
                        ->where('link_rewrite', $request->input('link_rewrite'));
                }),
            ],
            'link_navigation' => 'nullable|max:255|string',
            'menu_placement' => 'nullable|max:100|string',
            'insert_by' => 'required|numeric',
            'update_by' => 'required|numeric',
            'publish_status' => 'nullable|integer',
            'lang_name' => 'required|max:128|string',
            'lang_title' => 'required|max:128|string',
            'lang_page_content' => 'required',
            'lang_active_key' => 'nullable|max:128|string',
            'lang_meta_title' => 'nullable|max:160|string',
            'lang_meta_keywords' => 'nullable|max:255|string',
            'lang_meta_description' => 'nullable|max:255|string',
            'lang_meta_robots' => 'nullable|max:255|string',
            'lang_meta_canonical' => 'nullable|max:255|string',
            'img' => 'nullable',
            'attachment' => 'nullable',
            'author' => 'nullable|max:255|string',
            'content_source' => 'nullable|max:255|string',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date',
        ];
        if ($request->input('id') > 0) {
            $rules['link_rewrite'] = [
                'required',
                'max:255',
                'string',
                Rule::unique('pages')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input('site_id'))
                        ->where('platform_id', $request->input('platform_id'))
                        ->where('link_rewrite', $request->input('link_rewrite'))
                        ->where('id', '<>', $request->input('id'));
                }),
            ];
        }

        return $rules;
    }

    /**
     * Save Blog Page Data
     *
     * @return mixed
     */
    protected function saveBlogPageData($data, $module_name)
    {

        $saveData['alias'] = $data['alias'];
        $saveData['category_id'] = $data['category_id'];
        $saveData['parent_id'] = $data['parent_id'] ?? null;
        $saveData['content_type'] = $data['content_type'];

        $saveData['site_id'] = $data['site_id'] ?? htcms_get_siteId_for_admin();
        $saveData['platform_id'] = $data['platform_id'];

        $saveData['link_navigation'] = $data['link_navigation'];
        $saveData['link_rewrite'] = $data['link_rewrite'];

        $saveData['menu_placement'] = $data['menu_placement'];
        $saveData['exclude_in_listing'] = $data['exclude_in_listing'] ?? 0;

        $saveData['header_content'] = $data['header_content'];
        $saveData['footer_content'] = $data['footer_content'];

        $saveData['position'] = $this->dataSource::count() + 1;

        $saveData['enable_comments'] = $data['enable_comments'] ?? 0;
        $saveData['publish_status'] = $data['publish_status'] ?? 0;
        $saveData['required_login'] = $data['required_login'] ?? 0;
        $saveData['author'] = $data['author'];
        $saveData['content_source'] = $data['content_source'];
        $saveData['publish_at'] = $data['publish_at'] ?? null;
        $saveData['expire_at'] = $data['expire_at'] ?? null;

        $langData['name'] = $data['lang_name'];
        $langData['title'] = $data['lang_title'];
        $langData['active_key'] = $data['lang_active_key'];
        $langData['target'] = $data['lang_target'];
        $langData['link_relation'] = $data['lang_link_relation'];
        $langData['page_content'] = $data['lang_page_content'];
        $langData['description'] = $data['lang_description'];
        $langData['meta_title'] = $data['lang_meta_title'];
        $langData['meta_keywords'] = $data['lang_meta_keywords'];
        $langData['meta_description'] = $data['lang_meta_description'];
        $langData['meta_robots'] = $data['lang_meta_robots'];
        $langData['meta_canonical'] = $data['lang_meta_canonical'];

        if ($data['img_deleted'] != '0') {
            $saveData['img'] = '';
        }

        if ($data['attachment_deleted'] != '0') {
            $saveData['attachment'] = '';
        }

        if (! empty($data['image_path'])) {
            $saveData['img'] = $data['image_path'];
        }

        //use this if uploaded
        $img = $this->upload($module_name, request()->file('img'));

        if ($img != null) {
            $saveData['img'] = $img;
        }

        //attachment
        $attachment = $this->upload($module_name, request()->file('attachment'));
        if ($attachment != null) {
            $saveData['attachment'] = $attachment;
        }

        //date
        $saveData['updated_at'] = htcms_get_current_date();
        $langData['updated_at'] = htcms_get_current_date();

        if ($data['actionPerformed'] !== 'edit') {
            $saveData['insert_by'] = $data['insert_by'];
            $saveData['created_at'] = htcms_get_current_date();
            $langData['created_at'] = htcms_get_current_date();
        }

        $saveData['update_by'] = Auth::user()->id;

        $arrSaveData = ['model' => $this->dataSource,  'data' => $saveData];
        $arrLangData = ['data' => $langData];
        //
        if ($data['actionPerformed'] == 'edit') {
            $where = $data['id'];

            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {

            //This is in base controlle
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        // print_r($arrLangData);
        // exit;
        //
        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return $viewData;

    }
}
