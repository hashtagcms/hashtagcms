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
        //Create country site pivot
        Schema::create('country_site', function (Blueprint $table) {

            $table->bigInteger('country_id', false, true);
            $table->bigInteger('site_id', false, true);

        });

        //Relation on country<->site
        Schema::table('country_site', function (Blueprint $table) {

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['country_id', 'site_id']);

        });

        //Create currency site pivot
        Schema::create('currency_site', function (Blueprint $table) {

            $table->bigInteger('currency_id', false, true);
            $table->bigInteger('site_id', false, true);
            $table->decimal('conversion_rate', 13, 6)->nullable()->default(1.000000);
            $table->integer('markup')->nullable()->default(0);
            $table->enum('markup_type', ['Percent', 'Fixed'])->nullable()->default('Fixed');
        });

        Schema::table('currency_site', function (Blueprint $table) {

            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['currency_id', 'site_id']);
        });

        //Site Zone
        Schema::create('site_zone', function (Blueprint $table) {

            $table->bigInteger('zone_id', false, true);
            $table->bigInteger('site_id', false, true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('site_zone', function (Blueprint $table) {

            $table->foreign('zone_id')
                ->references('id')
                ->on('zones')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['zone_id', 'site_id']);
        });

        //Create pivot site<->platform
        Schema::create('platform_site', function (Blueprint $table) {

            $table->bigInteger('platform_id', false, true);
            $table->bigInteger('site_id', false, true);

        });

        //Relation on site<->plateform
        Schema::table('platform_site', function (Blueprint $table) {

            $table->foreign('platform_id')
                ->references('id')
                ->on('platforms')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['platform_id', 'site_id']);
        });

        //Lang
        Schema::create('lang_site', function (Blueprint $table) {

            $table->bigInteger('site_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('lang_site', function (Blueprint $table) {

            $table->foreign('lang_id')
                ->references('id')
                ->on('langs')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['lang_id', 'site_id']);

        });

        //Hook
        Schema::create('hook_site', function (Blueprint $table) {

            $table->bigInteger('hook_id', false, true);
            $table->bigInteger('site_id', false, true);

        });

        Schema::table('hook_site', function (Blueprint $table) {

            $table->foreign('hook_id')
                ->references('id')
                ->on('hooks')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['hook_id', 'site_id']);
        });

        //Modules
        Schema::create('module_site', function (Blueprint $table) {

            $table->bigInteger('site_id', false, true);
            $table->bigInteger('microsite_id', false, true)->default(0);

            $table->bigInteger('platform_id', false, true);

            $table->bigInteger('category_id', false, true);
            $table->bigInteger('hook_id', false, true);
            $table->bigInteger('module_id', false, true);

            $table->integer('position')->nullable();
            $table->tinyInteger('publish_status')->nullable()->default(0);

            $table->bigInteger('insert_by', false, true);
            $table->bigInteger('update_by', false, true)->nullable();
            $table->bigInteger('approved_by', false, true)->nullable();

            $table->timestamps();
            $table->softDeletes();

        });

        //Relation
        Schema::table('module_site', function (Blueprint $table) {

            //on site
            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            //on platform
            $table->foreign('platform_id')
                ->references('id')
                ->on('platforms')
                ->onDelete('cascade');

            //on category
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            //on hook
            $table->foreign('hook_id')
                ->references('id')
                ->on('hooks')
                ->onDelete('cascade');

            //on module
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');

            $table->primary(['site_id', 'platform_id', 'category_id', 'hook_id', 'module_id'], 'platform_site_category_hook_module');

        });

        //Create category site pivot
        Schema::create('category_site', function (Blueprint $table) {

            $table->bigInteger('category_id', false, true);
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('platform_id', false, true);

            $table->bigInteger('theme_id', false, true)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('icon_css', 255)->nullable();

            $table->text('header_content')->nullable();
            $table->text('footer_content')->nullable();

            $table->tinyInteger('exclude_in_listing')->nullable()->default(0);

            $table->integer('position')->nullable();

            $table->string('cache_category', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('category_site', function (Blueprint $table) {

            //on category
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            //on platform
            $table->foreign('platform_id')
                ->references('id')
                ->on('platforms')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->primary(['category_id', 'platform_id', 'site_id']);
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

        Schema::dropIfExists('country_site');
        Schema::dropIfExists('currency_site');
        Schema::dropIfExists('site_zone'); // what's the rule of pivot table - table zones and site min(z,s)
        Schema::dropIfExists('platform_site');
        Schema::dropIfExists('lang_site');
        Schema::dropIfExists('hook_site');
        Schema::dropIfExists('module_site');
        Schema::dropIfExists('category_site');

        Schema::enableForeignKeyConstraints();
    }
};
