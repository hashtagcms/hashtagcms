<?php
/**
 * Migration to add description column to roles table.
 * Added automatically for HashtagCms role-based access control enhancements.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('label');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (Schema::hasColumn('roles', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
