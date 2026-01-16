<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;

class CmsShowInstructions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:showInstructions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Show installation instructions to the user';

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
        $this->info("
  _    _           _     _             _____ __  __  _____ 
 | |  | |         | |   | |           / ____|  \/  |/ ____|
 | |__| | __ _ ___| |__ | |_ __ _  __| |    | \  / | (___  
 |  __  |/ _` / __| '_ \| __/ _` |/ _` |    | |\/| |\___ \ 
 | |  | | (_| \__ \ | | | || (_| | (_| |____| |  | |____) |
 |_|  |_|\__,_|___/_| |_|\__\__,_|\__, |_____|_|  |_|_____/ 
                                   __/ |                    
                                  |___/                     
");
        $this->line("");
        $this->comment("Welcome to HashtagCMS Starter Kit!");
        $this->line("");
        $this->info("To complete the installation, please follow these steps:");
        $this->line("");
        $this->table(
            ['Step', 'Action', 'Command'],
            [
                ['1', 'Configure Database', 'Edit .env file and set DB_CONNECTION=mysql'],
                ['2', 'Install CMS', 'php artisan cms:install'],
            ]
        );
        $this->line("");
        $this->warn("Note: Make sure your database credentials are correct in the .env file before running the install command.");
        $this->line("");
    }
}
