<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;

class CmsValidatorCommand extends Command
{
    use Common;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:validation
                            {name? : Name of the table}                                                        
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Output validations fields from a table';

    protected $name;

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
        $this->name = $this->argument('name');

        $data = $this->createInit($this->name);

        return $data;
    }

    public function createInit($name)
    {

        //#1. Name?
        if (empty($name)) {
            $name = $this->ask('Please enter table name...');
        }

        $validation_fields = $this->getValidationFields($name);

        $this->info('');
        $this->info('');
        $this->info('['.$validation_fields.']');
        $this->info('');
        $this->info('');

    }
}
