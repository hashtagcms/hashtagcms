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
        Schema::create('module_prop_langs', function (Blueprint $table) {
            $table->bigInteger('module_prop_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->string('value', 500);
            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('module_prop_langs', function (Blueprint $table) {

            $table->foreign('module_prop_id')
                ->references('id')
                ->on('module_props')
                ->onDelete('cascade');

            $table->primary(['module_prop_id', 'lang_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_prop_langs');
    }
};
