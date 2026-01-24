<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'logs';
        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert(
                [

                ]

            );
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
