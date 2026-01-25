<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns to pages table
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                if (!Schema::hasColumn('pages', 'publish_at')) {
                    $table->timestamp('publish_at')->nullable()->after('publish_status')->comment('Start displaying content');
                }
                if (!Schema::hasColumn('pages', 'expire_at')) {
                    $table->timestamp('expire_at')->nullable()->after('publish_at')->comment('Stop displaying content');
                }
            });
        }

        // Add columns to categories table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'publish_at')) {
                    $table->timestamp('publish_at')->nullable()->after('publish_status')->comment('Start displaying content');
                }
                if (!Schema::hasColumn('categories', 'expire_at')) {
                    $table->timestamp('expire_at')->nullable()->after('publish_at')->comment('Stop displaying content');
                }
            });
        }

        // Update MODULE_STORY data_handler with scheduling logic
        try {
            DB::table('modules')->where('alias', 'MODULE_STORY')->update([
                'data_handler' => "select c.id as cat_id, p.id, p.parent_id, p.site_id, p.microsite_id, p.platform_id, p.category_id, p.alias, p.exclude_in_listing, p.content_type, p.position, p.link_rewrite, p.link_navigation, p.menu_placement, p.header_content, p.footer_content, p.insert_by, p.update_by, p.enable_comments, p.required_login, p.publish_status, p.read_count, p.attachment, p.img, p.author, p.content_source, p.created_at, p.updated_at, p.read_count, pl.name, pl.title, pl.description, pl.page_content, pl.link_relation, pl.target, pl.active_key, pl.meta_title, pl.meta_keywords, pl.meta_description, pl.meta_robots, pl.meta_canonical 
from pages p 
left join page_langs pl on (p.id = pl.page_id) 
left join categories c on(p.category_id = c.id) 
left join (SELECT page_id as comment_page_id, COUNT(*) as comments_count FROM comments where deleted_at is null GROUP BY page_id) cmn ON (cmn.comment_page_id = p.id) 
where p.link_rewrite=:link_rewrite 
and p.site_id=:site_id 
and pl.lang_id=:lang_id 
and p.publish_status=1 
and c.id=:category_id 
and p.deleted_at is null
and (p.publish_at IS NULL OR p.publish_at <= NOW()) 
and (p.expire_at IS NULL OR p.expire_at >= NOW())"
            ]);
        } catch (\Exception $e) {
            // Table might not exist during fresh migration if basic tables aren't created yet, 
            // though usually this runs after. Use with caution.
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                if (Schema::hasColumn('pages', 'publish_at')) {
                    $table->dropColumn('publish_at');
                }
                if (Schema::hasColumn('pages', 'expire_at')) {
                    $table->dropColumn('expire_at');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'publish_at')) {
                    $table->dropColumn('publish_at');
                }
                if (Schema::hasColumn('categories', 'expire_at')) {
                    $table->dropColumn('expire_at');
                }
            });
        }
    }
};
