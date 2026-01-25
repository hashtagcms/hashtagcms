<?php

namespace HashtagCms\Events\SiteCloner;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when site cloning starts
 */
class SiteCloningStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $sourceSiteId,
        public int $targetSiteId,
        public string $jobId,
        public int $userId
    ) {
    }

    /**
     * Get the data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'source_site_id' => $this->sourceSiteId,
            'target_site_id' => $this->targetSiteId,
            'job_id' => $this->jobId,
            'user_id' => $this->userId,
            'status' => 'started',
            'timestamp' => now()->toIso8601String()
        ];
    }
}
