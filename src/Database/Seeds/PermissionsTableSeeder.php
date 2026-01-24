<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $table_name = 'permissions';
        $date = date('Y-m-d H:i:s');
        $permissions = array(
            array('id' => '1','name' => 'read','label' => 'Read Access','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'edit','label' => 'Write Access','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'delete','label' => 'Delete Access','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'approve','label' => 'Approval Access','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'publish','label' => 'Publish Access','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
          DB::table($table_name)->insert($permissions);
      }
    }
}
