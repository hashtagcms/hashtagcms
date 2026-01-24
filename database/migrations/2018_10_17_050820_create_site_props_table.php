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
        Schema::create('site_props', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('platform_id', false, true);
            $table->string('name', 100);
            $table->text('value');
            $table->string('group_name', 60)->nullable();
            $table->tinyInteger('is_public')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation on site
        Schema::table('site_props', function (Blueprint $table) {

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
        Schema::dropIfExists('site_props');
    }
};
