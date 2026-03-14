<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Base seeder class with support for checking existing records
 */
abstract class BaseSeeder extends Seeder
{
    /**
     * Check if we should verify existing records before inserting
     *
     * @return bool
     */
    protected function shouldCheckExisting(): bool
    {
        // Default to true for safety, can be overridden by env variable
        $env = getenv('SEED_CHECK_EXISTING');
        return $env === false ? true : $env === 'true';
    }

    /**
     * Insert data into a table, optionally checking for existing records first
     *
     * @param string $table Table name
     * @param array $data Data to insert
     * @param array $uniqueColumns Columns to use for checking existing records
     * @return void
     */
    protected function insertOrSkip(string $table, array $data, array $uniqueColumns = []): void
    {
        if (empty($data)) {
            return;
        }

        // If not checking for existing records, just insert all data
        if (!$this->shouldCheckExisting() || empty($uniqueColumns)) {
            DB::table($table)->insert($data);
            return;
        }

        // Process each record individually to check for duplicates
        foreach ($data as $record) {
            $query = DB::table($table);
            
            // Build query to find existing records
            foreach ($uniqueColumns as $column) {
                if (isset($record[$column])) {
                    $query->where($column, $record[$column]);
                }
            }
            
            // If record doesn't exist, insert it
            if ($query->count() === 0) {
                DB::table($table)->insert([$record]);
            }
        }
    }

    /**
     * Insert data into a table, updating if record exists based on unique columns
     *
     * @param string $table Table name
     * @param array $data Data to insert
     * @param array $uniqueColumns Columns to use for checking existing records
     * @return void
     */
    protected function insertOrUpdate(string $table, array $data, array $uniqueColumns = []): void
    {
        if (empty($data) || empty($uniqueColumns)) {
            return;
        }

        // If not checking for existing records, just insert all data
        if (!$this->shouldCheckExisting()) {
            DB::table($table)->insert($data);
            return;
        }

        // Process each record individually
        foreach ($data as $record) {
            $query = DB::table($table);
            
            // Build query to find existing records
            foreach ($uniqueColumns as $column) {
                if (isset($record[$column])) {
                    $query->where($column, $record[$column]);
                }
            }
            
            // If record exists, update it; otherwise insert
            $existingRecord = $query->first();
            if ($existingRecord) {
                $query->update($record);
            } else {
                DB::table($table)->insert([$record]);
            }
        }
    }

    /**
     * Get selected languages for installation
     *
     * @return array
     */
    protected function getSelectedLanguages(): array
    {
        $langs = config('hashtagcms.install_languages', 'en');
        return is_array($langs) ? $langs : explode(',', $langs);
    }

    /**
     * Load translation file for a given language
     *
     * @param string $fileName
     * @param string $isoCode
     * @return array
     */
    protected function loadTranslations(string $fileName, string $isoCode): array
    {
        $path = __DIR__ . "/Translations/{$isoCode}/{$fileName}.php";
        if (file_exists($path)) {
            return require $path;
        }
        return [];
    }
}
