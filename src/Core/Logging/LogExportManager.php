<?php

namespace HashtagCms\Core\Logging;

use HashtagCms\Core\Logging\Exporters\GraylogExporter;
use HashtagCms\Core\Logging\Exporters\Last9Exporter;
use HashtagCms\Core\Logging\Exporters\NullExporter;

/**
 * Resolves the configured log-export driver.
 *
 * Add a new backend by adding a case here and a class implementing
 * LogExporter — nothing else in the pipeline needs to change.
 */
class LogExportManager
{
    /**
     * Resolved exporters keyed by driver name, reused for the lifetime of
     * this manager instance (itself a singleton). Long-running queue
     * workers process many ShipLogEntryJob's per process, so caching here
     * lets each exporter's own connection memoization (e.g. GraylogExporter's
     * Publisher, Last9Exporter's Guzzle Client) actually take effect instead
     * of reconnecting on every single log entry.
     *
     * @var array<string, LogExporter>
     */
    protected array $resolved = [];

    public function driver(): LogExporter
    {
        $name = config('hashtagcmslog.driver', 'none');

        return $this->resolved[$name] ??= match ($name) {
            'graylog' => new GraylogExporter(),
            'last9' => new Last9Exporter(),
            default => new NullExporter(),
        };
    }
}
