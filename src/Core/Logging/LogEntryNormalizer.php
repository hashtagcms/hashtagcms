<?php

namespace HashtagCms\Core\Logging;

use Illuminate\Log\Events\MessageLogged;

class LogEntryNormalizer
{
    /**
     * PSR-3 level name => numeric severity (lower = more severe).
     */
    private const LEVEL_WEIGHT = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
    ];

    public static function meetsMinLevel(string $level, string $minLevel): bool
    {
        $levelWeight = self::LEVEL_WEIGHT[$level] ?? self::LEVEL_WEIGHT['error'];
        $minWeight = self::LEVEL_WEIGHT[$minLevel] ?? self::LEVEL_WEIGHT['error'];

        return $levelWeight <= $minWeight;
    }

    public static function fromEvent(MessageLogged $event): array
    {
        return [
            'level' => $event->level,
            'message' => $event->message,
            'context' => self::redact(self::normalizeThrowables((array) $event->context)),
            'timestamp' => time(),
            'app' => config('app.name', 'hashtagcms'),
            'env' => config('app.env', 'production'),
            'hostname' => gethostname() ?: null,
        ];
    }

    /**
     * Laravel's own exception handler logs via
     * Log::error($e->getMessage(), ['exception' => $e]) — a raw Throwable
     * in context. Exception's properties are protected, so json_encode()
     * on it silently yields "{}", losing the message/file/line/trace that
     * matter most. Replace any Throwable with a plain array before it
     * reaches an exporter.
     */
    protected static function normalizeThrowables(array $context): array
    {
        array_walk_recursive($context, function (&$value) {
            if ($value instanceof \Throwable) {
                $value = [
                    'class' => get_class($value),
                    'message' => $value->getMessage(),
                    'file' => $value->getFile() . ':' . $value->getLine(),
                    'trace' => $value->getTraceAsString(),
                ];
            }
        });

        return $context;
    }

    protected static function redact(array $context): array
    {
        $redactKeys = array_map('strtolower', config('hashtagcmslog.redact_keys', []));

        array_walk_recursive($context, function (&$value, $key) use ($redactKeys) {
            if (in_array(strtolower((string) $key), $redactKeys, true)) {
                $value = '[REDACTED]';
            }
        });

        return $context;
    }
}
