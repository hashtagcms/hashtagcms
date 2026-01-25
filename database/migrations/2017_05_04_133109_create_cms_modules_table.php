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

        Schema::create('cms_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('controller_name');
            $table->string('sub_title', 100)->nullable();
            $table->string('display_name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('icon_css')->nullable();
            $table->string('package', 50)->nullable()->comment('Package identifier (e.g., hashtagcms-pro)');
            $table->string('list_view_name')->nullable();
            $table->string('edit_view_name')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        //auto increment - already have it
        //DB::statement("ALTER TABLE cms_modules AUTO_INCREMENT = 5000");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_modules');
    }
};
