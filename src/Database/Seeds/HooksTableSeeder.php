<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'hooks';
        $date = date('Y-m-d H:i:s');
        $hooks = array(
            array('id' => '1','name' => 'Header','alias' => 'HOOK_HEADER','direction' => 'vertical','description' => 'This is usually on top of the page','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Hero Section','alias' => 'HOOK_HERO_SECTION','direction' => 'vertical','description' => 'Hook for big image or main attraction','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'After Hero Section','alias' => 'HOOK_AFTER_HERO_SECTION','direction' => 'vertical','description' => 'Hook for just after the hero section','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'Left Section 1','alias' => 'HOOK_LEFT_SECTION_ONE','direction' => 'vertical','description' => 'Left hook for section one','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'Left Section 2','alias' => 'HOOK_LEFT_SECTION_TWO','direction' => 'vertical','description' => 'Left hook for section two','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'Right','alias' => 'HOOK_RIGHT','direction' => 'vertical','description' => 'Right hook','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'Middle','alias' => 'HOOK_MIDDLE','direction' => 'vertical','description' => 'This hook is usually at the middle','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','name' => 'Footer','alias' => 'HOOK_FOOTER','direction' => 'vertical','description' => 'Footer Hook','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '10','name' => 'One Column','alias' => 'HOOK_ONE_COLUMN','direction' => 'vertical','description' => 'This is usually for one column layout','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );


        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($hooks);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
