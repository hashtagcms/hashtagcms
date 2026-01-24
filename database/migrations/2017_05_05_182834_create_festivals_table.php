<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('festivals', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);

            $table->string('name', 255);
            $table->string('image', 255)->nullable();
            $table->string('body_css', 255)->nullable();

            $table->string('lottie', 255)->nullable();
            $table->string('width', 50)->nullable()->default('100%');
            $table->string('height', 50)->nullable()->default('100%');
            $table->string('background', 50)->nullable()->default('transparent');
            $table->integer('speed')->nullable()->default(1);

            $table->string('position_css', 50)->nullable()->default('absolute');
            $table->string('top', 50)->nullable()->default(0);
            $table->string('left', 50)->nullable()->default(0);
            $table->integer('z_index')->nullable()->default(99999);

            $table->string('play_mode', 50)->nullable()->default('normal');
            $table->tinyInteger('direction')->nullable()->default(1);

            $table->boolean('autoplay')->nullable()->default(1);
            $table->boolean('loop')->nullable()->default(1);
            $table->boolean('hover')->nullable()->default(0);
            $table->boolean('controls')->nullable()->default(0);

            //styles
            $table->boolean('hide_on_complete')->nullable()->default(1);

            $table->text('extra')->nullable();

            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->tinyInteger('publish_status')->nullable()->default(0);
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });

        //Relation on site
        Schema::table('festivals', function (Blueprint $table) {

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('festivals');

        Schema::enableForeignKeyConstraints();

    }
};
