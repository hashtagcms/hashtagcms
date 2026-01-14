<?php

namespace HashtagCms\Events\SiteCloner;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when site cloning completes successfully
 */
class SiteCloningCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $sourceSiteId,
        public int $targetSiteId,
        public array $results,
        public float $duration
    ) {
    }

    /**
     * Get the data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'source_site_id' => $this->sourceSiteId,
            'target_site_id' => $this->targetSiteId,
            'results' => $this->results,
            'duration' => $this->duration,
            'status' => 'completed',
            'timestamp' => now()->toIso8601String()
        ];
    }
}
