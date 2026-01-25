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
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('user_id', false, true);
            $table->string('father_name', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('id_card_type', 255)->nullable();
            $table->string('id_card_number', 50)->nullable();

            $table->string('mobile', 50);
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation on users
        Schema::table('user_profiles', function (Blueprint $table) {

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('user_profiles');
    }
};
