<?php

namespace HashtagCms\Core\Traits\Admin;

use Elastica\Client;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Handler\ElasticSearchHandler;
use Monolog\Logger;

trait LogManager
{
    /**
     * @todo: Revisit this trait
     * Log as info
     */
    public function infoLog($message)
    {

        $client = new Client(['connectTimeout' => 1]);
        $options = [
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        ];
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog', 'record'));
        $log = new Logger('application_logs');
        $log->pushHandler($handler);

        $log->info(
            json_encode([
                'info_message' => $message,
            ]
            ), [url()->current()]
        );
    }

    /**
     * Desc: Log as warning
     */
    public function warnLog($message)
    {
        $client = new Client(['connectTimeout' => 1]);
        $options = [
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        ];
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog', 'record'));
        $log = new Logger('application_logs');
        $log->pushHandler($handler);
        $log->warn(
            json_encode([
                'warn_message' => $message,
            ]
            ), [url()->current()]
        );
    }

    /**
     * Desc: Log as error
     */
    public function errorLog($message)
    {
        $client = new Client(['connectTimeout' => 1]);
        $options = [
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        ];
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog', 'record'));
        $log = new Logger('error_logs');
        $log->pushHandler($handler);
        $log->error(
            json_encode([
                'error_message' => $message,
            ]
            ), [url()->current()]
        );
    }
}
