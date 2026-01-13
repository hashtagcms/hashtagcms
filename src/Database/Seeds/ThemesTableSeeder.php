<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'themes';
        $date = date('Y-m-d H:i:s');

        $themes = [
            ['id' => '1', 'site_id' => '1', 'name' => 'Basic', 'alias' => 'BASIC', 'directory' => 'basic', 'body_class' => 'app-default', 'img_preview' => null, 'skeleton' => '<div id="app" >
<div>
        %{cms.hook.HOOK_HEADER}%
</div>
<main class="basic-theme">
    <div class="content-wrapper oh">
        %{cms.hook.HOOK_ONE_COLUMN}%
    </div>
</main>
<hr />
<div class="footer clearfix">
        %{cms.hook.HOOK_FOOTER}%
</div>

</div>', 'header_content' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE; chrome=1"/>

<link rel="stylesheet" href="%{css_path}%/app.css" />', 'footer_content' => '<script src="%{js_path}%/app.js"></script>', 'created_at' => $date, 'updated_at' => $date, 'deleted_at' => null],
        ];

        if (DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($themes);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
