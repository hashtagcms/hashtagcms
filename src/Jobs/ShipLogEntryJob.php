<?php

namespace HashtagCms\Jobs;

use HashtagCms\Core\Logging\LogExportManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Ships one normalized log entry to the configured external backend
 * (Graylog / Last9 / ...). Intentionally never rethrows: a broken log
 * destination must not fail the queue worker or, worse, generate more log
 * entries that feed back into this same pipeline.
 */
class ShipLogEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(protected array $entry)
    {
    }

    public function handle(LogExportManager $manager): void
    {
        try {
            $manager->driver()->send($this->entry);
        } catch (\Throwable $e) {
            // Swallow deliberately — do not Log:: here, it would re-enter
            // this same export pipeline if it ever starts failing.
        }
    }
}
