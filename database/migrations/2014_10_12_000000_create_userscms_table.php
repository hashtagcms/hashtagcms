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

        //if use table exist change some fields
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('facebook_user_id')->nullable();
                $table->string('google_user_id')->nullable();
                $table->enum('user_type', ['Visitor', 'Staff'])->default('Visitor');
                $table->softDeletes();
            });
        } else {
            //else create
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();

                $table->string('facebook_user_id')->nullable();
                $table->string('google_user_id')->nullable();
                $table->enum('user_type', ['Visitor', 'Staff'])->default('Visitor');
                $table->softDeletes();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
