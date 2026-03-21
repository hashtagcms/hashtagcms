# Site Cloner Service

The `SiteClonerService` is a powerful administrative utility designed to replicate an entire site's configuration, modules, and theme assignments from a source site to a target site. This is particularly useful for staged rollouts or creating microsites from a base template.

## 🏗️ Architecture

The cloning process is orchestrated through a series of discrete steps, ensuring atomic operations and maintainability.

### Core Components
- **Service**: `HashtagCMS\Services\SiteCloner\SiteClonerService`
- **Steps**: Located in `src/Services/SiteCloner/Steps/`

## 🚀 Usage

Site cloning is orchestrated programmatically via the `SiteClonerService`.

### Programmatic Invocation

```php
use HashtagCMS\Services\SiteCloner\SiteClonerService;

public function replicate(SiteClonerService $cloner) {
    try {
        // Clone from Source Site ID to Target Site ID
        $results = $cloner->clone($sourceSiteId, $targetSiteId);
        // $results contains a summary of copied items
    } catch (\Exception $e) {
        // Handle validation or copy errors
    }
}
```


## 📋 Cloning Steps

The service executes the following sequence:

1.  **Attach Pivot Relations**: Replicates many-to-many relationships such as Platforms, Active Languages, and Hooks connected to the target site.
2.  **Copy Settings**: Duplicates high-level site settings including Module assignments, Themes, and Categories.
3.  **Update Defaults**: Synchronizes the target site's default category, theme, and platform to match the source.
4.  **Copy Module by Category**: Re-associates all modules with their respective categories in the new site context.

## ⚠️ Important Considerations
- **Validation**: The source and target sites must be different.
- **Data Isolation**: This service clones *configurations* and *structural assignments*. Content within specific modules (like Static Module text) is handled based on its global/site-specific scope settings.
- **Async Support**: For large sites, the cloner supports asynchronous execution via Laravel Jobs to prevent timeouts.

## Next Steps
- [Multisite Management](./05-multisite.md)
- [Architecture Overview](./04-architecture.md)
