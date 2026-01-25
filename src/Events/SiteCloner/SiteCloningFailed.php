<?php

namespace HashtagCms\Events\SiteCloner;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when site cloning fails
 */
class SiteCloningFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $sourceSiteId,
        public int $targetSiteId,
        public string $error,
        public string $step,
        public ?array $partialResults = null
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
            'error' => $this->error,
            'failed_step' => $this->step,
            'partial_results' => $this->partialResults,
            'status' => 'failed',
            'timestamp' => now()->toIso8601String()
        ];
    }
}
