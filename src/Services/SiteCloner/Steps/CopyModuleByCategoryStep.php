<?php

namespace HashtagCms\Services\SiteCloner\Steps;

use HashtagCms\Models\Category;
use HashtagCms\Models\Module;
use HashtagCms\Models\Site;

/**
 * Copy modules by category from source to target site
 */
class CopyModuleByCategoryStep
{
    /**
     * Execute the step to copy modules by category
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     * @return array Results of the operation
     */
    public function execute(int $sourceSiteId, int $targetSiteId): array
    {
        $results = [];

        try {
            $sourceSite = Site::with(['platform'])->find($sourceSiteId);
            $platforms = $sourceSite->platform;
            $categories = Category::withoutGlobalScopes()
                ->where('site_id', $sourceSiteId)
                ->get();

            foreach ($categories as $category) {
                $categoryResults = $this->copyCategoryModules(
                    $category,
                    $platforms,
                    $sourceSiteId,
                    $targetSiteId
                );
                $results = array_merge($results, $categoryResults);
            }
        } catch (\Exception $e) {
            $results[] = [
                'message' => 'Failed to copy modules by category: ' . $e->getMessage(),
                'component' => 'module_site_copy',
                'success' => false
            ];
        }

        return $results;
    }

    /**
     * Copy modules for a specific category across all platforms
     */
    protected function copyCategoryModules(
        Category $category,
        $platforms,
        int $sourceSiteId,
        int $targetSiteId
    ): array {
        $results = [];
        $linkRewrite = $category->link_rewrite;

        // Find matching category in target site
        $targetCategory = Category::withoutGlobalScopes()
            ->where([
                ['site_id', '=', $targetSiteId],
                ['link_rewrite', '=', $linkRewrite]
            ])
            ->first();

        if (!$targetCategory) {
            $results[] = [
                'success' => false,
                'message' => "Could not find category {$linkRewrite} in target site",
                'component' => 'module_site_copy'
            ];
            return $results;
        }

        // Copy modules for each platform
        foreach ($platforms as $platform) {
            $result = $this->copyModulesForPlatform(
                $category,
                $targetCategory,
                $platform,
                $sourceSiteId,
                $targetSiteId
            );
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Copy modules for a specific category and platform
     */
    protected function copyModulesForPlatform(
        Category $sourceCategory,
        Category $targetCategory,
        $platform,
        int $sourceSiteId,
        int $targetSiteId
    ): array {
        $fromData = [
            'site_id' => $sourceSiteId,
            'platform_id' => $platform->id,
            'category_id' => $sourceCategory->id,
            'microsite_id' => 0 // @todo: For microsite
        ];

        $toData = [
            'site_id' => $targetSiteId,
            'platform_id' => $platform->id,
            'category_id' => $targetCategory->id,
            'microsite_id' => 0 // @todo: For microsite
        ];

        $data = Module::copyData($fromData, $toData);

        return [
            'success' => $data['success'],
            'message' => "{$data['message']} - {$sourceCategory->link_rewrite}, platform: {$platform->link_rewrite}",
            'component' => 'module_site_copy',
            'data' => [
                'fromData' => $fromData,
                'toData' => $toData
            ]
        ];
    }
}
