# Database & Data Structure

HashtagCMS uses a highly normalized, multi-tenant database schema designed to support multiple sites, platforms, and languages from a single installation. The architecture is organized into four distinct layers.

## 1. The "Context" Layer (Identity)
This layer defines the identity and global constraints of the system. Everything in HashtagCMS is isolated by `site_id`, but these tables define the valid options available globally.

*   **`sites`**: The root tenant table. All content is child to a site.
*   **`langs`**: A master list of available languages (e.g., English, Arabic).
*   **`platforms`**: Device/Target contexts (e.g., Web, Mobile, PWA).
*   **`countries` & `currencies`**: Global geographical and financial definitions.

---

## 2. The "Structural" Layer (Building Blocks)
These tables define the "what" and "how" of the content, independent of where they appear.

*   **`modules`**: The functional units. They store the logic type (SQL query, Service, etc.) and the target Blade view. 
    *   *Note: Modules can be "Shared" across sites or site-specific.*
*   **`hooks`**: Placeholders in your HTML layout (e.g., `HEAD_TOP`, `BODY_END`) where modules can be attached.
*   **`categories`**: Logical groupings of content, typically represented as site navigation or URL segments.
*   **`themes`**: Folder references for the Blade skeletons and assets.

---

## 3. The "Glue" Layer (Site-Wise Relational Logic)
This is the heart of the system. Instead of hard links, HashtagCMS uses pivot tables to "activate" and "position" building blocks for a specific site.

*   **`lang_site` & `platform_site`**: Determines which languages and platforms are enabled for a specific site.
*   **`category_site`**: Links a Category to a Site/Platform combination and assigns a **Theme**.
*   **`module_site`**: The most critical configuration table. It maps:
    `Site` + `Platform` + `Category` + `Hook` + `Module`.
    *   **Logic**: *"On Site A, for the Web Platform, in the Products Category, put the 'Price Filter Module' into the 'Left Column Hook' at Position 1."*
*   **`sitewise` (Conceptual)**: Many tables like `hook_site`, `currency_site`, etc., follow this pattern to localize configuration.

---

## 4. The "Content" Layer (I18n)
All translatable strings are stored in separate "Lang" tables to keep the main structural tables light.

*   **Pattern**: `[table_name]_langs` (e.g., `category_langs`).
*   **Keys**: Usually a composite primary key of `(parent_id, lang_id)`.
*   **Fields**: Titles, descriptions, and SEO metadata (Meta Title, Keywords, etc.).

---

## Relationship Summary
To build a working site from scratch, the dependency order is:
1.  **Global Definitions** (Langs, Platforms)
2.  **Site Creation** (`sites`)
3.  **Context Activation** (`lang_site`, `platform_site`)
4.  **Design Definitions** (`themes`, `hooks`, `modules`)
5.  **Page Mapping** (`category_site` -> `module_site`)
6.  **Translation** (`*_langs`)

## Data Governance
-   **AdminBaseModel**: Backend models automatically handle auditable fields like `insert_by`, `update_by`, and soft deletes.
-   **Scopes**: The core system applies `SiteScope` and `LangScope` to almost every query, ensuring users only see content relevant to their current domain and language.

## Using Models
The models are located in `vendor/hashtagcms/hashtagcms/src/Models`.

### Fetching Data
```php
use HashtagCMS\Models\Page;

// Fetch page with translations
$page = Page::with('lang')->find(1);
echo $page->lang->title;
```

### Extending Models
If you need to add business logic to a Core Model, extend it in your `app/Models/` folder:

```php
namespace App\Models;
use HashtagCMS\Models\Page as BasePage;

class Page extends BasePage {
    public function getReadTimeAttribute() {
        return ceil(str_word_count($this->lang->content) / 200);
    }
}
```

> **Note**: While you can extend models for your own usage, the CMS Core uses direct class references, so your extended methods won't be visible in internal CMS logic unless you perform deep dependency injection overrides (advanced).
