<?php

namespace HashtagCms\Listeners;

use HashtagCms\Core\Logging\LogEntryNormalizer;
use HashtagCms\Core\Logging\LogExportManager;
use HashtagCms\Jobs\ShipLogEntryJob;
use Illuminate\Log\Events\MessageLogged;

/**
 * Fires for every Log::* call across every channel (this is how Laravel's
 * logger dispatches internally), so this captures exactly what already
 * goes to laravel.log — no changes to the host app's config/logging.php
 * needed.
 */
class ExportLogEntry
{
    public function handle(MessageLogged $event): void
    {
        $minLevel = config('hashtagcmslog.min_level', 'error');

        if (!LogEntryNormalizer::meetsMinLevel($event->level, $minLevel)) {
            return;
        }

        $entry = LogEntryNormalizer::fromEvent($event);

        try {
            if (config('hashtagcmslog.async', true)) {
                ShipLogEntryJob::dispatch($entry)->onQueue(config('hashtagcmslog.queue', 'default'));

                return;
            }

            app(LogExportManager::class)->driver()->send($entry);
        } catch (\Throwable $e) {
            // Swallow — queue push can throw synchronously too (backend
            // unreachable, unserializable context value). Logging must
            // never break the request/job that triggered it.
        }
    }
}
