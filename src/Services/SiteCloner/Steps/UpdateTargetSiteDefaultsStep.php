<?php

namespace HashtagCms\Services\SiteCloner\Steps;

use HashtagCms\Models\Category;
use HashtagCms\Models\Site;
use HashtagCms\Models\Theme;

/**
 * Update target site with default values from source site
 */
class UpdateTargetSiteDefaultsStep
{
    /**
     * Execute the step to update target site defaults
     *
     * @param Site $sourceSite
     * @param Site $targetSite
     * @return array Results of the operation
     */
    public function execute(Site $sourceSite, Site $targetSite): array
    {
        $results = [
            ['message' => '----------------------------------------', 'component' => '']
        ];

        try {
            // Find matching category in target site
            $targetCategory = $this->findMatchingCategory(
                $sourceSite->category_id,
                $targetSite->id
            );

            // Find matching theme in target site
            $targetTheme = $this->findMatchingTheme(
                $sourceSite->theme_id,
                $targetSite->id
            );

            // Update target site
            $targetSite->category_id = $targetCategory?->id ?? $sourceSite->category_id;
            $targetSite->theme_id = $targetTheme?->id ?? $sourceSite->theme_id;
            $targetSite->platform_id = $sourceSite->platform_id;
            $targetSite->lang_id = $sourceSite->lang_id;
            $targetSite->country_id = $sourceSite->country_id;
            $targetSite->save();

            $results[] = [
                'message' => 'Target site defaults updated successfully',
                'component' => 'site_defaults',
                'success' => true
            ];
        } catch (\Exception $e) {
            $results[] = [
                'message' => 'Failed to update target site defaults: ' . $e->getMessage(),
                'component' => 'site_defaults',
                'success' => false
            ];
        }

        return $results;
    }

    /**
     * Find matching category in target site by link_rewrite
     */
    protected function findMatchingCategory(int $sourceCategoryId, int $targetSiteId): ?Category
    {
        $sourceCategory = Category::withoutGlobalScopes()
            ->where('id', $sourceCategoryId)
            ->first();

        if (!$sourceCategory) {
            return null;
        }

        return Category::withoutGlobalScopes()
            ->where([
                ['link_rewrite', '=', $sourceCategory->link_rewrite],
                ['site_id', '=', $targetSiteId]
            ])
            ->first();
    }

    /**
     * Find matching theme in target site by alias
     */
    protected function findMatchingTheme(int $sourceThemeId, int $targetSiteId): ?Theme
    {
        $sourceTheme = Theme::withoutGlobalScopes()
            ->where('id', $sourceThemeId)
            ->first();

        if (!$sourceTheme) {
            return null;
        }

        return Theme::withoutGlobalScopes()
            ->where([
                ['alias', '=', $sourceTheme->alias],
                ['site_id', '=', $targetSiteId]
            ])
            ->first();
    }
}
