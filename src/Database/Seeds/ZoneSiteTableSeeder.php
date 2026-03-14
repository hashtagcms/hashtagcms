<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'site_zone';
        $date = date('Y-m-d H:i:s');
        $zone_site = [
            ['zone_id' => '3', 'site_id' => '1', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null]            
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($zone_site);
        } 
    }
}
