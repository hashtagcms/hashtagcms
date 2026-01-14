<?php

namespace HashtagCms\Queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Simple in-memory queue buffer for when queue is not configured
 * Falls back to database storage for persistence
 */
class SyncQueueBuffer
{
    protected static array $buffer = [];
    protected static bool $processing = false;

    /**
     * Add a job to the buffer
     */
    public static function push(string $jobClass, array $data, string $jobId): void
    {
        $job = [
            'id' => $jobId,
            'class' => $jobClass,
            'data' => $data,
            'attempts' => 0,
            'max_attempts' => 3,
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ];

        // Add to in-memory buffer
        self::$buffer[$jobId] = $job;

        // Also persist to database for reliability
        self::persistToDatabase($job);

        Log::info("Job added to sync queue buffer", ['job_id' => $jobId]);

        // Auto-process if not already processing
        if (!self::$processing) {
            self::processNext();
        }
    }

    /**
     * Process the next job in the buffer
     */
    public static function processNext(): void
    {
        if (self::$processing) {
            return;
        }

        self::$processing = true;

        try {
            // Get next pending job
            $job = self::getNextPendingJob();

            if ($job) {
                self::processJob($job);
            }
        } finally {
            self::$processing = false;
        }
    }

    /**
     * Get the next pending job
     */
    protected static function getNextPendingJob(): ?array
    {
        // First check in-memory buffer
        foreach (self::$buffer as $jobId => $job) {
            if ($job['status'] === 'pending') {
                return $job;
            }
        }

        // Then check database
        $dbJob = DB::table('sync_queue_jobs')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($dbJob) {
            return [
                'id' => $dbJob->id,
                'class' => $dbJob->class,
                'data' => json_decode($dbJob->data, true),
                'attempts' => $dbJob->attempts,
                'max_attempts' => $dbJob->max_attempts,
                'status' => $dbJob->status
            ];
        }

        return null;
    }

    /**
     * Process a single job
     */
    protected static function processJob(array $job): void
    {
        $jobId = $job['id'];
        $jobClass = $job['class'];
        $data = $job['data'];

        Log::info("Processing sync queue job", ['job_id' => $jobId, 'class' => $jobClass]);

        try {
            // Update status to processing
            self::updateJobStatus($jobId, 'processing');

            // Instantiate and execute the job
            $instance = new $jobClass(...array_values($data));

            // Resolve dependencies and call handle method
            app()->call([$instance, 'handle']);

            // Mark as completed
            self::updateJobStatus($jobId, 'completed');
            self::removeFromBuffer($jobId);

            Log::info("Sync queue job completed", ['job_id' => $jobId]);

        } catch (\Throwable $e) {
            Log::error("Sync queue job failed", [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Increment attempts
            $job['attempts']++;

            if ($job['attempts'] >= $job['max_attempts']) {
                // Max attempts reached, mark as failed
                self::updateJobStatus($jobId, 'failed', $e->getMessage());
                self::removeFromBuffer($jobId);

                // Call failed method if exists
                if (method_exists($instance ?? null, 'failed')) {
                    $instance->failed($e);
                }
            } else {
                // Retry
                self::updateJobStatus($jobId, 'pending');
                self::updateAttempts($jobId, $job['attempts']);

                // Re-add to buffer for retry
                if (isset(self::$buffer[$jobId])) {
                    self::$buffer[$jobId]['attempts'] = $job['attempts'];
                    self::$buffer[$jobId]['status'] = 'pending';
                }
            }
        }
    }

    /**
     * Persist job to database
     */
    protected static function persistToDatabase(array $job): void
    {
        try {
            DB::table('sync_queue_jobs')->insert([
                'id' => $job['id'],
                'class' => $job['class'],
                'data' => json_encode($job['data']),
                'attempts' => $job['attempts'],
                'max_attempts' => $job['max_attempts'],
                'status' => $job['status'],
                'created_at' => $job['created_at'],
                'updated_at' => $job['updated_at']
            ]);
        } catch (\Exception $e) {
            Log::warning("Failed to persist job to database", [
                'job_id' => $job['id'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update job status
     */
    protected static function updateJobStatus(string $jobId, string $status, ?string $error = null): void
    {
        // Update in-memory buffer
        if (isset(self::$buffer[$jobId])) {
            self::$buffer[$jobId]['status'] = $status;
            self::$buffer[$jobId]['updated_at'] = now()->toDateTimeString();
            if ($error) {
                self::$buffer[$jobId]['error'] = $error;
            }
        }

        // Update database
        try {
            $updateData = [
                'status' => $status,
                'updated_at' => now()->toDateTimeString()
            ];

            if ($error) {
                $updateData['error'] = $error;
            }

            DB::table('sync_queue_jobs')
                ->where('id', $jobId)
                ->update($updateData);
        } catch (\Exception $e) {
            Log::warning("Failed to update job status in database", [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update job attempts
     */
    protected static function updateAttempts(string $jobId, int $attempts): void
    {
        try {
            DB::table('sync_queue_jobs')
                ->where('id', $jobId)
                ->update([
                    'attempts' => $attempts,
                    'updated_at' => now()->toDateTimeString()
                ]);
        } catch (\Exception $e) {
            Log::warning("Failed to update job attempts", [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove job from buffer
     */
    protected static function removeFromBuffer(string $jobId): void
    {
        unset(self::$buffer[$jobId]);
    }

    /**
     * Get job status
     */
    public static function getJobStatus(string $jobId): ?array
    {
        // Check in-memory buffer first
        if (isset(self::$buffer[$jobId])) {
            return self::$buffer[$jobId];
        }

        // Check database
        try {
            $job = DB::table('sync_queue_jobs')
                ->where('id', $jobId)
                ->first();

            if ($job) {
                return [
                    'id' => $job->id,
                    'class' => $job->class,
                    'data' => json_decode($job->data, true),
                    'attempts' => $job->attempts,
                    'max_attempts' => $job->max_attempts,
                    'status' => $job->status,
                    'error' => $job->error ?? null,
                    'created_at' => $job->created_at,
                    'updated_at' => $job->updated_at
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get job status from database", [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Clear completed jobs older than specified hours
     */
    public static function clearOldJobs(int $hoursOld = 24): int
    {
        try {
            return DB::table('sync_queue_jobs')
                ->where('status', 'completed')
                ->where('updated_at', '<', now()->subHours($hoursOld))
                ->delete();
        } catch (\Exception $e) {
            Log::warning("Failed to clear old jobs", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Get buffer statistics
     */
    public static function getStats(): array
    {
        $memoryStats = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0
        ];

        foreach (self::$buffer as $job) {
            $status = $job['status'];
            if (isset($memoryStats[$status])) {
                $memoryStats[$status]++;
            }
        }

        try {
            $dbStats = DB::table('sync_queue_jobs')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        } catch (\Exception $e) {
            $dbStats = [];
        }

        return [
            'memory' => $memoryStats,
            'database' => $dbStats,
            'is_processing' => self::$processing
        ];
    }
}
