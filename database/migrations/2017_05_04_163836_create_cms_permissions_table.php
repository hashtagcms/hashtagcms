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

        Schema::create('cms_permissions', function (Blueprint $table) {

            $table->bigInteger('module_id', false, true);
            $table->bigInteger('user_id', false, true);
            $table->tinyInteger('readonly')->nullable()->default(0);

        });

        //Relation
        Schema::table('cms_permissions', function (Blueprint $table) {

            $table->foreign('module_id')
                ->references('id')
                ->on('cms_modules')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['user_id', 'module_id']);
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
        Schema::dropIfExists('cms_permissions');
        Schema::enableForeignKeyConstraints();
    }
};
