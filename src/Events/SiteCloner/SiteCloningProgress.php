<?php

namespace HashtagCms\Events\SiteCloner;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a cloning step progresses
 */
class SiteCloningProgress
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public string $step,
        public string $message,
        public int $currentStep,
        public int $totalSteps,
        public bool $success = true,
        public ?array $data = null
    ) {
    }

    /**
     * Get the data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'step' => $this->step,
            'message' => $this->message,
            'current_step' => $this->currentStep,
            'total_steps' => $this->totalSteps,
            'progress_percentage' => round(($this->currentStep / $this->totalSteps) * 100, 2),
            'success' => $this->success,
            'data' => $this->data,
            'timestamp' => now()->toIso8601String()
        ];
    }
}
