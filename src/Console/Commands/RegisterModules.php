<?php

namespace HashtagCms\Console\Commands;
use Illuminate\Console\Command;
use HashtagCms\Models\Module;

class RegisterModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:register-modules-from-view-folder {--path= : The path to scan for modules} {--site_id=1 : The site ID to register modules for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Scan a theme directory and register all Blade files as Frontend Modules (db entries)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $themeFolder = $this->ask("Theme folder name (under views/vendor/hashtagcms/fe/)?", 'sapphire');
        $basePath = resource_path("views/vendor/hashtagcms/fe/$themeFolder");
        $siteId = $this->ask("Site Id?", $this->option('site_id') ?? 1);


        
        // User specifically asked to skip: auth, _layout_, _service_ (they meant _services_)
        $skipFolders = ['auth', '_layout_', '_services_', 'example'];

        $this->info("Scanning directory: $basePath");

        if (!is_dir($basePath)) {
            $this->error("Directory not found: $basePath");
            return;
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
        $count = 0;

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            if ($file->getExtension() !== 'php' || !str_ends_with($file->getFilename(), '.blade.php')) continue;

            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $viewParts = explode(DIRECTORY_SEPARATOR, $relativePath);
            
            // Check if any part of the path is in the skip list
            $skip = false;
            foreach ($viewParts as $part) {
                if (in_array($part, $skipFolders)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            // View name (e.g., home/hero)
            $viewName = str_replace(['.blade.php', DIRECTORY_SEPARATOR], ['', '/'], $relativePath);
            
            $alias_without_prefix = strtoupper(str_replace(['/', '-'], ['_', '_'], $viewName));
            // Alias (e.g., HOME_HERO)
            $alias = 'MODULE_'.strtoupper(str_replace(['/', '-'], ['_', '_'], $viewName));
            
            // Name (e.g., Home Hero)
            $name = ucwords(str_replace(['/', '_', '-'], [' ', ' ', ' '], $viewName));

            $this->info("Registering Module: $name [$alias] -> $viewName");
            // with or without prefix with site
            $exists = Module::withoutGlobalScopes()
                ->where('site_id', $siteId)
                ->whereIn('alias', [$alias, $alias_without_prefix])
                ->exists();
            if ($exists) {
                $this->warn("Module $alias already exists. Skipping...");
                continue;
            }

            Module::create(
                [
                    'site_id' => $siteId,
                    'alias' => $alias,
                    'name' => $name,
                    'view_name' => $viewName,
                    'data_type' => 'Custom',
                    'method_type' => 'GET',
                    'is_mandatory' => 0,
                    'individual_cache' => 0,
                    'live_edit' => 1,
                    'shared' => 0,
                    'is_seo_module' => 0
                ]
            );
            $count++;
        }

        $this->info("Successfully registered $count modules.");
    }
}
