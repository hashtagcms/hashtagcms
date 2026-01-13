<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitePropsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $table_name = 'site_props';
        $date = date('Y-m-d H:i:s');
        $site_props = [
            ['id' => '1', 'site_id' => '1', 'platform_id' => '1', 'name' => 'site_installed', 'value' => '0', 'group_name' => null, 'is_public' => '0', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '2', 'site_id' => '1', 'platform_id' => '1', 'name' => 'Twitter', 'value' => 'https://www.twitter.com/hashtagcms', 'group_name' => 'SocialLinks', 'is_public' => '1', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
            ['id' => '3', 'site_id' => '1', 'platform_id' => '1', 'name' => 'Facebook', 'value' => 'https://www.facebook.com/hashtagcms.org', 'group_name' => 'SocialLinks', 'is_public' => '1', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($site_props);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
