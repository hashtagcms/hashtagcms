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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('zone_id', false, true);
            $table->bigInteger('currency_id', false, true)->nullable();
            $table->string('iso_code');
            $table->integer('call_prefix')->nullable();
            $table->tinyInteger('contains_states')->nullable()->default(0);
            $table->tinyInteger('need_identification_number')->nullable()->default(0);
            $table->tinyInteger('need_zip_code')->nullable()->default(0);
            $table->string('zip_code_format', 12)->nullable();
            $table->tinyInteger('display_tax_label')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

        });

        //Language
        Schema::create('country_langs', function (Blueprint $table) {

            $table->bigInteger('country_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->string('name', 65);

            $table->timestamps();
            $table->softDeletes();

        });

        //Relation
        Schema::table('country_langs', function (Blueprint $table) {
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');

            $table->primary(['country_id', 'lang_id']);
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

        Schema::dropIfExists('countries');
        Schema::dropIfExists('country_langs');

        Schema::enableForeignKeyConstraints();
    }
};
