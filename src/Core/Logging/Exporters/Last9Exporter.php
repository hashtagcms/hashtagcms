<?php

namespace HashtagCms\Core\Logging\Exporters;

use GuzzleHttp\Client;
use HashtagCms\Core\Logging\LogExporter;

/**
 * Ships log entries to Last9 (or any other OTLP/HTTP-logs compatible
 * backend) as an OpenTelemetry Logs payload over HTTP/JSON.
 *
 * Config: hashtagcmslog.last9.endpoint / auth_header / auth_value / timeout
 */
class Last9Exporter implements LogExporter
{
    protected ?Client $client = null;

    public function send(array $entry): void
    {
        $endpoint = config('hashtagcmslog.last9.endpoint');

        if (empty($endpoint)) {
            return;
        }

        $this->client()->post($endpoint, [
            'headers' => $this->headers(),
            'json' => $this->toOtlpPayload($entry),
        ]);
    }

    protected function client(): Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        return $this->client = new Client([
            'timeout' => config('hashtagcmslog.last9.timeout', 2),
        ]);
    }

    protected function headers(): array
    {
        $headers = ['Content-Type' => 'application/json'];

        $headerName = config('hashtagcmslog.last9.auth_header');
        $headerValue = config('hashtagcmslog.last9.auth_value');

        if (!empty($headerName) && !empty($headerValue)) {
            $headers[$headerName] = $headerValue;
        }

        return $headers;
    }

    /**
     * Build a minimal OTLP/HTTP JSON logs payload for one entry.
     * https://opentelemetry.io/docs/specs/otlp/#otlphttp
     */
    protected function toOtlpPayload(array $entry): array
    {
        $timeNano = (string) ((int) ($entry['timestamp'] ?? time()) * 1_000_000_000);

        $attributes = [
            $this->attribute('level', $entry['level'] ?? 'error'),
        ];

        foreach ($entry['context'] ?? [] as $key => $value) {
            $attributes[] = $this->attribute((string) $key, $value);
        }

        return [
            'resourceLogs' => [[
                'resource' => [
                    'attributes' => [
                        $this->attribute('service.name', $entry['app'] ?? 'hashtagcms'),
                        $this->attribute('deployment.environment', $entry['env'] ?? 'production'),
                        $this->attribute('host.name', $entry['hostname'] ?? gethostname()),
                    ],
                ],
                'scopeLogs' => [[
                    'logRecords' => [[
                        'timeUnixNano' => $timeNano,
                        'severityText' => strtoupper((string) ($entry['level'] ?? 'error')),
                        'body' => ['stringValue' => (string) ($entry['message'] ?? '')],
                        'attributes' => $attributes,
                    ]],
                ]],
            ]],
        ];
    }

    /**
     * @param  mixed  $value
     */
    protected function attribute(string $key, $value): array
    {
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }

        return [
            'key' => $key,
            'value' => ['stringValue' => (string) $value],
        ];
    }
}
