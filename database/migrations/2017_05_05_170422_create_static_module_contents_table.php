<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_module_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('insert_by', false, true);
            $table->bigInteger('update_by', false, true);
            $table->string('alias', 60);
            $table->timestamps();
            $table->softDeletes();
        });

        //auto increment
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE static_module_contents AUTO_INCREMENT = 5000");
        }

        //Language
        Schema::create('static_module_content_langs', function (Blueprint $table) {

            $table->bigInteger('static_module_content_id', false, true);
            $table->bigInteger('lang_id', false, true);

            $table->string('title', 255);
            $table->text('content');

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('static_module_content_langs', function (Blueprint $table) {

            $table->foreign('static_module_content_id')
                ->references('id')
                ->on('static_module_contents')
                ->onDelete('cascade');

            $table->primary(['static_module_content_id', 'lang_id'], 'static_content_id_lang_id');
        });

        //Relation on site
        Schema::table('static_module_contents', function (Blueprint $table) {

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

        Schema::dropIfExists('static_module_contents');
        Schema::dropIfExists('static_module_content_langs');

        Schema::enableForeignKeyConstraints();

    }
};
