<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('parent_id', false, true)->nullable();
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('microsite_id')->nullable()->default(0);
            $table->bigInteger('platform_id', false, true)->nullable();
            $table->bigInteger('category_id', false, true)->nullable();

            $table->string('alias', 60)->nullable();

            $table->tinyInteger('exclude_in_listing')->nullable()->default(0);

            $table->enum('content_type', ['page', 'blog'])->default('page');

            $table->tinyInteger('position')->nullable()->default(0);

            $table->string('link_rewrite', 255);
            $table->string('link_navigation', 255)->nullable()->comment('If you want to display on href. you must have apache rule for this if this is different from link_rewrite');
            $table->string('menu_placement', 100)->nullable()->default('bottom');

            $table->text('header_content')->nullable();
            $table->text('footer_content')->nullable();

            $table->bigInteger('insert_by', false, true);
            $table->bigInteger('update_by', false, true)->nullable();

            $table->tinyInteger('enable_comments')->nullable()->default(0);
            $table->tinyInteger('required_login')->nullable()->default(0);
            $table->tinyInteger('publish_status')->nullable()->default(0);
            $table->bigInteger('read_count')->nullable()->default(0);
            
            $table->timestamp('publish_at')->nullable()->comment('Start displaying content');
            $table->timestamp('expire_at')->nullable()->comment('Stop displaying content');

            $table->string('attachment', 255)->nullable();
            $table->string('img', 500)->nullable();
            $table->string('author', 255)->nullable();
            $table->string('content_source', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //auto increment
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE pages AUTO_INCREMENT = 5000");
        }

        //Language
        Schema::create('page_langs', function (Blueprint $table) {

            $table->bigInteger('page_id', false, true);
            $table->bigInteger('lang_id', false, true);

            $table->string('name', 128);
            $table->string('title', 128);

            $table->text('description')->nullable();
            $table->longText('page_content');

            $table->enum('link_relation', ['alternate', 'author', 'bookmark', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'prefetch', 'prev', 'search', 'tag'])->nullable();
            $table->enum('target', ['_blank', '_parent', '_self', '_top'])->nullable();

            $table->string('active_key', 128)->nullable();

            $table->string('meta_title', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_robots', 255)->nullable();
            $table->string('meta_canonical', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('page_langs', function (Blueprint $table) {

            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade');

            $table->primary(['page_id', 'lang_id']);

        });

        //Relation on site
        Schema::table('pages', function (Blueprint $table) {

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

        Schema::dropIfExists('pages');
        Schema::dropIfExists('page_langs');

        Schema::enableForeignKeyConstraints();

    }
};
