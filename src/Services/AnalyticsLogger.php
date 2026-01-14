<?php

namespace HashtagCms\Services;

use HashtagCms\Models\Category;
use HashtagCms\Models\Page;

class AnalyticsLogger
{
    protected $buffer = [];

    /**
     * Log a visit
     * @param string $type
     * @param int $id
     * @return void
     */
    public function log(string $type, int $id)
    {
        if (!isset($this->buffer[$type])) {
            $this->buffer[$type] = [];
        }
        if (!isset($this->buffer[$type][$id])) {
            $this->buffer[$type][$id] = 0;
        }
        $this->buffer[$type][$id]++;
    }

    /**
     * Flush buffer
     * @return void
     */
    public function flush()
    {
        if (empty($this->buffer)) {
            return;
        }

        foreach ($this->buffer as $type => $ids) {

            $modelClass = $this->getModelClass($type);

            if ($modelClass) {
                foreach ($ids as $id => $count) {
                    // Update read_count
                    // We can optimise this later to use raw update case when... but for now simple loop is fine as it runs on terminate
                    $modelClass::withoutGlobalScopes()->where('id', $id)->increment('read_count', $count);
                }
            }
        }

        // Reset buffer
        $this->buffer = [];
    }

    /**
     * Get Model class based on type
     * @param $type
     * @return string|null
     */
    protected function getModelClass($type)
    {
        switch (strtolower($type)) {
            case 'category':
                return Category::class;
            case 'page':
                return Page::class;
        }
        return null;
    }
}
