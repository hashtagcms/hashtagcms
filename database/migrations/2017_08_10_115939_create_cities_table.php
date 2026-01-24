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
        Schema::create('cities', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('country_id', false, true);
            $table->bigInteger('zone_id', false, true);
            $table->string('name', 100);
            $table->string('iso_code', 7)->nullable();
            $table->tinyInteger('tax_behavior')->nullable()->default(0);
            $table->string('airport_name', 256)->nullable();
            $table->string('airport_code', 20)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
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
        Schema::dropIfExists('cities');
    }
};
