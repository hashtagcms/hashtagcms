<?php

namespace HashtagCms\Services\SiteCloner\Steps;

use Illuminate\Support\Str;

/**
 * Attach pivot relations (platforms, hooks, languages, zones, countries, currencies)
 */
class AttachPivotRelationsStep
{
    protected array $itemsToAttach = [
        'platform',
        'hook',
        'language',
        'zone',
        'country',
        'currency'
    ];

    /**
     * Execute the step to attach pivot relations
     *
     * @param int $targetSiteId
     * @return array Results of the operation
     */
    public function execute(int $targetSiteId): array
    {
        $results = [];

        foreach ($this->itemsToAttach as $item) {
            try {
                $result = $this->attachItem($item, $targetSiteId);
                $results[] = [
                    'message' => $result['message'],
                    'component' => $item,
                    'success' => $result['success']
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'message' => "Failed to attach {$item}: " . $e->getMessage(),
                    'component' => $item,
                    'success' => false
                ];
            }
        }

        return $results;
    }

    /**
     * Attach a single item type to the target site
     */
    protected function attachItem(string $item, int $targetSiteId): array
    {
        $finder = ($item === 'language') ? 'lang' : $item;
        $finder = Str::title($finder);
        $namespace = config('hashtagcms.namespace');
        $modelClass = resolve($namespace . 'Models\\' . $finder);

        $attach = [
            'key' => Str::plural($item),
            'ids' => $modelClass::all('id')->pluck('id')->toArray(),
            'site_id' => $targetSiteId,
            'action' => 'add'
        ];

        // Use the existing saveSettings method from SiteController
        $controller = app(\HashtagCms\Http\Controllers\Admin\SiteController::class);
        $res = $controller->saveSettings($attach);

        $success = !empty($res) && $res['isSaved'];
        $message = $success
            ? Str::title($item) . ' copied'
            : "Unable to copy {$item}";

        return [
            'success' => $success,
            'message' => $message
        ];
    }
}
