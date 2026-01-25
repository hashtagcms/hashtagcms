<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class ImportDatabaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:importdata 
                            {--class=DatabaseSeeder : The seeder class to run}
                            {--force : Force the operation to run when in production}
                            {--update-domain : Update the domain field in sites table with APP_URL}
                            {--check-existing : Check for existing records before inserting to prevent duplicates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Import database data and update domain-specific fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database import...');

        // Run the seeder
        $class = $this->option('class');
        $force = $this->option('force');
        $checkExisting = $this->option('check-existing');
        
        $this->info("Running seeder: {$class}");
        
        // Set environment variable to be used by seeders
        if ($checkExisting) {
            $this->info("Checking for existing records before inserting");
            putenv('SEED_CHECK_EXISTING=true');
        }
        
        $params = ['--class' => "Database\\Seeders\\{$class}"];
        if ($force) {
            $params['--force'] = true;
        }
        
        Artisan::call('db:seed', $params);
        
        $this->info(Artisan::output());
        
        // Update domain if requested
        if ($this->option('update-domain')) {
            $this->updateSitesDomain();
        }
        
        // Clean up environment variable
        if ($checkExisting) {
            putenv('SEED_CHECK_EXISTING');
        }
        
        $this->info('Database import completed successfully!');
        
        return 0;
    }
    
    /**
     * Update the domain field in the sites table with the APP_URL from the environment
     */
    protected function updateSitesDomain()
    {
        $this->info('Updating sites domain with APP_URL...');
        
        // Check if sites table exists
        if (!Schema::hasTable('sites')) {
            $this->error('Sites table not found!');
            return;
        }
        
        // Get APP_URL from environment
        $appUrl = env('APP_URL');
        if (empty($appUrl)) {
            $this->error('APP_URL not set in environment!');
            return;
        }
        
        // Parse domain from APP_URL
        $parsedUrl = parse_url($appUrl);
        $domain = $parsedUrl['host'] ?? null;
        
        if (empty($domain)) {
            $this->error('Could not parse domain from APP_URL!');
            return;
        }
        
        // Update only the first row in sites table
        try {
            $site = DB::table('sites')->first();
            
            if ($site) {
                DB::table('sites')
                    ->where('id', $site->id)
                    ->update(['domain' => $domain]);
                
                $this->info("Updated domain for site ID {$site->id} to '{$domain}'");
            } else {
                $this->warn('No sites found in the database!');
            }
        } catch (\Exception $e) {
            $this->error("Error updating sites domain: {$e->getMessage()}");
        }
    }
}
