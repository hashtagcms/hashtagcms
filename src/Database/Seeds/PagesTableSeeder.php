<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'pages';
        $table_name_langs = 'page_langs';
        $date = date('Y-m-d H:i:s');

        $pages = array(
            array('id' => '1','site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '9','alias' => 'TEST','exclude_in_listing' => '0','content_type' => 'page','position' => '3','link_rewrite' => 'tnc','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('id' => '2','site_id' => '1','microsite_id' => '0','platform_id' => NULL,'category_id' => '9','alias' => 'PRIVACY_POLICY','exclude_in_listing' => '0','content_type' => 'page','position' => '4','link_rewrite' => 'privacy-policy','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-05 09:12:13','deleted_at' => NULL),
            array('id' => '3','site_id' => '1','microsite_id' => '0','platform_id' => NULL,'category_id' => '2','alias' => 'TEST_BLOG','exclude_in_listing' => '0','content_type' => 'blog','position' => '4','link_rewrite' => 'test-blog','link_navigation' => NULL,'menu_placement' => NULL,'header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => '2020-07-11 05:50:06','enable_comments' => '1','updated_at' => NULL,'deleted_at' => NULL)
        );

        $this->insertOrSkip($table_name, $pages, ['id']);

        $selectedLangs = $this->getSelectedLanguages();
        foreach ($selectedLangs as $isoCode) {
            $pageLangs = $this->loadTranslations('pages', $isoCode);
            
            // Find the lang_id for this isoCode
            $lang = DB::table('langs')->where('iso_code', $isoCode)->first();
            
            if ($lang && !empty($pageLangs)) {
                $dataToInsert = [];
                foreach ($pageLangs as $pageLang) {
                    $pageLang['lang_id'] = $lang->id;
                    $dataToInsert[] = $pageLang;
                }
                $this->insertOrUpdate($table_name_langs, $dataToInsert, ['page_id', 'lang_id']);
            }
        }
    }
}
