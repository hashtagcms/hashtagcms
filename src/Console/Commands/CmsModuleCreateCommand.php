<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\AdminBaseModel;

/**
 * CmsModuleCreateCommand  (cms:module)
 *
 * Interactive CLI wizard that collects everything needed to create
 * a new admin panel module — equivalent to the "System Module Definition"
 * form in the UI.
 */
class CmsModuleCreateCommand extends Command
{
    use Common;

    protected $signature = 'cms:module';

    protected $description = '[#CMS]: Interactive wizard to create a new admin panel module';

    private const RELATION_TYPES = [
        'hasMany',
        'belongsTo',
        'hasOne',
        'belongsToMany',
        'hasOneThrough',
        'hasManyThrough',
    ];

    public function handle(): int
    {
        $this->printBanner();

        // ─────────────────────────────────────────────
        // 1. Generate scaffolding?
        // ─────────────────────────────────────────────
        $this->line('');
        $generateFiles = $this->confirm(
            'Should the system automatically create the controller, model, view and validator files?',
            true
        );

        // ─────────────────────────────────────────────
        // 2. MODULE IDENTITY & PRESENTATION
        // ─────────────────────────────────────────────
        $this->section('MODULE IDENTITY & PRESENTATION');

        $moduleName = $this->askRequired('What is the name of this module? (e.g. BlogPost, Product, Zone)');
        $moduleName = Str::studly($moduleName);

        $subTitle = $this->ask(
            'Give it a short description or subtitle',
            'Manage ' . Str::headline($moduleName)
        );

        // ─────────────────────────────────────────────
        // 3. LOGIC & ARCHITECTURE
        // ─────────────────────────────────────────────
        $this->section('LOGIC & ARCHITECTURE');

        $defaultMapping = Str::kebab($moduleName);
        $controllerMapping = $this->ask(
            'What URL path should this module use in the admin panel? (e.g. blog-posts, products)',
            $defaultMapping
        );

        $parentId = $this->askParent();

        $iconCss = $this->ask(
            'Which FontAwesome icon should appear in the sidebar? (e.g. fa fa-list, fa fa-cogs)',
            'fa fa-list'
        );

        // ─────────────────────────────────────────────
        // 4. INTERFACE ROUTING
        // ─────────────────────────────────────────────
        $this->section('INTERFACE ROUTING');

        $listViewName = $this->ask(
            'Which template should be used for listing records?',
            'common/listing'
        );

        $editViewName = $this->ask(
            'Which template should be used for the add/edit form?',
            'addedit'
        );

        // ─────────────────────────────────────────────
        // 5. MASTER DATA CONFIGURATION
        // ─────────────────────────────────────────────
        $this->section('MASTER DATA CONFIGURATION');

        $dataSource = $this->askDataSource($moduleName);
        $relations  = $this->askRelations();

        // ─────────────────────────────────────────────
        // 6. Summary & Confirmation
        // ─────────────────────────────────────────────
        $this->printSummary([
            'Module Name'        => $moduleName,
            'Sub-Title'          => $subTitle,
            'Controller Mapping' => $controllerMapping,
            'Parent'             => $parentId ? "ID: $parentId" : 'Root Level',
            'Icon CSS'           => $iconCss,
            'Listing Template'   => $listViewName,
            'Editor Template'    => $editViewName,
            'Main Data Source'   => $dataSource,
            'Relations'          => empty($relations)
                ? 'None'
                : implode(', ', array_map(
                    fn($r) => $r['alias'] . ' (' . $r['type'] . ')' . ($r['langScope'] ? ' [LangScope ✓]' : ''),
                    $relations
                )),
            'Generate Files'     => $generateFiles ? 'Yes' : 'No',
        ]);

        if (! $this->confirm('Everything look correct? Proceed and create this module?', true)) {
            $this->warn('Cancelled. No changes were made.');
            return self::FAILURE;
        }

        // ─────────────────────────────────────────────
        // 7. Execute
        // ─────────────────────────────────────────────
        return $this->executeCreation(
            moduleName:        $moduleName,
            subTitle:          $subTitle,
            controllerMapping: $controllerMapping,
            parentId:          $parentId,
            iconCss:           $iconCss,
            listViewName:      $listViewName,
            editViewName:      $editViewName,
            dataSource:        $dataSource,
            relations:         $relations,
            generateFiles:     $generateFiles
        );
    }

    // ═══════════════════════════════════════════════════════════════
    // Prompts
    // ═══════════════════════════════════════════════════════════════

    /** Keep asking until a non-empty answer is given. */
    private function askRequired(string $question): string
    {
        do {
            $value = $this->ask($question);
            if (empty(trim((string) $value))) {
                $this->error('This field cannot be empty. Please enter a value.');
            }
        } while (empty(trim((string) $value)));

        return $value;
    }

    /** Choose an Organizational Parent module, or stay at root level. */
    private function askParent(): int
    {
        $modules = CmsModule::where('parent_id', 0)
            ->orderBy('position')
            ->pluck('name', 'id')
            ->toArray();

        if (count($modules) === 0) {
            $this->line('  (No parent modules found — defaulting to Root Level)');
            return 0;
        }

        $labels = array_merge(['No Parent (Root Level)'], array_values($modules));
        $keys   = array_merge([0], array_keys($modules));

        $selected = $this->choice(
            'Should this module sit inside another module? (choose a parent or leave at root level)',
            $labels,
            0
        );

        $index = array_search($selected, $labels, true);
        return (int) ($keys[$index] ?? 0);
    }

    /** Show live DB tables and let the user pick the main one. */
    private function askDataSource(string $moduleName): string
    {
        $tables = AdminBaseModel::getTables();

        if (empty($tables)) {
            $this->warn('Could not fetch database tables — please type the model name manually.');
            return Str::studly($this->ask('What is the main model/table name for this module?', $moduleName));
        }

        // Pre-select the table that most closely matches the module name
        $guess   = Str::snake(Str::plural($moduleName));
        $default = array_search($guess, $tables, true);
        $default = ($default !== false) ? (int) $default : 0;

        $chosen = $this->choice(
            'Which database table holds the main data for this module?',
            $tables,
            $default
        );

        // Convert table name → StudlyCase model name (e.g. blog_posts → BlogPost)
        return Str::studly(Str::singular($chosen));
    }

    /** Collect related model definitions one at a time. */
    private function askRelations(): array
    {
        $relations = [];

        $this->line('');
        $this->line('Does this module connect to other tables? (e.g. translations, categories, zones)');
        $this->line('Leave the name blank when you are done adding relations.');
        $this->line('');

        while (true) {
            $alias = $this->ask('  Name for this relation (e.g. lang, zone, category) — or press Enter to skip/finish');

            if (empty(trim((string) $alias))) {
                break;
            }

            $type = $this->choice(
                '  What kind of relationship is this?',
                self::RELATION_TYPES,
                'hasMany'
            );

            $defaultModel  = Str::studly($alias);
            $modelName     = Str::studly(
                $this->ask("  What is the related model class name?", $defaultModel)
            );

            $langScope = $this->confirm(
                '  Does this related model need multi-language (LangScope) support?',
                false
            );

            $relations[] = [
                'alias'     => $alias,
                'type'      => $type,
                'model'     => $modelName,
                'langScope' => $langScope,
            ];

            $this->line("  Added: {$alias} -> {$type}({$modelName})");
            $this->line('');
        }

        return $relations;
    }

    // ═══════════════════════════════════════════════════════════════
    // Execution
    // ═══════════════════════════════════════════════════════════════

    private function executeCreation(
        string $moduleName,
        string $subTitle,
        string $controllerMapping,
        int    $parentId,
        string $iconCss,
        string $listViewName,
        string $editViewName,
        string $dataSource,
        array  $relations,
        bool   $generateFiles
    ): int {
        $controllerName = $moduleName . 'Controller';

        try {
            if ($generateFiles) {
                $this->line('');
                $this->info('Generating scaffolding files...');

                // Controller + views
                $this->line("  -> Creating controller ({$controllerName}) and views...");
                Artisan::call('cms:controller', [
                    'name'       => $moduleName,
                    'dataSource' => $dataSource,
                    'dataWith'   => 'null',
                    'dataFields' => '*',
                ]);

                // Model + any related models
                $methodsStr = '';
                if (! empty($relations)) {
                    $parts = array_map(
                        fn($r) => "{$r['alias']},{$r['type']},{$r['model']}," . ($r['langScope'] ? '1' : '0'),
                        $relations
                    );
                    $methodsStr = implode('~', $parts);
                }

                $this->line("  -> Creating model ({$dataSource})...");
                Artisan::call('cms:model', [
                    'name'    => $dataSource,
                    'methods' => $methodsStr,
                ]);

                // FormRequest validator
                $this->line("  -> Creating validator ({$controllerName}Request)...");
                Artisan::call('cms:validator', [
                    'name'          => $dataSource,
                    'validatorName' => $controllerName . 'Request',
                ]);

                $this->info('  Files generated successfully.');
            }

            // Save to DB
            $this->line('');
            $this->info('Saving module to database...');

            CmsModule::create([
                'name'            => $moduleName,
                'sub_title'       => $subTitle,
                'controller_name' => $controllerMapping,
                'parent_id'       => $parentId,
                'icon_css'        => $iconCss,
                'list_view_name'  => $listViewName,
                'edit_view_name'  => $editViewName,
                'position'        => CmsModule::count() + 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $this->line('');
            $this->info('Module "' . $moduleName . '" created successfully!');
            $this->line('');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Something went wrong: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // Display helpers
    // ═══════════════════════════════════════════════════════════════

    private function printBanner(): void
    {
        $this->line('');
        $this->info('====================================================');
        $this->info('      SYSTEM MODULE DEFINITION WIZARD               ');
        $this->info('====================================================');
        $this->line('  This wizard will guide you through creating a new');
        $this->line('  module for the admin panel, step by step.');
        $this->info('====================================================');
    }

    private function section(string $title): void
    {
        $this->line('');
        $this->comment("---- {$title} ----");
        $this->line('');
    }

    private function printSummary(array $data): void
    {
        $this->line('');
        $this->info('---- REVIEW YOUR MODULE BEFORE SAVING ----');
        $this->line('');
        $rows = array_map(fn($k, $v) => [$k, $v], array_keys($data), array_values($data));
        $this->table(['Field', 'Value'], $rows);
        $this->line('');
    }
}
