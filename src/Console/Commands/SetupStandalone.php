<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupStandalone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:setup-standalone {--force : Force publish assets and views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Setup a standalone frontend environment by publishing configuration, assets, views and configuring .env for API usage.';

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

        // 4. Configure .env
        $this->configureEnv();

        $this->info('Standalone setup completed successfully.');
    }

    /**
     * Configure environment variables
     */
    protected function configureEnv()
    {
        $this->info('Configuring Environment Variables...');

        $apiUrl = $this->ask('Enter HashtagCMS API URL (e.g. https://admin.example.com)', 'https://admin.hashtagcms.org');
        $apiToken = $this->ask('Enter HashtagCMS API Token', '');
        $apiSecret = $this->ask('Enter HashtagCMS API Secret', '');

        $this->updateEnv([
            'HASHTAGCMS_API_HOST' => $apiUrl,
            'HASHTAGCMS_API_TOKEN' => $apiToken,
            'HASHTAGCMS_API_SECRET' => $apiSecret,
            'HASHTAGCMS_LOAD_MODULE_FROM_DB' => 'false'
        ]);

        $this->info('Updated .env file.');
        $this->info('Please create a site context for your domain in config/hashtagcms.php');

    }

    /**
     * Update .env file
     * @param array $data
     */
    protected function updateEnv($data = [])
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $env = file_get_contents($path);

            foreach ($data as $key => $value) {
                // If key exists, replace it
                if (strpos($env, $key . '=') !== false) {
                    $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
                } else {
                    // If key doesn't exist, append it
                    $env .= "\n{$key}={$value}";
                }
            }

            file_put_contents($path, $env);
        }
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
