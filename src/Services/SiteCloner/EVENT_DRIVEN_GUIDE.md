# Event-Driven Site Cloning

## Overview

The site cloning feature has been enhanced with an **event-driven, queue-based architecture** that provides:

- ✅ **Asynchronous execution** - No HTTP timeouts
- ✅ **Real-time progress tracking** - Know exactly what's happening
- ✅ **Resilience** - Failed jobs can be retried
- ✅ **Scalability** - Multiple clones can run in parallel
- ✅ **Better UX** - Immediate response, background processing

## Architecture

```
┌──────────────┐
│   Browser    │
│   (User)     │
└──────┬───────┘
       │ 1. POST /admin/site/clone-async
       ▼
┌──────────────────────┐
│  SiteController      │
│  cloneSiteAsync()    │
└──────┬───────────────┘
       │ 2. Dispatch Job
       ▼
┌──────────────────────┐         ┌─────────────────┐
│   CloneSiteJob       │────────▶│  Queue Worker   │
│   (Queued)           │         └─────────────────┘
└──────┬───────────────┘
       │ 3. Execute
       ▼
┌──────────────────────┐
│  SiteClonerService   │
│  (Business Logic)    │
└──────┬───────────────┘
       │ 4. Fire Events
       ▼
┌──────────────────────────────────────────────┐
│              Event System                     │
├──────────────┬──────────────┬────────────────┤
│   Started    │  Progress    │   Completed    │
│   Event      │  Event       │   Event        │
└──────┬───────┴──────┬───────┴────────┬───────┘
       │              │                │
       ▼              ▼                ▼
┌──────────────────────────────────────────────┐
│           Event Listeners                     │
│  - Cache status                               │
│  - Log progress                               │
│  - Send notifications                         │
│  - Broadcast to websockets                    │
└──────────────────────────────────────────────┘
       │
       ▼
┌──────────────┐
│   Browser    │◀─── 5. Poll for status
│   (User)     │     GET /admin/site/clone/status/{jobId}
└──────────────┘
```

## Components

### 1. Events

#### `SiteCloningStarted`
Fired when cloning begins.

```php
event(new SiteCloningStarted(
    $sourceSiteId,
    $targetSiteId,
    $jobId,
    $userId
));
```

#### `SiteCloningProgress`
Fired during each step of the cloning process.

```php
event(new SiteCloningProgress(
    $jobId,
    $step,        // e.g., 'pivot_relations', 'settings'
    $message,     // Human-readable message
    $currentStep, // 1, 2, 3...
    $totalSteps,  // Total number of steps
    $success,     // true/false
    $data         // Additional data
));
```

#### `SiteCloningCompleted`
Fired when cloning completes successfully.

```php
event(new SiteCloningCompleted(
    $jobId,
    $sourceSiteId,
    $targetSiteId,
    $results,
    $duration
));
```

#### `SiteCloningFailed`
Fired when cloning fails.

```php
event(new SiteCloningFailed(
    $jobId,
    $sourceSiteId,
    $targetSiteId,
    $error,
    $step,
    $partialResults
));
```

### 2. Queue Job

#### `CloneSiteJob`
Handles the actual cloning in the background.

**Features:**
- 3 retry attempts
- 1-hour timeout
- Dedicated queue (`site-cloner`)
- Progress tracking
- Comprehensive error handling

```php
CloneSiteJob::dispatch(
    $sourceSiteId,
    $targetSiteId,
    $userId,
    $jobId
);
```

### 3. Event Listeners

#### `HandleSiteCloningStarted`
- Caches initial job status
- Logs start event
- Optionally sends notification

#### `HandleSiteCloningProgress`
- Updates cached status
- Tracks progress percentage
- Maintains step history
- Can broadcast to websockets

#### `HandleSiteCloningCompleted`
- Updates final status
- Logs completion
- Sends success notification

#### `HandleSiteCloningFailed`
- Logs error details
- Caches failure info
- Sends failure notification

## Usage

### Basic Usage

#### 1. Start Cloning (Async)

```php
POST /admin/site/clone-async

{
    "sourceSiteId": 1,
    "tagetSiteId": 2
}

Response (202 Accepted):
{
    "status": 202,
    "title": "Cloning Started",
    "message": "Site cloning has been queued...",
    "job_id": "clone_5f8a7b2c3d1e4",
    "source_site": {
        "id": 1,
        "name": "Source Site",
        "domain": "source.com"
    },
    "target_site": {
        "id": 2,
        "name": "Target Site",
        "domain": "target.com"
    },
    "polling_url": "/admin/site/clone/status/clone_5f8a7b2c3d1e4",
    "estimated_time": "5-10 minutes"
}
```

#### 2. Poll for Status

```php
GET /admin/site/clone/status/{jobId}

Response:
{
    "job_id": "clone_5f8a7b2c3d1e4",
    "status": "in_progress",
    "progress": 45.5,
    "current_step": "settings",
    "current_message": "Copying modules...",
    "step_number": 15,
    "total_steps": 33,
    "started_at": "2025-12-21T11:00:00Z",
    "updated_at": "2025-12-21T11:02:30Z",
    "steps": [
        {
            "step": "pivot_relations",
            "message": "Platform copied",
            "success": true,
            "timestamp": "2025-12-21T11:00:15Z"
        },
        // ... more steps
    ]
}
```

#### 3. Completion Status

```php
GET /admin/site/clone/status/{jobId}

Response:
{
    "job_id": "clone_5f8a7b2c3d1e4",
    "status": "completed",
    "progress": 100,
    "current_step": "Completed",
    "results": [...],
    "duration": 342.5,
    "started_at": "2025-12-21T11:00:00Z",
    "completed_at": "2025-12-21T11:05:42Z"
}
```

### Frontend Implementation

#### JavaScript Example

```javascript
// Start cloning
async function cloneSite(sourceSiteId, targetSiteId) {
    const response = await fetch('/admin/site/clone-async', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            sourceSiteId,
            tagetSiteId: targetSiteId
        })
    });
    
    const data = await response.json();
    
    if (response.status === 202) {
        // Start polling for status
        pollCloneStatus(data.job_id);
    }
}

// Poll for status
function pollCloneStatus(jobId) {
    const interval = setInterval(async () => {
        const response = await fetch(`/admin/site/clone/status/${jobId}`);
        const status = await response.json();
        
        // Update UI with progress
        updateProgressBar(status.progress);
        updateStatusMessage(status.current_message);
        
        // Check if completed or failed
        if (status.status === 'completed') {
            clearInterval(interval);
            showSuccess('Site cloned successfully!');
        } else if (status.status === 'failed') {
            clearInterval(interval);
            showError(`Cloning failed: ${status.error}`);
        }
    }, 2000); // Poll every 2 seconds
}

// Update UI
function updateProgressBar(progress) {
    document.querySelector('.progress-bar').style.width = `${progress}%`;
    document.querySelector('.progress-text').textContent = `${progress}%`;
}

function updateStatusMessage(message) {
    document.querySelector('.status-message').textContent = message;
}
```

#### Vue.js Example

```vue
<template>
  <div class="site-cloner">
    <button @click="startCloning" :disabled="isCloning">
      Clone Site
    </button>
    
    <div v-if="isCloning" class="progress-container">
      <div class="progress-bar">
        <div class="progress-fill" :style="{ width: progress + '%' }"></div>
      </div>
      <p class="status-message">{{ statusMessage }}</p>
      <p class="progress-text">{{ progress }}%</p>
    </div>
    
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isCloning: false,
      progress: 0,
      statusMessage: '',
      error: null,
      pollInterval: null
    };
  },
  
  methods: {
    async startCloning() {
      this.isCloning = true;
      this.error = null;
      
      try {
        const response = await fetch('/admin/site/clone-async', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.csrfToken
          },
          body: JSON.stringify({
            sourceSiteId: this.sourceSiteId,
            tagetSiteId: this.targetSiteId
          })
        });
        
        const data = await response.json();
        
        if (response.status === 202) {
          this.pollStatus(data.job_id);
        }
      } catch (error) {
        this.error = error.message;
        this.isCloning = false;
      }
    },
    
    pollStatus(jobId) {
      this.pollInterval = setInterval(async () => {
        try {
          const response = await fetch(`/admin/site/clone/status/${jobId}`);
          const status = await response.json();
          
          this.progress = status.progress;
          this.statusMessage = status.current_message;
          
          if (status.status === 'completed') {
            this.handleSuccess(status);
          } else if (status.status === 'failed') {
            this.handleFailure(status);
          }
        } catch (error) {
          this.handleFailure({ error: error.message });
        }
      }, 2000);
    },
    
    handleSuccess(status) {
      clearInterval(this.pollInterval);
      this.isCloning = false;
      this.$emit('clone-completed', status);
      // Show success notification
    },
    
    handleFailure(status) {
      clearInterval(this.pollInterval);
      this.isCloning = false;
      this.error = status.error;
      this.$emit('clone-failed', status);
    }
  },
  
  beforeUnmount() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval);
    }
  }
};
</script>
```

## Configuration

### 1. Register Events and Listeners

In your `EventServiceProvider`:

```php
protected $listen = [
    \MarghoobSuleman\HashtagCms\Events\SiteCloner\SiteCloningStarted::class => [
        \MarghoobSuleman\HashtagCms\Listeners\SiteCloner\HandleSiteCloningStarted::class,
    ],
    \MarghoobSuleman\HashtagCms\Events\SiteCloner\SiteCloningProgress::class => [
        \MarghoobSuleman\HashtagCms\Listeners\SiteCloner\HandleSiteCloningProgress::class,
    ],
    \MarghoobSuleman\HashtagCms\Events\SiteCloner\SiteCloningCompleted::class => [
        \MarghoobSuleman\HashtagCms\Listeners\SiteCloner\HandleSiteCloningCompleted::class,
    ],
    \MarghoobSuleman\HashtagCms\Events\SiteCloner\SiteCloningFailed::class => [
        \MarghoobSuleman\HashtagCms\Listeners\SiteCloner\HandleSiteCloningFailed::class,
    ],
];
```

### 2. Configure Queue

In `.env`:

```env
QUEUE_CONNECTION=redis  # or database, sqs, etc.
```

### 3. Run Queue Worker

```bash
# Development
php artisan queue:work --queue=site-cloner

# Production (with Supervisor)
[program:hashtagcms-site-cloner]
command=php /path/to/artisan queue:work redis --queue=site-cloner --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/hashtagcms-site-cloner.log
```

### 4. Add Routes

```php
// In routes/web.php or admin routes
Route::post('admin/site/clone-async', [SiteController::class, 'cloneSiteAsync'])
    ->name('admin.site.clone.async');
    
Route::get('admin/site/clone/status/{jobId}', [SiteController::class, 'getCloneStatus'])
    ->name('admin.site.clone.status');
```

## Advanced Features

### 1. WebSocket Broadcasting (Optional)

For real-time updates without polling:

```php
// In events, implement ShouldBroadcast
class SiteCloningProgress implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('site-cloner.' . $this->jobId);
    }
}
```

Frontend:

```javascript
Echo.private(`site-cloner.${jobId}`)
    .listen('SiteCloningProgress', (e) => {
        updateProgress(e.progress);
    })
    .listen('SiteCloningCompleted', (e) => {
        showSuccess();
    });
```

### 2. Email Notifications

```php
// In listeners
use Illuminate\Support\Facades\Notification;
use App\Notifications\SiteCloningCompletedNotification;

public function handle(SiteCloningCompleted $event)
{
    $user = User::find($event->userId);
    $user->notify(new SiteCloningCompletedNotification($event));
}
```

### 3. Retry Failed Jobs

```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry {job-id}
```

## Benefits Summary

| Feature | Synchronous | Event-Driven |
|---------|-------------|--------------|
| **Response Time** | 5-10 minutes | < 1 second |
| **Timeout Risk** | High | None |
| **Progress Tracking** | No | Yes |
| **Retry on Failure** | No | Yes |
| **Parallel Execution** | No | Yes |
| **User Experience** | Poor | Excellent |
| **Resource Usage** | Blocks server | Efficient |
| **Scalability** | Limited | High |

## Migration Path

You can support both approaches:

```php
// Synchronous (legacy)
Route::post('admin/site/clone', [SiteController::class, 'cloneSite']);

// Asynchronous (new)
Route::post('admin/site/clone-async', [SiteController::class, 'cloneSiteAsync']);
```

Gradually migrate users to the async version.

## Conclusion

The event-driven approach provides a **production-ready, scalable solution** for site cloning that:

- ✅ Never times out
- ✅ Provides real-time feedback
- ✅ Handles failures gracefully
- ✅ Scales to multiple concurrent clones
- ✅ Delivers excellent user experience

This is the **recommended approach** for any long-running operation in a web application!
