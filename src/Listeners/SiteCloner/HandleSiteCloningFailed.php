<?php

namespace HashtagCms\Listeners\SiteCloner;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use HashtagCms\Events\SiteCloner\SiteCloningFailed;

/**
 * Listener for site cloning failed event
 */
class HandleSiteCloningFailed
{
    /**
     * Handle the event.
     */
    public function handle(SiteCloningFailed $event): void
    {
        // Update status in cache
        $status = Cache::get("clone_job_{$event->jobId}", []);

        $status = array_merge($status, [
            'status' => 'failed',
            'current_step' => 'Failed',
            'error' => $event->error,
            'failed_step' => $event->step,
            'partial_results' => $event->partialResults,
            'failed_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String()
        ]);

        Cache::put(
            "clone_job_{$event->jobId}",
            $status,
            now()->addDays(7) // Keep failed jobs for 7 days for debugging
        );

        Log::error("Site cloning failed", [
            'job_id' => $event->jobId,
            'source_site_id' => $event->sourceSiteId,
            'target_site_id' => $event->targetSiteId,
            'error' => $event->error,
            'failed_step' => $event->step
        ]);

        // Optionally send notification to user and admin
        // $userId = $status['user_id'] ?? null;
        // if ($userId) {
        //     $user = User::find($userId);
        //     $user->notify(new SiteCloningFailedNotification($event));
        // }

        // Notify admin
        // Notification::route('mail', config('hashtagcms.admin_email'))
        //     ->notify(new SiteCloningFailedAdminNotification($event));
    }
}
