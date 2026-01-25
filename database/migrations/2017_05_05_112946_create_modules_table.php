<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);

            $table->string('name', 60);
            $table->string('alias', 60);
            $table->string('linked_module', 60)->nullable();

            $table->string('view_name', 200);

            //$table->enum('data_type', ['Static','Query','Service','Custom','QueryService','UrlService']);
            $table->string('data_type', 100);

            $table->text('query_statement')->nullable();
            $table->enum('query_as', ['', 'param', 'data'])->nullable();

            $table->text('data_handler')->nullable();
            $table->text('data_key_map')->nullable();

            $table->text('description')->nullable();

            $table->tinyInteger('is_mandatory')->nullable()->default(0);

            $table->enum('method_type', ['GET', 'POST', 'PUT', 'DELETE'])->nullable();
            $table->string('service_params', 255)->nullable();
            $table->text('headers')->nullable();

            $table->tinyInteger('individual_cache')->nullable()->default(0)->comment('If you want to cache this module on each users');

            $table->string('cache_group', 100)->nullable();
            $table->tinyInteger('is_seo_module')->nullable()->default(0)->comment('Use this module for SEO. Needs to have meta fields');

            $table->tinyInteger('live_edit')->nullable()->default(0);
            $table->tinyInteger('shared')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        //auto increment
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE modules AUTO_INCREMENT = 5000");
        }

        //Relation on site
        Schema::table('modules', function (Blueprint $table) {

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

        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('modules');

        Schema::enableForeignKeyConstraints();

    }
};
