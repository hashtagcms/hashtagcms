<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update unique indexes to include deleted_at to support soft deletes.
     *
     * Composite indexes on module_props and site_props are updated so that
     * soft-deleted rows (deleted_at IS NOT NULL) do not collide with new active
     * rows (deleted_at IS NULL) that share the same natural key.
     *
     * Single-column unique indexes on tags.name, module_types.name and
     * users.email receive the same treatment.
     */
    public function up(): void
    {
        // ── module_props ──────────────────────────────────────────────────────
        Schema::table('module_props', function (Blueprint $table) {
            $table->dropUnique('module_props_module_site_platform_name_group_unique');
            $table->unique(
                ['module_id', 'site_id', 'platform_id', 'name', 'group', 'deleted_at'],
                'module_props_module_site_platform_name_group_unique'
            );
        });

        // ── site_props ────────────────────────────────────────────────────────
        // MySQL uses the unique index to back the FK on site_id, so we must
        // drop the FK first, swap the index, then re-add the FK.
        Schema::table('site_props', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['site_id']);
            }

            $table->dropUnique('site_props_site_platform_name_group_unique');
            $table->unique(
                ['site_id', 'platform_id', 'name', 'group_name', 'deleted_at'],
                'site_props_site_platform_name_group_unique'
            );

            if (DB::getDriverName() !== 'sqlite') {
                $table->foreign('site_id')
                    ->references('id')
                    ->on('sites')
                    ->onDelete('cascade');
            }
        });

        // ── tags ──────────────────────────────────────────────────────────────
        // Original: ->string('name', 100)->unique()  →  tags_name_unique
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('tags_name_unique');
            $table->unique(['name', 'deleted_at'], 'tags_name_deleted_at_unique');
        });

        // ── module_types ──────────────────────────────────────────────────────
        // Original: ->string('name', 60)->unique()  →  module_types_name_unique
        Schema::table('module_types', function (Blueprint $table) {
            $table->dropUnique('module_types_name_unique');
            $table->unique(['name', 'deleted_at'], 'module_types_name_deleted_at_unique');
        });

        // ── users ─────────────────────────────────────────────────────────────
        // Original: ->string('email')->unique()  →  users_email_unique
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique(['email', 'deleted_at'], 'users_email_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_deleted_at_unique');
            $table->unique(['email'], 'users_email_unique');
        });

        Schema::table('module_types', function (Blueprint $table) {
            $table->dropUnique('module_types_name_deleted_at_unique');
            $table->unique(['name'], 'module_types_name_unique');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('tags_name_deleted_at_unique');
            $table->unique(['name'], 'tags_name_unique');
        });

        Schema::table('module_props', function (Blueprint $table) {
            $table->dropUnique('module_props_module_site_platform_name_group_unique');
            $table->unique(
                ['module_id', 'site_id', 'platform_id', 'name', 'group'],
                'module_props_module_site_platform_name_group_unique'
            );
        });

        Schema::table('site_props', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['site_id']);
            }

            $table->dropUnique('site_props_site_platform_name_group_unique');
            $table->unique(
                ['site_id', 'platform_id', 'name', 'group_name'],
                'site_props_site_platform_name_group_unique'
            );

            if (DB::getDriverName() !== 'sqlite') {
                $table->foreign('site_id')
                    ->references('id')
                    ->on('sites')
                    ->onDelete('cascade');
            }
        });
    }
};
