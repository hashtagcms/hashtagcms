<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('label', 100)->nullable();
            $table->string('icon', 60)->nullable();
            $table->text('description')->nullable();
            $table->string('field_hint', 200)->nullable();
            $table->string('placeholder', 200)->nullable();
            $table->tinyInteger('publish_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default data
        $types = [
            [
                'name' => 'Static',
                'label' => 'Static Content',
                'icon' => 'fa fa-database',
                'description' => 'Reads data from the static_module_contents table. Enter the CONTENT_ALIAS value in the Data Handler field to identify which static content to load.',
                'field_hint' => 'Data Handler → CONTENT_ALIAS',
                'placeholder' => 'e.g. HERO_BANNER_CONTENT',
            ],
            [
                'name' => 'Query',
                'label' => 'Database Query',
                'icon' => 'fa fa-code',
                'description' => 'Write a raw MySQL SELECT query directly in the Data Handler field. The system will execute it and pass the result set to the view.',
                'field_hint' => 'Data Handler → MySQL query',
                'placeholder' => 'e.g. SELECT * FROM articles WHERE site_id=:site_id AND status=1',
            ],
            [
                'name' => 'Service',
                'label' => 'External Service (API)',
                'icon' => 'fa fa-globe',
                'description' => 'Write the external service/API URL in the Data Handler field. The system will call that endpoint and pass the JSON response to the view.',
                'field_hint' => 'Data Handler → Service URL',
                'placeholder' => 'e.g. https://api.example.com/v1/articles',
            ],
            [
                'name' => 'Custom',
                'label' => 'Custom Logic',
                'icon' => 'fa fa-puzzle-piece',
                'description' => 'Renders the module view directly. A PHP class reference is optional — if provided, its return value is passed to the view as data.',
                'field_hint' => 'Data Handler → Class reference (optional)',
                'placeholder' => 'e.g. App\\Handlers\\MyHandler',
            ],
            [
                'name' => 'QueryService',
                'label' => 'Query + Service',
                'icon' => 'fa fa-exchange',
                'description' => 'Hybrid type: put the external service URL in the Data Handler field, and write the MySQL query in the Query field.',
                'field_hint' => 'Data Handler → URL  |  Query → MySQL',
                'placeholder' => 'e.g. https://api.example.com/v1/search',
            ],
            [
                'name' => 'UrlService',
                'label' => 'URL Service',
                'icon' => 'fa fa-link',
                'description' => 'Similar to Service, but automatically reads parameters from the current request URL and appends them to the API call.',
                'field_hint' => 'Data Handler → Service URL (URL params auto-injected)',
                'placeholder' => 'e.g. https://api.example.com/v1/detail',
            ],
            [
                'name' => 'ServiceLater',
                'label' => 'Lazy Load Service',
                'icon' => 'fa fa-clock-o',
                'description' => 'The system will not call the API immediately. Instead, it passes the service URL directly to the view so the frontend can call it on demand.',
                'field_hint' => 'Data Handler → Service URL (passed to view, not called)',
                'placeholder' => 'e.g. https://api.example.com/v1/products',
            ]
        ];

        foreach ($types as $type) {
            DB::table('module_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_types');
    }
};
