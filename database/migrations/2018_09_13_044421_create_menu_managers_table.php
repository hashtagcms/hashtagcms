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
        Schema::create('menu_managers', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);
            $table->string('menu_group', 60);
            $table->bigInteger('parent_id', false, true)->nullable();
            $table->string('link_rewrite')->nullable();
            $table->integer('position')->nullable();
            $table->enum('menu_type', ['link', 'module'])->nullable()->default('link');
            $table->string('module_alias', 150)->nullable();
            $table->string('module_tag', 150)->nullable()->comment('If you want use this field instead of module_alias');
            $table->tinyInteger('publish_status')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        //Lang Table
        Schema::create('menu_manager_langs', function (Blueprint $table) {
            $table->bigInteger('menu_manager_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('menu_manager_langs', function (Blueprint $table) {
            $table->foreign('menu_manager_id')
                ->references('id')
                ->on('menu_managers')
                ->onDelete('cascade');

            $table->primary(['menu_manager_id', 'lang_id']);
        });

        //Relation on site
        Schema::table('menu_managers', function (Blueprint $table) {
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

        Schema::dropIfExists('menu_managers');
        Schema::dropIfExists('menu_manager_langs');

        Schema::enableForeignKeyConstraints();
    }
};
