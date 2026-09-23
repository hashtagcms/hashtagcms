<?php

namespace HashtagCms\Core\Logging\Exporters;

use HashtagCms\Core\Logging\LogExporter;

/**
 * Used when CMS_LOG_EXPORT_DRIVER is "none" or unrecognized. Keeps the
 * manager/listener code free of null checks.
 */
class NullExporter implements LogExporter
{
    public function send(array $entry): void
    {
        // no-op
    }
}
