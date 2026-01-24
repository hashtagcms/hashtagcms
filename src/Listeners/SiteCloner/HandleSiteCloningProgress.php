<?php

namespace HashtagCms\Listeners\SiteCloner;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use HashtagCms\Events\SiteCloner\SiteCloningProgress;

/**
 * Listener for site cloning progress event
 */
class HandleSiteCloningProgress
{
    /**
     * Handle the event.
     */
    public function handle(SiteCloningProgress $event): void
    {
        // Update status in cache
        $prefix = \HashtagCms\Core\Utils\RedisCacheManager::getInternalPrefix();
        $status = Cache::get("{$prefix}".\HashtagCms\Core\Utils\CacheKeys::CLONE_JOB."_{$event->jobId}", []);

        $status = array_merge($status, [
            'status' => 'in_progress',
            'progress' => round(($event->currentStep / $event->totalSteps) * 100, 2),
            'current_step' => $event->step,
            'current_message' => $event->message,
            'step_number' => $event->currentStep,
            'total_steps' => $event->totalSteps,
            'updated_at' => now()->toIso8601String(),
            'last_success' => $event->success
        ]);

        // Add step to history
        if (!isset($status['steps'])) {
            $status['steps'] = [];
        }

        $status['steps'][] = [
            'step' => $event->step,
            'message' => $event->message,
            'success' => $event->success,
            'timestamp' => now()->toIso8601String(),
            'data' => $event->data
        ];

        Cache::put(
            "{$prefix}".\HashtagCms\Core\Utils\CacheKeys::CLONE_JOB."_{$event->jobId}",
            $status,
            now()->addHours(24)
        );

        Log::debug("Site cloning progress", [
            'job_id' => $event->jobId,
            'step' => $event->step,
            'progress' => $status['progress']
        ]);

        // Optionally broadcast to websocket for real-time updates
        // broadcast(new SiteCloningProgressBroadcast($event));
    }
}
