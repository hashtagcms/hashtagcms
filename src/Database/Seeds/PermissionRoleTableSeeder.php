<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $table_name = 'permission_role';

        $permission_role = array(
            array('permission_id' => '1','role_id' => '3'),
            array('permission_id' => '2','role_id' => '3'),
            array('permission_id' => '3','role_id' => '3'),
            array('permission_id' => '4','role_id' => '3'),
            array('permission_id' => '5','role_id' => '3'),
            array('permission_id' => '1','role_id' => '4'),
            array('permission_id' => '2','role_id' => '4'),
            array('permission_id' => '5','role_id' => '4'),
            array('permission_id' => '1','role_id' => '5'),
            array('permission_id' => '4','role_id' => '5'),
            array('permission_id' => '5','role_id' => '5'),
            array('permission_id' => '1','role_id' => '6'),
            array('permission_id' => '2','role_id' => '6'),
            array('permission_id' => '3','role_id' => '6'),
            array('permission_id' => '1','role_id' => '7')
        );



        if(DB::table($table_name)->get()->count() == 0) {
          DB::table($table_name)->insert($permission_role);
        }
    }
}
