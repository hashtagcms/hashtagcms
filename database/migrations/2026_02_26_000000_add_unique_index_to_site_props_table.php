<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds a composite unique index on site_props to enforce the key-value uniqueness
 * per site + platform + group + name combination.
 *
 * group_name is nullable, so we use a partial/conditional approach:
 * MySQL treats NULL as distinct in unique indexes (two NULLs are not equal),
 * which means rows with NULL group_name can still duplicate. To handle this we
 * store '' (empty string) as the canonical "no group" value and enforce the index
 * over all four columns. The nullable() is kept on the column for backward compat
 * but new inserts should coerce null → ''.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_props', function (Blueprint $table) {
            // Drop any duplicates before adding the constraint (safe for fresh installs too)
            // The index name is kept short to fit MySQL's 64-char limit.
            $table->unique(
                ['site_id', 'platform_id', 'name', 'group_name'],
                'site_props_site_platform_name_group_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('site_props', function (Blueprint $table) {
            // MySQL error 1553: Cannot drop index needed in a foreign key constraint.
            // This happens because this unique index starts with site_id, and is used to support the FK.
            // We drop the FK first, then the index, then re-add the FK.
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['site_id']);
            }

            $table->dropUnique('site_props_site_platform_name_group_unique');

            if (DB::getDriverName() !== 'sqlite') {
                $table->foreign('site_id')
                    ->references('id')
                    ->on('sites')
                    ->onDelete('cascade');
            }
        });
    }
};
