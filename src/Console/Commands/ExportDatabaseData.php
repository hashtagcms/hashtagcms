<?php

namespace HashtagCms\Console\Commands;

use HashtagCms\Database\Seeds\BaseSeeder;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ExportDatabaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:exportdata 
                            {--tables= : Comma-separated list of tables to export (default: all)}
                            {--exclude= : Comma-separated list of tables to exclude}
                            {--limit= : Maximum number of records per table (default: all)}
                            {--output=database/seeders : Output directory for seed files}
                            {--unique-columns= : JSON mapping of table names to unique column arrays for duplicate checking}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Export database data to seed files';

    /**
     * Tables that should be excluded by default
     *
     * @var array
     */
    protected $defaultExcludedTables = [
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'failed_jobs',
        'jobs',
        'cache',
        'sessions',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database export...');

        // Get tables to export
        $tables = $this->getTablesToExport();
        
        if (empty($tables)) {
            $this->error('No tables found to export.');
            return 1;
        }

        $this->info('Found ' . count($tables) . ' tables to export.');

        // Create output directory if it doesn't exist
        $outputDir = $this->option('output');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Make sure BaseSeeder exists
        $this->ensureBaseSeederExists($outputDir);
        
        // Create DatabaseSeeder file that will call all other seeders
        $this->generateDatabaseSeeder($outputDir, $tables);

        // Export each table
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $table) {
            $this->exportTable($table, $outputDir);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Database export completed successfully!');
        $this->info('Run "php artisan db:seed" to import the data.');

        return 0;
    }

    /**
     * Get the list of tables to export
     *
     * @return array
     */
    protected function getTablesToExport()
    {
        // Get all tables
        $allTables = $this->getAllTables();

        // Get tables from option
        $optionTables = $this->option('tables');
        $tables = $optionTables ? explode(',', $optionTables) : $allTables;

        // Get excluded tables
        $excludedTables = $this->getExcludedTables();

        // Filter out excluded tables
        return array_filter($tables, function ($table) use ($excludedTables) {
            return !in_array($table, $excludedTables);
        });
    }

    /**
     * Get all tables in the database
     *
     * @return array
     */
    protected function getAllTables()
    {
        $tables = [];
        
        // Get the current connection
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        // Different query based on driver
        if ($driver === 'mysql') {
            $dbName = config("database.connections.{$connection}.database");
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$dbName}'");
            $tables = array_map(function ($table) {
                // Get the first property from the object (column name might vary between MySQL versions)
                $properties = get_object_vars($table);
                return reset($properties);
            }, $tables);
        } elseif ($driver === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
            $tables = array_map(function ($table) {
                return $table->tablename;
            }, $tables);
        } elseif ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tables = array_map(function ($table) {
                return $table->name;
            }, $tables);
        } else {
            $this->error("Unsupported database driver: {$driver}");
        }
        
        return $tables;
    }

    /**
     * Get the list of tables to exclude
     *
     * @return array
     */
    protected function getExcludedTables()
    {
        $excludedTables = $this->defaultExcludedTables;
        
        // Add user-specified excluded tables
        $optionExcludedTables = $this->option('exclude');
        if ($optionExcludedTables) {
            $excludedTables = array_merge($excludedTables, explode(',', $optionExcludedTables));
        }
        
        return $excludedTables;
    }

    /**
     * Export a table to a seed file
     *
     * @param string $table
     * @param string $outputDir
     * @return void
     */
    protected function exportTable($table, $outputDir)
    {
        try {
            // Get table data with chunking for large tables
            $limit = $this->option('limit');
            $batchSize = 1000; // Process 1000 records at a time to avoid memory issues
            
            // First check if table has any data
            $count = DB::table($table)->count();
            
            if ($count === 0) {
                $this->line("Table {$table} is empty. Skipping...");
                return;
            }
            
            // If limit is set, adjust count
            if ($limit && (int)$limit < $count) {
                $count = (int)$limit;
            }
            
            // Create seeder class name
            $className = $this->createSeederClassName($table);
            $filePath = "{$outputDir}/{$className}.php";
            
            // Start generating the seeder file
            $columns = Schema::getColumnListing($table);
            $timestamp = date('Y-m-d H:i:s');
            
            // Get unique columns for this table if specified
            $uniqueColumns = $this->getUniqueColumnsForTable($table);
            
            // Create the seeder file header
            $content = "<?php\n\n";
            $content .= "namespace Database\\Seeders;\n\n";
            $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
            $content .= "/**\n";
            $content .= " * Auto-generated seeder for the {$table} table\n";
            $content .= " * Generated on: {$timestamp}\n";
            $content .= " */\n";
            $content .= "class {$className} extends BaseSeeder\n";
            $content .= "{\n";
            $content .= "    /**\n";
            $content .= "     * Run the database seeds.\n";
            $content .= "     *\n";
            $content .= "     * @return void\n";
            $content .= "     */\n";
            $content .= "    public function run()\n";
            $content .= "    {\n";
            
            // Process in batches to handle large tables
            $totalBatches = ceil($count / $batchSize);
            
            for ($batch = 0; $batch < $totalBatches; $batch++) {
                $offset = $batch * $batchSize;
                $currentBatchSize = min($batchSize, $count - $offset);
                
                $content .= "        \$this->seedBatch{$batch}();\n";
            }
            
            $content .= "    }\n\n";
            
            // Write the initial part of the file
            file_put_contents($filePath, $content);
            
            // Process each batch and append to the file
            for ($batch = 0; $batch < $totalBatches; $batch++) {
                $offset = $batch * $batchSize;
                $currentBatchSize = min($batchSize, $count - $offset);
                
                $query = DB::table($table);
                $query->offset($offset)->limit($currentBatchSize);
                $data = $query->get();
                
                $batchContent = "    /**\n";
                $batchContent .= "     * Seed batch {$batch} of data\n";
                $batchContent .= "     *\n";
                $batchContent .= "     * @return void\n";
                $batchContent .= "     */\n";
                $batchContent .= "    private function seedBatch{$batch}()\n";
                $batchContent .= "    {\n";
                
                if ($data->isNotEmpty()) {
                    // Get unique columns for this table
                $uniqueColumnsStr = $uniqueColumns ? "['" . implode("', '", $uniqueColumns) . "']" : '[]';
                
                $batchContent .= "        \$this->insertOrSkip('{$table}', [\n";
                    
                    foreach ($data as $row) {
                        $batchContent .= "            [\n";
                        
                        foreach ($columns as $column) {
                            $value = $row->$column ?? null;
                            
                            if (is_null($value)) {
                                $batchContent .= "                '{$column}' => null,\n";
                            } elseif (is_bool($value)) {
                                $boolValue = $value ? 'true' : 'false';
                                $batchContent .= "                '{$column}' => {$boolValue},\n";
                            } elseif (is_numeric($value)) {
                                $batchContent .= "                '{$column}' => {$value},\n";
                            } elseif (is_string($value)) {
                                $escapedValue = str_replace("'", "\\'", $value);
                                $batchContent .= "                '{$column}' => '{$escapedValue}',\n";
                            } else {
                                // For complex types, use JSON
                                $jsonValue = json_encode($value);
                                $escapedValue = str_replace("'", "\\'", $jsonValue);
                                $batchContent .= "                '{$column}' => json_decode('{$escapedValue}', true),\n";
                            }
                        }
                        
                        $batchContent .= "            ],\n";
                    }
                    
                    $batchContent .= "        ], {$uniqueColumnsStr});\n";
                }
                
                $batchContent .= "    }\n\n";
                
                // Append this batch to the file
                file_put_contents($filePath, $batchContent, FILE_APPEND);
            }
            
            // Close the class
            file_put_contents($filePath, "}", FILE_APPEND);
            
        } catch (\Exception $e) {
            $this->error("Error exporting table {$table}: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Create seeder class name from table name
     *
     * @param string $table
     * @return string
     */
    protected function createSeederClassName($table)
    {
        return Str::studly(Str::singular($table)) . 'TableSeeder';
    }
    
    /**
     * Get unique columns for a table from the --unique-columns option
     *
     * @param string $table
     * @return array
     */
    protected function getUniqueColumnsForTable($table)
    {
        $uniqueColumnsOption = $this->option('unique-columns');
        
        if (empty($uniqueColumnsOption)) {
            // Default unique columns for common tables
            $defaults = [
                'users' => ['email'],
                'roles' => ['name'],
                'permissions' => ['name'],
                'countries' => ['code'],
                'languages' => ['code'],
                'country_langs' => ['country_id', 'lang_id'],
                'category_langs' => ['category_id', 'lang_id'],
                'sites' => ['domain'],
                'currencies' => ['code'],
            ];
            
            return $defaults[$table] ?? $this->guessUniqueColumns($table);
        }
        
        try {
            $uniqueColumnsMap = json_decode($uniqueColumnsOption, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->warn("Invalid JSON in --unique-columns option: " . json_last_error_msg());
                return $this->guessUniqueColumns($table);
            }
            
            return $uniqueColumnsMap[$table] ?? $this->guessUniqueColumns($table);
            
        } catch (\Exception $e) {
            $this->warn("Error parsing --unique-columns option: {$e->getMessage()}");
            return $this->guessUniqueColumns($table);
        }
    }
    
    /**
     * Try to guess unique columns for a table based on indexes and common column names
     *
     * @param string $table
     * @return array
     */
    protected function guessUniqueColumns($table)
    {
        // Common primary/unique key patterns
        if (Schema::hasColumn($table, 'id')) {
            return ['id'];
        }
        
        // Check for common unique fields
        $commonUniqueFields = ['email', 'username', 'code', 'slug', 'uuid'];
        foreach ($commonUniqueFields as $field) {
            if (Schema::hasColumn($table, $field)) {
                return [$field];
            }
        }
        
        // Check for composite keys with _id suffix
        $columns = Schema::getColumnListing($table);
        $idColumns = array_filter($columns, function($column) {
            return Str::endsWith($column, '_id');
        });
        
        if (!empty($idColumns)) {
            return $idColumns;
        }
        
        return [];
    }
    
    /**
     * Ensure the BaseSeeder class exists
     * 
     * This method is now just checking if the BaseSeeder exists in the application's seeders directory
     * If not, it creates a simple extension of the package BaseSeeder
     *
     * @param string $outputDir
     * @return void
     */
    protected function ensureBaseSeederExists($outputDir)
    {
        $baseSeederPath = "{$outputDir}/BaseSeeder.php";
        
        if (file_exists($baseSeederPath)) {
            $this->info("BaseSeeder already exists at {$baseSeederPath}");
            return;
        }
        
        $this->info("Creating BaseSeeder at {$baseSeederPath}");
        
        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use HashtagCms\\Database\\Seeds\\BaseSeeder as PackageBaseSeeder;\n\n";
        $content .= "/**\n";
        $content .= " * Base seeder class extending the HashtagCMS package BaseSeeder\n";
        $content .= " */\n";
        $content .= "abstract class BaseSeeder extends PackageBaseSeeder\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Check if we should verify existing records before inserting\n";
        $content .= "     *\n";
        $content .= "     * @return bool\n";
        $content .= "     */\n";
        $content .= "    protected function shouldCheckExisting(): bool\n";
        $content .= "    {\n";
        $content .= "        return getenv('SEED_CHECK_EXISTING') === 'true';\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Insert data into a table, optionally checking for existing records first\n";
        $content .= "     *\n";
        $content .= "     * @param string \$table Table name\n";
        $content .= "     * @param array \$data Data to insert\n";
        $content .= "     * @param array \$uniqueColumns Columns to use for checking existing records\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    protected function insertOrSkip(string \$table, array \$data, array \$uniqueColumns = []): void\n";
        $content .= "    {\n";
        $content .= "        if (empty(\$data)) {\n";
        $content .= "            return;\n";
        $content .= "        }\n\n";
        $content .= "        // If not checking for existing records, just insert all data\n";
        $content .= "        if (!\$this->shouldCheckExisting() || empty(\$uniqueColumns)) {\n";
        $content .= "            DB::table(\$table)->insert(\$data);\n";
        $content .= "            return;\n";
        $content .= "        }\n\n";
        $content .= "        // Process each record individually to check for duplicates\n";
        $content .= "        foreach (\$data as \$record) {\n";
        $content .= "            \$query = DB::table(\$table);\n";
        $content .= "            \n";
        $content .= "            // Build query to find existing records\n";
        $content .= "            foreach (\$uniqueColumns as \$column) {\n";
        $content .= "                if (isset(\$record[\$column])) {\n";
        $content .= "                    \$query->where(\$column, \$record[\$column]);\n";
        $content .= "                }\n";
        $content .= "            }\n";
        $content .= "            \n";
        $content .= "            // If record doesn't exist, insert it\n";
        $content .= "            if (\$query->count() === 0) {\n";
        $content .= "                DB::table(\$table)->insert([\$record]);\n";
        $content .= "            }\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Insert data into a table, updating if record exists based on unique columns\n";
        $content .= "     *\n";
        $content .= "     * @param string \$table Table name\n";
        $content .= "     * @param array \$data Data to insert\n";
        $content .= "     * @param array \$uniqueColumns Columns to use for checking existing records\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    protected function insertOrUpdate(string \$table, array \$data, array \$uniqueColumns = []): void\n";
        $content .= "    {\n";
        $content .= "        if (empty(\$data) || empty(\$uniqueColumns)) {\n";
        $content .= "            return;\n";
        $content .= "        }\n\n";
        $content .= "        // If not checking for existing records, just insert all data\n";
        $content .= "        if (!\$this->shouldCheckExisting()) {\n";
        $content .= "            DB::table(\$table)->insert(\$data);\n";
        $content .= "            return;\n";
        $content .= "        }\n\n";
        $content .= "        // Process each record individually\n";
        $content .= "        foreach (\$data as \$record) {\n";
        $content .= "            \$query = DB::table(\$table);\n";
        $content .= "            \n";
        $content .= "            // Build query to find existing records\n";
        $content .= "            foreach (\$uniqueColumns as \$column) {\n";
        $content .= "                if (isset(\$record[\$column])) {\n";
        $content .= "                    \$query->where(\$column, \$record[\$column]);\n";
        $content .= "                }\n";
        $content .= "            }\n";
        $content .= "            \n";
        $content .= "            // If record exists, update it; otherwise insert\n";
        $content .= "            \$existingRecord = \$query->first();\n";
        $content .= "            if (\$existingRecord) {\n";
        $content .= "                \$query->update(\$record);\n";
        $content .= "            } else {\n";
        $content .= "                DB::table(\$table)->insert([\$record]);\n";
        $content .= "            }\n";
        $content .= "        }\n";
        $content .= "    }\n";
        $content .= "}";
        
        file_put_contents($baseSeederPath, $content);
    }

    /**
     * Generate the DatabaseSeeder.php file
     *
     * @param string $outputDir
     * @param array $tables
     * @return void
     */
    protected function generateDatabaseSeeder($outputDir, $tables)
    {
        $this->info('Generating DatabaseSeeder.php');
        
        // Get all seeder files in the output directory
        $seeders = [];
        foreach (glob("{$outputDir}/*TableSeeder.php") as $seederFile) {
            $seederClass = pathinfo($seederFile, PATHINFO_FILENAME);
            $seeders[] = $seederClass;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n\n";
        $content .= "/**\n";
        $content .= " * Auto-generated database seeder\n";
        $content .= " * Generated on: {$timestamp}\n";
        $content .= " */\n";
        $content .= "class DatabaseSeeder extends Seeder\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Seed the application's database.\n";
        $content .= "     */\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        
        // Core tables first
        $content .= "        // Core tables first (based on dependencies)\n";
        $coreTables = ['SiteTableSeeder', 'LangTableSeeder', 'CountryTableSeeder', 'CurrencyTableSeeder', 
                      'ZoneTableSeeder', 'RoleTableSeeder', 'UserTableSeeder', 'PermissionTableSeeder'];
        
        foreach ($coreTables as $seeder) {
            if (in_array($seeder, $seeders)) {
                $content .= "        \$this->call({$seeder}::class);\n";
            }
        }
        
        // Relationship tables
        $content .= "\n        // Relationships and secondary tables\n";
        $relationshipTables = ['SiteLangTableSeeder', 'LangSiteTableSeeder', 'CountryLangTableSeeder', 
                             'CountrySiteTableSeeder', 'CurrencySiteTableSeeder', 'SiteZoneTableSeeder', 
                             'SitePropTableSeeder', 'RoleUserTableSeeder', 'PermissionRoleTableSeeder'];
        
        foreach ($relationshipTables as $seeder) {
            if (in_array($seeder, $seeders)) {
                $content .= "        \$this->call({$seeder}::class);\n";
            }
        }
        
        // CMS specific tables
        $content .= "\n        // CMS specific tables\n";
        $cmsTables = ['CmsModuleTableSeeder', 'CategoryTableSeeder', 'CategoryLangTableSeeder', 'CategorySiteTableSeeder'];
        
        foreach ($cmsTables as $seeder) {
            if (in_array($seeder, $seeders)) {
                $content .= "        \$this->call({$seeder}::class);\n";
            }
        }
        
        // Additional tables with conditional checks
        $content .= "\n        // Additional tables if they exist\n";
        $additionalTables = [
            'ModuleTableSeeder', 'ModuleSiteTableSeeder', 'PageTableSeeder', 'PageLangTableSeeder',
            'HookTableSeeder', 'HookSiteTableSeeder', 'ThemeTableSeeder', 'StaticModuleContentTableSeeder',
            'StaticModuleContentLangTableSeeder', 'PlatformTableSeeder', 'PlatformSiteTableSeeder',
            'SocialNetworkTableSeeder', 'TagTableSeeder', 'GalleryTableSeeder', 'GalleryTagTableSeeder',
            'InviteTableSeeder', 'InviteLimitTableSeeder', 'CityTableSeeder'
        ];
        
        foreach ($additionalTables as $seeder) {
            if (in_array($seeder, $seeders)) {
                $content .= "        if (class_exists('Database\\\\Seeders\\\\{$seeder}')) {\n";
                $content .= "            \$this->call({$seeder}::class);\n";
                $content .= "        }\n\n";
            }
        }
        
        // Add any remaining seeders that weren't in our predefined lists
        $allPredefined = array_merge($coreTables, $relationshipTables, $cmsTables, $additionalTables);
        $remaining = array_diff($seeders, $allPredefined);
        
        if (!empty($remaining)) {
            $content .= "\n        // Project-specific tables\n";
            foreach ($remaining as $seeder) {
                $content .= "        if (class_exists('Database\\\\Seeders\\\\{$seeder}')) {\n";
                $content .= "            \$this->call({$seeder}::class);\n";
                $content .= "        }\n\n";
            }
        }
        
        $content .= "    }\n";
        $content .= "}\n";
        
        $filePath = "{$outputDir}/DatabaseSeeder.php";
        file_put_contents($filePath, $content);
    }

    /**
     * Sort tables by dependencies to handle foreign key constraints
     *
     * @param array $tables
     * @return array
     */
    protected function sortTablesByDependencies($tables)
    {
        // This is a simplified approach - in a real-world scenario,
        // you would analyze foreign key constraints and build a dependency graph
        
        // Tables that should be seeded first (typically lookup tables)
        $priorityTables = [
            'sites',            
            'langs',
            'platforms',
            'hooks',
            'zones',
            'countries',            
            'cities',            
            'currencies',
            'users',
            'roles',
            'permissions'            
        ];
        
        // Sort tables by putting priority tables first
        $sortedTables = [];
        
        // First add priority tables that exist in our list
        foreach ($priorityTables as $table) {
            if (in_array($table, $tables)) {
                $sortedTables[] = $table;
            }
        }
        
        // Then add remaining tables
        foreach ($tables as $table) {
            if (!in_array($table, $sortedTables)) {
                $sortedTables[] = $table;
            }
        }
        
        return $sortedTables;
    }
}
