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
        //Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });

        //Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });

        //Create permission<->role pivot
        Schema::create('permission_role', function (Blueprint $table) {
            $table->bigInteger('permission_id', false, true);
            $table->bigInteger('role_id', false, true);
        });

        //Create role<->user pivot
        Schema::create('role_user', function (Blueprint $table) {
            $table->bigInteger('role_id', false, true);
            $table->bigInteger('user_id', false, true);
        });

        //Relation on permission role
        Schema::table('permission_role', function (Blueprint $table) {

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        //Relation on role user
        Schema::table('role_user', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['role_id', 'user_id']);
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

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');

        Schema::enableForeignKeyConstraints();
    }
};
