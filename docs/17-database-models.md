# Database & Models

## Schema Overview

The database is normalized to support high scalability.

### Core Tables
-   `sites`: The tenant root.
-   `platforms`: Web, iOS, etc.
-   `langs`: Available languages.

### Content Tables
-   `categories` + `category_langs`
-   `pages` + `page_langs`
-   `modules` + `module_props`
-   `static_module_contents` + `static_module_content_langs`

### Pivot/Relation Tables
-   `site_user`: Which users access which site.
-   `module_site`: **Crucial**. Maps a Module to a (Site + Platform + Category + Hook).
-   `category_site`: Maps a Category to a Site.

## Using Models
The models are located in `vendor/hashtagcms/hashtagcms/src/Models`.

### Fetching Data
```php
use HashtagCms\Models\Page;

// Fetch page with translations
$page = Page::with('lang')->find(1);
echo $page->lang->title;
```

### Extending Models
If you need to add business logic to a Core Model, extend it in your `app/Models/` folder:

```php
namespace App\Models;
use HashtagCms\Models\Page as BasePage;

class Page extends BasePage {
    public function getReadTimeAttribute() {
        return ceil(str_word_count($this->lang->content) / 200);
    }
}
```

> **Note**: While you can extend models for your own usage, the CMS Core uses direct class references, so your extended methods won't be visible in internal CMS logic unless you perform deep dependency injection overrides (advanced).
