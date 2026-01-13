<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $zones = array(
            array('id' => '1','name' => 'Europe','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'North America','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'Asia','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'Africa','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'Oceania','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'South America','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'Europe (out E.U)','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','name' => 'Central America/Antilla','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table('zones')->get()->count() == 0) {
            DB::table('zones')->insert($zones);

        } else {
            echo "SeedingError: `zones` table is not empty\n";
        }

    }
}
