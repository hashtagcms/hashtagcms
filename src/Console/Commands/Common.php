<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Common
{
    /**
     * Returns the absolute path to the package root directory
     * (the directory that contains /src and /hashtagcms).
     *
     * Uses dirname(__DIR__, 3) which walks:
     *   __DIR__  = .../src/Console/Commands
     *   level 1  = .../src/Console
     *   level 2  = .../src
     *   level 3  = .../ (package root)
     */
    protected function packageBasePath(): string
    {
        return dirname(__DIR__, 3);
    }

    /**
     * Get source file name relative to the package root.
     *
     * @param string $name  e.g. 'hashtagcms/cmsmodule/controller/index.ms'
     * @return string Absolute path
     */
    protected function getValidSourceFileName($name): string
    {
        return $this->packageBasePath() . '/' . ltrim($name, '/');
    }

    /**
     * @param  string  $path
     * @param  string  $type
     * @return string
     */
    protected function getValidTarget($path = '', $type = 'app')
    {
        if ($type == 'app') {
            return $this->laravel['path'] . '/' . $path;
        } else {
            return base_path($path);
        }
    }

    /**
     * Is Admin Controller Exists
     *
     * @return mixed
     */
    protected function isAdminControllerExists($name)
    {

        $path = $this->laravel['path'];
        $file_name = $path . '/Http/Controllers/Admin/' . Str::studly($name) . 'Controller.php';

        return $this->files->exists($file_name);
    }

    /**
     * isControllerExists
     *
     * @return mixed
     */
    protected function isControllerExists($name)
    {

        $path = $this->laravel['path'];
        $file_name = $path . '/Http/Controllers/' . Str::studly($name) . 'Controller.php';

        return $this->files->exists($file_name);
    }

    /**
     * Is Model Exists
     *
     * @return mixed
     */
    protected function isModelExists($name)
    {
        $path = $this->laravel['path'];
        $file_name = $path . '/Models/' . Str::studly(Str::singular($name)) . '.php';

        return $this->files->exists($file_name);
    }

    /**
     * Confirm Message
     *
     * @return string
     */
    protected function confirmMessage($question)
    {
        // When called via Artisan::call() from a web request, STDIN is not defined
        // and $this->confirm() will throw an error. In non-interactive context,
        // auto-confirm (default to 'Yes' to allow overwrite).
        if (!defined('STDIN') || !$this->input->isInteractive()) {
            $this->warn("Non-interactive context: auto-confirming '$question' as Yes");
            return 'Yes';
        }

        $answer = $this->confirm($question);
        $answer = ($answer == 1) ? 'Yes' : 'No';
        $this->warn("You said $answer");

        return $answer;
    }

    /**
     * Delete temp file
     */
    protected function clean($fileName)
    {
        //delete old files
        unlink($fileName);
    }

    /**
     * Create folder etc
     *
     * @param  string  $what
     * @return string
     */
    protected function init($what = 'model')
    {

        $targetTemp = $this->getValidTarget($this->paths['tempDir'], 'base');
        $targetDir = $this->getValidTarget($this->paths['targetDir'], 'app');

        //Create temp dir
        if (!$this->files->isDirectory($targetTemp)) {
            $this->files->makeDirectory($targetTemp, 0777, true, true);
        }

        //Model/Controller Directory
        if (!$this->files->isDirectory($targetDir)) {
            $this->files->makeDirectory($targetDir);
        }

        $sourceFile = $this->getValidSourceFileName($this->paths['sourceDir'] . "/$what/" . $this->paths['sourceFile']);
        $this->currentSourceFile = $tagetTempFile = $targetTemp . '/' . md5($sourceFile . '' . date('YY-DD-M H:i:s')) . '.ms';
        $this->files->copy($sourceFile, $tagetTempFile);

        return $tagetTempFile;
    }

    /******* Validator Fields ***********/
    /**
     * Get Max Length of a field
     *
     * @return string
     */
    protected function getMax($field)
    {
        preg_match('/\d{1,}/', $field, $found, PREG_OFFSET_CAPTURE);

        if (count($found) > 0) {
            return 'max:' . $found[0][0];
        }

        return '';
    }

    /**
     * Get data type of a field
     *
     * @return mixed|string
     */
    protected function getDataType($field)
    {

        preg_match('/[a-z].+\(/', $field, $found, PREG_OFFSET_CAPTURE);

        $typeArray = [
            'varchar' => 'string',
            'int' => 'numeric',
            'decimal' => 'numeric',
            'bigint' => 'numeric',
            'float' => 'numeric',
            'double' => 'numeric',
            'tinyint' => 'integer',
            'timestamp' => 'date'
        ];

        if (count($found) > 0) {
            $key = Str::replaceLast('(', '', $found[0][0]);

            return $typeArray[$key] ?? '';
        }

        return '';

    }

    /**
     * Check if field is required
     *
     * @return string
     */
    protected function getRequired($field)
    {

        if ($field == 'NO') {
            return 'required';
        }

        return '';
    }

    /**
     * Get Formatted Fields Value
     *
     * @return array
     */
    protected function getFormattedFieldsValue($table_name)
    {

        $table_name = strtolower($table_name);

        if (!Schema::hasTable(Str::plural($table_name))) {
            return [];
        }

        $lang = Str::endsWith($table_name, '_langs') ? 'lang_' : '';

        $isLang = ($lang == '') ? false : true;

        if (DB::connection()->getDriverName() !== 'mysql') {
            // SHOW COLUMNS is MySQL specific.
            // For now, we cannot auto-generate validation rules for other drivers without doctrine/dbal.
            return [];
        }

        $data = DB::select('SHOW COLUMNS FROM ' . Str::plural($table_name));

        $allFields = [];

        // echo $this->getDataType("int(10) unsigned");
        // echo $this->getDataType("varchar(255)");

        //dd($data);

        foreach ($data as $key => $fields) {

            $values = [];
            if ($fields->Field != 'id') {

                $max = $this->getMax($fields->Type);

                $dataType = $this->getDataType($fields->Type);

                $required = $this->getRequired($fields->Null);

                if ($required != '') {
                    $values[] = $required;
                }

                if ($max != '' && $dataType != 'numeric' && $dataType != 'integer') {
                    $values[] = $max;
                }

                if ($dataType != '') {
                    $values[] = $dataType;
                }

                $fields_value = implode('|', $values);

                if ($fields_value != '') {
                    $nullable = (count($values) >= 1 && $required == '') ? 'nullable|' : '';
                    $allFields[$lang . $fields->Field] = "$nullable" . $fields_value;
                }

                if ($isLang) {
                    //Dont need parent table id: ie. country_id if table is coutry_langs
                    $table_name_main = Str::singular(str_replace('_langs', '', $table_name));
                    unset($allFields[$lang . $table_name_main . '_id']);
                    unset($allFields['lang_lang_id']);
                }

            }
        }

        //var_dump($allFields);
        return $allFields;
    }

    /**
     * Get Validation Fields
     *
     * @param  string  $table_name
     * @param  int  $withLang
     * @return string
     */
    protected function getValidationFields($table_name = '', $withLang = 1)
    {

        $data = $this->getFormattedFieldsValue($table_name);

        $lang_table = Str::singular($table_name) . '_langs';

        if ($withLang != 0 && Schema::hasTable(Str::plural($lang_table))) {

            $lang_data = $this->getFormattedFieldsValue($lang_table);
            $data = array_merge($data, $lang_data);
        }

        $field_json_str = [];

        if (count($data) > 0) {
            foreach ($data as $key => $val) {
                $field_json_str[] = '"' . $key . '"' . ' => "' . $val . '"';
            }

        }

        return implode(',
                    ', $field_json_str);

    }

    // ═══════════════════════════════════════════════════════════════
    // View scaffolding — column introspection & HTML generation
    // ═══════════════════════════════════════════════════════════════

    /**
     * Columns that are system-managed and should never appear in a generated form.
     */
    protected function systemColumns(): array
    {
        return ['id', 'created_at', 'updated_at', 'deleted_at', 'insert_by', 'user_id', 'position'];
    }

    // ═══════════════════════════════════════════════════════════════
    // Controller stub generation — from DB schema
    // ═══════════════════════════════════════════════════════════════

    /**
     * Build the $dataFields array string for the controller.
     *
     * - Scalar columns → their name
     * - _id columns    → dropped in favour of the relation dot-notation (zone_id → zone.name)
     * - Lang relation  → lang.name
     * - Always includes id, updated_at
     */
    protected function generateDataFields(string $modelName): string
    {
        $columns = $this->getTableColumns($modelName);
        $system  = array_merge($this->systemColumns(), ['updated_at']); // re-add updated_at deliberately
        $fields  = ['id'];

        $hasLang = !empty($this->getTableColumns(Str::singular(Str::snake($modelName)) . '_langs'));
        if ($hasLang) {
            $fields[] = 'lang.name';
        }

        foreach ($columns as $col) {
            $name = $col['name'];
            if (in_array($name, $system)) {
                continue;
            }
            if (Str::endsWith($name, '_id')) {
                // e.g. zone_id  → zone.name
                $relation = Str::singular(str_replace('_id', '', $name));
                $fields[] = "{$relation}.name";
                continue;
            }
            $fields[] = $name;
        }

        $fields[] = 'updated_at';

        return '[\'' . implode("', '", $fields) . '\']';
    }

    /**
     * Build the $dataWith array string for the controller.
     * Includes 'lang' if a lang table exists, plus each relation from _id columns.
     */
    protected function generateDataWith(string $modelName): string
    {
        $columns   = $this->getTableColumns($modelName);
        $relations = [];

        $hasLang = !empty($this->getTableColumns(Str::singular(Str::snake($modelName)) . '_langs'));
        if ($hasLang) {
            $relations[] = 'lang';
        }

        foreach ($columns as $col) {
            if (Str::endsWith($col['name'], '_id')) {
                $relations[] = Str::camel(str_replace('_id', '', $col['name']));
            }
        }

        if (empty($relations)) {
            return "[]";
        }

        return "['" . implode("', '", array_unique($relations)) . "']";
    }

    /**
     * Build the $bindDataWithAddEdit property string for the controller.
     * Each _id column gets a binding so the edit form has a populated dropdown.
     *
     * e.g. zone_id → 'zones' => ['dataSource' => Zone::class, 'method' => 'all']
     */
    protected function generateBindData(string $modelName, string $namespace): string
    {
        $columns  = $this->getTableColumns($modelName);
        $bindings = [];

        foreach ($columns as $col) {
            $name = $col['name'];
            if (!Str::endsWith($name, '_id')) {
                continue;
            }
            $relation      = str_replace('_id', '', $name);               // zone
            $relationPlural = Str::plural($relation);                      // zones
            $modelClass    = Str::studly($relation);                       // Zone
            $bindings[]    = "        '{$relationPlural}' => ['dataSource' => {$modelClass}::class, 'method' => 'all']";
        }

        if (empty($bindings)) {
            return '// No relational dropdowns needed';
        }

        return "protected \$bindDataWithAddEdit = [\n" . implode(",\n", $bindings) . "\n    ];";
    }

    /**
     * Build the `use Model;` import lines for all related models (_id columns).
     *
     * Resolution order:
     *  1. Check if the class exists in the app namespace  (e.g. App\Models\Zone)
     *  2. Fall back to HashtagCms\Models\Zone  (package-bundled models)
     *  3. If neither found, emit the app namespace and leave a TODO comment
     */
    protected function generateUseModels(string $modelName, string $namespace): string
    {
        $columns = $this->getTableColumns($modelName);
        $uses    = [];

        // Normalise namespace: ensure it ends with a backslash
        $appNs   = rtrim($namespace, '\\') . '\\';
        $cmsNs   = 'HashtagCms\\Models\\';

        foreach ($columns as $col) {
            if (!Str::endsWith($col['name'], '_id')) {
                continue;
            }
            $modelClass    = Str::studly(str_replace('_id', '', $col['name']));
            $appFqcn       = $appNs . 'Models\\' . $modelClass;
            $cmsFqcn       = $cmsNs . $modelClass;

            if (class_exists($appFqcn)) {
                $uses[] = "use {$appFqcn};";
            } elseif (class_exists($cmsFqcn)) {
                // Model lives in the HashtagCms package (e.g. Currency, Zone)
                $uses[] = "use {$cmsFqcn};";
            } else {
                // Neither found — emit app namespace as best guess with a NOTE
                $uses[] = "use {$appFqcn}; // TODO: class not found at generation time — verify namespace";
            }
        }

        return implode("\n", array_unique($uses));
    }

    /**
     * Build the $saveData assignment block inside store().
     *
     * - normal columns       → $saveData['col'] = $data['col'];
     * - checkbox/tinyint(1)  → $saveData['col'] = $data['col'] ?? 0;
     * - _id columns          → $saveData['col'] = $data['col'];  (from dropdown)
     */
    protected function generateSaveDataBlock(string $modelName): string
    {
        $columns = $this->getTableColumns($modelName);
        $system  = $this->systemColumns();
        $lines   = [];

        foreach ($columns as $col) {
            $name = $col['name'];
            if (in_array($name, $system)) {
                continue;
            }
            $kind = $this->classifyColumn($col);
            if ($kind === 'checkbox') {
                $lines[] = "        \$saveData['{$name}'] = \$data['{$name}'] ?? 0;";
            } else {
                $lines[] = "        \$saveData['{$name}'] = \$data['{$name}'];";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Build the $langData block and the correct save-method call.
     * Returns an array with keys: langDataBlock, saveMethod, saveMethodInsert
     *
     * If a lang table exists:
     *   - langDataBlock  → $langData assignments + $arrLangData
     *   - saveMethod     → $this->saveDataWithLang($arrSaveData, $arrLangData, $where)
     * Otherwise:
     *   - langDataBlock  → empty string
     *   - saveMethod     → $this->saveData($arrSaveData, $where)
     */
    protected function generateLangDataBlock(string $modelName): array
    {
        $langModelName = Str::singular(Str::snake($modelName)) . '_langs';
        $langColumns   = $this->getTableColumns($langModelName);

        if (empty($langColumns)) {
            return [
                'langDataBlock'    => '',
                'saveMethod'       => '$this->saveData($arrSaveData, $where)',
                'saveMethodInsert' => '$this->saveData($arrSaveData)',
            ];
        }

        $system   = array_merge($this->systemColumns(), ['lang_id']);
        $lines    = [];

        foreach ($langColumns as $col) {
            $name = $col['name'];
            if (in_array($name, $system) || Str::endsWith($name, '_id')) {
                continue;
            }
            // Form sends lang_name → $langData['name']
            $lines[] = "        \$langData['{$name}'] = \$data['lang_{$name}'];";
        }

        $lines[] = "        \$langData['updated_at'] = htcms_get_current_date();";
        $lines[] = "        if (\$data['actionPerformed'] !== 'edit') {";
        $lines[] = "            \$langData['created_at'] = htcms_get_current_date();";
        $lines[] = "        }";
        $lines[] = "        \$arrLangData = ['data' => \$langData];";

        return [
            'langDataBlock'    => implode("\n", $lines),
            'saveMethod'       => '$this->saveDataWithLang($arrSaveData, $arrLangData, $where)',
            'saveMethodInsert' => '$this->saveDataWithLang($arrSaveData, $arrLangData)',
        ];
    }

    /**
     * Read raw column metadata from the DB for a given table name.
     * Accepts either a model name (Zone → zones) or a plain table name.
     *
     * Returns array of:
     *   [ 'name' => string, 'type' => string, 'nullable' => bool, 'default' => mixed ]
     */
    protected function getTableColumns(string $modelOrTable): array
    {
        // Normalise: convert StudlyCase model name to plural snake_case table name
        $tableName = Str::plural(Str::snake($modelOrTable));

        if (!Schema::hasTable($tableName)) {
            return [];
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $rows = DB::select('SHOW COLUMNS FROM ' . $tableName);
            return array_map(fn($r) => [
                'name'     => $r->Field,
                'type'     => strtolower($r->Type),
                'nullable' => $r->Null === 'YES',
                'default'  => $r->Default,
            ], $rows);
        }

        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA table_info({$tableName})");
            return array_map(fn($r) => [
                'name'     => $r->name,
                'type'     => strtolower($r->type),
                'nullable' => $r->notnull == 0,
                'default'  => $r->dflt_value,
            ], $rows);
        }

        if ($driver === 'pgsql') {
            $rows = DB::select("
                SELECT column_name AS name, data_type AS type,
                       is_nullable AS nullable, column_default AS default_val
                FROM information_schema.columns
                WHERE table_name = ? ORDER BY ordinal_position", [$tableName]);
            return array_map(fn($r) => [
                'name'     => $r->name,
                'type'     => strtolower($r->type),
                'nullable' => $r->nullable === 'YES',
                'default'  => $r->default_val,
            ], $rows);
        }

        return [];
    }

    /**
     * Classify a DB column into a logical form-field kind.
     *
     * Returns one of: text | textarea | number | decimal | checkbox |
     *                 date | datetime | select | enum
     */
    protected function classifyColumn(array $col): string
    {
        $name = $col['name'];
        $type = $col['type'];

        // Foreign key → dropdown
        if (Str::endsWith($name, '_id')) {
            return 'select';
        }
        // Boolean / bit flag → checkbox
        if ($type === 'tinyint(1)' || $type === 'boolean' || $type === 'bool') {
            return 'checkbox';
        }
        // Large text → textarea
        if (preg_match('/^(text|mediumtext|longtext|clob)/', $type)) {
            return 'textarea';
        }
        // Date only
        if ($type === 'date') {
            return 'date';
        }
        // Date + time
        if (preg_match('/^(datetime|timestamp)/', $type)) {
            return 'datetime';
        }
        // Decimal / float
        if (preg_match('/^(decimal|numeric|float|double|real)/', $type)) {
            return 'decimal';
        }
        // Integer (but not tinyint(1) which was already caught)
        if (preg_match('/^(int|bigint|smallint|mediumint|tinyint|integer)/', $type)) {
            return 'number';
        }
        // Enum → <select> with hard-coded options
        if (str_starts_with($type, 'enum')) {
            return 'enum';
        }

        // Default: plain text
        return 'text';
    }

    /**
     * Build the @php variable-defaults block for the addedit view.
     *
     * Example output line:
     *   $iso_code = old('iso_code', '');
     *   $need_zip_code = old('need_zip_code', 0);
     */
    protected function generateViewPhpVars(string $modelName): string
    {
        $columns = $this->getTableColumns($modelName);
        $system  = $this->systemColumns();
        $lines   = [];

        foreach ($columns as $col) {
            $name = $col['name'];
            if (in_array($name, $system)) {
                continue;
            }

            $kind    = $this->classifyColumn($col);
            $default = match ($kind) {
                'checkbox', 'number', 'decimal' => '0',
                default                         => "''",
            };

            $lines[] = "\${$name} = old('{$name}', {$default});";
        }

        // Lang table vars block — always initialise $lang as an array
        $langModelName = Str::singular(Str::snake($modelName)) . '_langs';
        $langColumns   = $this->getTableColumns($langModelName);

        if (!empty($langColumns)) {
            $lines[] = '$lang = [];';
            foreach ($langColumns as $col) {
                $name = $col['name'];
                if (in_array($name, $system) || $name === 'lang_id' || Str::endsWith($name, '_id')) {
                    continue;
                }
                $lines[] = "\$lang['{$name}'] = old('lang_{$name}', '');";
            }
        }

        return implode("\n        ", $lines);
    }

    /**
     * Build the HTML form-fields block for the addedit view from live DB columns.
     *
     * Produces Tailwind + FormHelper markup matching the established view pattern.
     */
    protected function generateViewFormFields(string $modelName): string
    {
        // CSS class constants matching the app's design system
        $inputCss    = "form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900";
        $selectCss   = "form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900";
        $checkboxCss = "w-5 h-5 rounded text-blue-600 focus:ring-blue-500";
        $labelCss    = "text-sm font-medium text-slate-700 block";

        $columns = $this->getTableColumns($modelName);
        $system  = $this->systemColumns();
        $html    = '';

        foreach ($columns as $col) {
            $name  = $col['name'];
            if (in_array($name, $system)) {
                continue;
            }

            $label   = Str::headline($name);
            $varRef  = "\${$name}";
            $kind    = $this->classifyColumn($col);

            $html .= "\n                <div class=\"space-y-2\">\n";

            switch ($kind) {

                case 'select':
                    // e.g. zone_id → $zones collection, selected = $zone_id
                    $rel     = Str::camel(Str::plural(str_replace('_id', '', $name)));
                    $selVar  = "\${$name}";
                    $html   .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html   .= "                    {!! FormHelper::select('{$name}', \${$rel}, array('class' => '{$selectCss}'), {$selVar}) !!}\n";
                    break;

                case 'checkbox':
                    // Replace div with flex row for checkbox + label inline
                    $html  = rtrim($html, "\n");
                    $html .= "\n                <div class=\"flex items-center gap-3\">\n";
                    $html .= "                    {!! FormHelper::checkbox('{$name}', {$varRef}, array('class' => '{$checkboxCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => 'text-sm font-medium text-slate-700')) !!}\n";
                    $html .= "                </div>\n";
                    continue 2; // skip the closing </div> below

                case 'textarea':
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::textarea('{$name}', {$varRef}, array('class' => '{$inputCss} rows-4', 'placeholder' => 'Enter {$label}')) !!}\n";
                    break;

                case 'date':
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::input('date', '{$name}', {$varRef}, array('class' => '{$inputCss}')) !!}\n";
                    break;

                case 'datetime':
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::input('datetime-local', '{$name}', {$varRef}, array('class' => '{$inputCss}')) !!}\n";
                    break;

                case 'decimal':
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::input('number', '{$name}', {$varRef}, array('class' => '{$inputCss}', 'step' => '0.01', 'placeholder' => '0.00')) !!}\n";
                    break;

                case 'number':
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::input('number', '{$name}', {$varRef}, array('class' => '{$inputCss}', 'placeholder' => 'Enter {$label}')) !!}\n";
                    break;

                case 'enum':
                    // Extract values from e.g. enum('draft','published','archived')
                    preg_match('/enum\((.+)\)/i', $col['type'], $m);
                    $options = [];
                    if (!empty($m[1])) {
                        foreach (explode(',', $m[1]) as $v) {
                            $v             = trim($v, "' \"");
                            $options[$v]   = Str::headline($v);
                        }
                    }
                    $optionsStr = var_export($options, true);
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::select('{$name}', {$optionsStr}, array('class' => '{$selectCss}'), {$varRef}) !!}\n";
                    break;

                default: // text / varchar / char
                    $html .= "                    {!! FormHelper::label('{$name}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                    $html .= "                    {!! FormHelper::input('text', '{$name}', {$varRef}, array('class' => '{$inputCss}', 'placeholder' => 'Enter {$label}')) !!}\n";
                    break;
            }

            $html .= "                </div>\n";
        }

        // ── Lang table section ───────────────────────────────────────────
        $langModelName = Str::singular(Str::snake($modelName)) . '_langs';
        $langColumns   = $this->getTableColumns($langModelName);

        if (!empty($langColumns)) {
            $langSystem = array_merge($system, ['lang_id']);
            $langHtml   = '';

            foreach ($langColumns as $col) {
                $name = $col['name'];
                if (in_array($name, $langSystem) || Str::endsWith($name, '_id')) {
                    continue;
                }

                $fieldName = "lang_{$name}";
                $label     = Str::headline($name);
                $varRef    = "\$lang['{$name}']";

                $langHtml .= "\n                    <div class=\"space-y-2\">\n";
                $langHtml .= "                        {!! FormHelper::label('{$fieldName}', '{$label}', array('class' => '{$labelCss}')) !!}\n";
                $langHtml .= "                        {!! FormHelper::input('text', '{$fieldName}', {$varRef}, array('class' => '{$inputCss}', 'placeholder' => 'Enter {$label}', 'required' => 'required')) !!}\n";
                $langHtml .= "                    </div>\n";
            }

            if ($langHtml !== '') {
                $html .= "\n                <!-- Language Fields -->";
                $html .= "\n                <div class=\"pt-6 border-t border-slate-100 space-y-6\">\n";
                $html .= "                    <h4 class=\"text-[10px] font-black uppercase tracking-widest text-slate-400\">Language Fields</h4>\n";
                $html .= $langHtml;
                $html .= "                </div>\n";
            }
        }

        // Fallback if table doesn't exist yet (e.g. migration not run)
        if (trim($html) === '') {
            $html = "                <!-- TODO: Table not found at generation time. Add your fields here. -->\n";
            $html .= "                <div class=\"p-4 bg-amber-50 border border-amber-200 rounded-xl\">\n";
            $html .= "                    <p class=\"text-xs text-amber-700\"><i class=\"fa fa-info-circle\"></i> No columns found. Run migrations first or add form fields manually.</p>\n";
            $html .= "                </div>\n";
        }

        return $html;
    }
}
