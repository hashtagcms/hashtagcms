<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Jobs\CloneSiteJob;
use HashtagCms\Models\Site;

/**
 * Trait for handling async site cloning
 */
trait AsyncSiteCloning
{
    /**
     * Clone a site asynchronously using queue
     * 
     * @param int|null $source_site_id
     * @param int|null $target_site_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cloneSiteAsync($source_site_id = null, $target_site_id = null)
    {
        // Check authorization
        if (!$this->checkPolicy('edit')) {
            if (\request()->ajax()) {
                return response()->json(Message::getWriteError(), 401);
            }
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        // Get site IDs from request if not provided
        if (empty($source_site_id)) {
            $data = request()->all();
            $source_site_id = $data['sourceSiteId'] ?? null;
            $target_site_id = $data['tagetSiteId'] ?? null;
        }

        // Validate input
        if (empty($source_site_id) || empty($target_site_id)) {
            return response()->json([
                'status' => 400,
                'title' => 'Validation Error',
                'message' => 'Source and target site IDs are required'
            ], 400);
        }

        // Validate sites are different
        if ($source_site_id == $target_site_id) {
            return response()->json([
                'status' => 400,
                'title' => 'Validation Error',
                'message' => "Source and target site cannot be the same"
            ], 400);
        }

        // Validate sites exist
        $sourceSite = Site::find($source_site_id);
        $targetSite = Site::find($target_site_id);

        if (!$sourceSite || !$targetSite) {
            return response()->json([
                'status' => 404,
                'title' => 'Not Found',
                'message' => 'Source or target site not found'
            ], 404);
        }

        try {
            // Generate job ID
            $jobId = uniqid('clone_', true);

            // Use smart dispatcher - automatically uses queue or sync buffer
            $dispatchResult = \HashtagCms\Queue\SmartJobDispatcher::dispatchSiteCloning(
                (int) $source_site_id,
                (int) $target_site_id,
                Auth::id(),
                $jobId
            );

            // Return immediate response with job ID
            return response()->json([
                'status' => 202, // Accepted
                'title' => 'Cloning Started',
                'message' => $dispatchResult['message'],
                'job_id' => $jobId,
                'dispatch_method' => $dispatchResult['method'], // 'queue' or 'sync_buffer'
                'source_site' => [
                    'id' => $sourceSite->id,
                    'name' => $sourceSite->name,
                    'domain' => $sourceSite->domain
                ],
                'target_site' => [
                    'id' => $targetSite->id,
                    'name' => $targetSite->name,
                    'domain' => $targetSite->domain
                ],
                'polling_url' => route('admin.site.clone.status', ['jobId' => $jobId]),
                'estimated_time' => '5-10 minutes',
                'note' => $dispatchResult['method'] === 'sync_buffer'
                    ? 'Queue not configured - using sync buffer. For better performance, configure Redis or database queue.'
                    : 'Job queued successfully'
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'title' => 'Error',
                'message' => 'Failed to queue site cloning: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the status of a cloning job
     * 
     * @param string $jobId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCloneStatus(string $jobId)
    {
        // Check authorization
        if (!$this->checkPolicy('read')) {
            return response()->json(Message::getReadError(), 401);
        }

        try {
            // Use smart dispatcher to get status (works for both queue and sync buffer)
            $status = \HashtagCms\Queue\SmartJobDispatcher::getJobStatus($jobId);

            if (!$status) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Job not found or expired'
                ], 404);
            }

            return response()->json($status);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to get job status: ' . $e->getMessage()
            ], 500);
        }
    }
}
