<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'platforms';
        $date = date('Y-m-d H:i:s');
        $platforms = array(
            array('id' => '1','name' => 'Desktop','link_rewrite' => 'web','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Android','link_rewrite' => 'android','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'iOS','link_rewrite' => 'ios','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'PWA','link_rewrite' => 'pwa','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );
        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($platforms);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
