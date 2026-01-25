<?php

namespace HashtagCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use HashtagCms\Events\SiteCloner\SiteCloningCompleted;
use HashtagCms\Events\SiteCloner\SiteCloningFailed;
use HashtagCms\Events\SiteCloner\SiteCloningProgress;
use HashtagCms\Events\SiteCloner\SiteCloningStarted;
use HashtagCms\Services\SiteCloner\SiteClonerService;

/**
 * Queue job to clone a site asynchronously
 */
class CloneSiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 3600; // 1 hour

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    protected string $jobId;
    protected float $startTime;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $sourceSiteId,
        public int $targetSiteId,
        public int $userId,
        ?string $jobId = null
    ) {
        $this->jobId = $jobId ?? uniqid('clone_', true);
        $this->onQueue('site-cloner'); // Use dedicated queue
    }

    /**
     * Execute the job.
     */
    public function handle(SiteClonerService $clonerService): void
    {
        $this->startTime = microtime(true);

        try {
            // Fire started event
            event(new SiteCloningStarted(
                $this->sourceSiteId,
                $this->targetSiteId,
                $this->jobId,
                $this->userId
            ));

            Log::info("Site cloning started", [
                'job_id' => $this->jobId,
                'source_site_id' => $this->sourceSiteId,
                'target_site_id' => $this->targetSiteId,
                'user_id' => $this->userId
            ]);

            // Execute the cloning with progress tracking
            $results = $this->cloneWithProgress($clonerService);

            $duration = microtime(true) - $this->startTime;

            // Fire completed event
            event(new SiteCloningCompleted(
                $this->jobId,
                $this->sourceSiteId,
                $this->targetSiteId,
                $results,
                $duration
            ));

            Log::info("Site cloning completed", [
                'job_id' => $this->jobId,
                'duration' => $duration,
                'results_count' => count($results)
            ]);

        } catch (\Exception $e) {
            $this->handleFailure($e);
        }
    }

    /**
     * Clone with progress tracking
     */
    protected function cloneWithProgress(SiteClonerService $clonerService): array
    {
        $results = [];
        $totalSteps = 4; // Number of main steps
        $currentStep = 0;

        // We'll modify the service to accept a progress callback
        // For now, we'll just execute and track

        $allResults = $clonerService->clone($this->sourceSiteId, $this->targetSiteId);

        // Group results by component to track progress
        $stepGroups = [
            'pivot_relations' => ['platform', 'hook', 'language', 'zone', 'country', 'currency'],
            'settings' => ['modules', 'staticmodules', 'themes', 'categories', 'siteproperties', 'moduleproperties'],
            'site_defaults' => ['site_defaults'],
            'module_copy' => ['module_site_copy']
        ];

        foreach ($allResults as $result) {
            $component = $result['component'] ?? 'unknown';

            // Determine which step this belongs to
            foreach ($stepGroups as $stepName => $components) {
                if (in_array($component, $components)) {
                    $currentStep++;

                    event(new SiteCloningProgress(
                        $this->jobId,
                        $stepName,
                        $result['message'] ?? 'Processing...',
                        $currentStep,
                        count($allResults),
                        $result['success'] ?? true,
                        $result
                    ));

                    break;
                }
            }

            $results[] = $result;
        }

        return $results;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->handleFailure($exception);
    }

    /**
     * Handle failure and fire event
     */
    protected function handleFailure(\Throwable $exception): void
    {
        Log::error("Site cloning failed", [
            'job_id' => $this->jobId,
            'source_site_id' => $this->sourceSiteId,
            'target_site_id' => $this->targetSiteId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        event(new SiteCloningFailed(
            $this->jobId,
            $this->sourceSiteId,
            $this->targetSiteId,
            $exception->getMessage(),
            'unknown', // We could track which step failed
            []
        ));

        // Optionally notify admin
        // Notification::route('mail', config('hashtagcms.admin_email'))
        //     ->notify(new SiteCloningFailedNotification($this->jobId, $exception));
    }

    /**
     * Get the job ID
     */
    public function getJobId(): string
    {
        return $this->jobId;
    }
}
