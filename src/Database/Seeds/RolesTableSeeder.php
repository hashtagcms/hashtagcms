<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'roles';
        $date = date('Y-m-d H:i:s');
        $roles = [
            ['id' => '1', 'name' => 'super-admin', 'label' => 'Super Admin', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '2', 'name' => 'admin', 'label' => 'Admin', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '3', 'name' => 'editor', 'label' => 'Editor', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '4', 'name' => 'approver', 'label' => 'Approver', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '5', 'name' => 'contributor', 'label' => 'Contributor', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '6', 'name' => 'ReadOnly', 'label' => 'Read Only', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($roles);
        }

        $table_name = 'role_user';

        $role_user = [
            ['role_id' => '1', 'user_id' => '1'],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($role_user);
        }
    }
}
