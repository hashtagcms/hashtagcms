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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('site_id', false, true);
            $table->string('name', 60);
            $table->string('alias', 60);
            $table->string('directory', 60);
            $table->string('body_class', 255)->nullable();
            $table->string('img_preview')->nullable();
            $table->text('skeleton');
            $table->text('header_content')->nullable();
            $table->text('footer_content')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        //Relation on site
        Schema::table('themes', function (Blueprint $table) {

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

        });

        //auto increment
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE themes AUTO_INCREMENT = 5000");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
};
