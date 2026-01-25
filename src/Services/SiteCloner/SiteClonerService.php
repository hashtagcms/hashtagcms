<?php

namespace HashtagCms\Services\SiteCloner;

use Illuminate\Support\Collection;
use HashtagCms\Models\Site;
use HashtagCms\Services\SiteCloner\Steps\AttachPivotRelationsStep;
use HashtagCms\Services\SiteCloner\Steps\CopySettingsStep;
use HashtagCms\Services\SiteCloner\Steps\CopyModuleByCategoryStep;
use HashtagCms\Services\SiteCloner\Steps\UpdateTargetSiteDefaultsStep;

/**
 * Service to clone a site from source to target
 * Orchestrates the cloning process through multiple steps
 */
class SiteClonerService
{
    protected array $results = [];

    public function __construct(
        protected AttachPivotRelationsStep $attachPivotStep,
        protected CopySettingsStep $copySettingsStep,
        protected UpdateTargetSiteDefaultsStep $updateDefaultsStep,
        protected CopyModuleByCategoryStep $copyModulesStep
    ) {
    }

    /**
     * Clone a site from source to target
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     * @return array Results of the cloning operation
     * @throws \Exception
     */
    public function clone(int $sourceSiteId, int $targetSiteId): array
    {
        $this->validateSites($sourceSiteId, $targetSiteId);

        $sourceSite = Site::allInfo($sourceSiteId);
        $targetSite = Site::find($targetSiteId);

        if (!$sourceSite || !$targetSite) {
            throw new \Exception('Source or target site not found');
        }

        // Step 1: Attach pivot relations (platforms, hooks, languages, etc.)
        $this->addResults(
            $this->attachPivotStep->execute($targetSiteId)
        );

        // Step 2: Copy settings (modules, themes, categories, etc.)
        $this->addResults(
            $this->copySettingsStep->execute($sourceSiteId, $targetSiteId)
        );

        // Step 3: Update target site defaults (category, theme, platform, etc.)
        $this->addResults(
            $this->updateDefaultsStep->execute($sourceSite, $targetSite)
        );

        // Step 4: Copy modules by category
        $this->addResults(
            $this->copyModulesStep->execute($sourceSiteId, $targetSiteId)
        );

        return $this->results;
    }

    /**
     * Validate that source and target sites are different
     */
    protected function validateSites(int $sourceSiteId, int $targetSiteId): void
    {
        if ($sourceSiteId === $targetSiteId) {
            throw new \InvalidArgumentException("Source and target site cannot be the same");
        }
    }

    /**
     * Add results from a step to the overall results
     */
    protected function addResults(array $stepResults): void
    {
        $this->results = array_merge($this->results, $stepResults);
    }

    /**
     * Get all results
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
