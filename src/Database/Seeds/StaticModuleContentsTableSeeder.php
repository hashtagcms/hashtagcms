<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticModuleContentsTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'static_module_contents';
        $table_name_langs = 'static_module_content_langs';
        $date = date('Y-m-d H:i:s');

        $static_module_contents = [
            ['id' => '3', 'site_id' => '1', 'alias' => 'CONTENT_STATIC', 'update_by' => '1', 'insert_by' => '1', 'created_at' => null, 'updated_at' => $date, 'deleted_at' => null],
        ];

        $this->insertOrSkip($table_name, $static_module_contents, ['id']);

        $selectedLangs = $this->getSelectedLanguages();
        foreach ($selectedLangs as $isoCode) {
            $staticModuleContentLangs = $this->loadTranslations('static_module_contents', $isoCode);
            
            // Find the lang_id for this isoCode
            $lang = DB::table('langs')->where('iso_code', $isoCode)->first();
            
            if ($lang && !empty($staticModuleContentLangs)) {
                $dataToInsert = [];
                foreach ($staticModuleContentLangs as $staticModuleContentLang) {
                    $staticModuleContentLang['lang_id'] = $lang->id;
                    $dataToInsert[] = $staticModuleContentLang;
                }
                $this->insertOrUpdate($table_name_langs, $dataToInsert, ['static_module_content_id', 'lang_id']);
            }
        }
    }
}
