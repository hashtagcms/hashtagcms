<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $sites = [
            ['id' => '1', 'name' => 'Hashtag CMS', 'category_id' => '1', 'theme_id' => '1', 'platform_id' => '1', 'lang_id' => '1', 'country_id' => '110', 'under_maintenance' => '0', 'domain' => 'www.hashtagcms.com', 'context' => 'rexhashtagcms', 'favicon' => '', 'lang_count' => '1', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        $site_langs = [
            ['site_id' => '1', 'lang_id' => '1', 'title' => 'Welcome to #CMS', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['site_id' => '1', 'lang_id' => '2', 'title' => '#CMS में आपका स्वागत है', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        if (DB::table('sites')->get()->count() == 0) {
            DB::table('sites')->insert($sites);
            DB::table('site_langs')->insert($site_langs);
        } else {
            echo "SeedingError: `sites` table is not empty\n";
        }
    }
}
