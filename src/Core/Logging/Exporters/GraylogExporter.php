<?php

namespace HashtagCms\Core\Logging\Exporters;

use Gelf\Message;
use Gelf\Publisher;
use Gelf\Transport\TcpTransport;
use Gelf\Transport\UdpTransport;
use HashtagCms\Core\Logging\LogExporter;

/**
 * Ships log entries to Graylog via GELF (TCP or UDP).
 *
 * Requires composer package "graylog2/gelf-php".
 */
class GraylogExporter implements LogExporter
{
    /**
     * RFC 5424 syslog severity numbers, PSR-3 level name => int.
     */
    private const SYSLOG_LEVELS = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
    ];

    protected ?Publisher $publisher = null;

    public function send(array $entry): void
    {
        $publisher = $this->publisher();

        if ($publisher === null) {
            return;
        }

        $message = new Message();
        $message->setShortMessage((string) ($entry['message'] ?? ''));
        $message->setLevel(self::SYSLOG_LEVELS[$entry['level'] ?? 'error'] ?? self::SYSLOG_LEVELS['error']);
        $message->setTimestamp($entry['timestamp'] ?? time());

        if (!empty($entry['hostname'])) {
            $message->setHost($entry['hostname']);
        }

        foreach (['app', 'env'] as $field) {
            if (isset($entry[$field])) {
                $message->setAdditional($field, $entry[$field]);
            }
        }

        if (!empty($entry['context'])) {
            $message->setAdditional('context', json_encode($entry['context']));
        }

        $publisher->publish($message);
    }

    protected function publisher(): ?Publisher
    {
        if ($this->publisher !== null) {
            return $this->publisher;
        }

        $host = config('hashtagcmslog.graylog.host');
        $port = config('hashtagcmslog.graylog.port', 12201);
        $protocol = config('hashtagcmslog.graylog.protocol', 'tcp');

        if (empty($host)) {
            return null;
        }

        $transport = ($protocol === 'udp')
            ? new UdpTransport($host, $port)
            : new TcpTransport($host, $port);

        if (method_exists($transport, 'setConnectTimeout')) {
            $transport->setConnectTimeout(2);
        }

        $publisher = new Publisher();
        $publisher->addTransport($transport);

        return $this->publisher = $publisher;
    }
}
