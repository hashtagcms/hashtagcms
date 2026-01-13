<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use HashtagCms\Models\SiteProp;

class Cmsversion extends Command
{
    protected $composer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Display the version of Hashtag CMS';

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
        $this->info('Hashtag CMS version: '.config('hashtagcmscommon.version'));
    }


}
