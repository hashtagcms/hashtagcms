<?php

namespace HashtagCms\Queue;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use HashtagCms\Jobs\CloneSiteJob;

/**
 * Smart job dispatcher that automatically uses queue or sync buffer
 * based on configuration
 */
class SmartJobDispatcher
{
    /**
     * Dispatch a job intelligently
     * - Uses queue if configured (redis, database, sqs, etc.)
     * - Falls back to sync buffer if queue is 'sync' or not configured
     */
    public static function dispatch(string $jobClass, array $parameters, ?string $jobId = null): array
    {
        $queueConnection = Config::get('queue.default', 'sync');
        $jobId = $jobId ?? uniqid('job_', true);

        Log::info("Dispatching job", [
            'job_class' => $jobClass,
            'job_id' => $jobId,
            'queue_connection' => $queueConnection
        ]);

        // Check if real queue is configured
        if (self::isRealQueueConfigured($queueConnection)) {
            return self::dispatchToQueue($jobClass, $parameters, $jobId);
        } else {
            return self::dispatchToSyncBuffer($jobClass, $parameters, $jobId);
        }
    }

    /**
     * Check if a real queue driver is configured
     */
    protected static function isRealQueueConfigured(string $connection): bool
    {
        // 'sync' means no real queue
        if ($connection === 'sync') {
            return false;
        }

        // Check if connection is properly configured
        $driver = Config::get("queue.connections.{$connection}.driver");

        // Real queue drivers
        $realDrivers = ['redis', 'database', 'beanstalkd', 'sqs', 'iron'];

        return in_array($driver, $realDrivers);
    }

    /**
     * Dispatch to real queue
     */
    protected static function dispatchToQueue(string $jobClass, array $parameters, string $jobId): array
    {
        Log::info("Using real queue", ['job_id' => $jobId]);

        // Dispatch to Laravel queue
        $job = new $jobClass(...array_values($parameters));

        // Set job ID if the job supports it
        if (method_exists($job, 'setJobId')) {
            $job->setJobId($jobId);
        }

        dispatch($job);

        return [
            'method' => 'queue',
            'job_id' => $jobId,
            'status' => 'queued',
            'message' => 'Job dispatched to queue worker'
        ];
    }

    /**
     * Dispatch to sync buffer
     */
    protected static function dispatchToSyncBuffer(string $jobClass, array $parameters, string $jobId): array
    {
        Log::info("Using sync buffer (queue not configured)", ['job_id' => $jobId]);

        // Add to sync buffer
        SyncQueueBuffer::push($jobClass, $parameters, $jobId);

        return [
            'method' => 'sync_buffer',
            'job_id' => $jobId,
            'status' => 'processing',
            'message' => 'Job processing in sync buffer (queue not configured)'
        ];
    }

    /**
     * Dispatch site cloning job
     * Convenience method for site cloning
     */
    public static function dispatchSiteCloning(
        int $sourceSiteId,
        int $targetSiteId,
        int $userId,
        ?string $jobId = null
    ): array {
        return self::dispatch(
            CloneSiteJob::class,
            [
                'sourceSiteId' => $sourceSiteId,
                'targetSiteId' => $targetSiteId,
                'userId' => $userId,
                'jobId' => $jobId
            ],
            $jobId
        );
    }

    /**
     * Get job status (works for both queue and sync buffer)
     */
    public static function getJobStatus(string $jobId): ?array
    {
        $queueConnection = Config::get('queue.default', 'sync');

        if (self::isRealQueueConfigured($queueConnection)) {
            // For real queue, check cache (set by event listeners)
            return cache()->get("clone_job_{$jobId}");
        } else {
            // For sync buffer, check buffer status
            $bufferStatus = SyncQueueBuffer::getJobStatus($jobId);

            if ($bufferStatus) {
                // Also check cache for event-based status
                $cacheStatus = cache()->get("clone_job_{$jobId}");

                // Merge both sources
                return array_merge($bufferStatus, $cacheStatus ?? []);
            }

            return cache()->get("clone_job_{$jobId}");
        }
    }

    /**
     * Get queue statistics
     */
    public static function getStats(): array
    {
        $queueConnection = Config::get('queue.default', 'sync');

        $stats = [
            'queue_connection' => $queueConnection,
            'is_real_queue' => self::isRealQueueConfigured($queueConnection),
            'timestamp' => now()->toIso8601String()
        ];

        if (!$stats['is_real_queue']) {
            $stats['sync_buffer'] = SyncQueueBuffer::getStats();
        }

        return $stats;
    }

    /**
     * Clear old completed jobs
     */
    public static function clearOldJobs(int $hoursOld = 24): int
    {
        $queueConnection = Config::get('queue.default', 'sync');

        if (!self::isRealQueueConfigured($queueConnection)) {
            return SyncQueueBuffer::clearOldJobs($hoursOld);
        }

        return 0;
    }
}
