<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'langs';
        $date = date('Y-m-d H:i:s');
        $langs = array(
            array('id' => '1','name' => 'English','iso_code' => 'en','language_code' => 'en','date_format_lite' => 'Y-m-d','date_format_full' => 'y-m-d H:i:s','is_rtl' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Hindi','iso_code' => 'hi','language_code' => 'hi','date_format_lite' => 'Y-m-d','date_format_full' => 'y-m-d H:i:s','is_rtl' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($langs);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
