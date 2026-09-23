<?php

namespace HashtagCms\Core\Logging;

interface LogExporter
{
    /**
     * Send one normalized log entry to the external backend.
     *
     * Implementations must not throw — network/backend failures are caught
     * by the caller and swallowed so a broken log destination never breaks
     * the app or feeds back into the logging pipeline.
     *
     * @param  array  $entry
     * @return void
     */
    public function send(array $entry): void;
}
