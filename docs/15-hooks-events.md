# Hooks & Events

HashtagCMS allows you to tap into its lifecycle without modifying the core using its Hook system (Zones) and standard Laravel Events.

## CMS Hooks (Zones)

In HashtagCMS terminology, a "Hook" is primarily a **Zone** or **Position** within a theme where modules can be placed.
Examples: `Header`, `Footer`, `LeftSidebar`, `HomePageTop`.

-   **Management**: Go to **Admin > Hooks** to create or manage hooks.
-   **Usage**: In your Theme Skeleton or Blade files, you define placeholders like `%{cms.hook.Header}%`.
-   **Modules**: You assign modules to these hooks via **Admin > Platform > Site > Zone** (or Drag & Drop).

---

## System Events (Laravel Events)

HashtagCMS fires standard Laravel events during its execution lifecycle. You can define listeners in your `EventServiceProvider` to execute custom logic (e.g., logging, analytics, data modification).

### Available Events

| Event Class | Description | Payload Data |
| :--- | :--- | :--- |
| `HashtagCms\Events\ModuleLoaded` | Fired immediately after a module's data is loaded/processed. | `$module` (Module Object), `$data` (Processed Data) |
| `HashtagCms\Events\PageLoaded` | Fired after the entire page content (all modules) is resolved and ready. | `$data` (Full API Response Array) |
| `HashtagCms\Events\UserVisit` | Fired when a user visits a page (useful for tracking). | `$data` (Request Info, IP, etc.) |

### Example: Listening to ModuleLoaded

1.  **Create a Listener**:
    ```bash
    php artisan make:listener LogModuleData
    ```

2.  **Implement Logic**:
    ```php
    namespace App\Listeners;
    
    use HashtagCms\Events\ModuleLoaded;
    use Illuminate\Support\Facades\Log;
    
    class LogModuleData
    {
        public function handle(ModuleLoaded $event)
        {
            $moduleName = $event->module->alias;
            // Log specific module data
            if ($moduleName === 'featured-products') {
                Log::info("Featured Products Loaded: " . count($event->data));
            }
        }
    }
    ```

3.  **Register Listener**:
    In `app/Providers/EventServiceProvider.php`:
    ```php
    use HashtagCms\Events\ModuleLoaded;
    use App\Listeners\LogModuleData;
    
    protected $listen = [
        ModuleLoaded::class => [
            LogModuleData::class,
        ],
    ];
    ```

---

## Interceptors (Middleware)

You can key into the request lifecycle *before* the CMS processes data by using Middleware.

### Global Middleware
Register in `app/Http/Kernel.php` to affect all routes.

### CMS-Specific Middleware
Register in `config/hashtagcms.php`:
```php
'additional_middleware' => [
    \App\Http\Middleware\MyCustomLogic::class,
],
```

### Example: modifying Site Context
```php
public function handle($request, Closure $next)
{
    // Force a specific language based on IP
    if ($request->ip() === '127.0.0.1') {
        $request->merge(['lang' => 'fr']);
    }
    return $next($request);
}
```
