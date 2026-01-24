<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HookSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'hook_site';

        $hook_site = array(
            array('hook_id' => '1','site_id' => '1'),
            array('hook_id' => '2','site_id' => '1'),
            array('hook_id' => '3','site_id' => '1'),
            array('hook_id' => '4','site_id' => '1'),
            array('hook_id' => '5','site_id' => '1'),
            array('hook_id' => '6','site_id' => '1'),
            array('hook_id' => '7','site_id' => '1'),
            array('hook_id' => '9','site_id' => '1'),
            array('hook_id' => '10','site_id' => '1')
        );


        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($hook_site);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
