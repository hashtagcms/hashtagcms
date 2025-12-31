<?php

namespace MarghoobSuleman\HashtagCms\Listeners;

use MarghoobSuleman\HashtagCms\Events\UserVisit;
use MarghoobSuleman\HashtagCms\Services\AnalyticsLogger;

class RecordUserVisit
{
    protected $logger;

    /**
     * Create the event listener.
     *
     * @param AnalyticsLogger $logger
     * @return void
     */
    public function __construct(AnalyticsLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle the event.
     *
     * @param  UserVisit  $event
     * @return void
     */
    public function handle(UserVisit $event)
    {
        $this->logger->log($event->type, $event->id);
    }
}
