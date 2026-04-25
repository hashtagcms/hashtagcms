<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update unique indexes to include deleted_at to support soft deletes.
     */
    public function up(): void
    {
        // Fix module_props
        Schema::table('module_props', function (Blueprint $table) {
            $table->dropUnique('module_props_module_site_platform_name_group_unique');
            $table->unique(
                ['module_id', 'site_id', 'platform_id', 'name', 'group', 'deleted_at'],
                'module_props_module_site_platform_name_group_unique'
            );
        });

        // Fix site_props
        Schema::table('site_props', function (Blueprint $table) {
            $table->dropUnique('site_props_site_platform_name_group_unique');
            $table->unique(
                ['site_id', 'platform_id', 'name', 'group_name', 'deleted_at'],
                'site_props_site_platform_name_group_unique'
            );
        });

        // Fix tags
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['name', 'deleted_at']);
        });

        // Fix module_types
        Schema::table('module_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['name', 'deleted_at']);
        });

        // Fix users
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique(['email', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'deleted_at']);
            $table->unique(['email']);
        });

        Schema::table('module_types', function (Blueprint $table) {
            $table->dropUnique(['name', 'deleted_at']);
            $table->unique(['name']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['name', 'deleted_at']);
            $table->unique(['name']);
        });

        Schema::table('module_props', function (Blueprint $table) {
            $table->dropUnique('module_props_module_site_platform_name_group_unique');
            $table->unique(
                ['module_id', 'site_id', 'platform_id', 'name', 'group'],
                'module_props_module_site_platform_name_group_unique'
            );
        });

        Schema::table('site_props', function (Blueprint $table) {
            $table->dropUnique('site_props_site_platform_name_group_unique');
            $table->unique(
                ['site_id', 'platform_id', 'name', 'group_name'],
                'site_props_site_platform_name_group_unique'
            );
        });
    }
};
