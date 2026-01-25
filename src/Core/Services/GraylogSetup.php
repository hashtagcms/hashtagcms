<?php

namespace HashtagCms\Core\Services;

use Gelf\Publisher;
use Gelf\Transport\TcpTransport;

/**
 * Not using for now
 * Class GraylogSetup
 */
class GraylogSetup
{
    public function getGelfPublisher(): Publisher
    {
        $transport = new TcpTransport(config('graylog.host'), config('graylog.port'));
        $transport->setConnectTimeout(2);
        $publisher = new Publisher();
        $publisher->addTransport($transport);

        return $publisher;
    }
}
