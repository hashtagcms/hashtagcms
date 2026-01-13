<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CmsFrontendControllerCommand extends Command
{
    use Common;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Create auth controller for frontend';

    protected $files;

    private $paths = [
        'sourceDir' => 'hashtagcms/cmsmodule',
        'sourceFile' => 'login.ms',
        'tempDir' => 'storage/temp',
        'targetDir' => 'Http/Controllers',
        'vendor' => 'vendor/hashtagcms',
    ];

    private $currentSourceFile;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->init('fe');
        $this->createController('Login');
        $this->clean($this->currentSourceFile);

        //Change for other controller
        $this->paths['sourceFile'] = 'register.ms';
        $this->init('fe');
        $this->createController('Register');
        $this->clean($this->currentSourceFile);

        return 1;

    }

    /**
     * Create controller
     */
    public function createController($controller_name)
    {

        if (! $this->isControllerExists($controller_name)) {
            //Ask more question
            $this->replaceControllerContext($controller_name);
        }

    }

    protected function replaceControllerContext($name)
    {

        $controller_name = $name.'Controller';

        $filename = $this->currentSourceFile;

        //Search pattern
        $patterns = [];
        $patterns['namespace'] = '/{{namespace}}/';

        //replacement pattern
        $replacements = [];

        $replacements['namespace'] = $this->laravel->getNamespace();

        $replaced = preg_replace(
            $patterns,
            $replacements,
            file_get_contents($filename)
        );

        file_put_contents(
            $filename,
            $replaced,
            FILE_BINARY
        );

        $targetFileName = $this->getValidTarget($this->paths['targetDir'].'/'.$controller_name.'.php', 'app');
        $this->files->copy($filename, $targetFileName);

        info('Controller created  '.$controller_name);
        $this->alert($controller_name.' created successfully');

    }
}
