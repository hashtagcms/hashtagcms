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
                            {dataWith? : To display data with some other tables}
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

    /** @var ScaffoldGenerator */
    protected $scaffold;

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

        // Initialise the ScaffoldGenerator with the resolved package base path
        $this->scaffold = new ScaffoldGenerator($this->files, $this->packageBasePath());

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

        $controller_name = Str::studly($this->name);

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
        $isInteractive = defined('STDIN') && $this->input->isInteractive();

        //#2: Question
        if (empty($this->dataFields)) {
            $this->dataFields = $isInteractive
                ? $this->ask('Please enter fields name (dataFields): You can write like: id, name, etc', '*')
                : '*';
        }

        //#3: Question
        if (empty($this->dataSource)) {
            $this->dataSource = $isInteractive
                ? $this->ask('Please enter model name: (dataSource): ', Str::studly($this->name))
            : Str::studly($this->name);
        }

        $this->dataSource = Str::studly(Str::singular($this->dataSource));

        $dataWith = $this->getDataWith($this->dataSource);

        if ($dataWith == null || $dataWith == 'null') {
            $dataWith = null;
            $this->setDataWith($this->dataSource, $dataWith);
        }

        if (empty($dataWith) && $dataWith != null) {
            if ($isInteractive) {
                $data = $this->ask("Any relation with another model? (dataWith) type 'null' if no relation with other model. ie. ", 'lang');
                $data = (strtolower($data) == 'null' || empty(trim($data))) ? null : $data;
                $this->setDataWith($this->dataSource, $data);
            }
        }

        $this->replaceControllerContext($controller_name);

        $this->info('Controller created successfully.');
    }

    protected function replaceControllerContext($name)
    {
        $controller_name = Str::studly($name) . 'Controller';
        $filename        = $this->currentSourceFile;
        $namespace       = $this->laravel->getNamespace();
        $dataSource      = Str::studly(Str::singular($this->dataSource));

        // ── field rendering ───────────────────────────────────────────
        $this->dataFields = str_replace(' ', '', $this->dataFields);
        $dataFields = $this->dataFields;

        if ($dataFields !== '*') {
            $raw        = explode(',', $this->dataFields);
            $dataFields = "['" . implode("','", $raw) . "']";
        } else {
            // Auto-derive from DB schema when caller passed '*'
            $dataFields = $this->generateDataFields($dataSource);
        }

        // ── dataWith ──────────────────────────────────────────────────
        $rawDataWith = $this->getDataWith($this->dataSource);
        if ($rawDataWith === null) {
            // No argument provided: auto-discover from DB schema
            $dataWith = $this->generateDataWith($dataSource);
        } elseif ($rawDataWith === '' || $rawDataWith === 'null' || $rawDataWith === '[]') {
            // Explicitly empty: []
            $dataWith = "[]";
        } else {
            // Comma-separated list provided
            $parts    = explode(',', $rawDataWith);
            $dataWith = "['" . implode("', '", $parts) . "']";
        }

        // ── related model imports ─────────────────────────────────────
        $useRelatedModels = $this->generateUseModels($dataSource, $namespace);

        // ── $bindDataWithAddEdit property ─────────────────────────────
        $bindDataWithAddEdit = $this->generateBindData($dataSource, $namespace);

        // ── store() save blocks ───────────────────────────────────────
        $saveDataBlock = $this->generateSaveDataBlock($dataSource);
        $langBlocks    = $this->generateLangDataBlock($dataSource);

        $replacements = [
            'namespace'            => $namespace,
            'controller_name'      => $controller_name,
            'dataFields'           => $dataFields,
            'dataSource'           => $dataSource,
            'dataSourceLower'      => strtolower($dataSource),
            'dataWith'             => $dataWith,
            'useRelatedModels'     => $useRelatedModels,
            'bindDataWithAddEdit'  => $bindDataWithAddEdit,
            'validationFields'     => $this->getValidationFields($dataSource),
            'saveDataBlock'        => $saveDataBlock,
            'langDataBlock'        => $langBlocks['langDataBlock'],
            'saveMethod'           => $langBlocks['saveMethod'],
            'saveMethodInsert'     => $langBlocks['saveMethodInsert'],
        ];

        // Use ScaffoldGenerator to replace tokens in the existing temp stub
        $content = $this->files->get($filename);
        $content = $this->scaffold->replaceTokens($content, $replacements);
        $this->files->put($filename, $content);

        $targetFileName = $this->getValidTarget($this->paths['targetDir'] . '/' . $controller_name . '.php', 'app');
        $this->files->copy($filename, $targetFileName);

        info('Controller created  ' . $controller_name);
    }

    /**
     * Copy views — uses ScaffoldGenerator for cleaner stub → target write.
     * Computes phpVarsBlock and formFieldsBlock from the live DB table structure.
     */
    private function copyViews($name)
    {
        $name = strtolower($name);

        $adminBaseResourceFolder = htcms_admin_base_resource();
        $vendor = $this->paths['vendor'];

        $viewDir    = $vendor . '/' . $adminBaseResourceFolder . '/' . strtolower($name);
        $viewFolder = resource_path('views/' . $viewDir);

        $this->scaffold->ensureDirectory($viewFolder);

        $this->alert('Creating views...');

        // Compute view-field blocks from the live DB table schema
        $dataSourceModel = Str::studly(Str::singular($this->dataSource ?? $name));

        $replacements = [
            'moduleName'     => Str::headline($name),
            'phpVarsBlock'   => $this->generateViewPhpVars($dataSourceModel),
            'formFieldsBlock' => $this->generateViewFormFields($dataSourceModel),
        ];

        foreach ($this->views as $stubFile => $targetFile) {
            $stubPath   = $this->scaffold->getStubPath($this->paths['sourceDir'] . '/views/' . $stubFile);
            $targetPath = resource_path('views/' . $vendor . '/' . $adminBaseResourceFolder . "/$name/" . $targetFile);

            $written = $this->scaffold->generate($stubPath, $targetPath, $replacements, false);
            $this->info($written ? 'Copied: ' . $targetPath : 'Skipped (already exists): ' . $targetPath);
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
