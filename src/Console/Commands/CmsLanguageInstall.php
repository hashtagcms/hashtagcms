<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CmsLanguageInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:language-install {langs? : Languages to install (comma-separated ISO codes)} {--langs= : Languages to install (comma-separated ISO codes)}';

    /**
     * The console command aliases.
     *
     * @var array
     */
    //protected $aliases = ['cms:language-install', 'hashtagcms:language-install'];    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Install additional languages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $langs = $this->argument('langs') ?: $this->option('langs');
        
        if (empty($langs)) {
            $this->error('Please provide at least one language ISO code.');
            return;
        }

        $langsArray = explode(',', $langs);
        $validatedLangs = [];
        $unsupportedLangs = [];

        foreach ($langsArray as $lang) {
            $lang = trim($lang);
            $path = __DIR__ . "/../../Database/Seeds/Translations/{$lang}";
            if (is_dir($path)) {
                $validatedLangs[] = $lang;
            } else {
                $unsupportedLangs[] = $lang;
            }
        }

        if (empty($validatedLangs)) {
            $this->error('No valid languages provided.');
            return;
        }

        if (!empty($unsupportedLangs)) {
            $this->warn('The following languages are not supported and will be skipped: ' . implode(', ', $unsupportedLangs));
        }

        $this->alert('Installing languages: ' . implode(', ', $validatedLangs));

        // Store validated languages in config for seeders to access
        config(['hashtagcms.install_languages' => $validatedLangs]);

        $this->info('> Seeding Tables...');

        // We run the seeders that support multi-language
        $seeders = [
            'LangsTableSeeder',
            'CountryLangsTableSeeder',
            'SitesTableSeeder',
            'CategoriesTableSeeder',
            'PagesTableSeeder',
            'StaticModuleContentsTableSeeder',
        ];

        foreach ($seeders as $seeder) {
            $this->info(">> Running $seeder...");
            Artisan::call('db:seed', [
                '--class' => "HashtagCms\\Database\\Seeds\\$seeder",
            ]);
        }

        $this->info('Languages installed successfully.');
    }
}
