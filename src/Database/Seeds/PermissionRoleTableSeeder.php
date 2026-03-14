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

        $permission_role = [
            // Admin (Role ID: 2) -> read, edit, delete, approve, publish
            ['permission_id' => '1', 'role_id' => '2'],
            ['permission_id' => '2', 'role_id' => '2'],
            ['permission_id' => '3', 'role_id' => '2'],
            ['permission_id' => '4', 'role_id' => '2'],
            ['permission_id' => '5', 'role_id' => '2'],

            // Editor (Role ID: 3) -> read, edit, delete, approve, publish
            ['permission_id' => '1', 'role_id' => '3'],
            ['permission_id' => '2', 'role_id' => '3'],
            ['permission_id' => '3', 'role_id' => '3'],
            ['permission_id' => '4', 'role_id' => '3'],
            ['permission_id' => '5', 'role_id' => '3'],

            // Approver (Role ID: 4) -> read, approve, publish
            ['permission_id' => '1', 'role_id' => '4'],
            ['permission_id' => '4', 'role_id' => '4'],
            ['permission_id' => '5', 'role_id' => '4'],

            // Contributor (Role ID: 5) -> read, edit, delete, approve, publish
            ['permission_id' => '1', 'role_id' => '5'],
            ['permission_id' => '2', 'role_id' => '5'],
            ['permission_id' => '3', 'role_id' => '5'],
            ['permission_id' => '4', 'role_id' => '5'],
            ['permission_id' => '5', 'role_id' => '5'],

            // ReadOnly (Role ID: 6) -> read
            ['permission_id' => '1', 'role_id' => '6'],
        ];



        if(DB::table($table_name)->get()->count() == 0) {
          DB::table($table_name)->insert($permission_role);
        }
    }
}
