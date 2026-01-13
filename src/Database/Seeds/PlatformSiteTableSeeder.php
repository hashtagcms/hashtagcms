<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'platform_site';

        $platform_site = [
            ['platform_id' => '1', 'site_id' => '1'],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($platform_site);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
