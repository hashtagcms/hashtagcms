<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CmsModuleControllerCommand extends Command
{
    use Common;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:controller
                            {name : The name of a controller}
                            {dataFields? : Fields to be displayed in listing}
                            {dataSource? : To fetch the data from this model}                            
                            {dataWith? : To dispaly data with some other tables}
                            {actionFields? : Action fields. ie. ["edit", "delete"]}
                            {bindDataWithAddEdit? : These data will be available at the time of add/edit}                            
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Create a controller for admin panel';

    protected $files;

    protected $name;

    protected $dataFields;

    protected $dataSource;

    protected $dataWith;

    protected $bindDataWithAddEdit;

    private $paths = [
        'sourceDir' => 'hashtagcms/cmsmodule',
        'sourceFile' => 'index.ms',
        'tempDir' => 'storage/temp',
        'targetDir' => 'Http/Controllers/Admin',
        'vendor' => 'vendor/hashtagcms',
    ];

    private $views = ['addedit.ms' => 'addedit.blade.php'];

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
        $this->name = $this->argument('name');

        $this->dataFields = $this->argument('dataFields');

        $this->dataSource = $this->argument('dataSource');

        $dataWith = $this->argument('dataWith');

        $this->setDataWith($this->dataSource, $dataWith);

        $this->bindDataWithAddEdit = $this->argument('bindDataWithAddEdit');

        $this->init('controller');

        $data = $this->createController($this->name);

        $this->clean($this->currentSourceFile);

        return $data;

    }

    /**
     * Create controller
     */
    public function createController($controller_name)
    {

        //#1. Name?
        if (empty($controller_name)) {
            $controller_name = $this->ask('Please enter controller name...');
        }

        $controller_name = Str::title($this->name);

        $this->alert("Creating {$controller_name}Controller");

        if (! $this->isAdminControllerExists($controller_name)) {
            //Ask more question
            $this->askQuestionAndCreateController($controller_name);

        } else {

            $this->error('Controller already exist...');

            $answer = $this->confirmMessage('Do you want to overwrite it?');

            if ($answer == 'Yes') {
                //Ask more question
                $this->askQuestionAndCreateController($controller_name);
            }
        }

        //Copy views
        $this->copyViews($controller_name);

    }

    protected function askQuestionAndCreateController($controller_name, $isExist = false)
    {

        //#2: Question
        if (empty($this->dataFields)) {
            $this->dataFields = $this->ask('Please enter fields name (dataFields): You can write like: id, name, etc', '*');
        }

        //$this->dataFields = explode(",", $this->dataFields);
        //$this->dataFields = explode(",", $this->dataFields);

        //#3: Question
        if (empty($this->dataSource)) {
            $this->dataSource = $this->ask('Please enter model name: (dataSource): ', Str::title($this->name));
        }

        $this->dataSource = Str::title($this->dataSource);

        $dataWith = $this->getDataWith($this->dataSource);

        if ($dataWith == null || $dataWith == 'null') {
            $dataWith = null;
            $this->setDataWith($this->dataSource, $dataWith);
        }

        if (empty($dataWith) && $dataWith != null) {
            $data = $this->ask("Any relation with another model? (dataWith) type 'null' if no relation with other model. ie. ", 'lang');
            $data = (strtolower($data) == 'null' || empty(trim($data))) ? null : $data;
            $this->setDataWith($this->dataSource, $data);

        }

        $isExist = $this->isAdminControllerExists($controller_name);

        $this->replaceControllerContext($controller_name);

        $this->info('Controller created successfully.');

    }

    protected function replaceControllerContext($name)
    {

        $controller_name = Str::title($name).'Controller';

        $filename = $this->currentSourceFile;

        //Search pattern
        $patterns = [];
        $patterns['namespace'] = '/{{namespace}}/';
        $patterns['controller_name'] = '/{{controller_name}}/';
        $patterns['dataFields'] = '/{{dataFields}}/';
        $patterns['dataSource'] = '/{{dataSource}}/';
        $patterns['dataWith'] = '/{{dataWith}}/';
        $patterns['actionFields'] = '/{{actionFields}}/';
        $patterns['bindDataWithAddEdit'] = '/{{bindDataWithAddEdit}}/';

        $patterns['validationFields'] = '/{{validationFields}}/';

        //replacement pattern
        $replacements = [];

        $replacements['namespace'] = $this->laravel->getNamespace();
        $replacements['controller_name'] = $controller_name;

        $this->dataFields = str_replace(' ', '', $this->dataFields);

        $dataFields = $this->dataFields;

        if ($dataFields != '*') {
            $dataFields = explode(',', $this->dataFields);
            $dataFields = "['".implode("','", $dataFields)."']";
        } else {
            $dataFields = "'*'";
        }

        $replacements['dataFields'] = $dataFields;
        $replacements['dataSource'] = Str::title($this->dataSource);

        $dataWith = $this->getDataWith($this->dataSource);

        if (! empty($dataWith)) {
            $dataWith = explode(',', $dataWith);
            $dataWith = "['".implode("','", $dataWith)."']";
        } else {
            $dataWith = "''";
        }

        $replacements['dataWith'] = $dataWith;

        $replacements['actionFields'] = 'array("edit", "delete")';
        $replacements['bindDataWithAddEdit'] = 'array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                            "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"))';

        $replacements['validationFields'] = $this->getValidationFields($name);

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

    }

    /**
     * Copy views
     */
    private function copyViews($name)
    {
        $name = strtolower($name); //Controller name

        $adminBaseResourceFolder = htcms_admin_base_resource();
        $vendor = $this->paths['vendor'];

        $viewDir = $vendor.'/'.$adminBaseResourceFolder.'/'.strtolower($name);
        $viewFolder = resource_path('views/'.$viewDir);

        if (! $this->files->isDirectory($viewFolder)) {
            $this->files->makeDirectory($viewFolder, 0755, true, true);
        }

        $this->alert('creating views...');
        foreach ($this->views as $s => $t) {
            $source = $this->getValidSourceFileName($this->paths['sourceDir'].'/views/'.$s);
            $target = resource_path('views/'.$vendor.'/'.$adminBaseResourceFolder."/$name/".$t);
            if (! $this->files->exists($target)) {
                $this->files->copy($source, $target);
                $this->info('Copied: '.$target);
            }
        }

    }

    /********* common ***************/

    protected function setDataWith($model_name, $value)
    {
        $this->dataWith[strtolower($model_name)] = $value;
    }

    protected function getDataWith($model_name)
    {
        return $this->dataWith[strtolower($model_name)] ?? null;
    }
}
