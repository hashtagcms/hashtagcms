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
            ['id' => '1', 'name' => 'super-admin', 'label' => 'Super Admin', 'description' => 'Has absolute access to every part of the system, including all sites, global configurations, and license management.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '2', 'name' => 'admin', 'label' => 'Admin', 'description' => 'Has full access (Read, Edit, Delete, Approve, Publish) to all enabled cms modules within their assigned sites.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '3', 'name' => 'editor', 'label' => 'Editor', 'description' => 'Can manage content (Read, Edit, Delete, Approve, Publish) created by any user within their assigned sites and cms modules.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '4', 'name' => 'approver', 'label' => 'Approver', 'description' => 'Focused on the publication workflow; has Read, Approve, and Publish permissions.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '5', 'name' => 'contributor', 'label' => 'Contributor', 'description' => 'Can manage content (Read, Edit, Delete, Approve, Publish) but only their own content.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '6', 'name' => 'ReadOnly', 'label' => 'Read Only', 'description' => 'Has view-only (Read) access to the cms modules they are authorized to see.', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
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
