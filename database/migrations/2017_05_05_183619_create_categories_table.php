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
        Schema::create('categories', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('parent_id', false, true)->nullable();
            $table->bigInteger('site_id', false, true);

            $table->tinyInteger('is_site_default')->nullable()->default(0);
            $table->tinyInteger('is_root_category')->nullable()->default(0);

            $table->tinyInteger('is_new')->nullable()->default(0);

            $table->tinyInteger('has_wap')->nullable()->default(0);
            $table->string('wap_url')->nullable()->comment('Url if you have WAP site');

            $table->string('link_rewrite', 255);
            $table->string('link_navigation', 255)->nullable()->comment('If you want to display on href. you must have apache rule for this if this is different from link_rewrite');
            $table->string('link_rewrite_pattern', 255)->nullable();
            $table->string('controller_name', 255)->nullable();

            $table->tinyInteger('has_some_special_module')->nullable()->default(0);
            $table->string('special_module_alias', 255)->nullable();

            $table->tinyInteger('required_login')->nullable()->default(0);

            $table->bigInteger('insert_by', false, true);
            $table->bigInteger('update_by', false, true)->nullable();

            $table->tinyInteger('publish_status')->nullable()->default(0);
            $table->bigInteger('read_count')->nullable()->default(0);
            
            $table->timestamp('publish_at')->nullable()->comment('Start displaying content');
            $table->timestamp('expire_at')->nullable()->comment('Stop displaying content');

            $table->timestamps();
            $table->softDeletes();
        });

        //auto increment
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE categories AUTO_INCREMENT = 5000");
        }

        //Language
        Schema::create('category_langs', function (Blueprint $table) {

            $table->bigInteger('category_id', false, true);
            $table->bigInteger('lang_id', false, true);

            $table->string('name', 128);
            $table->string('title', 128);
            $table->text('excerpt')->nullable();

            $table->longText('content')->nullable();

            $table->string('active_key', 128)->nullable();
            $table->string('third_party_mapping_key', 255)->nullable();
            $table->string('b2b_mapping', 255)->nullable();

            $table->tinyInteger('is_external')->nullable()->default(0);

            $table->enum('link_relation', ['alternate', 'author', 'bookmark', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'prefetch', 'prev', 'search', 'tag'])->nullable();
            $table->enum('target', ['_blank', '_parent', '_self', '_top'])->nullable();

            $table->string('meta_title', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_robots', 255)->nullable();
            $table->string('meta_canonical', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

        });

        //Relation
        Schema::table('category_langs', function (Blueprint $table) {

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->primary(['category_id', 'lang_id']);

        });

        //Relation on site
        Schema::table('categories', function (Blueprint $table) {

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

        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_langs');

        Schema::enableForeignKeyConstraints();

    }
};
