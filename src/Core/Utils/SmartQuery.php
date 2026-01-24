<?php

namespace HashtagCms\Core\Utils;

use Illuminate\Support\Facades\DB;

class SmartQuery
{
    /**
     * Execute a smart query based on JSON configuration.
     *
     * @param string|array $jsonQuery
     * @param array $replacements Binding parameters (e.g., :site_id)
     * @return \Illuminate\Support\Collection|null
     */
    public static function execute($jsonQuery, $replacements = [])
    {
        // 1. Decode JSON if string
        if (is_string($jsonQuery)) {
            $config = json_decode($jsonQuery, true);
        } else {
            $config = $jsonQuery;
        }

        if (!is_array($config) || !isset($config['from'])) {
            return null; // Invalid config
        }

        // 2. Start Query Builder
        $query = DB::table($config['from']);

        // 3. Select columns
        if (isset($config['select'])) {
            $columns = is_array($config['select']) ? $config['select'] : explode(',', $config['select']);
            // Trim whitespace
            $columns = array_map('trim', $columns);
            $query->select($columns);
        }

        // 4. Where Clauses
        if (isset($config['where']) && is_array($config['where'])) {
            foreach ($config['where'] as $condition) {
                // Determine structure: [col, op, val] or [col, val]
                $col = $condition[0];
                $operator = '=';
                $value = null;

                if (count($condition) === 3) {
                    $operator = $condition[1];
                    $value = $condition[2];
                } elseif (count($condition) === 2) {
                    $value = $condition[1];
                } else {
                    continue;
                }

                // Handle bindings in values (e.g., ":site_id")
                if (is_string($value) && str_starts_with($value, ':')) {
                    $key = ltrim($value, ':');
                    if (isset($replacements[$key])) {
                        $value = $replacements[$key];
                    }
                }

                $query->where($col, $operator, $value);
            }
        }

        // 5. Order By
        if (isset($config['orderBy'])) {
            // Support multiple order bys? Format: ["col", "direction"]
            // If simple array: ["id", "desc"]
            $col = $config['orderBy'][0] ?? 'id';
            $dir = $config['orderBy'][1] ?? 'asc';
            $query->orderBy($col, $dir);
        }

        // 6. Limit
        if (isset($config['limit'])) {
            $query->limit((int) $config['limit']);
        }

        // 7. Offset / Skip
        if (isset($config['offset'])) {
            $query->offset((int) $config['offset']);
        }

        return $query->get();
    }
}
