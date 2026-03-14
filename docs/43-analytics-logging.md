# Analytics & Logging Service

HashtagCMS includes a lightweight internal analytics service designed to track content engagement without the overhead of external trackers. It primarily focuses on measuring `read_count` for Pages and Categories.

## ⚙️ How it Works

The `AnalyticsLogger` uses a buffered approach to optimize database performance. Instead of writing to the database on every request, it increments counts in memory and flushes them to the database at the end of the request lifecycle.

### Core Components
- **Service**: `HashtagCMS\Services\AnalyticsLogger`
- **Models Affected**: `Page`, `Category`

## 🚀 Implementation

The logger is automatically triggered during the request termination phase in monolithic (Blade-based) installs.

### Manual Logging

If you are building custom modules or extending the API, you can manually log interactions:

```php
use HashtagCMS\Services\AnalyticsLogger;

public function show(Page $page, AnalyticsLogger $logger) {
    // Log a page view
    $logger->log('page', $page->id);
    
    return view('page.show', compact('page'));
}
```

## 📊 Data Persistence

- **Buffer**: The service maintains an internal `$buffer` array.
- **Flush**: The `flush()` method is called during `terminate()`. It iterates through the buffer and performs an atomic `increment('read_count', $count)` on the respective models.
- **Optimization**: It utilizes `withoutGlobalScopes()` to ensure updates occur regardless of current site/lang context filters.

## 🛠️ Configuration

While essentially automatic, you can check the `read_count` column in the `pages` or `categories` tables to verify tracking. 

> [!TIP]
> This data can be easily exposed in your frontend using a **Query Module** to show "Most Popular Posts" or "Trending Categories".

---

## Next Steps
- [Module Development](./10-modules.md)
- [Performance Optimization](./28-performance.md)
