<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a composite unique index on module_props to enforce key-value uniqueness
 * per module + site + platform + group + name combination.
 *
 * `group` is nullable; we coerce null → '' on insert so the unique index
 * (which treats NULLs as distinct) works correctly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_props', function (Blueprint $table) {
            $table->unique(
                ['module_id', 'site_id', 'platform_id', 'name', 'group'],
                'module_props_module_site_platform_name_group_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('module_props', function (Blueprint $table) {
            $table->dropUnique('module_props_module_site_platform_name_group_unique');
        });
    }
};
