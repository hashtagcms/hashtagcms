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
        Schema::create('langs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->char('iso_code', 2);
            $table->string('language_code', 5);
            $table->string('date_format_lite', 32)->nullable()->default('Y-m-d');
            $table->string('date_format_full', 32)->nullable()->default('Y-m-d H:i:s');
            $table->tinyInteger('is_rtl')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
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

        Schema::dropIfExists('langs');
        Schema::dropIfExists('lang_site'); //@todo: why this is here

        Schema::enableForeignKeyConstraints();
    }
};
