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
        Schema::create('microsites', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);

            $table->string('name', 255);
            $table->string('link_rewrite', 255);
            $table->string('home_url', 255);
            $table->string('logo', 255)->nullable();
            $table->string('logo2', 255)->nullable();

            $table->text('header_content')->nullable();
            $table->text('footer_content')->nullable();

            $table->text('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_robots')->nullable();

            $table->string('channel', 100)->nullable()->comment('This can be used for third party login access etc');
            $table->string('login_url', 255)->nullable()->comment('This can be used for third party login access etc');

            $table->tinyInteger('revalidate')->nullable()->default(0);
            $table->tinyInteger('publish_status')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

        });

        //Relation on site
        Schema::table('microsites', function (Blueprint $table) {

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
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
        Schema::dropIfExists('microsites');
    }
};
