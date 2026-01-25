<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $table_name = 'users';
        $date = date('Y-m-d H:i:s');
        $uniqid = uniqid();
        $users = array(

            array('id' => '1','name' => 'Marghoob Suleman','email' =>'admin@admin.com','password' => Hash::make($uniqid),'facebook_user_id' => NULL,'google_user_id' => NULL,'user_type' => 'Staff','remember_token' => 'PeyFofT6KHPU90K9HxQ17A0gH8npZSY3w4yMs2TYSzkWRlMtGHLj9LtJ5Q7d','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($users);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }


    }
}
