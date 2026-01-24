<?php

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
        $tableName = 'festivals';
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (! Schema::hasColumn($tableName, 'name')) {

                $table->string('name', 255)->after('site_id');

                $table->string('width', 50)->nullable()->default('100%')->after('lottie');
                $table->string('height', 50)->nullable()->default('100%')->after('lottie');
                $table->string('background', 50)->nullable()->default('transparent')->after('lottie');
                $table->integer('speed')->nullable()->default(1)->after('lottie');

                $table->string('position_css', 50)->nullable()->default('absolute')->after('lottie');
                $table->string('top', 50)->nullable()->default(0)->after('lottie');
                $table->string('left', 50)->nullable()->default(0)->after('lottie');
                $table->integer('z_index')->nullable()->default(99999)->after('lottie');

                $table->string('play_mode', 50)->nullable()->default('normal')->after('lottie');
                $table->tinyInteger('direction')->nullable()->default(1)->after('lottie');

                $table->boolean('autoplay')->nullable()->default(1)->after('lottie');
                $table->boolean('loop')->nullable()->default(1)->after('lottie');
                $table->boolean('hover')->nullable()->default(0)->after('lottie');
                $table->boolean('controls')->nullable()->default(0)->after('lottie');

                //styles
                $table->boolean('hide_on_complete')->nullable()->default(1)->after('lottie');

                $table->text('extra')->nullable()->after('lottie');
                $table->integer('position')->nullable()->after('publish_status');

            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
