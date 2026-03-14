<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangsTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'langs';
        $selectedLangs = $this->getSelectedLanguages();

        foreach ($selectedLangs as $isoCode) {
            $langs = $this->loadTranslations('langs', $isoCode);
            foreach ($langs as $lang) {
                $this->insertOrSkip($table_name, [$lang], ['iso_code']);
            }
        }
    }
}
