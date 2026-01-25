<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'lang_site';
        $date = date('Y-m-d H:i:s');
        $lang_site = array(
            array('site_id' => '1','lang_id' => '1','position' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','lang_id' => '2','position' => '2','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );


        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($lang_site);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
