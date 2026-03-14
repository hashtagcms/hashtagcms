<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CmsModuleModelCommand extends Command
{
    use Common;

    protected $name;

    protected $methods = [];

    protected $files;

    protected $currentSourceFile;

    private $paths = [
        'sourceDir' => 'hashtagcms/cmsmodule',
        'sourceFile' => 'index.ms',
        'tempDir' => 'storage/temp',
        'targetDir' => 'Models',
        'vendor' => 'vendor/hashtagcms',
    ];

    private $hasLangScope = [];

    /** @var ScaffoldGenerator */
    protected $scaffold;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:model
                            {name : name of the model}
                            {methods? : methods as methodName,Relation,DataSource,useLangScope~}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Create Admin Model';

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
        $methods = $this->argument('methods');

        $this->name = Str::studly(Str::singular($this->name));

        // Initialise the ScaffoldGenerator with the resolved package base path
        $this->scaffold = new ScaffoldGenerator($this->files, $this->packageBasePath());

        $this->createModel($this->name, $methods);
    }

    /**
     * @param string $methods
     */
    private function createModel($name, $methods = '', $fixModelName = true)
    {
        $name = ($fixModelName) ? Str::studly(Str::singular($name)) : $name;

        $isExists = $this->isModelExists($name);

        if (! $isExists) {

            $this->alert("Creating Model $name");

            //Let's check if there is any relation/method - separated with ~
            if (empty($methods)) {
                $this->methods[$name] = null;
            } else {
                $this->methods[$name] = ($methods == '') ? '' : explode('~', $methods);
            }

            $fileName = $this->init('model');

            $this->replaceModelContext($name, $fileName, $fixModelName);

            $this->clean($fileName);

            $this->info("'$name' created successfully.");

            $this->line('--------------------------------');

            $this->createExtraModels($name);

        } else {

            $this->error("$name model already exists");

        }
    }

    private function replaceModelContext($name, $currentFileName, $fixModelName = true)
    {
        $relationData = $this->getRelationData($name, $fixModelName);
        $langData     = $this->getLangScope($name);

        $replacements = [
            'namespace'       => $this->laravel->getNamespace(),
            'model'           => $name,
            'relationMethods' => $relationData['useMethods'],
            'useModels'       => $relationData['useModels'],
            'useLangScope'    => $langData['useScope'],
            'langScopeBoot'   => $langData['useMethod'],
        ];

        // Use ScaffoldGenerator to replace tokens in the existing temp stub file
        $content = $this->files->get($currentFileName);
        $content = $this->scaffold->replaceTokens($content, $replacements);
        $this->files->put($currentFileName, $content);

        $targetFileName = $this->getValidTarget($this->paths['targetDir'] . '/' . $name . '.php', 'app');
        $this->files->copy($currentFileName, $targetFileName);
    }

    /**
     * @return mixed
     */
    private function getRelationData($name, $fixModelName = true)
    {
        $methods = $this->methods[$name] ?? null;

        $namespace = $this->laravel->getNamespace();

        $useModels = '';
        $useMethods = '';

        $extraModels = [];

        if ($methods != null) {
            foreach ($methods as $key => $val) {
                $current = explode(',', $val);
                $method      = $current[0];
                $relation    = $current[1];
                $source      = $current[2];
                $hasLangScope = $current[3] ?? false;

                $dataSource = $source;

                $useMethods .= "\n
    public function $method() {
        return \$this->$relation($dataSource::class);
    }\n";

                $useModels .= "use $namespace\\Models\\" . $source . ";\n";

                //extra models
                $extraModels[] = $source;

                //Add lang scope flag for relation model
                if ($hasLangScope != false) {
                    $this->setLangScope($source);
                }
            }
        }

        $data['useModels']    = $useModels;
        $data['useMethods']   = $useMethods;
        $data['extraModels']  = $extraModels;

        return $data;
    }

    private function createExtraModels($name)
    {
        $relationData = $this->getRelationData($name);
        $models = $relationData['extraModels'];
        if (count($models) > 0) {
            foreach ($models as $key => $val) {
                $modelName = $val;
                if (! $this->isModelExists($modelName)) {
                    $this->createModel($modelName, '', false);
                }
            }
        }
    }

    /**
     * Set Lang Scope
     */
    private function setLangScope($model)
    {
        $model = strtolower($model);
        $this->hasLangScope[$model] = $model;
    }

    /**
     * Get Lang Scope
     *
     * @return mixed
     */
    private function getLangScope($model)
    {
        $model = strtolower($model);
        $data['useScope'] = '';
        $data['useMethod'] = '';

        if (isset($this->hasLangScope[$model])) {
            $data['useScope'] = "use HashtagCms\Core\Scopes\LangScope;";
            $data['useMethod'] = 'protected static function boot() {

        parent::boot();
        static::addGlobalScope(new LangScope);

    }';
        }

        return $data;
    }
}
