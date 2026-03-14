<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'country_site';       
        $country_site = [            
            ['country_id' => '110', 'site_id' => '1']         
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($country_site);
        }
    }
}
