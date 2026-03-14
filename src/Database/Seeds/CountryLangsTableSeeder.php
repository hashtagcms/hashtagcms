<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryLangsTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'country_langs';
        $selectedLangs = $this->getSelectedLanguages();

        foreach ($selectedLangs as $isoCode) {
            $countries = $this->loadTranslations('countries', $isoCode);
            
            // Find the lang_id for this isoCode
            $lang = DB::table('langs')->where('iso_code', $isoCode)->first();
            
            if ($lang && !empty($countries)) {
                $dataToInsert = [];
                foreach ($countries as $country) {
                    $country['lang_id'] = $lang->id;
                    $dataToInsert[] = $country;
                }
                $this->insertOrUpdate($table_name, $dataToInsert, ['country_id', 'lang_id']);
            }
        }
    }
}
