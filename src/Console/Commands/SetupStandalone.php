<?php

namespace MarghoobSuleman\HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupStandalone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:standalone {--force : Force publish assets and views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a standalone frontend environment by publishing configuration, assets, and views. Use this if you only need the frontend.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Standalone Setup...');

        // 1. Publish Config (Only hashtagcms.php)
        $this->publishConfig();

        // 2. Publish Assets
        $this->info('Publishing Assets...');
        $this->call('vendor:publish', [
            '--tag' => 'hashtagcms.assets'
        ]);

        // 3. Publish Frontend Views
        $this->info('Publishing Frontend Views...');
        $this->call('vendor:publish', [
            '--tag' => 'hashtagcms.views.frontend'
        ]);

        $this->info('Standalone setup completed successfully.');
    }

    /**
     * Publish the hashtagcms.php config file manually.
     */
    protected function publishConfig()
    {
        // Calculate source path relative to this file
        // This file: src/Console/Commands/SetupStandalone.php
        // Config: config/hashtagcms.php
        $source = __DIR__ . '/../../../config/hashtagcms.php';

        // Destination: Laravel project's config folder
        $destination = config_path('hashtagcms.php');

        if (!File::exists($source)) {
            $this->error("Source config file not found at: $source");
            return;
        }

        if (File::exists($destination) && !$this->option('force')) {
            if (!$this->confirm("Config file [config/hashtagcms.php] already exists. Do you want to overwrite it?")) {
                $this->info('Skipped publishing config.');
                return;
            }
        }

        File::copy($source, $destination);
        $this->info('Published config [config/hashtagcms.php].');
    }
}
