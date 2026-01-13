<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'currency_site';

        $currency_site = [
            ['currency_id' => '4', 'site_id' => '1', 'conversion_rate' => '0.021730', 'markup' => '0', 'markup_type' => 'Fixed'],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($currency_site);

        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
