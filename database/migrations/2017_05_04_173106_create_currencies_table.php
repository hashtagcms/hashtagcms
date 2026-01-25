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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->string('iso_code', 3);
            $table->string('iso_code_num', 3);
            $table->string('sign');
            $table->tinyInteger('blank')->nullable()->default(0);
            $table->tinyInteger('format')->nullable()->default(0);
            $table->tinyInteger('decimals')->nullable()->defaul(1);
            $table->decimal('conversion_rate', 13, 6);
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

        Schema::dropIfExists('currencies');

        Schema::enableForeignKeyConstraints();

    }
};
