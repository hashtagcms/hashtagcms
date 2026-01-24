<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'medias';
        $table_name_langs = 'media_langs';
        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert(
                [

                ]
            );
            DB::table($table_name_langs)->insert(
                [

                ]
            );
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
