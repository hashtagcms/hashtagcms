# Site Cloner - Before vs After

## Before (Monolithic)

```
┌─────────────────────────────────────────────────────────────┐
│                   SiteController::cloneSite()                │
│                         (130+ lines)                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ✗ Authorization check                                      │
│  ✗ Validate site IDs                                        │
│  ✗ Check if source == target                                │
│  ✗ Load source and target sites                             │
│  ✗ Empty try-catch block                                    │
│                                                              │
│  ✗ Loop through pivot items (6 items)                       │
│    ├─ Resolve model class                                   │
│    ├─ Get all IDs                                            │
│    ├─ Call saveSettings()                                    │
│    └─ Build result message                                   │
│                                                              │
│  ✗ Loop through items to copy (6 items)                     │
│    ├─ Get data from source site                             │
│    ├─ Call copySettings()                                    │
│    ├─ Count copied and ignored                               │
│    └─ Build result message                                   │
│                                                              │
│  ✗ Get source site defaults                                 │
│  ✗ Find matching category in target                         │
│  ✗ Find matching theme in target                            │
│  ✗ Update target site                                        │
│  ✗ Save target site                                          │
│                                                              │
│  ✗ Load source site with platforms                          │
│  ✗ Get all categories from source                           │
│  ✗ Loop through categories                                  │
│    ├─ Find matching category in target                      │
│    └─ Loop through platforms                                │
│       ├─ Build fromData                                     │
│       ├─ Build toData                                       │
│       ├─ Call Module::copyData()                            │
│       └─ Build result message                               │
│                                                              │
│  ✗ Return results array                                     │
│                                                              │
└─────────────────────────────────────────────────────────────┘

Problems:
- Too many responsibilities
- Hard to test
- Hard to maintain
- Nested loops
- Mixed abstraction levels
- Inconsistent error handling
```

## After (Service-Oriented)

```
┌──────────────────────────────────────────────────────────────┐
│              SiteController::cloneSite()                      │
│                    (40 lines)                                 │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ✓ Authorization check                                       │
│  ✓ Get site IDs from request                                 │
│  ✓ Delegate to SiteClonerService                             │
│  ✓ Handle exceptions                                          │
│  ✓ Return results                                             │
│                                                               │
└──────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────┐
│                   SiteClonerService                           │
│                  (Orchestrator - 60 lines)                    │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ✓ Validate sites (source != target)                         │
│  ✓ Load source and target sites                              │
│  ✓ Execute steps in order                                    │
│  ✓ Aggregate results                                          │
│  ✓ Return results                                             │
│                                                               │
└──────────────────────────────────────────────────────────────┘
            │              │              │              │
            ▼              ▼              ▼              ▼
┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│   Step 1    │  │   Step 2    │  │   Step 3    │  │   Step 4    │
├─────────────┤  ├─────────────┤  ├─────────────┤  ├─────────────┤
│   Attach    │  │    Copy     │  │   Update    │  │    Copy     │
│   Pivot     │  │  Settings   │  │   Target    │  │   Modules   │
│ Relations   │  │             │  │   Site      │  │     By      │
│             │  │             │  │  Defaults   │  │  Category   │
│ (70 lines)  │  │ (80 lines)  │  │ (90 lines)  │  │ (120 lines) │
└─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘

Benefits:
✓ Single responsibility per class
✓ Easy to test each step independently
✓ Easy to maintain and modify
✓ Clear, linear flow
✓ Consistent error handling
✓ Easy to extend with new steps
```

## Code Comparison

### Before: cloneSite() - 130+ lines

```php
public function cloneSite($source_site_id = null, $target_site_id = null)
{
    // Authorization (5 lines)
    if (! $this->checkPolicy('edit')) { ... }
    
    // Get IDs (5 lines)
    if (empty($source_site_id)) { ... }
    
    // Validate (5 lines)
    if ($source_site_id == $target_site_id) { ... }
    
    // Load sites (5 lines)
    $siteInfo = Site::allInfo($source_site_id);
    $targetSiteInfo = Site::find($target_site_id);
    if ($targetSiteInfo === null || $siteInfo === null) { ... }
    
    // Empty try-catch (3 lines)
    try { } catch (\Exception $exception) { }
    
    // Attach pivot items (15 lines)
    $itemsToAttach = ['platform', 'hook', 'language', 'zone', 'country', 'currency'];
    foreach ($itemsToAttach as $key => $item) {
        $finder = ($item == 'language') ? 'lang' : $item;
        $finder = Str::title($finder);
        $namespace = config('hashtagcms.namespace');
        $finder = resolve($namespace.'Models\\\\'.$finder);
        $attach['key'] = Str::plural($item);
        $attach['ids'] = $finder::all('id')->pluck('id')->toArray();
        $attach['site_id'] = $target_site_id;
        $attach['action'] = 'add';
        $res = $this->saveSettings($attach);
        $msg = (! empty($res) && $res['isSaved']) ? Str::title($item).' copied' : "Unable to copy $item";
        $datas[] = ['message' => $msg, 'component' => $item];
    }
    
    // Copy settings (20 lines)
    $itemsToCopy = ['modules', 'staticmodules', 'themes', 'categories', 'siteproperties', 'moduleproperties'];
    foreach ($itemsToCopy as $key => $item) {
        try {
            $data['fromSite'] = ['site_id' => $source_site_id, 'data' => $this->getBySite($source_site_id, $item)->toArray()];
            $data['toSite'] = ['site_id' => $target_site_id];
            $data['type'] = $item;
            $res = $this->copySettings($data);
            $title = $item;
            $ignored = count($res['ignored']);
            $copied = count($res['copied']);
            $datas[] = ['message' => "$copied $title copied and $ignored $title ignored", 'component' => $item];
        } catch (Exception $exception) {
            $datas[] = ['message' => "$copied $title copied and $ignored $title ignored", 'component' => $item];
            $datas[] = $exception->getMessage();
        }
    }
    
    // Update target site (20 lines)
    $category_id = $siteInfo->category_id;
    $theme_id = $siteInfo->theme_id;
    $platform_id = $siteInfo->platform_id;
    $lang_id = $siteInfo->lang_id;
    $country_id = $siteInfo->country_id;
    $categoryInfo = Category::withoutGlobalScopes()->where('id', '=', $category_id)->first();
    $targetCategoryInfo = Category::withoutGlobalScopes()->where([['link_rewrite', '=', $categoryInfo->link_rewrite], ['site_id', '=', $target_site_id]])->first();
    $themeInfo = Theme::withoutGlobalScopes()->where('id', '=', $theme_id)->first();
    $targetThemeInfo = Theme::withoutGlobalScopes()->where([['alias', '=', $themeInfo->alias], ['site_id', '=', $target_site_id]])->first();
    $targetSiteInfo->category_id = $targetCategoryInfo->id;
    $targetSiteInfo->theme_id = $targetThemeInfo->id;
    $targetSiteInfo->platform_id = $platform_id;
    $targetSiteInfo->lang_id = $lang_id;
    $targetSiteInfo->country_id = $country_id;
    $targetSiteInfo->save();
    
    // Copy modules by category (30 lines)
    $site = Site::with(['platform'])->find($source_site_id);
    $allTeants = $site->platform;
    $categories = Category::withoutGlobalScopes()->where('site_id', '=', $source_site_id)->get();
    $data = ['success' => false];
    foreach ($categories as $category) {
        $link_rewrite = $category->link_rewrite;
        $tagetCategoryInfo = Category::withoutGlobalScopes()->where([['site_id', '=', $target_site_id], ['link_rewrite', '=', $link_rewrite]])->first();
        if ($tagetCategoryInfo) {
            foreach ($allTeants as $platform) {
                $fromData['site_id'] = $source_site_id;
                $fromData['platform_id'] = $platform->id;
                $fromData['category_id'] = $category->id;
                $fromData['microsite_id'] = 0;
                $toData['site_id'] = $target_site_id;
                $toData['platform_id'] = $platform->id;
                $toData['category_id'] = $tagetCategoryInfo->id;
                $toData['microsite_id'] = 0;
                $data = Module::copyData($fromData, $toData);
                $datas[] = ['success' => $data['success'], 'message' => $data['message']." - $link_rewrite, and platform: {$platform->link_rewrite}", 'component' => 'module_site_copy', 'data' => ['fromData' => $fromData, 'toData' => $toData]];
            }
        } else {
            $datas[] = ['success' => false, 'message' => "Could not find cateogry $link_rewrite in target site", 'component' => 'module_site_copy'];
        }
    }
    
    return $datas;
}
```

### After: cloneSite() - 40 lines

```php
public function cloneSite($source_site_id = null, $target_site_id = null)
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
        $source_site_id = $data['sourceSiteId'];
        $target_site_id = $data['tagetSiteId'];
    }

    try {
        // Use the SiteClonerService to handle the cloning
        $clonerService = app(\MarghoobSuleman\HashtagCms\Services\SiteCloner\SiteClonerService::class);
        $results = $clonerService->clone((int) $source_site_id, (int) $target_site_id);
        
        return $results;
    } catch (\InvalidArgumentException $e) {
        $errorData = [
            'status' => 400,
            'title' => 'Alert',
            'message' => $e->getMessage()
        ];
        return response()->json($errorData, $errorData['status']);
    } catch (\Exception $e) {
        $errorData = [
            'status' => 500,
            'title' => 'Error',
            'message' => 'Failed to clone site: ' . $e->getMessage()
        ];
        return response()->json($errorData, $errorData['status']);
    }
}
```

## Metrics

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Lines in controller method | 130+ | 40 | **69% reduction** |
| Cyclomatic complexity | ~25 | ~5 | **80% reduction** |
| Number of responsibilities | 7 | 1 | **86% reduction** |
| Nesting depth | 4 levels | 2 levels | **50% reduction** |
| Testability | Very hard | Easy | **Significantly improved** |
| Maintainability | Low | High | **Significantly improved** |
| Reusability | None | High | **Significantly improved** |

## Summary

The refactoring transforms a complex, monolithic method into a clean, maintainable architecture:

- **Controller**: Handles HTTP concerns only (authorization, request/response)
- **Service**: Orchestrates the business logic
- **Steps**: Each handles a specific part of the cloning process

This makes the code:
- ✅ Easier to understand
- ✅ Easier to test
- ✅ Easier to maintain
- ✅ Easier to extend
- ✅ More reliable
