<?php

namespace HashtagCms\Listeners\SiteCloner;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use HashtagCms\Events\SiteCloner\SiteCloningCompleted;

/**
 * Listener for site cloning completed event
 */
class HandleSiteCloningCompleted
{
    /**
     * Handle the event.
     */
    public function handle(SiteCloningCompleted $event): void
    {
        // Update final status in cache
        $status = Cache::get("clone_job_{$event->jobId}", []);

        $status = array_merge($status, [
            'status' => 'completed',
            'progress' => 100,
            'current_step' => 'Completed',
            'results' => $event->results,
            'duration' => $event->duration,
            'completed_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String()
        ]);

        Cache::put(
            "clone_job_{$event->jobId}",
            $status,
            now()->addDays(7) // Keep completed jobs for 7 days
        );

        Log::info("Site cloning completed", [
            'job_id' => $event->jobId,
            'source_site_id' => $event->sourceSiteId,
            'target_site_id' => $event->targetSiteId,
            'duration' => $event->duration
        ]);

        // Optionally send notification to user
        // $userId = $status['user_id'] ?? null;
        // if ($userId) {
        //     $user = User::find($userId);
        //     $user->notify(new SiteCloningCompletedNotification($event));
        // }
    }
}
