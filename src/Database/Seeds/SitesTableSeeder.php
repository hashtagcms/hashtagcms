<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        
        // Find default lang and country
        $firstLang = DB::table('langs')->first();
        $firstCountry = DB::table('countries')->where('iso_code', 'IN')->first() ?? DB::table('countries')->first();
        
        $langId = $firstLang ? $firstLang->id : '1';
        $countryId = $firstCountry ? $firstCountry->id : '110';

        $sites = [
            ['id' => '1', 'name' => 'Hashtag CMS', 'category_id' => '1', 'theme_id' => '1', 'platform_id' => '1', 'lang_id' => $langId, 'country_id' => $countryId, 'under_maintenance' => '0', 'domain' => 'www.hashtagcms.com', 'context' => 'rexhashtagcms', 'favicon' => '', 'lang_count' => '1', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        $this->insertOrSkip('sites', $sites, ['id']);

        $selectedLangs = $this->getSelectedLanguages();
        foreach ($selectedLangs as $isoCode) {
            $siteLangs = $this->loadTranslations('sites', $isoCode);
            
            // Find the lang_id for this isoCode
            $lang = DB::table('langs')->where('iso_code', $isoCode)->first();
            
            if ($lang && !empty($siteLangs)) {
                $dataToInsert = [];
                foreach ($siteLangs as $siteLang) {
                    $siteLang['lang_id'] = $lang->id;
                    $dataToInsert[] = $siteLang;
                }
                $this->insertOrUpdate('site_langs', $dataToInsert, ['site_id', 'lang_id']);
            }
        }
    }
}
