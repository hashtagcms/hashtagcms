# Site Cloner Refactoring

## Overview

The `cloneSite` method in `SiteController` has been refactored from a monolithic 130+ line method into a clean, maintainable service-oriented architecture.

## Problem

The original `cloneSite` method had multiple issues:

1. **Too many responsibilities** - Authorization, validation, pivot attachments, copying settings, updating defaults, copying modules
2. **Long method** (130+ lines) - Hard to understand and maintain
3. **Nested loops** - Difficult to follow logic
4. **Mixed abstraction levels** - Low-level database queries mixed with high-level business logic
5. **Hard to test** - Impossible to unit test individual pieces
6. **Inconsistent error handling** - Some errors caught, others not
7. **Code duplication** - Similar patterns repeated throughout

## Solution

### Architecture

The refactoring follows the **Single Responsibility Principle** and **Strategy Pattern**:

```
SiteController::cloneSite()
    ↓
SiteClonerService (Orchestrator)
    ↓
    ├── AttachPivotRelationsStep
    ├── CopySettingsStep
    ├── UpdateTargetSiteDefaultsStep
    └── CopyModuleByCategoryStep
```

### New Structure

#### 1. **SiteClonerService** (Main Orchestrator)
- **Location**: `src/Services/SiteCloner/SiteClonerService.php`
- **Responsibility**: Orchestrates the cloning process through multiple steps
- **Benefits**:
  - Clear, linear flow
  - Easy to add/remove steps
  - Centralized result aggregation

```php
public function clone(int $sourceSiteId, int $targetSiteId): array
{
    $this->validateSites($sourceSiteId, $targetSiteId);
    
    // Step 1: Attach pivot relations
    $this->addResults($this->attachPivotStep->execute($targetSiteId));
    
    // Step 2: Copy settings
    $this->addResults($this->copySettingsStep->execute($sourceSiteId, $targetSiteId));
    
    // Step 3: Update target site defaults
    $this->addResults($this->updateDefaultsStep->execute($sourceSite, $targetSite));
    
    // Step 4: Copy modules by category
    $this->addResults($this->copyModulesStep->execute($sourceSiteId, $targetSiteId));
    
    return $this->results;
}
```

#### 2. **AttachPivotRelationsStep**
- **Location**: `src/Services/SiteCloner/Steps/AttachPivotRelationsStep.php`
- **Responsibility**: Attach pivot relations (platforms, hooks, languages, zones, countries, currencies)
- **Benefits**:
  - Isolated logic for pivot attachments
  - Easy to modify which items to attach
  - Proper error handling per item

#### 3. **CopySettingsStep**
- **Location**: `src/Services/SiteCloner/Steps/CopySettingsStep.php`
- **Responsibility**: Copy settings (modules, themes, categories, site properties, module properties)
- **Benefits**:
  - Reusable copy logic
  - Consistent error handling
  - Easy to add new item types

#### 4. **UpdateTargetSiteDefaultsStep**
- **Location**: `src/Services/SiteCloner/Steps/UpdateTargetSiteDefaultsStep.php`
- **Responsibility**: Update target site with default values from source
- **Benefits**:
  - Clear separation of concerns
  - Reusable category/theme matching logic
  - Null-safe operations

#### 5. **CopyModuleByCategoryStep**
- **Location**: `src/Services/SiteCloner/Steps/CopyModuleByCategoryStep.php`
- **Responsibility**: Copy modules by category and platform
- **Benefits**:
  - Complex nested logic broken into smaller methods
  - Easy to understand flow
  - Better error messages

### Updated Controller

The `SiteController::cloneSite()` method is now clean and focused:

```php
public function cloneSite($source_site_id = null, $target_site_id = null)
{
    // Check authorization
    if (!$this->checkPolicy('edit')) {
        // ... return error
    }

    // Get site IDs from request if not provided
    if (empty($source_site_id)) {
        $data = request()->all();
        $source_site_id = $data['sourceSiteId'];
        $target_site_id = $data['tagetSiteId'];
    }

    try {
        // Use the SiteClonerService to handle the cloning
        $clonerService = app(\MarghoobSuleman\HashtagCms\Services\SiteCloner\SiteClonerService::class);
        $results = $clonerService->clone((int) $source_site_id, (int) $target_site_id);
        
        return $results;
    } catch (\InvalidArgumentException $e) {
        // Handle validation errors
    } catch (\Exception $e) {
        // Handle general errors
    }
}
```

## Benefits

### 1. **Maintainability**
- Each class has a single, clear responsibility
- Easy to locate and fix bugs
- Changes to one step don't affect others

### 2. **Testability**
- Each step can be unit tested independently
- Mock dependencies easily
- Test edge cases without running entire clone process

### 3. **Readability**
- Clear, descriptive class and method names
- Linear flow in the orchestrator
- Self-documenting code

### 4. **Extensibility**
- Add new steps by creating new classes
- Modify existing steps without touching controller
- Easy to add logging, events, or notifications

### 5. **Error Handling**
- Consistent error handling across all steps
- Detailed error messages with context
- Graceful degradation (one step failure doesn't stop others)

### 6. **Dependency Injection**
- Steps are injected into the service
- Easy to swap implementations
- Better for testing and mocking

## Usage

### Basic Usage

```php
// In controller or anywhere
$clonerService = app(\MarghoobSuleman\HashtagCms\Services\SiteCloner\SiteClonerService::class);
$results = $clonerService->clone($sourceSiteId, $targetSiteId);
```

### Testing

```php
// Unit test a single step
$step = new AttachPivotRelationsStep();
$results = $step->execute($targetSiteId);
$this->assertArrayHasKey('success', $results[0]);
```

### Extending

To add a new step:

1. Create a new class in `src/Services/SiteCloner/Steps/`
2. Implement an `execute()` method that returns an array of results
3. Inject it into `SiteClonerService`
4. Call it in the `clone()` method

```php
// Example: Add a new step
class CopyUserPermissionsStep
{
    public function execute(int $sourceSiteId, int $targetSiteId): array
    {
        // Your logic here
        return [
            ['message' => 'Permissions copied', 'component' => 'permissions', 'success' => true]
        ];
    }
}

// In SiteClonerService constructor
public function __construct(
    protected AttachPivotRelationsStep $attachPivotStep,
    protected CopySettingsStep $copySettingsStep,
    protected UpdateTargetSiteDefaultsStep $updateDefaultsStep,
    protected CopyModuleByCategoryStep $copyModulesStep,
    protected CopyUserPermissionsStep $copyPermissionsStep // New step
) {}

// In clone() method
$this->addResults($this->copyPermissionsStep->execute($sourceSiteId, $targetSiteId));
```

## Migration Notes

- **No breaking changes** - The API remains the same
- **Backward compatible** - Returns the same result format
- **Drop-in replacement** - No changes needed in calling code
- **Service container** - Uses Laravel's service container for dependency injection

## Performance

- **No performance degradation** - Same database queries
- **Potential improvements** - Easier to add caching, queuing, or parallel processing
- **Memory efficient** - Results aggregated incrementally

## Future Improvements

1. **Add events** - Fire events at each step for logging/monitoring
2. **Add queuing** - Make cloning async for large sites
3. **Add progress tracking** - Real-time progress updates
4. **Add rollback** - Ability to rollback failed clones
5. **Add validation** - Pre-flight checks before cloning
6. **Add caching** - Cache frequently accessed data

## Conclusion

This refactoring transforms a complex, monolithic method into a clean, maintainable, and testable service-oriented architecture. The code is now easier to understand, modify, and extend while maintaining full backward compatibility.
