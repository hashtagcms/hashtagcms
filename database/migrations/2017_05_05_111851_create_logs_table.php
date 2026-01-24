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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('module_id', false, true)->nullable();
            $table->bigInteger('record_id', false, true)->nullable();
            $table->string('action_performed', 60);
            $table->longText('query')->nullable();
            $table->longText('executed_query')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
