<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * CmsModuleValidatorCommand
 *
 * Scaffolds a Laravel FormRequest validator class for an admin panel module,
 * pre-populated with validation rules from the database table schema.
 *
 * Usage:
 *   php artisan cms:validator {name} {validatorName?}
 *
 * Called automatically by CmsmoduleController::createModule() when creating files.
 */
class CmsModuleValidatorCommand extends Command
{
    use Common;

    protected $signature = 'cms:validator
                            {name : The table/model name to generate rules from}
                            {validatorName? : The class name for the FormRequest (defaults to {Name}Request)}
                            ';

    protected $description = '[#CMS]: Scaffold a FormRequest validator class for an admin module';

    protected $files;

    /** @var ScaffoldGenerator */
    protected $scaffold;

    private $paths = [
        'sourceDir'  => 'hashtagcms/cmsmodule',
        'sourceFile' => 'index.ms',
        'tempDir'    => 'storage/temp',
        'targetDir'  => 'Http/Requests/Admin',
        'vendor'     => 'vendor/hashtagcms',
    ];

    private $currentSourceFile;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name          = Str::studly(Str::singular($this->argument('name')));
        $validatorName = $this->argument('validatorName') ?? ($name . 'Request');
        $validatorName = Str::studly($validatorName);

        $this->scaffold = new ScaffoldGenerator($this->files, $this->packageBasePath());

        $targetPath = $this->getValidTarget($this->paths['targetDir'] . '/' . $validatorName . '.php', 'app');

        if ($this->files->exists($targetPath)) {
            $this->warn("Validator already exists: $targetPath — skipping.");
            return;
        }

        $stubPath = $this->scaffold->getStubPath($this->paths['sourceDir'] . '/validator/validator.ms');

        if (!$this->files->exists($stubPath)) {
            $this->error("Validator stub not found at: $stubPath");
            return;
        }

        $replacements = [
            'namespace'         => $this->laravel->getNamespace(),
            'validator_name'    => $validatorName,
            'validation_fields' => $this->getValidationFields($name),
        ];

        $written = $this->scaffold->generate($stubPath, $targetPath, $replacements, false);

        if ($written) {
            $this->info("Validator created: $targetPath");
            info("Validator created: $validatorName");
        } else {
            $this->warn("Validator skipped (already exists): $targetPath");
        }
    }
}
