<?php

namespace HashtagCms\Services\SiteCloner\Steps;


/**
 * Copy settings like modules, themes, categories, etc.
 */
class CopySettingsStep
{
    protected array $itemsToCopy = [
        'modules',
        'staticmodules',
        'themes',
        'categories',
        'siteproperties',
        'moduleproperties'
    ];

    /**
     * Execute the step to copy settings
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     * @return array Results of the operation
     */
    public function execute(int $sourceSiteId, int $targetSiteId): array
    {
        $results = [];
        $controller = app(\HashtagCms\Http\Controllers\Admin\SiteController::class);

        foreach ($this->itemsToCopy as $item) {
            try {
                $result = $this->copyItem($item, $sourceSiteId, $targetSiteId, $controller);
                $results[] = $result;
            } catch (\Exception $e) {
                $results[] = [
                    'message' => "Failed to copy {$item}: " . $e->getMessage(),
                    'component' => $item,
                    'success' => false
                ];
            }
        }

        return $results;
    }

    /**
     * Copy a single item type from source to target
     */
    protected function copyItem(
        string $item,
        int $sourceSiteId,
        int $targetSiteId,
        $controller
    ): array {
        $data = [
            'fromSite' => [
                'site_id' => $sourceSiteId,
                'data' => $controller->getBySite($sourceSiteId, $item)->toArray()
            ],
            'toSite' => ['site_id' => $targetSiteId],
            'type' => $item
        ];

        $res = $controller->copySettings($data);

        $ignored = count($res['ignored'] ?? []);
        $copied = count($res['copied'] ?? []);

        return [
            'message' => "{$copied} {$item} copied and {$ignored} {$item} ignored",
            'component' => $item,
            'success' => $res['inserted'] ?? false,
            'copied' => $copied,
            'ignored' => $ignored
        ];
    }
}
