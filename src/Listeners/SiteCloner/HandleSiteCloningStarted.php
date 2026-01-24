<?php

namespace HashtagCms\Listeners\SiteCloner;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use HashtagCms\Events\SiteCloner\SiteCloningStarted;

/**
 * Listener for site cloning started event
 */
class HandleSiteCloningStarted
{
    /**
     * Handle the event.
     */
    public function handle(SiteCloningStarted $event): void
    {
        $prefix = \HashtagCms\Core\Utils\RedisCacheManager::getInternalPrefix();
        Cache::put(
            "{$prefix}".\HashtagCms\Core\Utils\CacheKeys::CLONE_JOB."_{$event->jobId}",
            [
                'job_id' => $event->jobId,
                'source_site_id' => $event->sourceSiteId,
                'target_site_id' => $event->targetSiteId,
                'user_id' => $event->userId,
                'status' => 'started',
                'progress' => 0,
                'current_step' => 'Initializing',
                'started_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String()
            ],
            now()->addHours(24) // Keep for 24 hours
        );

        Log::info("Site cloning started", [
            'job_id' => $event->jobId,
            'source_site_id' => $event->sourceSiteId,
            'target_site_id' => $event->targetSiteId
        ]);

        // Optionally send notification to user
        // $user = User::find($event->userId);
        // $user->notify(new SiteCloningStartedNotification($event));
    }
}
