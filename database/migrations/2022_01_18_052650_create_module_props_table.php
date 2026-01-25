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
        Schema::create('module_props', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('module_id', false, true);
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('platform_id', false, true);
            $table->string('name', 100);
            $table->string('group', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        //Relation
        Schema::table('module_props', function (Blueprint $table) {
            try {
                //on modules
                $table->foreign('module_id')
                    ->references('id')
                    ->on('modules')
                    ->onDelete('cascade');

                //on sites
                $table->foreign('site_id')
                    ->references('id')
                    ->on('sites')
                    ->onDelete('cascade');

                //on platform
                $table->foreign('platform_id')
                    ->references('id')
                    ->on('platforms')
                    ->onDelete('cascade');

                $table->index(['module_id', 'site_id', 'platform_id'], 'module_platform_site_idx');

            } catch (Exception $exception) {
                info($exception->getMessage());
            }

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_props');
    }
};
